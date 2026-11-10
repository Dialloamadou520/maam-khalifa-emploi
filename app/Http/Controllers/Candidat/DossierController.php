<?php

namespace App\Http\Controllers\Candidat;

use App\Enums\ServiceTarget;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Scholarship;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Demandes de prestations (CV, coaching, dossier de bourse…) et leur suivi.
 * Partagé par les espaces candidat et recruteur : le rôle détermine les prestations proposées.
 */
class DossierController extends Controller
{
    public function index(Request $request): View
    {
        return view('dossiers.index', [
            'dossiers' => $request->user()->serviceRequests()->with(['service', 'scholarship', 'payments'])->latest()->paginate(15),
            'prefixe' => $this->prefixe($request),
        ]);
    }

    public function create(Request $request): View
    {
        return view('dossiers.create', [
            'services' => Service::actives()->pour($this->cible($request))->get(),
            'bourses' => $request->user()->isCandidat() ? Scholarship::visibles()->orderBy('titre')->get() : collect(),
            'serviceChoisi' => $request->integer('service') ?: null,
            'bourseChoisie' => Scholarship::where('slug', $request->query('bourse'))->value('id'),
            'prefixe' => $this->prefixe($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cible = $this->cible($request);

        $data = $request->validate([
            'service_id' => ['required', Rule::exists('services', 'id')->where('est_active', true)->whereIn('cible', [$cible->value, ServiceTarget::Tous->value])],
            'scholarship_id' => ['nullable', Rule::exists('scholarships', 'id')->where('est_publiee', true)],
            'details' => ['nullable', 'string', 'max:5000'],
        ], [], ['service_id' => 'prestation', 'scholarship_id' => 'bourse']);

        $service = Service::findOrFail($data['service_id']);

        $dossier = $request->user()->serviceRequests()->create([
            'service_id' => $service->id,
            'scholarship_id' => $request->user()->isCandidat() ? ($data['scholarship_id'] ?? null) : null,
            'montant' => $service->prix,
            'details' => $data['details'] ?? null,
        ]);

        ActivityLog::enregistrer('dossier', "{$request->user()->name} a demandé « {$service->nom} » ({$dossier->reference})", $dossier);

        return redirect()->route($this->prefixe($request).'.dossiers.show', $dossier)
            ->with('status', 'Votre demande a été enregistrée. L\'agence vous recontactera rapidement.');
    }

    public function show(Request $request, ServiceRequest $dossier): View
    {
        abort_unless($dossier->user_id === $request->user()->id, 403);

        return view('dossiers.show', [
            'dossier' => $dossier->load(['service', 'scholarship', 'payments']),
            'prefixe' => $this->prefixe($request),
        ]);
    }

    private function cible(Request $request): ServiceTarget
    {
        return $request->user()->isRecruteur() ? ServiceTarget::Recruteur : ServiceTarget::Candidat;
    }

    private function prefixe(Request $request): string
    {
        return $request->user()->isRecruteur() ? 'recruteur' : 'candidat';
    }
}
