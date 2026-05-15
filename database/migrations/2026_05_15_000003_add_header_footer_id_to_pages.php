<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('header_id')->nullable()->constrained('headers')->nullOnDelete()->after('footer_variant');
            $table->foreignId('footer_id')->nullable()->constrained('footers')->nullOnDelete()->after('header_id');
            $table->text('custom_css')->nullable()->after('footer_id');
            $table->text('custom_js')->nullable()->after('custom_css');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['header_id']);
            $table->dropForeign(['footer_id']);
            $table->dropColumn(['header_id','footer_id','custom_css','custom_js']);
        });
    }
};
