<?php

namespace Tests\Feature;

use App\Enums\OfferStatus;
use App\Models\JobOffer;
use App\Models\Scholarship;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_pages_publiques_s_affichent(): void
    {
        foreach (['accueil', 'offres.index', 'bourses.index', 'prestations', 'a-propos'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_seules_les_offres_validees_et_non_expirees_sont_listees(): void
    {
        $publiee = JobOffer::factory()->publiee()->create(['titre' => 'Comptable confirmé']);
        $enAttente = JobOffer::factory()->create(['titre' => 'Offre en attente']);
        $expiree = JobOffer::factory()->publiee()->create(['titre' => 'Offre expirée', 'date_limite' => now()->subDay()]);

        $this->get(route('offres.index'))
            ->assertSee($publiee->titre)
            ->assertDontSee($enAttente->titre)
            ->assertDontSee($expiree->titre);

        $this->get(route('offres.show', $publiee))->assertOk()->assertSee($publiee->company->nom);
        $this->get(route('offres.show', $enAttente))->assertNotFound();
        $this->get(route('offres.show', $expiree))->assertNotFound();
    }

    public function test_les_offres_peuvent_etre_filtrees(): void
    {
        JobOffer::factory()->publiee()->create(['titre' => 'Développeur Laravel', 'type_contrat' => 'cdi', 'lieu' => 'Dakar']);
        JobOffer::factory()->publiee()->create(['titre' => 'Stagiaire marketing', 'type_contrat' => 'stage', 'lieu' => 'Thiès']);

        $this->get(route('offres.index', ['q' => 'Laravel']))
            ->assertSee('Développeur Laravel')
            ->assertDontSee('Stagiaire marketing');

        $this->get(route('offres.index', ['contrat' => 'stage']))
            ->assertSee('Stagiaire marketing')
            ->assertDontSee('Développeur Laravel');

        $this->get(route('offres.index', ['lieu' => 'Thiès']))
            ->assertSee('Stagiaire marketing')
            ->assertDontSee('Développeur Laravel');
    }

    public function test_la_consultation_d_une_offre_incremente_les_vues(): void
    {
        $offre = JobOffer::factory()->publiee()->create();

        $this->get(route('offres.show', $offre));

        $this->assertSame(1, $offre->fresh()->vues);
    }

    public function test_les_bourses_non_publiees_sont_masquees(): void
    {
        $visible = Scholarship::factory()->create(['titre' => 'Bourse visible']);
        $masquee = Scholarship::factory()->create(['titre' => 'Bourse brouillon', 'est_publiee' => false]);

        $this->get(route('bourses.index'))->assertSee($visible->titre)->assertDontSee($masquee->titre);
        $this->get(route('bourses.show', $masquee))->assertNotFound();
    }

    public function test_les_prestations_actives_sont_affichees_avec_leur_tarif(): void
    {
        Service::factory()->create(['nom' => 'Rédaction de CV', 'prix' => 5000]);
        Service::factory()->create(['nom' => 'Prestation archivée', 'est_active' => false]);

        $this->get(route('prestations'))
            ->assertSee('Rédaction de CV')
            ->assertSee('5 000 FCFA')
            ->assertDontSee('Prestation archivée');
    }

    public function test_l_accueil_n_affiche_pas_les_offres_non_validees(): void
    {
        JobOffer::factory()->create(['titre' => 'Offre rejetée', 'statut' => OfferStatus::Rejetee]);

        $this->get(route('accueil'))->assertDontSee('Offre rejetée');
    }
}
