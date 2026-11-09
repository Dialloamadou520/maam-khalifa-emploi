<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Dépôt et lecture des pièces des candidats sur le disque configuré (local ou S3/Supabase).
 */
class DocumentStorage
{
    public const EXTENSIONS = 'pdf,doc,docx,jpg,jpeg,png';

    public const TAILLE_MAX_KO = 5120;

    public function deposer(User $user, UploadedFile $fichier, DocumentType $type, ?string $titre = null): Document
    {
        $chemin = $fichier->store("documents/{$user->id}", ['disk' => $this->nomDisque()]);

        return $user->documents()->create([
            'type' => $type,
            'titre' => $titre ?: pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME),
            'chemin' => $chemin,
            'nom_original' => $fichier->getClientOriginalName(),
            'mime' => $fichier->getClientMimeType(),
            'taille' => $fichier->getSize(),
        ]);
    }

    public function telecharger(Document $document): StreamedResponse
    {
        abort_unless($this->disque()->exists($document->chemin), 404);

        return $this->disque()->download($document->chemin, $document->nom_original);
    }

    public function supprimer(Document $document): void
    {
        $this->disque()->delete($document->chemin);
        $document->delete();
    }

    public function disque(): Filesystem
    {
        return Storage::disk($this->nomDisque());
    }

    private function nomDisque(): string
    {
        return config('filesystems.documents', 'local');
    }
}
