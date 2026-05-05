<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Tenant;
use Database\Seeders\SanzahraTenantSeeder;
use Illuminate\Console\Command;

class MigrateSanzahra extends Command
{
    protected $signature = 'sanzahra:migrate {--fresh : Drop existing Sanzahra pages before migrating}';
    protected $description = 'Migrate Sanzahra static web to Hawkins CMS';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $tenant = Tenant::where('id', 'sanzahra')->first();
            if ($tenant) {
                Page::withoutGlobalScopes()
                    ->where('tenant_id', $tenant->id)
                    ->delete();
                $this->info('Existing Sanzahra pages deleted.');
            }
        }

        $this->call('db:seed', ['--class' => SanzahraTenantSeeder::class]);
        $this->info('Sanzahra migration complete.');

        return 0;
    }
}
