<?php

namespace App\Http\Controllers;

use App\Enums\ServiceTarget;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\Scholarship;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function accueil(): View
    {
        return view('public.accueil', [
            'offres' => JobOffer::visibles()->with('company')->latest('publiee_le')->take(6)->get(),
            'bourses' => Scholarship::visibles()->orderBy('date_limite')->take(3)->get(),
            'chiffres' => [
                'offres' => JobOffer::visibles()->count(),
                'entreprises' => Company::count(),
                'candidats' => User::where('role', 'candidat')->count(),
            ],
        ]);
    }

    public function prestations(): View
    {
        $services = Service::actives()->get();

        return view('public.prestations', [
            'pourCandidats' => $services->filter(fn (Service $s) => $s->cible !== ServiceTarget::Recruteur),
            'pourEntreprises' => $services->filter(fn (Service $s) => $s->cible !== ServiceTarget::Candidat),
        ]);
    }

    public function apropos(): View
    {
        return view('public.a-propos');
    }
}
