<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Candidat\ProfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('candidat.profil', [
            'user' => $user,
            'profil' => $user->candidateProfile()->firstOrCreate(),
        ]);
    }

    public function update(ProfilRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $user->update(Arr::only($data, ['name', 'telephone']));
        $user->candidateProfile()->firstOrCreate()->update(Arr::except($data, ['name', 'telephone']));

        return redirect()->route('candidat.profil.edit')->with('status', 'Votre profil a été mis à jour.');
    }
}
