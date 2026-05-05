<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('theme')->default('sanzahra')->after('name');
            $table->boolean('ecommerce_enabled')->default(false)->after('theme');
            $table->enum('header_layout', ['center', 'left', 'right'])->default('center')->after('ecommerce_enabled');
            $table->string('logo_path')->nullable()->after('header_layout');
            $table->string('stripe_account_id')->nullable()->after('logo_path');
            $table->enum('payment_gateway', ['stripe_connect', 'redsys', 'paypal', 'none'])->default('none')->after('stripe_account_id');
            $table->string('plan')->default('basic')->after('payment_gateway');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['name','theme','ecommerce_enabled','header_layout','logo_path','stripe_account_id','payment_gateway','plan']);
        });
    }
};
