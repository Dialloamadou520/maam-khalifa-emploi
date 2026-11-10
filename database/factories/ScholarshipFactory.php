<?php

namespace Database\Factories;

use App\Enums\ScholarshipType;
use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scholarship>
 */
class ScholarshipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre' => 'Bourse '.fake()->randomElement(['d\'excellence', 'de mobilité', 'de master', 'entrepreneuriat jeunes']),
            'organisme' => fake()->randomElement(['Campus France', 'Ministère de l\'Enseignement supérieur', 'DER/FJ', 'Fondation Sonatel']),
            'type' => fake()->randomElement(ScholarshipType::cases()),
            'pays' => fake()->randomElement(['Sénégal', 'France', 'Canada', 'Maroc']),
            'niveau' => fake()->randomElement(['Licence', 'Master', 'Doctorat', 'Tous niveaux']),
            'montant' => 'Prise en charge partielle',
            'description' => fake()->paragraphs(2, true),
            'conditions' => fake()->paragraph(),
            'date_limite' => now()->addMonths(2)->toDateString(),
            'est_publiee' => true,
        ];
    }
}
