<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_id')->nullable()->after('id');
            $table->foreign('menu_id')->references('id')->on('menus')->nullOnDelete();
            $table->index('menu_id');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->dropIndex(['menu_id']);
            $table->dropColumn('menu_id');
        });
    }
};
