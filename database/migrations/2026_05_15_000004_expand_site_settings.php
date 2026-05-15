<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $cols = Schema::getColumnListing('site_settings');
            if (!in_array('favicon_path', $cols))        $table->string('favicon_path')->nullable()->after('logo_path');
            if (!in_array('accent_color', $cols))        $table->string('accent_color', 7)->default('#c9a96e')->after('favicon_path');
            if (!in_array('font_heading', $cols))        $table->string('font_heading')->default('Cormorant Garamond')->after('accent_color');
            if (!in_array('font_body', $cols))           $table->string('font_body')->default('Montserrat')->after('font_heading');
            if (!in_array('google_analytics_code', $cols)) $table->text('google_analytics_code')->nullable()->after('font_body');
            if (!in_array('custom_head_code', $cols))   $table->text('custom_head_code')->nullable()->after('google_analytics_code');
            if (!in_array('custom_body_code', $cols))   $table->text('custom_body_code')->nullable()->after('custom_head_code');
            if (!in_array('social_instagram', $cols))   $table->string('social_instagram')->nullable();
            if (!in_array('social_facebook', $cols))    $table->string('social_facebook')->nullable();
            if (!in_array('social_twitter', $cols))     $table->string('social_twitter')->nullable();
            if (!in_array('social_linkedin', $cols))    $table->string('social_linkedin')->nullable();
            if (!in_array('social_youtube', $cols))     $table->string('social_youtube')->nullable();
            if (!in_array('default_header_id', $cols))  $table->unsignedBigInteger('default_header_id')->nullable();
            if (!in_array('default_footer_id', $cols))  $table->unsignedBigInteger('default_footer_id')->nullable();
            if (!in_array('maintenance_mode', $cols))   $table->boolean('maintenance_mode')->default(false);
            if (!in_array('maintenance_message', $cols)) $table->text('maintenance_message')->nullable();
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
