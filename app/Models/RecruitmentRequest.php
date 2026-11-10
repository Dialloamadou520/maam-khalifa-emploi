<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Enums\RecruitmentRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule_poste', 'nombre_postes', 'type_contrat', 'lieu', 'description',
        'competences_requises', 'budget', 'date_souhaitee',
    ];

    protected $attributes = [
        'statut' => 'soumise',
    ];

    protected function casts(): array
    {
        return [
            'type_contrat' => ContractType::class,
            'statut' => RecruitmentRequestStatus::class,
            'date_souhaitee' => 'date',
            'budget' => 'integer',
            'nombre_postes' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traitee_par');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CandidateMatch::class);
    }
}
