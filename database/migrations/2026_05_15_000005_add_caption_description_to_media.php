<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $cols = Schema::getColumnListing('media');
            if (!in_array('caption', $cols))     $table->string('caption')->nullable()->after('alt');
            if (!in_array('description', $cols)) $table->text('description')->nullable()->after('caption');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['caption', 'description']);
        });
    }
};
