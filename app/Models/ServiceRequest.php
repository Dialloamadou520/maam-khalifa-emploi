<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Dossier ouvert par un utilisateur pour une prestation de l'agence.
 */
class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'scholarship_id', 'montant', 'details', 'statut', 'note_admin'];

    protected $attributes = [
        'statut' => 'soumis',
    ];

    protected function casts(): array
    {
        return [
            'statut' => ServiceRequestStatus::class,
            'montant' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceRequest $dossier) {
            $dossier->reference ??= 'DOS-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function estPaye(): bool
    {
        return $this->payments()->where('statut', PaymentStatus::Payee)->sum('montant') >= $this->montant;
    }
}
