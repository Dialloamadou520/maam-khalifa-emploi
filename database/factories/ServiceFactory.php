<?php

namespace Database\Factories;

use App\Enums\ServiceTarget;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->randomElement(['Rédaction de CV', 'Coaching entretien', 'Accompagnement bourse']),
            'cible' => ServiceTarget::Candidat,
            'description' => fake()->sentence(),
            'prix' => 10000,
            'delai' => '72 h',
            'est_active' => true,
        ];
    }
}
