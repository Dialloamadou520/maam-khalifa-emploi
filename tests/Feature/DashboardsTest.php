<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardsTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_tableau_de_bord_candidat_affiche_ses_candidatures(): void
    {
        $profil = CandidateProfile::factory()->create();
        $offre = JobOffer::factory()->publiee()->create(['titre' => 'Responsable RH']);
        $candidature = new JobApplication(['job_offer_id' => $offre->id]);
        $candidature->user_id = $profil->user_id;
        $candidature->save();

        $this->actingAs($profil->user)
            ->get(route('candidat.dashboard'))
            ->assertOk()
            ->assertSee('Responsable RH')
            ->assertSee('Envoyée')
            ->assertSee('Profil complété');
    }

    public function test_le_tableau_de_bord_recruteur_affiche_ses_offres_et_candidatures(): void
    {
        $entreprise = Company::factory()->create(['est_verifiee' => false]);
        $offre = JobOffer::factory()->for($entreprise)->create(['titre' => 'Chargé de clientèle']);
        $candidat = User::factory()->candidat()->create(['name' => 'Fatou Sarr']);
        $candidature = new JobApplication(['job_offer_id' => $offre->id]);
        $candidature->user_id = $candidat->id;
        $candidature->save();

        $this->actingAs($entreprise->user)
            ->get(route('recruteur.dashboard'))
            ->assertOk()
            ->assertSee('Chargé de clientèle')
            ->assertSee('Fatou Sarr')
            ->assertSee('en cours de vérification');
    }

    public function test_le_tableau_de_bord_admin_affiche_les_indicateurs(): void
    {
        $admin = User::factory()->admin()->create();
        JobOffer::factory()->create(['titre' => 'Offre à valider']);
        ActivityLog::create(['action' => 'test', 'description' => 'Nouvelle inscription test']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Offre à valider')
            ->assertSee('Nouvelle inscription test')
            ->assertSee('0 FCFA');
    }
}
