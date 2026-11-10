<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre_profil', 'date_naissance', 'genre', 'adresse', 'ville', 'pays', 'niveau_etude',
        'domaine', 'annees_experience', 'competences', 'langues', 'disponibilite',
        'pretention_salariale', 'presentation', 'linkedin', 'visible_recruteurs',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'visible_recruteurs' => 'boolean',
            'annees_experience' => 'integer',
            'pretention_salariale' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pourcentage de remplissage du profil professionnel (CV inclus).
     */
    public function completion(): int
    {
        $champs = ['titre_profil', 'ville', 'niveau_etude', 'domaine', 'annees_experience', 'competences', 'presentation'];
        $remplis = collect($champs)->filter(fn (string $champ) => filled($this->{$champ}))->count();
        $aUnCv = $this->user?->documents()->where('type', 'cv')->exists() ? 1 : 0;

        return (int) round(($remplis + $aUnCv) / (count($champs) + 1) * 100);
    }

    /**
     * @return list<string>
     */
    public function listeCompetences(): array
    {
        return collect(preg_split('/[,;\n]+/', (string) $this->competences))
            ->map(fn (string $c) => trim($c))
            ->filter()
            ->values()
            ->all();
    }
}
