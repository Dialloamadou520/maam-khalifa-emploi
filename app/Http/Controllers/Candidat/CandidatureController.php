<?php

namespace App\Http\Controllers\Candidat;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Services\DocumentStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CandidatureController extends Controller
{
    public function __construct(private readonly DocumentStorage $stockage) {}

    public function index(Request $request): View
    {
        $statut = $request->validate(['statut' => ['nullable', Rule::enum(ApplicationStatus::class)]])['statut'] ?? null;

        return view('candidat.candidatures.index', [
            'candidatures' => $request->user()->jobApplications()
                ->with('jobOffer.company')
                ->when($statut, fn ($q) => $q->where('statut', $statut))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'statut' => $statut,
        ]);
    }

    public function create(Request $request, JobOffer $offre): View|RedirectResponse
    {
        abort_unless($offre->estVisible(), 404);

        if ($this->dejaPostule($request, $offre)) {
            return redirect()->route('candidat.candidatures.index')->with('erreur', 'Vous avez déjà postulé à cette offre.');
        }

        $documents = $request->user()->documents()->latest()->get();

        return view('candidat.candidatures.create', [
            'offre' => $offre->load('company'),
            'cvs' => $documents->where('type', DocumentType::Cv),
            'lettres' => $documents->where('type', DocumentType::LettreMotivation),
        ]);
    }

    public function store(Request $request, JobOffer $offre): RedirectResponse
    {
        abort_unless($offre->estVisible(), 404);

        if ($this->dejaPostule($request, $offre)) {
            return redirect()->route('candidat.candidatures.index')->with('erreur', 'Vous avez déjà postulé à cette offre.');
        }

        $user = $request->user();
        $fichier = ['file', 'mimes:'.DocumentStorage::EXTENSIONS, 'max:'.DocumentStorage::TAILLE_MAX_KO];

        $data = $request->validate([
            'cv_document_id' => ['nullable', 'required_without:cv_fichier', Rule::exists('documents', 'id')->where('user_id', $user->id)->where('type', DocumentType::Cv->value)],
            'cv_fichier' => ['nullable', 'required_without:cv_document_id', ...$fichier],
            'lettre_document_id' => ['nullable', Rule::exists('documents', 'id')->where('user_id', $user->id)->where('type', DocumentType::LettreMotivation->value)],
            'lettre_fichier' => ['nullable', ...$fichier],
            'message' => ['nullable', 'string', 'max:3000'],
        ], [
            'cv_document_id.required_without' => 'Choisissez un CV ou déposez-en un nouveau.',
            'cv_fichier.required_without' => 'Choisissez un CV ou déposez-en un nouveau.',
        ]);

        DB::transaction(function () use ($request, $user, $offre, $data) {
            $cvId = $request->hasFile('cv_fichier')
                ? $this->stockage->deposer($user, $request->file('cv_fichier'), DocumentType::Cv)->id
                : $data['cv_document_id'];

            $lettreId = $request->hasFile('lettre_fichier')
                ? $this->stockage->deposer($user, $request->file('lettre_fichier'), DocumentType::LettreMotivation)->id
                : ($data['lettre_document_id'] ?? null);

            $candidature = new JobApplication([
                'job_offer_id' => $offre->id,
                'cv_document_id' => $cvId,
                'lettre_document_id' => $lettreId,
                'message' => $data['message'] ?? null,
            ]);
            $candidature->user_id = $user->id;
            $candidature->save();

            ActivityLog::enregistrer('candidature', "{$user->name} a postulé à « {$offre->titre} »", $candidature);
        });

        return redirect()->route('candidat.candidatures.index')->with('status', 'Votre candidature a bien été envoyée.');
    }

    public function destroy(Request $request, JobApplication $candidature): RedirectResponse
    {
        abort_unless($candidature->user_id === $request->user()->id, 403);

        if ($candidature->statut !== ApplicationStatus::Envoyee) {
            return back()->with('erreur', 'Cette candidature est déjà en cours de traitement et ne peut plus être retirée.');
        }

        $candidature->delete();

        return back()->with('status', 'Candidature retirée.');
    }

    private function dejaPostule(Request $request, JobOffer $offre): bool
    {
        return $request->user()->jobApplications()->where('job_offer_id', $offre->id)->exists();
    }
}
