<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Gestion des comptes candidats et recruteurs, et accès centralisé à leurs documents.
 */
class UtilisateurController extends Controller
{
    public function candidats(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'etat' => ['nullable', Rule::in(['actifs', 'inactifs'])],
        ]);

        return view('admin.utilisateurs.candidats', [
            'utilisateurs' => $this->recherche(Role::Candidat, $filtres)
                ->with('candidateProfile')
                ->withCount(['jobApplications', 'serviceRequests', 'documents'])
                ->paginate(20)
                ->withQueryString(),
            'filtres' => $filtres,
        ]);
    }

    public function recruteurs(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'etat' => ['nullable', Rule::in(['actifs', 'inactifs', 'a_verifier'])],
        ]);

        return view('admin.utilisateurs.recruteurs', [
            'utilisateurs' => $this->recherche(Role::Recruteur, $filtres)
                ->with(['company' => fn ($q) => $q->withCount(['jobOffers', 'recruitmentRequests'])])
                ->paginate(20)
                ->withQueryString(),
            'filtres' => $filtres,
        ]);
    }

    public function show(User $utilisateur): View
    {
        abort_if($utilisateur->isAdmin(), 404);

        return view('admin.utilisateurs.show', [
            'utilisateur' => $utilisateur->load([
                'candidateProfile', 'documents', 'jobApplications.jobOffer.company', 'serviceRequests.service',
                'company' => fn ($q) => $q->with(['jobOffers' => fn ($o) => $o->withCount('applications')->latest(), 'recruitmentRequests']),
            ]),
        ]);
    }

    public function basculer(User $utilisateur): RedirectResponse
    {
        abort_if($utilisateur->isAdmin(), 403);

        $utilisateur->forceFill(['est_actif' => ! $utilisateur->est_actif])->save();
        $etat = $utilisateur->est_actif ? 'réactivé' : 'désactivé';
        ActivityLog::enregistrer('utilisateur', "Compte de {$utilisateur->name} {$etat}", $utilisateur);

        return back()->with('status', "Compte {$etat}.");
    }

    public function verifier(User $utilisateur): RedirectResponse
    {
        abort_unless($utilisateur->isRecruteur() && $utilisateur->company, 404);

        $entreprise = $utilisateur->company;
        $entreprise->forceFill(['est_verifiee' => ! $entreprise->est_verifiee])->save();
        $etat = $entreprise->est_verifiee ? 'vérifiée' : 'remise en attente de vérification';
        ActivityLog::enregistrer('entreprise', "Entreprise {$entreprise->nom} {$etat}", $entreprise);

        return back()->with('status', "Entreprise {$etat}.");
    }

    public function document(Document $document, DocumentStorage $stockage): StreamedResponse
    {
        return $stockage->telecharger($document);
    }

    private function recherche(Role $role, array $filtres): Builder
    {
        return User::query()
            ->where('role', $role)
            ->when($filtres['q'] ?? null, fn (Builder $q, string $t) => $q->where(fn (Builder $w) => $w
                ->whereLike('name', "%{$t}%")
                ->orWhereLike('email', "%{$t}%")
                ->orWhereLike('telephone', "%{$t}%")
                ->orWhereHas('company', fn (Builder $c) => $c->whereLike('nom', "%{$t}%"))))
            ->when(($filtres['etat'] ?? null) === 'actifs', fn (Builder $q) => $q->where('est_actif', true))
            ->when(($filtres['etat'] ?? null) === 'inactifs', fn (Builder $q) => $q->where('est_actif', false))
            ->when(($filtres['etat'] ?? null) === 'a_verifier', fn (Builder $q) => $q->whereHas('company', fn (Builder $c) => $c->where('est_verifiee', false)))
            ->latest();
    }
}
