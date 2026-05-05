<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hawkins superadmin
        User::updateOrCreate(
            ['email' => 'admin@hawkins.es'],
            [
                'name' => 'Hawkins Admin',
                'email' => 'admin@hawkins.es',
                'password' => Hash::make('Hawkins2024!'),
                'tenant_id' => null,
            ]
        );
    }
}
