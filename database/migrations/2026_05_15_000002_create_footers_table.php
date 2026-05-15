<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('footers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Footer principal');
            $table->enum('type', ['classic','centered','dark','minimal','mega'])->default('classic');
            // Colores
            $table->string('bg_color', 7)->default('#111111');
            $table->string('text_color', 7)->default('#ffffff');
            $table->string('link_color', 7)->default('#c9a96e');
            $table->string('border_color', 7)->default('#333333');
            // Logo y branding
            $table->string('logo_path')->nullable();
            $table->string('logo_text')->nullable();
            $table->text('tagline')->nullable();
            // Contacto
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            // Redes sociales
            $table->string('social_instagram')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_linkedin')->nullable();
            $table->string('social_youtube')->nullable();
            // Copyright
            $table->string('copyright_text')->nullable();
            // Newsletter
            $table->boolean('show_newsletter')->default(false);
            $table->string('newsletter_title')->nullable();
            $table->string('newsletter_placeholder')->nullable()->default('Tu email');
            // Columnas de menú (JSON: [{title, menu_location}])
            $table->json('menu_columns')->nullable();
            // Config
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footers');
    }
};
