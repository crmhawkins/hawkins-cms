<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('favicon_path')->nullable()->after('logo_path');
            $table->string('accent_color', 7)->default('#c9a96e')->after('favicon_path');
            $table->string('font_heading')->default('Cormorant Garamond')->after('accent_color');
            $table->string('font_body')->default('Montserrat')->after('font_heading');
            $table->text('google_analytics_code')->nullable()->after('font_body');
            $table->text('custom_head_code')->nullable()->after('google_analytics_code');
            $table->text('custom_body_code')->nullable()->after('custom_head_code');
            // Redes sociales globales
            $table->string('social_instagram')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_linkedin')->nullable();
            $table->string('social_youtube')->nullable();
            // Default header/footer
            $table->unsignedBigInteger('default_header_id')->nullable();
            $table->unsignedBigInteger('default_footer_id')->nullable();
            // Mantenimiento
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable()->default('Sitio en mantenimiento. Volvemos pronto.');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'favicon_path','accent_color','font_heading','font_body',
                'google_analytics_code','custom_head_code','custom_body_code',
                'social_instagram','social_facebook','social_twitter','social_linkedin','social_youtube',
                'default_header_id','default_footer_id','maintenance_mode','maintenance_message'
            ]);
        });
    }
};
