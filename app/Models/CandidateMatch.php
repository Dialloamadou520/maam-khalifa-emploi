<?php

namespace App\Models;

use App\Enums\MatchStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mise en relation d'un candidat avec une entreprise, à l'initiative de l'agence.
 */
class CandidateMatch extends Model
{
    use HasFactory;

    protected $fillable = ['candidate_id', 'company_id', 'job_offer_id', 'recruitment_request_id', 'statut', 'commentaire'];

    protected $attributes = [
        'statut' => 'proposee',
    ];

    protected function casts(): array
    {
        return ['statut' => MatchStatus::class];
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function recruitmentRequest(): BelongsTo
    {
        return $this->belongsTo(RecruitmentRequest::class);
    }

    public function creePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }
}
