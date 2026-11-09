<?php

namespace App\Http\Controllers\Candidat;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $profil = $user->candidateProfile()->firstOrCreate();

        return view('candidat.dashboard', [
            'user' => $user,
            'completion' => $profil->completion(),
            'stats' => [
                'candidatures' => $user->jobApplications()->count(),
                'documents' => $user->documents()->count(),
                'dossiers' => $user->serviceRequests()->count(),
                'mises_en_relation' => $user->matches()->count(),
            ],
            'candidatures' => $user->jobApplications()->with('jobOffer.company')->latest()->take(5)->get(),
            'offres' => JobOffer::visibles()->with('company')->latest('publiee_le')->take(4)->get(),
        ]);
    }
}
