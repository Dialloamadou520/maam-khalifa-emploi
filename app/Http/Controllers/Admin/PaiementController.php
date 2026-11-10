<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaiementController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'statut' => ['nullable', Rule::enum(PaymentStatus::class)],
            'methode' => ['nullable', Rule::enum(PaymentMethod::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        return view('admin.paiements.index', [
            'paiements' => Payment::with(['user', 'serviceRequest.service', 'enregistrePar'])
                ->when($filtres['statut'] ?? null, fn (Builder $q, string $s) => $q->where('statut', $s))
                ->when($filtres['methode'] ?? null, fn (Builder $q, string $m) => $q->where('methode', $m))
                ->when($filtres['q'] ?? null, fn (Builder $q, string $t) => $q->where(fn (Builder $w) => $w
                    ->whereLike('reference', "%{$t}%")
                    ->orWhereLike('transaction_id', "%{$t}%")
                    ->orWhereHas('user', fn (Builder $u) => $u->whereLike('name', "%{$t}%"))
                    ->orWhereHas('serviceRequest', fn (Builder $d) => $d->whereLike('reference', "%{$t}%"))))
                ->orderByRaw('case when statut = ? then 0 else 1 end', [PaymentStatus::EnAttente->value])
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'totaux' => [
                'encaisse' => (int) Payment::where('statut', PaymentStatus::Payee)->sum('montant'),
                'mois' => (int) Payment::where('statut', PaymentStatus::Payee)->where('payee_le', '>=', now()->startOfMonth())->sum('montant'),
                'en_attente' => Payment::where('statut', PaymentStatus::EnAttente)->count(),
                'impaye' => (int) ServiceRequest::whereNot('statut', 'annule')->get()->sum(fn (ServiceRequest $d) => max(0, $d->montant - $d->payments()->where('statut', PaymentStatus::Payee)->sum('montant'))),
            ],
            'filtres' => $filtres,
        ]);
    }

    public function store(Request $request, ServiceRequest $dossier): RedirectResponse
    {
        $data = $request->validate([
            'montant' => ['required', 'integer', 'min:1'],
            'methode' => ['required', Rule::enum(PaymentMethod::class)],
            'transaction_id' => ['nullable', 'string', 'max:100'],
        ]);

        $paiement = new Payment([...$data, 'service_request_id' => $dossier->id, 'statut' => PaymentStatus::Payee, 'payee_le' => now()]);
        $paiement->user_id = $dossier->user_id;
        $paiement->enregistre_par = $request->user()->id;
        $paiement->save();

        ActivityLog::enregistrer('paiement', "Encaissement {$paiement->reference} de ".fcfa($paiement->montant)." pour {$dossier->reference}", $paiement);

        return redirect()->route('admin.dossiers.show', $dossier)->with('status', 'Paiement enregistré.');
    }

    public function confirmer(Request $request, Payment $paiement): RedirectResponse
    {
        if ($paiement->statut !== PaymentStatus::EnAttente) {
            return back()->with('erreur', 'Ce paiement a déjà été traité.');
        }

        $paiement->forceFill(['statut' => PaymentStatus::Payee, 'payee_le' => now(), 'enregistre_par' => $request->user()->id])->save();
        ActivityLog::enregistrer('paiement', "Paiement {$paiement->reference} confirmé (".fcfa($paiement->montant).')', $paiement);

        return back()->with('status', "Paiement {$paiement->reference} confirmé.");
    }

    public function rejeter(Request $request, Payment $paiement): RedirectResponse
    {
        if ($paiement->statut !== PaymentStatus::EnAttente) {
            return back()->with('erreur', 'Ce paiement a déjà été traité.');
        }

        $paiement->forceFill(['statut' => PaymentStatus::Echouee, 'enregistre_par' => $request->user()->id])->save();
        ActivityLog::enregistrer('paiement', "Paiement {$paiement->reference} rejeté (transaction introuvable)", $paiement);

        return back()->with('status', "Paiement {$paiement->reference} rejeté.");
    }
}
