<?php

namespace App\Http\Controllers\Candidat;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Paiement des prestations par le client (candidat ou entreprise). Le client déclare son
 * paiement (Wave, Orange Money…) avec la référence de transaction ; l'agence le confirme.
 */
class PaiementController extends Controller
{
    public function index(Request $request): View
    {
        return view('paiements.index', [
            'paiements' => $request->user()->payments()->with('serviceRequest.service')->latest()->paginate(15),
            'prefixe' => $this->prefixe($request),
        ]);
    }

    public function create(Request $request, ServiceRequest $dossier): View|RedirectResponse
    {
        $this->autoriser($request, $dossier);

        if ($this->resteAPayer($dossier) <= 0) {
            return redirect()->route($this->prefixe($request).'.dossiers.show', $dossier)->with('status', 'Cette prestation est déjà réglée.');
        }

        return view('paiements.create', [
            'dossier' => $dossier->load('service'),
            'reste' => $this->resteAPayer($dossier),
            'enAttente' => $dossier->payments()->where('statut', PaymentStatus::EnAttente)->exists(),
            'prefixe' => $this->prefixe($request),
        ]);
    }

    public function store(Request $request, ServiceRequest $dossier): RedirectResponse
    {
        $this->autoriser($request, $dossier);
        $mobile = [PaymentMethod::Wave->value, PaymentMethod::OrangeMoney->value, PaymentMethod::FreeMoney->value];

        $data = $request->validate([
            'methode' => ['required', Rule::enum(PaymentMethod::class)],
            'telephone' => [Rule::requiredIf(in_array($request->input('methode'), $mobile, true)), 'nullable', 'string', 'max:30'],
            'transaction_id' => [Rule::requiredIf($request->input('methode') !== PaymentMethod::Especes->value), 'nullable', 'string', 'max:100'],
        ], [], ['methode' => 'moyen de paiement', 'transaction_id' => 'référence de la transaction']);

        $reste = $this->resteAPayer($dossier);
        abort_if($reste <= 0, 422);

        $paiement = new Payment([
            'service_request_id' => $dossier->id,
            'montant' => $reste,
            'methode' => $data['methode'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'meta' => array_filter(['telephone' => $data['telephone'] ?? null]),
        ]);
        $paiement->user_id = $request->user()->id;
        $paiement->save();

        ActivityLog::enregistrer('paiement', "Paiement {$paiement->reference} déclaré pour {$dossier->reference} (".fcfa($paiement->montant).')', $paiement);

        return redirect()->route($this->prefixe($request).'.dossiers.show', $dossier)
            ->with('status', 'Paiement enregistré : l\'agence le confirme dès réception.');
    }

    private function resteAPayer(ServiceRequest $dossier): int
    {
        return max(0, $dossier->montant - (int) $dossier->payments()->where('statut', PaymentStatus::Payee)->sum('montant'));
    }

    private function autoriser(Request $request, ServiceRequest $dossier): void
    {
        abort_unless($dossier->user_id === $request->user()->id, 403);
    }

    private function prefixe(Request $request): string
    {
        return $request->user()->isRecruteur() ? 'recruteur' : 'candidat';
    }
}
