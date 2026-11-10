<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@maamkhalifa.sn'],
            [
                'name' => 'Administrateur Maam Khalifa',
                'password' => 'password',
                'role' => Role::Admin,
                'est_actif' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
