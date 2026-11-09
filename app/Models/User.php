<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'telephone', 'password', 'role', 'est_actif'];

    protected $hidden = ['password', 'remember_token'];

    protected $attributes = [
        'role' => 'candidat',
        'est_actif' => true,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'est_actif' => 'boolean',
        ];
    }

    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CandidateMatch::class, 'candidate_id');
    }

    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::Admin);
    }

    public function isCandidat(): bool
    {
        return $this->hasRole(Role::Candidat);
    }

    public function isRecruteur(): bool
    {
        return $this->hasRole(Role::Recruteur);
    }

    /**
     * Route du tableau de bord correspondant au rôle de l'utilisateur.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            Role::Admin => 'admin.dashboard',
            Role::Recruteur => 'recruteur.dashboard',
            Role::Candidat => 'candidat.dashboard',
        };
    }
}
