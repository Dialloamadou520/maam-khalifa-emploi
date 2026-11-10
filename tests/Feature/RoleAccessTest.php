<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function espaces(): array
    {
        return [
            'candidat' => ['candidat', 'candidat.dashboard'],
            'recruteur' => ['recruteur', 'recruteur.dashboard'],
            'admin' => ['admin', 'admin.dashboard'],
        ];
    }

    #[DataProvider('espaces')]
    public function test_chaque_role_est_redirige_vers_son_espace(string $role, string $route): void
    {
        $user = User::factory()->{$role}()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route($route));
        $this->actingAs($user)->get(route($route))->assertOk();
    }

    #[DataProvider('espaces')]
    public function test_les_espaces_sont_cloisonnes(string $role, string $route): void
    {
        foreach (array_diff(array_keys(self::espaces()), [$role]) as $autreRole) {
            $autre = User::factory()->{$autreRole}()->create();

            $this->actingAs($autre)->get(route($route))->assertForbidden();
        }
    }

    #[DataProvider('espaces')]
    public function test_les_espaces_sont_proteges_pour_les_visiteurs(string $role, string $route): void
    {
        $this->get(route($route))->assertRedirect(route('login'));
    }

    public function test_un_compte_desactive_ne_peut_pas_se_connecter(): void
    {
        $user = User::factory()->candidat()->inactif()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_compte_desactive_en_cours_de_session_est_deconnecte(): void
    {
        $user = User::factory()->recruteur()->inactif()->create();

        $this->actingAs($user)->get(route('recruteur.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
