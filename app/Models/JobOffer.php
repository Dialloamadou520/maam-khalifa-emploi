<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\OfferStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'type_contrat', 'lieu', 'secteur', 'niveau_etude', 'experience_min', 'salaire_min',
        'salaire_max', 'nombre_postes', 'description', 'profil_recherche', 'avantages', 'date_limite',
    ];

    protected $attributes = [
        'statut' => 'en_attente',
    ];

    protected function casts(): array
    {
        return [
            'type_contrat' => ContractType::class,
            'statut' => OfferStatus::class,
            'date_limite' => 'date',
            'publiee_le' => 'datetime',
            'salaire_min' => 'integer',
            'salaire_max' => 'integer',
            'experience_min' => 'integer',
            'nombre_postes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (JobOffer $offre) {
            $offre->slug ??= Str::slug(Str::limit($offre->titre, 60, '')).'-'.Str::lower(Str::random(6));
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validee_par');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Offres visibles par le public : validées par l'agence et non expirées.
     */
    #[Scope]
    protected function visibles(Builder $query): void
    {
        $query->where('statut', OfferStatus::Publiee)
            ->where(fn (Builder $q) => $q->whereNull('date_limite')->orWhereDate('date_limite', '>=', today()));
    }

    public function estVisible(): bool
    {
        return $this->statut === OfferStatus::Publiee
            && ($this->date_limite === null || $this->date_limite->gte(today()));
    }

    public function salaireLibelle(): ?string
    {
        return match (true) {
            $this->salaire_min && $this->salaire_max => fcfa($this->salaire_min).' – '.fcfa($this->salaire_max),
            (bool) $this->salaire_min => 'À partir de '.fcfa($this->salaire_min),
            (bool) $this->salaire_max => 'Jusqu\'à '.fcfa($this->salaire_max),
            default => null,
        };
    }
}
