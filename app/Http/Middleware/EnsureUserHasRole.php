<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Réserve un espace aux utilisateurs actifs du rôle indiqué (ex. « role:recruteur »).
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        abort_unless($user !== null && $user->hasRole(Role::from($role)), 403);

        if (! $user->est_actif) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();

            return redirect()->route('login')->with('erreur', 'Votre compte a été désactivé. Contactez l\'agence.');
        }

        return $next($request);
    }
}
