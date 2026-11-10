<?php

namespace App\Models;

use App\Enums\ServiceTarget;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Prestation proposée par l'agence (rédaction de CV, coaching, présélection…) et son tarif.
 */
class Service extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'cible', 'description', 'prix', 'delai', 'est_active', 'ordre'];

    protected function casts(): array
    {
        return [
            'cible' => ServiceTarget::class,
            'prix' => 'integer',
            'est_active' => 'boolean',
            'ordre' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Service $service) {
            $service->slug ??= Str::slug($service->nom).'-'.Str::lower(Str::random(4));
        });
    }

    public function requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    #[Scope]
    protected function actives(Builder $query): void
    {
        $query->where('est_active', true)->orderBy('ordre')->orderBy('nom');
    }

    #[Scope]
    protected function pour(Builder $query, ServiceTarget $cible): void
    {
        $query->whereIn('cible', [$cible->value, ServiceTarget::Tous->value]);
    }
}
