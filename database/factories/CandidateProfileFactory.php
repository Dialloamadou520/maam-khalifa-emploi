<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CandidateProfile>
 */
class CandidateProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->candidat(),
            'titre_profil' => fake()->randomElement(['Comptable', 'Développeur web', 'Assistante de direction', 'Commercial terrain', 'Chargé de communication', 'Technicien réseaux']),
            'ville' => fake()->randomElement(['Dakar', 'Thiès', 'Saint-Louis', 'Touba', 'Ziguinchor', 'Kaolack']),
            'pays' => 'Sénégal',
            'niveau_etude' => fake()->randomElement(['Baccalauréat', 'Licence', 'Master', 'BTS']),
            'domaine' => fake()->randomElement(['Finance', 'Informatique', 'Administration', 'Commerce', 'Communication']),
            'annees_experience' => fake()->numberBetween(0, 12),
            'competences' => implode(', ', fake()->randomElements(['Excel', 'Gestion de projet', 'Relation client', 'Laravel', 'Comptabilité SYSCOHADA', 'Anglais', 'Marketing digital', 'Négociation'], 3)),
            'langues' => 'Français, Wolof',
            'disponibilite' => 'Immédiate',
            'presentation' => fake()->paragraph(),
            'visible_recruteurs' => true,
        ];
    }
}
