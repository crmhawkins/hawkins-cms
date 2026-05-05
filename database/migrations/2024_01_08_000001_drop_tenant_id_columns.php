<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Removes tenant_id columns left by a previous multi-tenant install.
// Safe on fresh installs (columns won't exist → no-op).
return new class extends Migration {
    private array $tables = [
        'pages', 'blocks', 'block_revisions', 'menu_items',
        'headers', 'products', 'product_variants', 'carts',
        'orders', 'contact_submissions',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasTable($table)) continue;
            if (!Schema::hasColumn($table, 'tenant_id')) continue;

            Schema::table($table, function (Blueprint $t) {
                // Drop FK if it exists before dropping the column
                try {
                    $t->dropForeign(['tenant_id']);
                } catch (\Throwable) {}
                $t->dropColumn('tenant_id');
            });
        }
    }

    public function down(): void {}
};
