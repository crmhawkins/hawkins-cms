<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('headers', function (Blueprint $table) {
            // Tipo de header
            $table->string('name')->default('Header principal')->after('id');
            $table->enum('type', ['classic','centered','split','minimal','mega'])->default('classic')->after('name');
            // Cambiar layout existente a nullable
            $table->string('layout')->default('logo_left')->change();
            // Logo
            $table->string('logo_text')->nullable()->after('logo_path');
            $table->integer('logo_height')->default(50)->after('logo_text');
            // Colores hover
            $table->string('hover_color', 7)->default('#c9a96e')->after('text_color');
            $table->string('active_color', 7)->default('#c9a96e')->after('hover_color');
            // Comportamiento
            $table->boolean('sticky')->default(false)->after('active_color');
            $table->boolean('transparent_on_top')->default(false)->after('sticky');
            // CTA button
            $table->string('cta_text')->nullable()->after('transparent_on_top');
            $table->string('cta_url')->nullable()->after('cta_text');
            $table->string('cta_bg_color', 7)->nullable()->after('cta_url');
            $table->string('cta_text_color', 7)->nullable()->after('cta_bg_color');
            // Contacto en header (tipo mega)
            $table->string('phone')->nullable()->after('cta_text_color');
            $table->string('email')->nullable()->after('phone');
            // Redes sociales
            $table->string('social_instagram')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_linkedin')->nullable();
            $table->string('social_youtube')->nullable();
            // Opciones visuales
            $table->boolean('show_search')->default(false);
            $table->boolean('show_social')->default(false);
            $table->boolean('is_default')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->dropColumn([
                'name','type','logo_text','logo_height','hover_color','active_color',
                'sticky','transparent_on_top','cta_text','cta_url','cta_bg_color','cta_text_color',
                'phone','email','social_instagram','social_facebook','social_twitter',
                'social_linkedin','social_youtube','show_search','show_social','is_default'
            ]);
        });
    }
};
