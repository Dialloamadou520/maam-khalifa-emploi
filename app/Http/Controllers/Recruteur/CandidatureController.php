<?php

namespace App\Http\Controllers\Recruteur;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Recruteur\Concerns\AvecEntreprise;
use App\Models\ActivityLog;
use App\Models\JobApplication;
use App\Services\DocumentStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidatureController extends Controller
{
    use AvecEntreprise;

    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'offre' => ['nullable', 'integer'],
            'statut' => ['nullable', Rule::enum(ApplicationStatus::class)],
        ]);
        $entreprise = $this->entreprise($request);

        return view('recruteur.candidatures.index', [
            'candidatures' => $entreprise->jobApplications()
                ->with(['candidat.candidateProfile', 'jobOffer'])
                ->when($filtres['offre'] ?? null, fn ($q, $id) => $q->where('job_applications.job_offer_id', $id))
                ->when($filtres['statut'] ?? null, fn ($q, $s) => $q->where('job_applications.statut', $s))
                ->latest('job_applications.created_at')
                ->paginate(20)
                ->withQueryString(),
            'offres' => $entreprise->jobOffers()->orderBy('titre')->get(['id', 'titre']),
            'filtres' => $filtres,
        ]);
    }

    public function show(Request $request, JobApplication $candidature): View
    {
        $this->autoriser($request, $candidature);

        if ($candidature->statut === ApplicationStatus::Envoyee) {
            $candidature->update(['statut' => ApplicationStatus::EnExamen]);
        }

        return view('recruteur.candidatures.show', [
            'candidature' => $candidature->load(['candidat.candidateProfile', 'jobOffer', 'cv', 'lettre']),
        ]);
    }

    public function update(Request $request, JobApplication $candidature): RedirectResponse
    {
        $this->autoriser($request, $candidature);

        $data = $request->validate([
            'statut' => ['required', Rule::enum(ApplicationStatus::class)->except([ApplicationStatus::Envoyee])],
            'note_recruteur' => ['nullable', 'string', 'max:3000'],
        ]);

        $candidature->update($data);
        ActivityLog::enregistrer('candidature', "Candidature de {$candidature->candidat->name} : {$candidature->statut->label()}", $candidature);

        return redirect()->route('recruteur.candidatures.show', $candidature)->with('status', 'Statut de la candidature mis à jour.');
    }

    public function document(Request $request, JobApplication $candidature, string $type, DocumentStorage $stockage): StreamedResponse
    {
        $this->autoriser($request, $candidature);

        $document = match ($type) {
            'cv' => $candidature->cv,
            'lettre' => $candidature->lettre,
            default => null,
        };
        abort_if($document === null, 404);

        return $stockage->telecharger($document);
    }

    private function autoriser(Request $request, JobApplication $candidature): void
    {
        $this->verifierEntreprise($request, $candidature->jobOffer->company_id);
    }
}
