<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceTarget;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Catalogue des prestations et tarifs. Un changement de tarif ne modifie pas les dossiers déjà ouverts.
 */
class PrestationController extends Controller
{
    public function index(): View
    {
        return view('admin.prestations.index', [
            'prestations' => Service::withCount('requests')->orderBy('cible')->orderBy('ordre')->orderBy('nom')->get()->groupBy(fn (Service $s) => $s->cible->value),
        ]);
    }

    public function create(): View
    {
        return view('admin.prestations.form', ['prestation' => new Service(['est_active' => true, 'cible' => ServiceTarget::Candidat, 'ordre' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $prestation = Service::create($this->valider($request));
        ActivityLog::enregistrer('prestation', "Prestation « {$prestation->nom} » créée à ".fcfa($prestation->prix), $prestation);

        return redirect()->route('admin.prestations.index')->with('status', 'Prestation créée.');
    }

    public function edit(Service $prestation): View
    {
        return view('admin.prestations.form', ['prestation' => $prestation]);
    }

    public function update(Request $request, Service $prestation): RedirectResponse
    {
        $ancienPrix = $prestation->prix;
        $prestation->update($this->valider($request));

        $detail = $ancienPrix !== $prestation->prix ? ' (tarif : '.fcfa($ancienPrix).' → '.fcfa($prestation->prix).')' : '';
        ActivityLog::enregistrer('prestation', "Prestation « {$prestation->nom} » modifiée{$detail}", $prestation);

        return redirect()->route('admin.prestations.index')->with('status', 'Prestation mise à jour.');
    }

    private function valider(Request $request): array
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'cible' => ['required', Rule::enum(ServiceTarget::class)],
            'description' => ['nullable', 'string', 'max:3000'],
            'prix' => ['required', 'integer', 'min:0', 'max:100000000'],
            'delai' => ['nullable', 'string', 'max:100'],
            'ordre' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        return [...$data, 'ordre' => (int) ($data['ordre'] ?? 0), 'est_active' => $request->boolean('est_active')];
    }
}
