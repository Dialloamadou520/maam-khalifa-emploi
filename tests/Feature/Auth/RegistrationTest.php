<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_d_inscription_s_affiche(): void
    {
        $this->get('/register')->assertOk()->assertSee('Je suis candidat');
    }

    public function test_un_candidat_peut_creer_son_compte(): void
    {
        $response = $this->post('/register', [
            'role' => 'candidat',
            'name' => 'Awa Ndiaye',
            'email' => 'awa@example.com',
            'telephone' => '771234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'awa@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame(Role::Candidat, $user->role);
        $this->assertNotNull($user->candidateProfile);
        $response->assertRedirect(route('candidat.dashboard'));
    }

    public function test_un_recruteur_peut_creer_son_compte_entreprise(): void
    {
        $response = $this->post('/register', [
            'role' => 'recruteur',
            'name' => 'Moussa Diop',
            'entreprise' => 'Teranga Services',
            'email' => 'rh@teranga.sn',
            'telephone' => '338001122',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'rh@teranga.sn')->firstOrFail();

        $this->assertSame(Role::Recruteur, $user->role);
        $this->assertSame('Teranga Services', $user->company->nom);
        $this->assertFalse($user->company->est_verifiee);
        $response->assertRedirect(route('recruteur.dashboard'));
    }

    public function test_le_nom_de_l_entreprise_est_obligatoire_pour_un_recruteur(): void
    {
        $this->post('/register', [
            'role' => 'recruteur',
            'name' => 'Moussa Diop',
            'email' => 'rh@teranga.sn',
            'telephone' => '338001122',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('entreprise');

        $this->assertGuest();
    }

    public function test_on_ne_peut_pas_s_inscrire_comme_administrateur(): void
    {
        $this->post('/register', [
            'role' => 'admin',
            'name' => 'Pirate',
            'email' => 'pirate@example.com',
            'telephone' => '770000000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'pirate@example.com']);
    }
}
