<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.register', [
            'profil' => $request->query('profil') === Role::Recruteur->value ? Role::Recruteur->value : Role::Candidat->value,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in([Role::Candidat->value, Role::Recruteur->value])],
            'name' => ['required', 'string', 'max:255'],
            'entreprise' => ['required_if:role,recruteur', 'nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telephone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [], [
            'name' => 'nom complet',
            'entreprise' => 'nom de l\'entreprise',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'password' => $data['password'],
                'role' => Role::from($data['role']),
            ]);

            if ($user->isRecruteur()) {
                $user->company()->create([
                    'nom' => $data['entreprise'],
                    'telephone' => $data['telephone'],
                    'email_contact' => $data['email'],
                ]);
            } else {
                $user->candidateProfile()->create();
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        ActivityLog::enregistrer('inscription', "Nouveau compte {$user->role->label()} : {$user->name}", $user);

        return redirect()->route($user->dashboardRoute())
            ->with('status', 'Bienvenue sur la plateforme de l\'Agence Maam Khalifa !');
    }
}
