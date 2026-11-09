<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->recruteur(),
            'nom' => fake()->company(),
            'secteur' => fake()->randomElement(['Banque & assurance', 'BTP', 'Télécoms', 'Agroalimentaire', 'Commerce', 'Services']),
            'taille' => fake()->randomElement(['1-10', '11-50', '51-200', '200+']),
            'ville' => 'Dakar',
            'pays' => 'Sénégal',
            'telephone' => '33'.fake()->numerify('#######'),
            'email_contact' => fake()->companyEmail(),
            'description' => fake()->paragraph(),
            'est_verifiee' => true,
        ];
    }
}
