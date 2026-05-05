<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('title');
            $table->string('slug');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->json('seo_meta')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void { Schema::dropIfExists('pages'); }
};
