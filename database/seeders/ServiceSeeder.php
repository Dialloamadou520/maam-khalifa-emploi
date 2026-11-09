<?php

namespace Database\Seeders;

use App\Enums\ServiceTarget;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Prestations et tarifs de départ, modifiables ensuite depuis l'espace administrateur.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $prestations = [
            ['Rédaction / refonte de CV', ServiceTarget::Candidat, 5000, '48 h', 'CV professionnel mis en forme et optimisé pour les recruteurs.'],
            ['Rédaction de lettre de motivation', ServiceTarget::Candidat, 3000, '48 h', 'Lettre personnalisée pour une offre ou une candidature spontanée.'],
            ['Coaching entretien d\'embauche', ServiceTarget::Candidat, 10000, '1 séance', 'Simulation d\'entretien et conseils personnalisés.'],
            ['Accompagnement dossier de bourse', ServiceTarget::Candidat, 25000, 'Jusqu\'au dépôt', 'Montage complet du dossier de bourse ou de financement.'],
            ['Orientation professionnelle', ServiceTarget::Candidat, 7500, '1 séance', 'Bilan de compétences et plan de carrière.'],
            ['Publication d\'offre mise en avant', ServiceTarget::Recruteur, 15000, '30 jours', 'Offre affichée en tête de liste et relayée par l\'agence.'],
            ['Présélection de candidats', ServiceTarget::Recruteur, 50000, '7 jours', 'Tri des candidatures et short-list de profils qualifiés.'],
            ['Recrutement clé en main', ServiceTarget::Recruteur, 150000, '3 à 4 semaines', 'Sourcing, entretiens et présentation des finalistes.'],
        ];

        foreach ($prestations as $ordre => [$nom, $cible, $prix, $delai, $description]) {
            Service::firstOrCreate(
                ['nom' => $nom],
                compact('cible', 'prix', 'delai', 'description') + ['ordre' => $ordre, 'est_active' => true],
            );
        }
    }
}
