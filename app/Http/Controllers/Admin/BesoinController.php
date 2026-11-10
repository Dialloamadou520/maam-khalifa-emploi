<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecruitmentRequestStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CandidateProfile;
use App\Models\RecruitmentRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BesoinController extends Controller
{
    public function index(Request $request): View
    {
        $statut = $request->validate(['statut' => ['nullable', Rule::enum(RecruitmentRequestStatus::class)]])['statut'] ?? null;

        return view('admin.besoins.index', [
            'besoins' => RecruitmentRequest::with('company')->withCount('matches')
                ->when($statut, fn (Builder $q) => $q->where('statut', $statut))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'statut' => $statut,
        ]);
    }

    public function show(Request $request, RecruitmentRequest $besoin): View
    {
        $q = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? null;
        $besoin->load(['company', 'traitePar', 'matches.candidat.candidateProfile']);

        $suggestions = CandidateProfile::query()
            ->with('user')
            ->whereHas('user', fn (Builder $u) => $u->where('role', Role::Candidat)->where('est_actif', true))
            ->whereNotIn('user_id', $besoin->matches->pluck('candidate_id'))
            ->when($q, fn (Builder $b, string $t) => $b->where(fn (Builder $w) => $w
                ->whereLike('titre_profil', "%{$t}%")
                ->orWhereLike('competences', "%{$t}%")
                ->orWhereLike('domaine', "%{$t}%")
                ->orWhereHas('user', fn (Builder $u) => $u->whereLike('name', "%{$t}%"))))
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('admin.besoins.show', ['besoin' => $besoin, 'suggestions' => $suggestions, 'q' => $q]);
    }

    public function update(Request $request, RecruitmentRequest $besoin): RedirectResponse
    {
        $data = $request->validate([
            'statut' => ['required', Rule::enum(RecruitmentRequestStatus::class)],
            'note_admin' => ['nullable', 'string', 'max:3000'],
        ]);

        $besoin->forceFill([...$data, 'traitee_par' => $request->user()->id])->save();
        ActivityLog::enregistrer('besoin', "Besoin « {$besoin->intitule_poste} » : {$besoin->statut->label()}", $besoin);

        return redirect()->route('admin.besoins.show', $besoin)->with('status', 'Besoin mis à jour.');
    }
}
