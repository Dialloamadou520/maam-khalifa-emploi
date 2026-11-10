<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Centralisation des dossiers de prestations (candidats et entreprises).
 */
class DossierController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'statut' => ['nullable', Rule::enum(ServiceRequestStatus::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        return view('admin.dossiers.index', [
            'dossiers' => ServiceRequest::with(['user', 'service', 'payments'])
                ->when($filtres['statut'] ?? null, fn (Builder $q, string $s) => $q->where('statut', $s))
                ->when($filtres['q'] ?? null, fn (Builder $q, string $t) => $q->where(fn (Builder $w) => $w
                    ->whereLike('reference', "%{$t}%")
                    ->orWhereHas('user', fn (Builder $u) => $u->whereLike('name', "%{$t}%")->orWhereLike('email', "%{$t}%"))))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'filtres' => $filtres,
        ]);
    }

    public function show(ServiceRequest $dossier): View
    {
        return view('admin.dossiers.show', [
            'dossier' => $dossier->load(['user.documents', 'service', 'scholarship', 'payments.enregistrePar', 'traitePar']),
        ]);
    }

    public function update(Request $request, ServiceRequest $dossier): RedirectResponse
    {
        $data = $request->validate([
            'statut' => ['required', Rule::enum(ServiceRequestStatus::class)],
            'note_admin' => ['nullable', 'string', 'max:3000'],
        ], [], ['note_admin' => 'message']);

        $dossier->forceFill([...$data, 'traite_par' => $request->user()->id])->save();
        ActivityLog::enregistrer('dossier', "Dossier {$dossier->reference} : {$dossier->statut->label()}", $dossier);

        return redirect()->route('admin.dossiers.show', $dossier)->with('status', 'Dossier mis à jour.');
    }
}
