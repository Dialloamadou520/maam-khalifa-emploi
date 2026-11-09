<?php

namespace App\Http\Controllers\Recruteur;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $entreprise = $user->company()->firstOrCreate([], ['nom' => $user->name]);

        $offresParStatut = $entreprise->jobOffers()
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        return view('recruteur.dashboard', [
            'entreprise' => $entreprise,
            'stats' => [
                'publiees' => (int) ($offresParStatut[OfferStatus::Publiee->value] ?? 0),
                'en_attente' => (int) ($offresParStatut[OfferStatus::EnAttente->value] ?? 0),
                'candidatures' => $entreprise->jobApplications()->count(),
                'besoins' => $entreprise->recruitmentRequests()->count(),
            ],
            'candidatures' => $entreprise->jobApplications()->with(['candidat', 'jobOffer'])->latest('job_applications.created_at')->take(5)->get(),
            'offres' => $entreprise->jobOffers()->withCount('applications')->latest()->take(5)->get(),
        ]);
    }
}
