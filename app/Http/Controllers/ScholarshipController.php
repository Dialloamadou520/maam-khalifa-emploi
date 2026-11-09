<?php

namespace App\Http\Controllers;

use App\Enums\ScholarshipType;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Catalogue public des bourses et financements.
 */
class ScholarshipController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::enum(ScholarshipType::class)],
        ]);

        $bourses = Scholarship::visibles()
            ->when($filtres['q'] ?? null, fn ($q, $terme) => $q->where(fn ($sq) => $sq
                ->where('titre', 'like', "%{$terme}%")
                ->orWhere('organisme', 'like', "%{$terme}%")
                ->orWhere('pays', 'like', "%{$terme}%")))
            ->when($filtres['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->orderByRaw('date_limite is null')
            ->orderBy('date_limite')
            ->paginate(12)
            ->withQueryString();

        return view('bourses.index', compact('bourses', 'filtres'));
    }

    public function show(Scholarship $bourse): View
    {
        abort_unless($bourse->est_publiee, 404);

        return view('bourses.show', compact('bourse'));
    }
}
