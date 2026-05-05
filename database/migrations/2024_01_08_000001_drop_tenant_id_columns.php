<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Removes tenant_id columns left by a previous multi-tenant install.
// Safe on fresh installs (columns won't exist → no-op).
return new class extends Migration {
    private array $tables = [
        'users', 'pages', 'blocks', 'block_revisions', 'menu_items',
        'headers', 'products', 'product_variants', 'carts',
        'orders', 'contact_submissions',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) continue;
            if (!Schema::hasColumn($table, 'tenant_id')) continue;

            // Drop any FK that references tenant_id on this table
            $this->dropForeignKeysForColumn($table, 'tenant_id');

            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('tenant_id');
            });
        }
    }

    private function dropForeignKeysForColumn(string $table, string $column): void
    {
        $dbName = DB::getDatabaseName();

        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME = ?
              AND COLUMN_NAME = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName, $table, $column]);

        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }
    }

    public function down(): void {}
};
