<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantCreate extends Command
{
    protected $signature = 'tenant:create {name} {--domain=} {--subdomain=} {--theme=sanzahra} {--admin-email=} {--admin-password=}';
    protected $description = 'Create a new tenant with domain(s) and admin user';

    public function handle(): int
    {
        $name = $this->argument('name');
        $id = Str::slug($name);

        $tenant = Tenant::create([
            'id' => $id,
            'name' => $name,
            'theme' => $this->option('theme'),
        ]);

        if ($domain = $this->option('domain')) {
            $tenant->domains()->create(['domain' => $domain]);
            $this->info("Domain added: {$domain}");
        }

        if ($subdomain = $this->option('subdomain')) {
            $tenant->domains()->create(['domain' => $subdomain]);
            $this->info("Subdomain added: {$subdomain}");
        }

        if ($email = $this->option('admin-email')) {
            $password = $this->option('admin-password') ?? 'password';
            User::create([
                'tenant_id' => $tenant->id,
                'name' => $name . ' Admin',
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            $this->info("Admin user created: {$email}");
        }

        $this->info("Tenant created: {$id}");
        return self::SUCCESS;
    }
}
