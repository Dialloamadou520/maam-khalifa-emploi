<?php

namespace App\Models;

use App\Enums\ScholarshipType;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'organisme', 'type', 'pays', 'niveau', 'domaine', 'montant', 'description',
        'conditions', 'lien_externe', 'date_limite', 'est_publiee',
    ];

    protected function casts(): array
    {
        return [
            'type' => ScholarshipType::class,
            'date_limite' => 'date',
            'est_publiee' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Scholarship $bourse) {
            $bourse->slug ??= Str::slug(Str::limit($bourse->titre, 60, '')).'-'.Str::lower(Str::random(6));
        });
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[Scope]
    protected function visibles(Builder $query): void
    {
        $query->where('est_publiee', true)
            ->where(fn (Builder $q) => $q->whereNull('date_limite')->orWhereDate('date_limite', '>=', today()));
    }
}
