<?php

namespace Tests\Feature\Recruteur;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Enums\RecruitmentRequestStatus;
use App\Enums\ServiceTarget;
use App\Models\CandidateMatch;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Service;
use App\Models\User;
use App\Services\DocumentStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EspaceRecruteurTest extends TestCase
{
    use RefreshDatabase;

    private User $recruteur;

    private Company $entreprise;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->entreprise = Company::factory()->create();
        $this->recruteur = $this->entreprise->user;
    }

    private function offreValide(array $surcharge = []): array
    {
        return array_merge([
            'titre' => 'Chef comptable',
            'type_contrat' => 'cdi',
            'lieu' => 'Dakar',
            'nombre_postes' => 1,
            'description' => 'Tenue de la comptabilité générale.',
            'salaire_min' => 400000,
            'salaire_max' => 600000,
        ], $surcharge);
    }

    private function candidature(JobOffer $offre, ?User $candidat = null, ApplicationStatus $statut = ApplicationStatus::Envoyee): JobApplication
    {
        $candidat ??= CandidateProfile::factory()->create()->user;
        $cv = app(DocumentStorage::class)->deposer($candidat, UploadedFile::fake()->create('cv.pdf', 20, 'application/pdf'), DocumentType::Cv);

        $candidature = new JobApplication(['job_offer_id' => $offre->id, 'cv_document_id' => $cv->id, 'statut' => $statut]);
        $candidature->user_id = $candidat->id;
        $candidature->save();

        return $candidature;
    }

    public function test_toutes_les_pages_du_recruteur_s_affichent(): void
    {
        $offre = JobOffer::factory()->publiee()->for($this->entreprise)->create();
        $this->candidature($offre);

        foreach (['dashboard', 'entreprise.edit', 'offres.index', 'offres.create', 'candidatures.index', 'besoins.index', 'besoins.create', 'profils.index', 'recrutements.index', 'dossiers.index', 'dossiers.create'] as $route) {
            $this->actingAs($this->recruteur)->get(route("recruteur.{$route}"))->assertOk();
        }
    }

    public function test_le_recruteur_met_a_jour_son_entreprise_sans_pouvoir_se_verifier(): void
    {
        $this->entreprise->update(['est_verifiee' => false]);

        $this->actingAs($this->recruteur)->put(route('recruteur.entreprise.update'), [
            'nom' => 'Sonatel SA',
            'telephone' => '338000000',
            'est_verifiee' => 1,
        ])->assertSessionHasNoErrors();

        $this->entreprise->refresh();
        $this->assertSame('Sonatel SA', $this->entreprise->nom);
        $this->assertFalse($this->entreprise->est_verifiee);
    }

    public function test_une_offre_soumise_attend_la_validation_de_l_agence(): void
    {
        $this->actingAs($this->recruteur)->post(route('recruteur.offres.store'), $this->offreValide())->assertSessionHasNoErrors();

        $offre = JobOffer::firstOrFail();
        $this->assertSame(OfferStatus::EnAttente, $offre->statut);
        $this->assertSame($this->entreprise->id, $offre->company_id);
        $this->get(route('offres.show', $offre))->assertNotFound();
    }

    public function test_brouillon_et_validation_des_salaires(): void
    {
        $this->actingAs($this->recruteur)->post(route('recruteur.offres.store'), $this->offreValide(['salaire_max' => 100000]))
            ->assertSessionHasErrors('salaire_max');

        $this->actingAs($this->recruteur)->post(route('recruteur.offres.store'), $this->offreValide(['brouillon' => 1]));
        $this->assertSame(OfferStatus::Brouillon, JobOffer::firstOrFail()->statut);
    }

    public function test_modifier_une_offre_publiee_la_repasse_en_validation(): void
    {
        $offre = JobOffer::factory()->publiee()->for($this->entreprise)->create();

        $this->actingAs($this->recruteur)->put(route('recruteur.offres.update', $offre), $this->offreValide(['titre' => 'Chef comptable senior']));

        $offre->refresh();
        $this->assertSame('Chef comptable senior', $offre->titre);
        $this->assertSame(OfferStatus::EnAttente, $offre->statut);
    }

    public function test_cloture_d_une_offre(): void
    {
        $offre = JobOffer::factory()->publiee()->for($this->entreprise)->create();

        $this->actingAs($this->recruteur)->patch(route('recruteur.offres.cloturer', $offre));

        $this->assertSame(OfferStatus::Cloturee, $offre->fresh()->statut);
        $this->actingAs($this->recruteur)->get(route('recruteur.offres.edit', $offre))->assertSessionHas('erreur');
    }

    public function test_un_recruteur_n_accede_pas_aux_offres_d_une_autre_entreprise(): void
    {
        $offre = JobOffer::factory()->publiee()->create();
        $candidature = $this->candidature($offre);

        $this->actingAs($this->recruteur)->get(route('recruteur.offres.show', $offre))->assertForbidden();
        $this->actingAs($this->recruteur)->put(route('recruteur.offres.update', $offre), $this->offreValide())->assertForbidden();
        $this->actingAs($this->recruteur)->get(route('recruteur.candidatures.show', $candidature))->assertForbidden();
        $this->actingAs($this->recruteur)->get(route('recruteur.candidatures.document', [$candidature, 'cv']))->assertForbidden();
    }

    public function test_le_recruteur_examine_et_fait_avancer_une_candidature(): void
    {
        $offre = JobOffer::factory()->publiee()->for($this->entreprise)->create();
        $candidature = $this->candidature($offre);

        $this->actingAs($this->recruteur)->get(route('recruteur.candidatures.show', $candidature))
            ->assertOk()->assertSee($candidature->candidat->email);
        $this->assertSame(ApplicationStatus::EnExamen, $candidature->fresh()->statut);

        $this->actingAs($this->recruteur)->get(route('recruteur.candidatures.document', [$candidature, 'cv']))->assertDownload();
        $this->actingAs($this->recruteur)->get(route('recruteur.candidatures.document', [$candidature, 'lettre']))->assertNotFound();

        $this->actingAs($this->recruteur)->patch(route('recruteur.candidatures.update', $candidature), [
            'statut' => 'entretien',
            'note_recruteur' => 'Entretien mardi 10h',
        ])->assertSessionHasNoErrors();

        $this->assertSame(ApplicationStatus::Entretien, $candidature->fresh()->statut);
        $this->actingAs($candidature->candidat)->get(route('candidat.candidatures.index'))
            ->assertSee('Entretien programmé')->assertDontSee('Entretien mardi 10h');
    }

    public function test_filtrage_des_candidatures_recues(): void
    {
        $offre = JobOffer::factory()->publiee()->for($this->entreprise)->create();
        $this->candidature($offre, statut: ApplicationStatus::Retenue);
        $this->candidature($offre);
        $this->candidature(JobOffer::factory()->publiee()->create());

        $reponse = $this->actingAs($this->recruteur)->get(route('recruteur.candidatures.index', ['statut' => 'retenue']));
        $this->assertSame(1, $reponse->viewData('candidatures')->total());
        $this->assertSame(2, $this->actingAs($this->recruteur)->get(route('recruteur.candidatures.index'))->viewData('candidatures')->total());
    }

    public function test_besoin_en_recrutement_soumis_puis_annule(): void
    {
        $this->actingAs($this->recruteur)->post(route('recruteur.besoins.store'), [
            'intitule_poste' => 'Commerciaux terrain',
            'nombre_postes' => 5,
            'type_contrat' => 'cdd',
            'description' => 'Force de vente pour la région de Thiès.',
        ])->assertSessionHasNoErrors();

        $besoin = $this->entreprise->recruitmentRequests()->firstOrFail();
        $this->assertSame(RecruitmentRequestStatus::Soumise, $besoin->statut);
        $this->actingAs($this->recruteur)->get(route('recruteur.besoins.show', $besoin))->assertOk()->assertSee('Commerciaux terrain');

        $this->actingAs($this->recruteur)->patch(route('recruteur.besoins.annuler', $besoin));
        $this->assertSame(RecruitmentRequestStatus::Annulee, $besoin->fresh()->statut);

        $autre = Company::factory()->create()->user;
        $this->actingAs($autre)->get(route('recruteur.besoins.show', $besoin))->assertForbidden();
    }

    public function test_recherche_de_profils_visibles_uniquement(): void
    {
        CandidateProfile::factory()->create(['titre_profil' => 'Développeur Laravel', 'competences' => 'PHP, Laravel', 'visible_recruteurs' => true]);
        $cache = CandidateProfile::factory()->create(['titre_profil' => 'Développeur caché', 'competences' => 'PHP', 'visible_recruteurs' => false]);
        CandidateProfile::factory()->create(['titre_profil' => 'Comptable', 'competences' => 'Sage']);

        $this->actingAs($this->recruteur)->get(route('recruteur.profils.index', ['q' => 'php']))
            ->assertOk()->assertSee('Développeur Laravel')->assertDontSee('Développeur caché')->assertDontSee('Comptable');

        $this->actingAs($this->recruteur)->get(route('recruteur.profils.show', $cache))->assertNotFound();
    }

    public function test_demande_de_mise_en_relation_sans_doublon_et_suivi(): void
    {
        $profil = CandidateProfile::factory()->create(['visible_recruteurs' => true]);

        $this->actingAs($this->recruteur)->get(route('recruteur.profils.show', $profil))
            ->assertOk()->assertDontSee($profil->user->email);

        $this->actingAs($this->recruteur)->post(route('recruteur.profils.contacter', $profil), ['commentaire' => 'Profil intéressant']);
        $this->actingAs($this->recruteur)->post(route('recruteur.profils.contacter', $profil))->assertSessionHas('erreur');

        $match = CandidateMatch::sole();
        $this->assertSame(MatchStatus::Proposee, $match->statut);
        $this->assertSame($this->entreprise->id, $match->company_id);

        $this->actingAs($this->recruteur)->patch(route('recruteur.recrutements.update', $match), ['statut' => 'embauche']);
        $this->assertSame(MatchStatus::Embauche, $match->fresh()->statut);

        $this->actingAs(Company::factory()->create()->user)->patch(route('recruteur.recrutements.update', $match), ['statut' => 'refusee'])->assertForbidden();
    }

    public function test_le_recruteur_commande_une_prestation_entreprise(): void
    {
        $service = Service::factory()->create(['cible' => ServiceTarget::Recruteur, 'prix' => 150000]);

        $this->actingAs($this->recruteur)->post(route('recruteur.dossiers.store'), ['service_id' => $service->id])
            ->assertRedirect();

        $dossier = $this->recruteur->serviceRequests()->sole();
        $this->assertSame(150000, $dossier->montant);
        $this->actingAs($this->recruteur)->get(route('recruteur.dossiers.show', $dossier))->assertOk();
    }
}
