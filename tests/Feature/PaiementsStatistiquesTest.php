<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementsStatistiquesTest extends TestCase
{
    use RefreshDatabase;

    private User $candidat;

    private ServiceRequest $dossier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->candidat = CandidateProfile::factory()->create()->user;
        $this->dossier = $this->candidat->serviceRequests()->create(['service_id' => Service::factory()->create()->id, 'montant' => 10000]);
    }

    public function test_le_candidat_declare_un_paiement_wave_que_l_agence_confirme(): void
    {
        $this->actingAs($this->candidat)->get(route('candidat.dossiers.show', $this->dossier))->assertSee('Payer la prestation');
        $this->actingAs($this->candidat)->get(route('candidat.paiements.create', $this->dossier))->assertOk()->assertSee('10 000 FCFA');

        $this->actingAs($this->candidat)->post(route('candidat.paiements.store', $this->dossier), ['methode' => 'wave'])
            ->assertSessionHasErrors(['telephone', 'transaction_id']);

        $this->actingAs($this->candidat)->post(route('candidat.paiements.store', $this->dossier), [
            'methode' => 'wave', 'telephone' => '771234567', 'transaction_id' => 'TX-123',
        ])->assertRedirect(route('candidat.dossiers.show', $this->dossier));

        $paiement = Payment::sole();
        $this->assertSame(PaymentStatus::EnAttente, $paiement->statut);
        $this->assertSame(10000, $paiement->montant);
        $this->assertFalse($this->dossier->estPaye());

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.paiements.index'))->assertOk()->assertSee('TX-123');
        $this->actingAs($admin)->patch(route('admin.paiements.confirmer', $paiement));

        $this->assertSame(PaymentStatus::Payee, $paiement->fresh()->statut);
        $this->assertTrue($this->dossier->estPaye());
        $this->actingAs($this->candidat)->get(route('candidat.paiements.create', $this->dossier))->assertRedirect(route('candidat.dossiers.show', $this->dossier));
        $this->actingAs($this->candidat)->get(route('candidat.paiements.index'))->assertSee($paiement->reference);
    }

    public function test_paiement_en_especes_sans_reference_et_rejet(): void
    {
        $this->actingAs($this->candidat)->post(route('candidat.paiements.store', $this->dossier), ['methode' => 'especes'])->assertSessionHasNoErrors();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->patch(route('admin.paiements.rejeter', Payment::sole()));
        $this->assertSame(PaymentStatus::Echouee, Payment::sole()->statut);
        $this->actingAs($admin)->patch(route('admin.paiements.confirmer', Payment::sole()))->assertSessionHas('erreur');
    }

    public function test_un_client_ne_paie_pas_le_dossier_d_un_autre(): void
    {
        $autre = CandidateProfile::factory()->create()->user;

        $this->actingAs($autre)->get(route('candidat.paiements.create', $this->dossier))->assertForbidden();
        $this->actingAs($autre)->post(route('candidat.paiements.store', $this->dossier), ['methode' => 'especes'])->assertForbidden();
    }

    public function test_le_recruteur_paie_sa_prestation(): void
    {
        $recruteur = Company::factory()->create()->user;
        $dossier = $recruteur->serviceRequests()->create(['service_id' => Service::factory()->create()->id, 'montant' => 50000]);

        $this->actingAs($recruteur)->post(route('recruteur.paiements.store', $dossier), ['methode' => 'virement', 'transaction_id' => 'VIR-9'])
            ->assertRedirect(route('recruteur.dossiers.show', $dossier));
        $this->actingAs($recruteur)->get(route('recruteur.paiements.index'))->assertOk()->assertSee('VIR-9');
    }

    public function test_l_admin_encaisse_un_paiement_partiel_au_guichet(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.paiements.store', $this->dossier), ['montant' => 4000, 'methode' => 'especes'])->assertSessionHasNoErrors();
        $this->assertFalse($this->dossier->estPaye());

        $this->actingAs($this->candidat)->get(route('candidat.paiements.create', $this->dossier))->assertSee('6 000 FCFA');
        $this->actingAs($admin)->post(route('admin.paiements.store', $this->dossier), ['montant' => 6000, 'methode' => 'orange_money']);
        $this->assertTrue($this->dossier->estPaye());
        $this->assertSame($admin->id, Payment::latest('id')->first()->enregistre_par);
    }

    public function test_statistiques_et_exports(): void
    {
        $admin = User::factory()->admin()->create();
        $paiement = new Payment(['service_request_id' => $this->dossier->id, 'montant' => 10000, 'methode' => 'wave', 'statut' => PaymentStatus::Payee, 'payee_le' => now()]);
        $paiement->user_id = $this->candidat->id;
        $paiement->save();

        $this->actingAs($admin)->get(route('admin.statistiques'))->assertOk()->assertSee('10 000 FCFA')->assertSee('Candidats inscrits');

        foreach (['paiements', 'dossiers', 'candidatures', 'offres', 'candidats'] as $type) {
            $reponse = $this->actingAs($admin)->get(route('admin.statistiques.export', ['type' => $type]));
            $reponse->assertOk()->assertDownload("{$type}-".now()->format('Y-m-d').'.csv');
        }

        $csv = $this->actingAs($admin)->get(route('admin.statistiques.export', ['type' => 'paiements']))->streamedContent();
        $this->assertStringContainsString($paiement->reference, $csv);
        $this->assertStringContainsString('Wave', $csv);

        $this->actingAs($this->candidat)->get(route('admin.statistiques'))->assertForbidden();
    }
}
