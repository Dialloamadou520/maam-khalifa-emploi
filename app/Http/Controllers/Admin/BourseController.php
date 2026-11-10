<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ScholarshipType;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BourseController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? null;

        return view('admin.bourses.index', [
            'bourses' => Scholarship::query()
                ->when($q, fn (Builder $b, string $t) => $b->where(fn (Builder $w) => $w->whereLike('titre', "%{$t}%")->orWhereLike('organisme', "%{$t}%")))
                ->withCount('serviceRequests')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.bourses.form', ['bourse' => new Scholarship(['est_publiee' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $bourse = Scholarship::create($this->valider($request));
        ActivityLog::enregistrer('bourse', "Bourse « {$bourse->titre} » ajoutée", $bourse);

        return redirect()->route('admin.bourses.index')->with('status', 'Bourse ajoutée.');
    }

    public function edit(Scholarship $bourse): View
    {
        return view('admin.bourses.form', ['bourse' => $bourse]);
    }

    public function update(Request $request, Scholarship $bourse): RedirectResponse
    {
        $bourse->update($this->valider($request));
        ActivityLog::enregistrer('bourse', "Bourse « {$bourse->titre} » modifiée", $bourse);

        return redirect()->route('admin.bourses.index')->with('status', 'Bourse mise à jour.');
    }

    public function destroy(Scholarship $bourse): RedirectResponse
    {
        ActivityLog::enregistrer('bourse', "Bourse « {$bourse->titre} » supprimée");
        $bourse->delete();

        return redirect()->route('admin.bourses.index')->with('status', 'Bourse supprimée.');
    }

    private function valider(Request $request): array
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'organisme' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ScholarshipType::class)],
            'pays' => ['nullable', 'string', 'max:100'],
            'niveau' => ['nullable', 'string', 'max:100'],
            'domaine' => ['nullable', 'string', 'max:150'],
            'montant' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:10000'],
            'conditions' => ['nullable', 'string', 'max:5000'],
            'lien_externe' => ['nullable', 'url', 'max:255'],
            'date_limite' => ['nullable', 'date'],
        ], [], ['lien_externe' => 'lien officiel', 'date_limite' => 'date limite']);

        return [...$data, 'est_publiee' => $request->boolean('est_publiee')];
    }
}
