<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Enums\RecruitmentRequestStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CandidateMatch;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\RecruitmentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MiseEnRelationController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->validate(['statut' => ['nullable', Rule::enum(MatchStatus::class)]])['statut'] ?? null;

        return view('admin.mises-en-relation.index', [
            'misesEnRelation' => CandidateMatch::with(['candidat', 'company', 'jobOffer', 'recruitmentRequest', 'creePar'])
                ->when($statut, fn (Builder $q) => $q->where('statut', $statut))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'statut' => $statut,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.mises-en-relation.create', [
            'candidats' => User::where('role', Role::Candidat)->where('est_actif', true)->orderBy('name')->get(['id', 'name', 'email']),
            'entreprises' => Company::orderBy('nom')->get(['id', 'nom']),
            'offres' => JobOffer::where('statut', OfferStatus::Publiee)->with('company:id,nom')->orderBy('titre')->get(['id', 'titre', 'company_id']),
            'besoins' => RecruitmentRequest::whereIn('statut', [RecruitmentRequestStatus::Soumise, RecruitmentRequestStatus::EnTraitement])
                ->with('company:id,nom')->latest()->get(['id', 'intitule_poste', 'company_id']),
            'choix' => $request->only(['candidat', 'entreprise', 'offre', 'besoin']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'candidate_id' => ['required', Rule::exists('users', 'id')->where('role', Role::Candidat->value)],
            'company_id' => ['nullable', 'exists:companies,id'],
            'job_offer_id' => ['nullable', 'exists:job_offers,id'],
            'recruitment_request_id' => ['nullable', 'exists:recruitment_requests,id'],
            'commentaire' => ['nullable', 'string', 'max:2000'],
        ], [], ['candidate_id' => 'candidat', 'company_id' => 'entreprise']);

        $besoin = isset($data['recruitment_request_id']) ? RecruitmentRequest::find($data['recruitment_request_id']) : null;
        $offre = isset($data['job_offer_id']) ? JobOffer::find($data['job_offer_id']) : null;
        $entreprises = collect([$besoin?->company_id, $offre?->company_id, isset($data['company_id']) ? (int) $data['company_id'] : null])->filter()->unique();

        if ($entreprises->isEmpty()) {
            throw ValidationException::withMessages(['company_id' => 'Choisissez une entreprise, une offre ou un besoin.']);
        }
        if ($entreprises->count() > 1) {
            throw ValidationException::withMessages(['company_id' => 'L\'offre, le besoin et l\'entreprise choisis ne correspondent pas.']);
        }

        $companyId = $entreprises->first();
        $enCours = CandidateMatch::where('candidate_id', $data['candidate_id'])->where('company_id', $companyId)
            ->whereIn('statut', [MatchStatus::Proposee, MatchStatus::Acceptee])->exists();
        if ($enCours) {
            return back()->withInput()->with('erreur', 'Une mise en relation est déjà en cours entre ce candidat et cette entreprise.');
        }

        $match = new CandidateMatch([...$data, 'company_id' => $companyId]);
        $match->cree_par = $request->user()->id;
        $match->save();

        if ($besoin?->statut === RecruitmentRequestStatus::Soumise) {
            $besoin->forceFill(['statut' => RecruitmentRequestStatus::EnTraitement, 'traitee_par' => $request->user()->id])->save();
        }

        $match->load(['candidat', 'company']);
        ActivityLog::enregistrer('mise_en_relation', "{$match->candidat->name} proposé(e) à {$match->company->nom}", $match);

        return ($besoin ? redirect()->route('admin.besoins.show', $besoin) : redirect()->route('admin.mises-en-relation.index'))
            ->with('status', 'Mise en relation enregistrée.');
    }

    public function update(Request $request, CandidateMatch $miseEnRelation): RedirectResponse
    {
        $data = $request->validate([
            'statut' => ['required', Rule::enum(MatchStatus::class)],
            'commentaire' => ['nullable', 'string', 'max:2000'],
        ]);

        $miseEnRelation->update($data);
        ActivityLog::enregistrer('mise_en_relation', "Mise en relation {$miseEnRelation->candidat->name} / {$miseEnRelation->company->nom} : {$miseEnRelation->statut->label()}", $miseEnRelation);

        return back()->with('status', 'Mise en relation mise à jour.');
    }
}
