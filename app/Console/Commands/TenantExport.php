<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TenantExport extends Command
{
    protected $signature = 'tenant:export {id} {--output=}';
    protected $description = 'Export all data for a tenant (GDPR Art. 20)';

    public function handle(): int
    {
        $tenantId = $this->argument('id');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "tenant-{$tenantId}-{$timestamp}";
        $exportPath = storage_path("app/exports/{$filename}");

        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        // Get all tables with tenant_id column
        $tables = $this->getTenantTables();
        $data = [];

        // Bypass MySQL trigger for superadmin read
        DB::statement('SET @current_tenant_id = NULL');

        foreach ($tables as $table) {
            $rows = DB::table($table)->where('tenant_id', $tenantId)->get()->toArray();
            $data[$table] = $rows;
            $this->info("Exported {$table}: " . count($rows) . " rows");
        }

        // Write JSON files
        mkdir($exportPath, 0755, true);
        foreach ($data as $table => $rows) {
            file_put_contents("{$exportPath}/{$table}.json", json_encode($rows, JSON_PRETTY_PRINT));
        }

        // Create zip
        $zipPath = $this->option('output') ?? storage_path("app/exports/{$filename}.zip");
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE);
        foreach (glob("{$exportPath}/*.json") as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Cleanup temp dir
        array_map('unlink', glob("{$exportPath}/*.json"));
        rmdir($exportPath);

        // Log to audit
        DB::table('audit_log')->insert([
            'tenant_id' => $tenantId,
            'action' => 'tenant_export',
            'metadata' => json_encode(['tables' => array_keys($data), 'zip' => $zipPath]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Export complete: {$zipPath}");
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
}
