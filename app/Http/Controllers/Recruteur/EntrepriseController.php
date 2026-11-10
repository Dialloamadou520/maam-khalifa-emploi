<?php

namespace App\Http\Controllers\Recruteur;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Recruteur\Concerns\AvecEntreprise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EntrepriseController extends Controller
{
    use AvecEntreprise;

    public const TAILLES = ['1-10', '11-50', '51-200', '200+'];

    public function edit(Request $request): View
    {
        return view('recruteur.entreprise', ['entreprise' => $this->entreprise($request), 'tailles' => self::TAILLES]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'secteur' => ['nullable', 'string', 'max:150'],
            'taille' => ['nullable', Rule::in(self::TAILLES)],
            'ninea' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'pays' => ['nullable', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:30'],
            'email_contact' => ['nullable', 'email', 'max:255'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ], [], ['email_contact' => 'email de contact', 'site_web' => 'site web']);

        $data['pays'] ??= 'Sénégal';
        $this->entreprise($request)->update($data);

        return redirect()->route('recruteur.entreprise.edit')->with('status', 'Les informations de l\'entreprise ont été enregistrées.');
    }
}
