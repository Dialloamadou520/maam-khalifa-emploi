<?php

namespace App\Http\Controllers\Recruteur;

use App\Enums\ApplicationStatus;
use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Recruteur\Concerns\AvecEntreprise;
use App\Models\ActivityLog;
use App\Models\CandidateMatch;
use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Suivi des recrutements : entonnoir des candidatures par offre et mises en relation avec l'agence.
 */
class RecrutementController extends Controller
{
    use AvecEntreprise;

    public function index(Request $request): View
    {
        $entreprise = $this->entreprise($request);
        $offres = $entreprise->jobOffers()
            ->whereIn('statut', [OfferStatus::Publiee, OfferStatus::Cloturee])
            ->latest()
            ->get();

        $compteurs = JobApplication::query()
            ->whereIn('job_offer_id', $offres->pluck('id'))
            ->toBase()
            ->selectRaw('job_offer_id, statut, count(*) as total')
            ->groupBy('job_offer_id', 'statut')
            ->get()
            ->groupBy('job_offer_id')
            ->map(fn ($lignes) => $lignes->pluck('total', 'statut')->map(fn ($n) => (int) $n));

        return view('recruteur.recrutements.index', [
            'offres' => $offres,
            'compteurs' => $compteurs,
            'totaux' => collect(ApplicationStatus::cases())->mapWithKeys(fn (ApplicationStatus $s) => [
                $s->value => $compteurs->sum(fn ($c) => $c[$s->value] ?? 0),
            ]),
            'statuts' => ApplicationStatus::cases(),
            'misesEnRelation' => $entreprise->matches()->with(['candidat.candidateProfile', 'jobOffer', 'recruitmentRequest'])->latest()->get(),
        ]);
    }

    public function update(Request $request, CandidateMatch $miseEnRelation): RedirectResponse
    {
        $this->verifierEntreprise($request, $miseEnRelation->company_id);

        $data = $request->validate([
            'statut' => ['required', Rule::enum(MatchStatus::class)->except([MatchStatus::Proposee])],
        ]);

        $miseEnRelation->update($data);
        ActivityLog::enregistrer('mise_en_relation', "Mise en relation avec {$miseEnRelation->candidat->name} : {$miseEnRelation->statut->label()}", $miseEnRelation);

        return redirect()->route('recruteur.recrutements.index')->with('status', 'Suivi mis à jour.');
    }
}
