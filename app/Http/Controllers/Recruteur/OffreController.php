<?php

namespace App\Http\Controllers\Recruteur;

use App\Enums\ApplicationStatus;
use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Recruteur\Concerns\AvecEntreprise;
use App\Http\Requests\Recruteur\OffreRequest;
use App\Models\ActivityLog;
use App\Models\JobOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Offres de l'entreprise. Toute offre soumise (ou modifiée) repasse en validation par l'agence.
 */
class OffreController extends Controller
{
    use AvecEntreprise;

    public function index(Request $request): View
    {
        $statut = $request->validate(['statut' => ['nullable', Rule::enum(OfferStatus::class)]])['statut'] ?? null;

        return view('recruteur.offres.index', [
            'offres' => $this->entreprise($request)->jobOffers()
                ->withCount('applications')
                ->when($statut, fn ($q) => $q->where('statut', $statut))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'statut' => $statut,
        ]);
    }

    public function create(): View
    {
        return view('recruteur.offres.form', ['offre' => new JobOffer(['nombre_postes' => 1])]);
    }

    public function store(OffreRequest $request): RedirectResponse
    {
        $offre = new JobOffer($request->validated());
        $offre->company_id = $this->entreprise($request)->id;
        $offre->statut = $request->boolean('brouillon') ? OfferStatus::Brouillon : OfferStatus::EnAttente;
        $offre->save();

        ActivityLog::enregistrer('offre', "Offre « {$offre->titre} » ".($offre->statut === OfferStatus::Brouillon ? 'enregistrée en brouillon' : 'soumise à validation'), $offre);

        return redirect()->route('recruteur.offres.show', $offre)->with('status', $offre->statut === OfferStatus::Brouillon
            ? 'Brouillon enregistré.'
            : 'Offre soumise : elle sera publiée après validation par l\'agence.');
    }

    public function show(Request $request, JobOffer $offre): View
    {
        $this->verifierEntreprise($request, $offre->company_id);

        return view('recruteur.offres.show', [
            'offre' => $offre,
            'candidatures' => $offre->applications()->with('candidat.candidateProfile')->latest()->get(),
            'parStatut' => $offre->applications()->selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut'),
            'statuts' => ApplicationStatus::cases(),
        ]);
    }

    public function edit(Request $request, JobOffer $offre): View|RedirectResponse
    {
        $this->verifierEntreprise($request, $offre->company_id);

        if ($offre->statut === OfferStatus::Cloturee) {
            return redirect()->route('recruteur.offres.show', $offre)->with('erreur', 'Une offre clôturée ne peut plus être modifiée.');
        }

        return view('recruteur.offres.form', ['offre' => $offre]);
    }

    public function update(OffreRequest $request, JobOffer $offre): RedirectResponse
    {
        $this->verifierEntreprise($request, $offre->company_id);
        abort_if($offre->statut === OfferStatus::Cloturee, 403);

        $offre->fill($request->validated());

        if ($request->boolean('brouillon') && $offre->statut === OfferStatus::Brouillon) {
            $message = 'Brouillon mis à jour.';
        } else {
            $offre->statut = OfferStatus::EnAttente;
            $offre->motif_rejet = null;
            $message = 'Offre mise à jour : elle repasse en validation par l\'agence.';
        }

        $offre->save();
        ActivityLog::enregistrer('offre', "Offre « {$offre->titre} » modifiée", $offre);

        return redirect()->route('recruteur.offres.show', $offre)->with('status', $message);
    }

    public function cloturer(Request $request, JobOffer $offre): RedirectResponse
    {
        $this->verifierEntreprise($request, $offre->company_id);

        $offre->forceFill(['statut' => OfferStatus::Cloturee])->save();
        ActivityLog::enregistrer('offre', "Offre « {$offre->titre} » clôturée", $offre);

        return redirect()->route('recruteur.offres.show', $offre)->with('status', 'Offre clôturée : elle n\'est plus visible des candidats.');
    }
}
