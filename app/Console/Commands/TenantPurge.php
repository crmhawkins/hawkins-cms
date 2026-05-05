<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantPurge extends Command
{
    protected $signature = 'tenant:purge {id} {--confirm}';
    protected $description = 'Permanently purge all data for a tenant (GDPR Art. 17)';

    public function handle(): int
    {
        if (!$this->option('confirm')) {
            $this->error('Must pass --confirm flag to purge tenant data.');
            return self::FAILURE;
        }

        $tenantId = $this->argument('id');
        $tenant = \App\Models\Tenant::find($tenantId);

        if (!$tenant) {
            $this->warn("Tenant {$tenantId} not found or already purged.");
            DB::table('audit_log')->insert([
                'action' => 'tenant_purge_skipped',
                'metadata' => json_encode(['tenant_id' => $tenantId, 'reason' => 'not_found']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return self::SUCCESS;
        }

        // Bypass triggers for superadmin operation
        DB::statement('SET @current_tenant_id = NULL');

        // Get all tenant-scoped tables
        $tables = $this->getTenantTables();

        DB::transaction(function () use ($tenant, $tenantId, $tables) {
            // Soft-delete where available, then hard-delete
            foreach ($tables as $table) {
                if (Schema::hasColumn($table, 'deleted_at')) {
                    DB::table($table)->where('tenant_id', $tenantId)->update(['deleted_at' => now()]);
                }
                DB::table($table)->where('tenant_id', $tenantId)->delete();
                $this->info("Purged table: {$table}");
            }

            // Delete tenant storage
            $storagePath = storage_path("app/tenants/{$tenantId}");
            if (is_dir($storagePath)) {
                $this->deleteDirectory($storagePath);
                $this->info("Deleted storage: {$storagePath}");
            }

            // Remove domains
            $tenant->domains()->delete();

            // Delete tenant record
            $tenant->delete();
        });

        // Audit log is NEVER purged (legal retention)
        DB::table('audit_log')->insert([
            'action' => 'tenant_purge',
            'metadata' => json_encode(['tenant_id' => $tenantId, 'tables_purged' => $tables]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Tenant {$tenantId} purged successfully.");
        return self::SUCCESS;
    }

    private function getTenantTables(): array
    {
        $allTables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tenantTables = [];

        foreach ($allTables as $tableObj) {
            $table = array_values((array) $tableObj)[0];
            $hasColumn = DB::select("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = 'tenant_id'", [$dbName, $table]);
            if ($hasColumn[0]->cnt > 0) {
                $tenantTables[] = $table;
            }
        }

        return $tenantTables;
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
