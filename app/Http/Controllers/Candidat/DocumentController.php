<?php

namespace App\Http\Controllers\Candidat;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\DocumentStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentStorage $stockage) {}

    public function index(): View
    {
        return view('candidat.documents', [
            'documents' => auth()->user()->documents()->latest()->get()->groupBy(fn (Document $d) => $d->type->value),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::enum(DocumentType::class)],
            'titre' => ['nullable', 'string', 'max:150'],
            'fichier' => ['required', 'file', 'mimes:'.DocumentStorage::EXTENSIONS, 'max:'.DocumentStorage::TAILLE_MAX_KO],
        ], [], ['fichier' => 'fichier']);

        $this->stockage->deposer($request->user(), $request->file('fichier'), DocumentType::from($data['type']), $data['titre'] ?? null);

        return back()->with('status', 'Document ajouté.');
    }

    public function show(Document $document): StreamedResponse
    {
        abort_unless($document->user_id === auth()->id(), 403);

        return $this->stockage->telecharger($document);
    }

    public function destroy(Document $document): RedirectResponse
    {
        abort_unless($document->user_id === auth()->id(), 403);

        $this->stockage->supprimer($document);

        return back()->with('status', 'Document supprimé.');
    }
}
