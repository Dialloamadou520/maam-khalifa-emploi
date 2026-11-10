<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferStatus;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Enums\ServiceRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CandidateMatch;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Payment;
use App\Models\RecruitmentRequest;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'candidats' => User::where('role', Role::Candidat)->count(),
                'recruteurs' => User::where('role', Role::Recruteur)->count(),
                'offres_publiees' => JobOffer::where('statut', OfferStatus::Publiee)->count(),
                'offres_en_attente' => JobOffer::where('statut', OfferStatus::EnAttente)->count(),
                'candidatures' => JobApplication::count(),
                'besoins' => RecruitmentRequest::count(),
                'dossiers_ouverts' => ServiceRequest::whereIn('statut', [ServiceRequestStatus::Soumis, ServiceRequestStatus::EnCours])->count(),
                'mises_en_relation' => CandidateMatch::count(),
                'encaisse' => (int) Payment::where('statut', PaymentStatus::Payee)->sum('montant'),
                'encaisse_mois' => (int) Payment::where('statut', PaymentStatus::Payee)->where('payee_le', '>=', now()->startOfMonth())->sum('montant'),
            ],
            'offresEnAttente' => JobOffer::where('statut', OfferStatus::EnAttente)->with('company')->oldest()->take(5)->get(),
            'derniersInscrits' => User::where('role', '!=', Role::Admin)->latest()->take(6)->get(),
            'activites' => ActivityLog::with('user')->latest()->take(8)->get(),
        ]);
    }
}
