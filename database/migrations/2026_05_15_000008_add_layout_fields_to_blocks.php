<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->string('bg_color')->nullable()->after('sort');
            $table->string('text_color')->nullable()->after('bg_color');
            $table->unsignedSmallInteger('padding_top')->default(0)->after('text_color');
            $table->unsignedSmallInteger('padding_bottom')->default(0)->after('padding_top');
            $table->unsignedSmallInteger('padding_x')->default(0)->after('padding_bottom');
            $table->unsignedSmallInteger('margin_top')->default(0)->after('padding_x');
            $table->unsignedSmallInteger('margin_bottom')->default(0)->after('margin_top');
            $table->enum('container_width', ['full','wide','normal','narrow'])->default('normal')->after('margin_bottom');
            $table->enum('separator_top', ['none','wave','diagonal','curve','triangle'])->default('none')->after('container_width');
            $table->enum('separator_bottom', ['none','wave','diagonal','curve','triangle'])->default('none')->after('separator_top');
            $table->string('separator_color')->nullable()->after('separator_bottom');
            $table->boolean('full_width')->default(false)->after('separator_color');
            $table->string('css_class')->nullable()->after('full_width');
            $table->text('custom_css')->nullable()->after('css_class');
        });
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropColumn([
                'bg_color','text_color','padding_top','padding_bottom','padding_x',
                'margin_top','margin_bottom','container_width',
                'separator_top','separator_bottom','separator_color',
                'full_width','css_class','custom_css',
            ]);
        });
    }
};
