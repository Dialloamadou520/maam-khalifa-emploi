<?php

namespace Database\Seeders;

use App\Enums\OfferStatus;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Données de démonstration (hors production) : comptes, offres et bourses.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'candidat@demo.sn')->exists()) {
            return;
        }

        CandidateProfile::factory()
            ->for(User::factory()->candidat()->state(['name' => 'Awa Ndiaye', 'email' => 'candidat@demo.sn']))
            ->create(['titre_profil' => 'Comptable junior', 'ville' => 'Dakar']);
        CandidateProfile::factory()->count(8)->create();

        $entreprise = Company::factory()
            ->for(User::factory()->recruteur()->state(['name' => 'Moussa Diop', 'email' => 'recruteur@demo.sn']))
            ->create(['nom' => 'Teranga Services SARL']);

        JobOffer::factory()->count(4)->publiee()->for($entreprise)->create();
        JobOffer::factory()->for($entreprise)->create(['titre' => 'Chargé de recouvrement', 'statut' => OfferStatus::EnAttente]);

        Company::factory()->count(3)->create()->each(
            fn (Company $company) => JobOffer::factory()->count(2)->publiee()->for($company)->create()
        );

        Scholarship::factory()->count(5)->create();
    }
}
