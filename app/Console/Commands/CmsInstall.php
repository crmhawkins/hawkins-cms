<?php

namespace App\Console\Commands;

use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CmsInstall extends Command
{
    protected $signature = 'cms:install';
    protected $description = 'Install and configure Hawkins CMS';

    public function handle(): int
    {
        $this->info('Installing Hawkins CMS...');

        $this->call('migrate', ['--force' => true]);

        $siteName = $this->ask('Site name', config('app.name', 'My Site'));
        $siteUrl = $this->ask('Site URL', config('app.url', 'http://localhost'));

        SiteSettings::updateOrCreate(['id' => 1], [
            'site_name' => $siteName,
            'site_url' => $siteUrl,
        ]);

        $adminEmail = $this->ask('Admin email', 'admin@example.com');
        $adminPassword = $this->secret('Admin password');
        $adminName = $this->ask('Admin name', 'Admin');

        foreach (['superadmin', 'admin', 'editor'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $user = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
            ]
        );
        $user->assignRole('superadmin');

        $this->info("CMS installed. Login at {$siteUrl}/admin with {$adminEmail}");

        return self::SUCCESS;
    }
}
