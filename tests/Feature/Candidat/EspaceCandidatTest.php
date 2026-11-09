<?php

namespace Tests\Feature\Candidat;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Enums\ServiceTarget;
use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\JobOffer;
use App\Models\Scholarship;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EspaceCandidatTest extends TestCase
{
    use RefreshDatabase;

    private User $candidat;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->candidat = CandidateProfile::factory()->create()->user;
    }

    public function test_le_candidat_met_a_jour_son_profil(): void
    {
        $this->actingAs($this->candidat)->get(route('candidat.profil.edit'))->assertOk();

        $this->actingAs($this->candidat)->put(route('candidat.profil.update'), [
            'name' => 'Awa Ndiaye',
            'telephone' => '771112233',
            'titre_profil' => 'Comptable confirmée',
            'annees_experience' => 6,
            'competences' => 'SYSCOHADA, Sage, Excel',
        ])->assertRedirect(route('candidat.profil.edit'));

        $this->candidat->refresh();
        $this->assertSame('Awa Ndiaye', $this->candidat->name);
        $this->assertSame('Comptable confirmée', $this->candidat->candidateProfile->titre_profil);
        $this->assertSame(['SYSCOHADA', 'Sage', 'Excel'], $this->candidat->candidateProfile->listeCompetences());
        $this->assertFalse($this->candidat->candidateProfile->visible_recruteurs);
    }

    public function test_le_candidat_depose_telecharge_et_supprime_un_cv(): void
    {
        $this->actingAs($this->candidat)->post(route('candidat.documents.store'), [
            'type' => 'cv',
            'fichier' => UploadedFile::fake()->create('mon-cv.pdf', 200, 'application/pdf'),
        ])->assertSessionHasNoErrors();

        $document = $this->candidat->documents()->firstOrFail();
        $this->assertSame(DocumentType::Cv, $document->type);
        Storage::disk('local')->assertExists($document->chemin);

        $this->actingAs($this->candidat)->get(route('candidat.documents.index'))->assertSee('mon-cv');
        $this->actingAs($this->candidat)->get(route('candidat.documents.show', $document))->assertDownload('mon-cv.pdf');

        $this->actingAs($this->candidat)->delete(route('candidat.documents.destroy', $document));
        Storage::disk('local')->assertMissing($document->chemin);
        $this->assertModelMissing($document);
    }

    public function test_les_fichiers_non_autorises_sont_refuses(): void
    {
        $this->actingAs($this->candidat)->post(route('candidat.documents.store'), [
            'type' => 'cv',
            'fichier' => UploadedFile::fake()->create('script.exe', 10),
        ])->assertSessionHasErrors('fichier');
    }

    public function test_un_candidat_ne_peut_pas_lire_le_document_d_un_autre(): void
    {
        $autre = CandidateProfile::factory()->create()->user;
        $this->actingAs($autre)->post(route('candidat.documents.store'), [
            'type' => 'cv',
            'fichier' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf'),
        ]);

        $document = $autre->documents()->firstOrFail();

        $this->actingAs($this->candidat)->get(route('candidat.documents.show', $document))->assertForbidden();
        $this->actingAs($this->candidat)->delete(route('candidat.documents.destroy', $document))->assertForbidden();
    }

    public function test_le_candidat_postule_avec_un_nouveau_cv(): void
    {
        $offre = JobOffer::factory()->publiee()->create();

        $this->actingAs($this->candidat)->get(route('candidat.candidatures.create', $offre))->assertOk();

        $this->actingAs($this->candidat)->post(route('candidat.candidatures.store', $offre), [
            'cv_fichier' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
            'message' => 'Très motivée',
        ])->assertRedirect(route('candidat.candidatures.index'));

        $candidature = JobApplication::firstOrFail();
        $this->assertSame($this->candidat->id, $candidature->user_id);
        $this->assertSame(ApplicationStatus::Envoyee, $candidature->statut);
        $this->assertNotNull($candidature->cv);

        $this->actingAs($this->candidat)->get(route('offres.show', $offre))->assertSee('déjà postulé');
    }

    public function test_un_cv_est_obligatoire_et_on_ne_postule_qu_une_fois(): void
    {
        $offre = JobOffer::factory()->publiee()->create();

        $this->actingAs($this->candidat)->post(route('candidat.candidatures.store', $offre), [])
            ->assertSessionHasErrors('cv_document_id');

        $this->actingAs($this->candidat)->post(route('candidat.candidatures.store', $offre), [
            'cv_fichier' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);
        $this->actingAs($this->candidat)->post(route('candidat.candidatures.store', $offre), [
            'cv_document_id' => $this->candidat->documents()->first()->id,
        ])->assertSessionHas('erreur');

        $this->assertSame(1, JobApplication::count());
    }

    public function test_on_ne_peut_pas_postuler_a_une_offre_non_validee(): void
    {
        $offre = JobOffer::factory()->create();

        $this->actingAs($this->candidat)->get(route('candidat.candidatures.create', $offre))->assertNotFound();
    }

    public function test_le_candidat_retire_une_candidature_non_traitee_seulement(): void
    {
        $offre = JobOffer::factory()->publiee()->create();
        $candidature = new JobApplication(['job_offer_id' => $offre->id, 'statut' => ApplicationStatus::Entretien]);
        $candidature->user_id = $this->candidat->id;
        $candidature->save();

        $this->actingAs($this->candidat)->delete(route('candidat.candidatures.destroy', $candidature))->assertSessionHas('erreur');
        $this->assertModelExists($candidature);

        $candidature->update(['statut' => ApplicationStatus::Envoyee]);
        $this->actingAs($this->candidat)->delete(route('candidat.candidatures.destroy', $candidature));
        $this->assertModelMissing($candidature);
    }

    public function test_le_candidat_demande_une_prestation_au_tarif_en_vigueur(): void
    {
        $service = Service::factory()->create(['nom' => 'Accompagnement bourse', 'prix' => 25000]);
        $bourse = Scholarship::factory()->create();

        $this->actingAs($this->candidat)
            ->get(route('candidat.dossiers.create', ['bourse' => $bourse->slug]))
            ->assertOk()
            ->assertSee('Accompagnement bourse');

        $this->actingAs($this->candidat)->post(route('candidat.dossiers.store'), [
            'service_id' => $service->id,
            'scholarship_id' => $bourse->id,
            'details' => 'Master au Canada',
        ])->assertSessionHasNoErrors();

        $dossier = $this->candidat->serviceRequests()->firstOrFail();
        $this->assertSame(25000, $dossier->montant);
        $this->assertStringStartsWith('DOS-', $dossier->reference);

        $service->update(['prix' => 30000]);
        $this->assertSame(25000, $dossier->fresh()->montant);

        $this->actingAs($this->candidat)->get(route('candidat.dossiers.show', $dossier))->assertOk()->assertSee('25 000 FCFA');
        $this->actingAs($this->candidat)->get(route('candidat.dossiers.index'))->assertSee($dossier->reference);
    }

    public function test_un_candidat_ne_peut_pas_commander_une_prestation_entreprise(): void
    {
        $service = Service::factory()->create(['cible' => ServiceTarget::Recruteur]);

        $this->actingAs($this->candidat)->post(route('candidat.dossiers.store'), ['service_id' => $service->id])
            ->assertSessionHasErrors('service_id');
    }

    public function test_un_candidat_ne_voit_pas_le_dossier_d_un_autre(): void
    {
        $autre = CandidateProfile::factory()->create()->user;
        $dossier = $autre->serviceRequests()->create(['service_id' => Service::factory()->create()->id, 'montant' => 1000]);

        $this->actingAs($this->candidat)->get(route('candidat.dossiers.show', $dossier))->assertForbidden();
    }
}
