<?php

namespace Tests\Feature\Admin;

use App\Enums\MatchStatus;
use App\Enums\OfferStatus;
use App\Enums\RecruitmentRequestStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\CandidateMatch;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\JobOffer;
use App\Models\Scholarship;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EspaceAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->admin = User::factory()->admin()->create();
    }

    public function test_toutes_les_pages_admin_s_affichent(): void
    {
        $candidat = CandidateProfile::factory()->create()->user;
        $offre = JobOffer::factory()->create();
        $besoin = $offre->company->recruitmentRequests()->create(['intitule_poste' => 'Caissiers', 'type_contrat' => 'cdd', 'description' => 'x']);
        $dossier = $candidat->serviceRequests()->create(['service_id' => Service::factory()->create()->id, 'montant' => 5000]);
        $bourse = Scholarship::factory()->create();

        $pages = [
            route('admin.dashboard'), route('admin.candidats.index'), route('admin.recruteurs.index'),
            route('admin.utilisateurs.show', $candidat), route('admin.utilisateurs.show', $offre->company->user),
            route('admin.offres.index'), route('admin.offres.show', $offre),
            route('admin.besoins.index'), route('admin.besoins.show', $besoin),
            route('admin.dossiers.index'), route('admin.dossiers.show', $dossier),
            route('admin.bourses.index'), route('admin.bourses.create'), route('admin.bourses.edit', $bourse),
            route('admin.prestations.index'), route('admin.prestations.create'), route('admin.prestations.edit', $dossier->service),
            route('admin.mises-en-relation.index'), route('admin.mises-en-relation.create'),
        ];

        foreach ($pages as $page) {
            $this->actingAs($this->admin)->get($page)->assertOk();
        }
    }

    public function test_les_autres_roles_n_accedent_pas_a_l_administration(): void
    {
        $this->actingAs(Company::factory()->create()->user)->get(route('admin.offres.index'))->assertForbidden();
        $this->actingAs(CandidateProfile::factory()->create()->user)->get(route('admin.candidats.index'))->assertForbidden();
    }

    public function test_recherche_et_desactivation_d_un_candidat(): void
    {
        $candidat = CandidateProfile::factory()->create()->user;
        $candidat->update(['name' => 'Fatou Sarr']);
        CandidateProfile::factory()->create();

        $reponse = $this->actingAs($this->admin)->get(route('admin.candidats.index', ['q' => 'Fatou']));
        $this->assertSame(1, $reponse->viewData('utilisateurs')->total());

        $this->actingAs($this->admin)->patch(route('admin.utilisateurs.basculer', $candidat));
        $this->assertFalse($candidat->fresh()->est_actif);

        $this->actingAs($candidat->fresh())->get(route('candidat.dashboard'))->assertRedirect(route('login'));
        $this->actingAs($this->admin)->patch(route('admin.utilisateurs.basculer', $this->admin))->assertForbidden();
    }

    public function test_verification_d_une_entreprise(): void
    {
        $entreprise = Company::factory()->create(['est_verifiee' => false]);

        $this->actingAs($this->admin)->get(route('admin.recruteurs.index', ['etat' => 'a_verifier']))->assertSee($entreprise->nom);
        $this->actingAs($this->admin)->patch(route('admin.utilisateurs.verifier', $entreprise->user));

        $this->assertTrue($entreprise->fresh()->est_verifiee);
    }

    public function test_l_admin_telecharge_un_document_candidat(): void
    {
        $candidat = CandidateProfile::factory()->create()->user;
        $this->actingAs($candidat)->post(route('candidat.documents.store'), ['type' => 'cv', 'fichier' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf')]);

        $this->actingAs($this->admin)->get(route('admin.documents.show', $candidat->documents()->first()))->assertDownload('cv.pdf');
    }

    public function test_validation_d_une_offre_la_publie(): void
    {
        $offre = JobOffer::factory()->create();
        $this->get(route('offres.show', $offre))->assertNotFound();

        $this->actingAs($this->admin)->get(route('admin.offres.index'))->assertSee($offre->titre);
        $this->actingAs($this->admin)->patch(route('admin.offres.valider', $offre))->assertSessionHas('status');

        $offre->refresh();
        $this->assertSame(OfferStatus::Publiee, $offre->statut);
        $this->assertSame($this->admin->id, $offre->validee_par);
        $this->assertNotNull($offre->publiee_le);
        $this->get(route('offres.show', $offre))->assertOk();
    }

    public function test_rejet_d_une_offre_avec_motif_visible_par_le_recruteur(): void
    {
        $offre = JobOffer::factory()->create();

        $this->actingAs($this->admin)->patch(route('admin.offres.rejeter', $offre), [])->assertSessionHasErrors('motif_rejet');
        $this->actingAs($this->admin)->patch(route('admin.offres.rejeter', $offre), ['motif_rejet' => 'Salaire non conforme']);

        $this->assertSame(OfferStatus::Rejetee, $offre->fresh()->statut);
        $this->actingAs($offre->company->user)->get(route('recruteur.offres.show', $offre))->assertSee('Salaire non conforme');
    }

    public function test_traitement_d_un_dossier_visible_par_le_client(): void
    {
        $candidat = CandidateProfile::factory()->create()->user;
        $dossier = $candidat->serviceRequests()->create(['service_id' => Service::factory()->create()->id, 'montant' => 5000]);

        $this->actingAs($this->admin)->get(route('admin.dossiers.index', ['q' => $dossier->reference]))->assertSee($dossier->reference);
        $this->actingAs($this->admin)->patch(route('admin.dossiers.update', $dossier), ['statut' => 'en_cours', 'note_admin' => 'Rendez-vous lundi à 10 h']);

        $this->assertSame(ServiceRequestStatus::EnCours, $dossier->fresh()->statut);
        $this->assertSame($this->admin->id, $dossier->fresh()->traite_par);
        $this->actingAs($candidat)->get(route('candidat.dossiers.show', $dossier))->assertSee('Rendez-vous lundi à 10 h');
    }

    public function test_crud_des_bourses(): void
    {
        $this->actingAs($this->admin)->post(route('admin.bourses.store'), [
            'titre' => 'Bourse Erasmus+', 'organisme' => 'Union européenne', 'type' => 'bourse',
            'description' => 'Mobilité internationale', 'est_publiee' => 1,
        ])->assertSessionHasNoErrors();

        $bourse = Scholarship::sole();
        $this->get(route('bourses.index'))->assertSee('Bourse Erasmus+');

        $this->actingAs($this->admin)->put(route('admin.bourses.update', $bourse), [
            'titre' => 'Bourse Erasmus+', 'organisme' => 'Union européenne', 'type' => 'bourse', 'description' => 'Mobilité',
        ]);
        $this->assertFalse($bourse->fresh()->est_publiee);
        $this->get(route('bourses.index'))->assertDontSee('Bourse Erasmus+');

        $this->actingAs($this->admin)->delete(route('admin.bourses.destroy', $bourse));
        $this->assertModelMissing($bourse);
    }

    public function test_nouveau_tarif_sans_effet_sur_les_dossiers_existants(): void
    {
        $service = Service::factory()->create(['prix' => 10000]);
        $dossier = CandidateProfile::factory()->create()->user->serviceRequests()->create(['service_id' => $service->id, 'montant' => 10000]);

        $this->actingAs($this->admin)->put(route('admin.prestations.update', $service), [
            'nom' => $service->nom, 'cible' => 'candidat', 'prix' => 15000, 'est_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertSame(15000, $service->fresh()->prix);
        $this->assertSame(10000, $dossier->fresh()->montant);

        $this->actingAs($this->admin)->put(route('admin.prestations.update', $service), ['nom' => $service->nom, 'cible' => 'candidat', 'prix' => 15000]);
        $this->assertFalse($service->fresh()->est_active);
        $this->actingAs($dossier->user)->get(route('candidat.dossiers.create'))->assertDontSee($service->nom);
    }

    public function test_proposer_un_candidat_sur_un_besoin(): void
    {
        $entreprise = Company::factory()->create();
        $besoin = $entreprise->recruitmentRequests()->create(['intitule_poste' => 'Comptable', 'type_contrat' => 'cdi', 'description' => 'x']);
        $candidat = CandidateProfile::factory()->create(['titre_profil' => 'Comptable confirmé'])->user;

        $this->actingAs($this->admin)->get(route('admin.besoins.show', [$besoin, 'q' => 'Comptable']))->assertSee($candidat->name);

        $this->actingAs($this->admin)->post(route('admin.mises-en-relation.store'), [
            'candidate_id' => $candidat->id,
            'recruitment_request_id' => $besoin->id,
        ])->assertRedirect(route('admin.besoins.show', $besoin));

        $match = CandidateMatch::sole();
        $this->assertSame($entreprise->id, $match->company_id);
        $this->assertSame(RecruitmentRequestStatus::EnTraitement, $besoin->fresh()->statut);

        $this->actingAs($entreprise->user)->get(route('recruteur.besoins.show', $besoin))->assertSee($candidat->name);

        $this->actingAs($this->admin)->post(route('admin.mises-en-relation.store'), [
            'candidate_id' => $candidat->id, 'company_id' => $entreprise->id,
        ])->assertSessionHas('erreur');

        $this->actingAs($this->admin)->patch(route('admin.mises-en-relation.update', $match), ['statut' => 'embauche']);
        $this->assertSame(MatchStatus::Embauche, $match->fresh()->statut);
    }

    public function test_mise_en_relation_incoherente_refusee(): void
    {
        $candidat = CandidateProfile::factory()->create()->user;
        $offre = JobOffer::factory()->publiee()->create();

        $this->actingAs($this->admin)->post(route('admin.mises-en-relation.store'), ['candidate_id' => $candidat->id])
            ->assertSessionHasErrors('company_id');
        $this->actingAs($this->admin)->post(route('admin.mises-en-relation.store'), [
            'candidate_id' => $candidat->id, 'job_offer_id' => $offre->id, 'company_id' => Company::factory()->create()->id,
        ])->assertSessionHasErrors('company_id');

        $this->assertSame(0, CandidateMatch::count());
    }
}
