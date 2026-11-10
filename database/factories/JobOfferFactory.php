<?php

namespace Database\Factories;

use App\Enums\ContractType;
use App\Enums\OfferStatus;
use App\Models\Company;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobOffer>
 */
class JobOfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'titre' => fake()->randomElement(['Comptable confirmé', 'Développeur PHP/Laravel', 'Assistante administrative', 'Commercial B2B', 'Responsable RH', 'Stagiaire marketing digital']),
            'type_contrat' => fake()->randomElement(ContractType::cases()),
            'lieu' => fake()->randomElement(['Dakar', 'Thiès', 'Saint-Louis', 'Mbour']),
            'secteur' => fake()->randomElement(['Finance', 'Informatique', 'Administration', 'Commerce']),
            'niveau_etude' => fake()->randomElement(['Licence', 'Master', 'BTS']),
            'experience_min' => fake()->numberBetween(0, 5),
            'salaire_min' => 250000,
            'salaire_max' => 450000,
            'nombre_postes' => 1,
            'description' => fake()->paragraphs(2, true),
            'profil_recherche' => fake()->paragraph(),
            'date_limite' => now()->addWeeks(4)->toDateString(),
            'statut' => OfferStatus::EnAttente,
        ];
    }

    public function publiee(): static
    {
        return $this->state(fn () => ['statut' => OfferStatus::Publiee, 'publiee_le' => now()]);
    }
}
