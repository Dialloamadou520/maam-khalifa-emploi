<?php

namespace App\Http\Controllers\Recruteur;

use App\Enums\ContractType;
use App\Enums\RecruitmentRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Recruteur\Concerns\AvecEntreprise;
use App\Models\ActivityLog;
use App\Models\RecruitmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Besoins en recrutement confiés à l'agence (recherche et présélection de candidats).
 */
class BesoinController extends Controller
{
    use AvecEntreprise;

    public function index(Request $request): View
    {
        return view('recruteur.besoins.index', [
            'besoins' => $this->entreprise($request)->recruitmentRequests()->withCount('matches')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('recruteur.besoins.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'intitule_poste' => ['required', 'string', 'max:255'],
            'nombre_postes' => ['required', 'integer', 'min:1', 'max:500'],
            'type_contrat' => ['required', Rule::enum(ContractType::class)],
            'lieu' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:10000'],
            'competences_requises' => ['nullable', 'string', 'max:3000'],
            'budget' => ['nullable', 'integer', 'min:0'],
            'date_souhaitee' => ['nullable', 'date', 'after_or_equal:today'],
        ], [], [
            'intitule_poste' => 'intitulé du poste',
            'nombre_postes' => 'nombre de postes',
            'type_contrat' => 'type de contrat',
            'competences_requises' => 'compétences requises',
            'date_souhaitee' => 'date d\'embauche souhaitée',
        ]);

        $besoin = $this->entreprise($request)->recruitmentRequests()->create($data);
        ActivityLog::enregistrer('besoin', "Besoin en recrutement « {$besoin->intitule_poste} » soumis par {$besoin->company->nom}", $besoin);

        return redirect()->route('recruteur.besoins.show', $besoin)->with('status', 'Votre besoin a été transmis à l\'agence, qui vous proposera des profils.');
    }

    public function show(Request $request, RecruitmentRequest $besoin): View
    {
        $this->verifierEntreprise($request, $besoin->company_id);

        return view('recruteur.besoins.show', [
            'besoin' => $besoin->load('matches.candidat.candidateProfile'),
        ]);
    }

    public function annuler(Request $request, RecruitmentRequest $besoin): RedirectResponse
    {
        $this->verifierEntreprise($request, $besoin->company_id);

        if ($besoin->statut !== RecruitmentRequestStatus::Soumise) {
            return back()->with('erreur', 'Ce besoin est déjà pris en charge par l\'agence : contactez-la pour l\'annuler.');
        }

        $besoin->forceFill(['statut' => RecruitmentRequestStatus::Annulee])->save();
        ActivityLog::enregistrer('besoin', "Besoin « {$besoin->intitule_poste} » annulé", $besoin);

        return redirect()->route('recruteur.besoins.show', $besoin)->with('status', 'Besoin annulé.');
    }
}
