<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'secteur', 'taille', 'ninea', 'adresse', 'ville', 'pays', 'telephone',
        'email_contact', 'site_web', 'description', 'est_verifiee',
    ];

    protected function casts(): array
    {
        return ['est_verifiee' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class);
    }

    public function jobApplications(): HasManyThrough
    {
        return $this->hasManyThrough(JobApplication::class, JobOffer::class);
    }

    public function recruitmentRequests(): HasMany
    {
        return $this->hasMany(RecruitmentRequest::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CandidateMatch::class);
    }
}
