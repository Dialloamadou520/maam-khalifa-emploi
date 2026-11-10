<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Redirige l'utilisateur connecté vers le tableau de bord de son espace.
     */
    public function __invoke(): RedirectResponse
    {
        return redirect()->route(auth()->user()->dashboardRoute());
    }
}
