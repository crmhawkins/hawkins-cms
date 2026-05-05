<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['superadmin', 'admin', 'editor'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@hawkins.es'],
            [
                'name' => 'Admin',
                'email' => 'admin@hawkins.es',
                'password' => Hash::make('Hawkins2024!'),
            ]
        );
        $admin->assignRole('superadmin');

        SiteSettings::firstOrCreate(['id' => 1], [
            'site_name'    => 'Mi Sitio',
            'site_url'     => env('APP_URL', 'http://localhost'),
            'theme'        => 'sanzahra',
            'ecommerce_enabled' => false,
            'payment_gateway'   => 'none',
        ]);

        Page::updateOrCreate(['slug' => 'home'], [
            'title'        => 'Inicio',
            'status'       => 'published',
            'published_at' => now(),
        ]);
    }
}
