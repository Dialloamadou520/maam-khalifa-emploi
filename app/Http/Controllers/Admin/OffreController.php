<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Modération des offres : l'agence valide (publie) ou rejette chaque offre soumise.
 */
class OffreController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'statut' => ['nullable', Rule::in([...OfferStatus::values(), 'toutes'])],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $statut = $filtres['statut'] ?? OfferStatus::EnAttente->value;

        return view('admin.offres.index', [
            'offres' => JobOffer::with('company')->withCount('applications')
                ->when($statut !== 'toutes', fn (Builder $q) => $q->where('statut', $statut))
                ->when($filtres['q'] ?? null, fn (Builder $q, string $t) => $q->where(fn (Builder $w) => $w
                    ->whereLike('titre', "%{$t}%")
                    ->orWhereHas('company', fn (Builder $c) => $c->whereLike('nom', "%{$t}%"))))
                ->orderByRaw('case when statut = ? then 0 else 1 end', [OfferStatus::EnAttente->value])
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'compteurs' => JobOffer::toBase()->selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut'),
            'statut' => $statut,
            'q' => $filtres['q'] ?? null,
        ]);
    }

    public function show(JobOffer $offre): View
    {
        return view('admin.offres.show', [
            'offre' => $offre->load(['company.user', 'validateur'])->loadCount('applications'),
        ]);
    }

    public function valider(Request $request, JobOffer $offre): RedirectResponse
    {
        if (! in_array($offre->statut, [OfferStatus::EnAttente, OfferStatus::Rejetee], true)) {
            return back()->with('erreur', 'Seule une offre en attente ou rejetée peut être validée.');
        }

        $offre->forceFill([
            'statut' => OfferStatus::Publiee,
            'publiee_le' => now(),
            'validee_par' => $request->user()->id,
            'motif_rejet' => null,
        ])->save();
        ActivityLog::enregistrer('offre', "Offre « {$offre->titre} » validée et publiée", $offre);

        return redirect()->route('admin.offres.index')->with('status', "Offre « {$offre->titre} » publiée.");
    }

    public function rejeter(Request $request, JobOffer $offre): RedirectResponse
    {
        $data = $request->validate(['motif_rejet' => ['required', 'string', 'max:2000']], [], ['motif_rejet' => 'motif']);

        $offre->forceFill([
            'statut' => OfferStatus::Rejetee,
            'motif_rejet' => $data['motif_rejet'],
            'validee_par' => $request->user()->id,
        ])->save();
        ActivityLog::enregistrer('offre', "Offre « {$offre->titre} » rejetée", $offre);

        return redirect()->route('admin.offres.index')->with('status', "Offre « {$offre->titre} » rejetée.");
    }
}
