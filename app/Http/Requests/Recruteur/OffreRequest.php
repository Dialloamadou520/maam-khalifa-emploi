<?php

namespace App\Http\Requests\Recruteur;

use App\Enums\ContractType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OffreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isRecruteur() ?? false;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'type_contrat' => ['required', Rule::enum(ContractType::class)],
            'lieu' => ['required', 'string', 'max:150'],
            'secteur' => ['nullable', 'string', 'max:150'],
            'niveau_etude' => ['nullable', 'string', 'max:100'],
            'experience_min' => ['nullable', 'integer', 'min:0', 'max:40'],
            'salaire_min' => ['nullable', 'integer', 'min:0'],
            'salaire_max' => array_filter(['nullable', 'integer', 'min:0', $this->filled('salaire_min') ? 'gte:salaire_min' : null]),
            'nombre_postes' => ['required', 'integer', 'min:1', 'max:500'],
            'description' => ['required', 'string', 'max:10000'],
            'profil_recherche' => ['nullable', 'string', 'max:5000'],
            'avantages' => ['nullable', 'string', 'max:3000'],
            'date_limite' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'type_contrat' => 'type de contrat',
            'experience_min' => 'expérience minimale',
            'salaire_min' => 'salaire minimum',
            'salaire_max' => 'salaire maximum',
            'nombre_postes' => 'nombre de postes',
            'profil_recherche' => 'profil recherché',
            'date_limite' => 'date limite',
        ];
    }
}
