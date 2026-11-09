<?php

namespace App\Http\Controllers\Recruteur;

use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Recruteur\Concerns\AvecEntreprise;
use App\Models\ActivityLog;
use App\Models\CandidateMatch;
use App\Models\CandidateProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Recherche de profils (candidats ayant accepté d'être visibles). Les coordonnées restent
 * confidentielles : l'entreprise passe par une demande de mise en relation traitée par l'agence.
 */
class ProfilController extends Controller
{
    use AvecEntreprise;

    public const NIVEAUX = ['Sans diplôme', 'BFEM', 'Baccalauréat', 'BTS / DUT', 'Licence', 'Master', 'Doctorat'];

    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'domaine' => ['nullable', 'string', 'max:150'],
            'niveau' => ['nullable', Rule::in(self::NIVEAUX)],
            'experience' => ['nullable', 'integer', 'min:0', 'max:40'],
            'ville' => ['nullable', 'string', 'max:100'],
        ]);

        $profils = $this->profilsVisibles()
            ->with('user')
            ->when($filtres['q'] ?? null, fn (Builder $q, string $terme) => $q->where(fn (Builder $w) => $w
                ->whereLike('titre_profil', "%{$terme}%")
                ->orWhereLike('competences', "%{$terme}%")
                ->orWhereLike('presentation', "%{$terme}%")))
            ->when($filtres['domaine'] ?? null, fn (Builder $q, string $d) => $q->whereLike('domaine', "%{$d}%"))
            ->when($filtres['niveau'] ?? null, fn (Builder $q, string $n) => $q->where('niveau_etude', $n))
            ->when(isset($filtres['experience']), fn (Builder $q) => $q->where('annees_experience', '>=', (int) $filtres['experience']))
            ->when($filtres['ville'] ?? null, fn (Builder $q, string $v) => $q->whereLike('ville', "%{$v}%"))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('recruteur.profils.index', ['profils' => $profils, 'filtres' => $filtres, 'niveaux' => self::NIVEAUX]);
    }

    public function show(Request $request, CandidateProfile $profil): View
    {
        abort_unless($this->profilsVisibles()->whereKey($profil->id)->exists(), 404);
        $entreprise = $this->entreprise($request);

        return view('recruteur.profils.show', [
            'profil' => $profil->load('user'),
            'demande' => $entreprise->matches()->where('candidate_id', $profil->user_id)->latest()->first(),
            'offres' => $entreprise->jobOffers()->where('statut', OfferStatus::Publiee)->orderBy('titre')->get(['id', 'titre']),
        ]);
    }

    public function contacter(Request $request, CandidateProfile $profil): RedirectResponse
    {
        abort_unless($this->profilsVisibles()->whereKey($profil->id)->exists(), 404);
        $entreprise = $this->entreprise($request);

        $data = $request->validate([
            'job_offer_id' => ['nullable', Rule::exists('job_offers', 'id')->where('company_id', $entreprise->id)],
            'commentaire' => ['nullable', 'string', 'max:2000'],
        ], [], ['job_offer_id' => 'offre']);

        $dejaEnCours = $entreprise->matches()->where('candidate_id', $profil->user_id)
            ->whereIn('statut', [MatchStatus::Proposee, MatchStatus::Acceptee])->exists();

        if ($dejaEnCours) {
            return back()->with('erreur', 'Une mise en relation avec ce candidat est déjà en cours.');
        }

        $match = new CandidateMatch([
            'candidate_id' => $profil->user_id,
            'company_id' => $entreprise->id,
            'job_offer_id' => $data['job_offer_id'] ?? null,
            'commentaire' => $data['commentaire'] ?? null,
        ]);
        $match->cree_par = $request->user()->id;
        $match->save();

        ActivityLog::enregistrer('mise_en_relation', "{$entreprise->nom} demande une mise en relation avec {$profil->user->name}", $match);

        return redirect()->route('recruteur.profils.show', $profil)->with('status', 'Demande transmise : l\'agence organise la mise en relation avec ce candidat.');
    }

    private function profilsVisibles(): Builder
    {
        return CandidateProfile::query()
            ->where('visible_recruteurs', true)
            ->whereHas('user', fn (Builder $q) => $q->where('role', Role::Candidat)->where('est_actif', true));
    }
}
