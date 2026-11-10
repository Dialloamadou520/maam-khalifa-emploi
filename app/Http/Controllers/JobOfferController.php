<?php

namespace App\Http\Controllers;

use App\Enums\ContractType;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Consultation publique des offres d'emploi validées par l'agence.
 */
class JobOfferController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'contrat' => ['nullable', Rule::enum(ContractType::class)],
            'lieu' => ['nullable', 'string', 'max:100'],
        ]);

        $offres = JobOffer::visibles()
            ->with('company')
            ->when($filtres['q'] ?? null, fn ($q, $terme) => $q->where(fn ($sq) => $sq
                ->where('titre', 'like', "%{$terme}%")
                ->orWhere('description', 'like', "%{$terme}%")
                ->orWhere('secteur', 'like', "%{$terme}%")))
            ->when($filtres['contrat'] ?? null, fn ($q, $contrat) => $q->where('type_contrat', $contrat))
            ->when($filtres['lieu'] ?? null, fn ($q, $lieu) => $q->where('lieu', 'like', "%{$lieu}%"))
            ->latest('publiee_le')
            ->paginate(12)
            ->withQueryString();

        return view('offres.index', compact('offres', 'filtres'));
    }

    public function show(JobOffer $offre): View
    {
        abort_unless($offre->estVisible(), 404);

        $offre->increment('vues');

        $dejaPostule = auth()->user()?->jobApplications()->where('job_offer_id', $offre->id)->exists() ?? false;

        return view('offres.show', [
            'offre' => $offre->load('company'),
            'dejaPostule' => $dejaPostule,
            'similaires' => JobOffer::visibles()->with('company')
                ->whereKeyNot($offre->id)
                ->where(fn ($q) => $q->where('secteur', $offre->secteur)->orWhere('type_contrat', $offre->type_contrat))
                ->latest('publiee_le')->take(3)->get(),
        ]);
    }
}
