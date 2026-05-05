<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('headers', function (Blueprint $table) {
            $table->id();
            $table->enum('layout', ['split', 'logo_left', 'logo_right'])->default('split');
            $table->string('logo_path')->nullable();
            $table->string('bg_color', 7)->default('#ffffff');
            $table->string('text_color', 7)->default('#000000');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('headers');
    }
};
