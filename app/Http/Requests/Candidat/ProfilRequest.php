<?php

namespace App\Http\Requests\Candidat;

use Illuminate\Foundation\Http\FormRequest;

class ProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCandidat() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:30'],
            'titre_profil' => ['nullable', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date', 'before:today'],
            'genre' => ['nullable', 'in:homme,femme'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'pays' => ['nullable', 'string', 'max:100'],
            'niveau_etude' => ['nullable', 'string', 'max:100'],
            'domaine' => ['nullable', 'string', 'max:150'],
            'annees_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'competences' => ['nullable', 'string', 'max:2000'],
            'langues' => ['nullable', 'string', 'max:255'],
            'disponibilite' => ['nullable', 'string', 'max:100'],
            'pretention_salariale' => ['nullable', 'integer', 'min:0'],
            'presentation' => ['nullable', 'string', 'max:5000'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'visible_recruteurs' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom complet',
            'titre_profil' => 'titre du profil',
            'niveau_etude' => 'niveau d\'études',
            'annees_experience' => 'années d\'expérience',
            'pretention_salariale' => 'prétention salariale',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['visible_recruteurs' => $this->boolean('visible_recruteurs')]);
    }
}
