<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'sujet_type', 'sujet_id', 'description'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sujet(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Trace une action dans le journal d'activité de l'agence.
     */
    public static function enregistrer(string $action, string $description, ?Model $sujet = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'sujet_type' => $sujet?->getMorphClass(),
            'sujet_id' => $sujet?->getKey(),
            'description' => $description,
        ]);
    }
}
