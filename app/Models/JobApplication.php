<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = ['job_offer_id', 'cv_document_id', 'lettre_document_id', 'message', 'statut', 'note_recruteur'];

    protected $attributes = [
        'statut' => 'envoyee',
    ];

    protected function casts(): array
    {
        return ['statut' => ApplicationStatus::class];
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'cv_document_id');
    }

    public function lettre(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'lettre_document_id');
    }
}
