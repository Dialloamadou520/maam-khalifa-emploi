<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\MatchStatus;
use App\Enums\PaymentStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\CandidateMatch;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Payment;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Statistiques d'activité sur 12 mois et exports CSV (compatibles Excel).
 */
class StatistiqueController extends Controller
{
    public function index(): View
    {
        $debut = now()->startOfMonth()->subMonths(11);
        $mois = collect(range(0, 11))->map(fn (int $i) => $debut->copy()->addMonths($i)->format('Y-m'));

        $series = [
            'Candidats inscrits' => $this->parMois(User::where('role', Role::Candidat)->where('created_at', '>=', $debut)->pluck('created_at'), $mois),
            'Entreprises inscrites' => $this->parMois(User::where('role', Role::Recruteur)->where('created_at', '>=', $debut)->pluck('created_at'), $mois),
            'Offres soumises' => $this->parMois(JobOffer::where('created_at', '>=', $debut)->pluck('created_at'), $mois),
            'Candidatures' => $this->parMois(JobApplication::where('created_at', '>=', $debut)->pluck('created_at'), $mois),
            'Dossiers ouverts' => $this->parMois(ServiceRequest::where('created_at', '>=', $debut)->pluck('created_at'), $mois),
        ];

        $encaissements = Payment::where('statut', PaymentStatus::Payee)->where('payee_le', '>=', $debut)->get(['montant', 'payee_le']);
        $chiffreAffaires = $mois->mapWithKeys(fn (string $m) => [$m => (int) $encaissements->filter(fn (Payment $p) => $p->payee_le->format('Y-m') === $m)->sum('montant')]);

        return view('admin.statistiques', [
            'mois' => $mois,
            'series' => $series,
            'chiffreAffaires' => $chiffreAffaires,
            'candidaturesParStatut' => collect(ApplicationStatus::cases())->mapWithKeys(fn ($s) => [$s->label() => JobApplication::where('statut', $s)->count()]),
            'misesEnRelation' => collect(MatchStatus::cases())->mapWithKeys(fn ($s) => [$s->label() => CandidateMatch::where('statut', $s)->count()]),
            'prestations' => ServiceRequest::with('service')->get()->groupBy('service.nom')->map(fn (Collection $d) => ['dossiers' => $d->count(), 'montant' => $d->sum('montant')])->sortByDesc('dossiers'),
            'paiementsParMethode' => Payment::where('statut', PaymentStatus::Payee)->get()->groupBy(fn (Payment $p) => $p->methode?->label() ?? 'Non précisé')->map->sum('montant'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $type = $request->validate(['type' => ['required', Rule::in(['paiements', 'candidatures', 'dossiers', 'candidats', 'offres'])]])['type'];

        [$entetes, $lignes] = match ($type) {
            'paiements' => [['Référence', 'Date', 'Client', 'Dossier', 'Prestation', 'Montant (FCFA)', 'Moyen', 'Transaction', 'Statut'],
                Payment::with(['user', 'serviceRequest.service'])->latest()->lazy()->map(fn (Payment $p) => [
                    $p->reference, ($p->payee_le ?? $p->created_at)->format('d/m/Y'), $p->user->name, $p->serviceRequest->reference,
                    $p->serviceRequest->service->nom, $p->montant, $p->methode?->label(), $p->transaction_id, $p->statut->label(),
                ])],
            'candidatures' => [['Date', 'Candidat', 'Email', 'Offre', 'Entreprise', 'Statut'],
                JobApplication::with(['candidat', 'jobOffer.company'])->latest()->lazy()->map(fn (JobApplication $c) => [
                    $c->created_at->format('d/m/Y'), $c->candidat->name, $c->candidat->email, $c->jobOffer->titre, $c->jobOffer->company->nom, $c->statut->label(),
                ])],
            'dossiers' => [['Référence', 'Date', 'Client', 'Rôle', 'Prestation', 'Montant (FCFA)', 'Statut'],
                ServiceRequest::with(['user', 'service'])->latest()->lazy()->map(fn (ServiceRequest $d) => [
                    $d->reference, $d->created_at->format('d/m/Y'), $d->user->name, $d->user->role->label(), $d->service->nom, $d->montant, $d->statut->label(),
                ])],
            'candidats' => [['Nom', 'Email', 'Téléphone', 'Profil', 'Ville', 'Niveau', 'Expérience', 'Inscrit le', 'Actif'],
                User::where('role', Role::Candidat)->with('candidateProfile')->latest()->lazy()->map(fn (User $u) => [
                    $u->name, $u->email, $u->telephone, $u->candidateProfile?->titre_profil, $u->candidateProfile?->ville,
                    $u->candidateProfile?->niveau_etude, $u->candidateProfile?->annees_experience, $u->created_at->format('d/m/Y'), $u->est_actif ? 'Oui' : 'Non',
                ])],
            'offres' => [['Titre', 'Entreprise', 'Contrat', 'Lieu', 'Statut', 'Candidatures', 'Créée le'],
                JobOffer::with('company')->withCount('applications')->latest()->lazy()->map(fn (JobOffer $o) => [
                    $o->titre, $o->company->nom, $o->type_contrat->label(), $o->lieu, $o->statut->label(), $o->applications_count, $o->created_at->format('d/m/Y'),
                ])],
        };

        return response()->streamDownload(function () use ($entetes, $lignes) {
            $sortie = fopen('php://output', 'w');
            fwrite($sortie, "\xEF\xBB\xBF");
            fputcsv($sortie, $entetes, ';');
            foreach ($lignes as $ligne) {
                fputcsv($sortie, $ligne, ';');
            }
            fclose($sortie);
        }, "{$type}-".now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function parMois(Collection $dates, Collection $mois): Collection
    {
        $comptes = $dates->countBy(fn ($d) => Carbon::parse($d)->format('Y-m'));

        return $mois->mapWithKeys(fn (string $m) => [$m => $comptes[$m] ?? 0]);
    }
}
