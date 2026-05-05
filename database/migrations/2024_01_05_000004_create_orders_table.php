<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'paid', 'shipped', 'cancelled', 'refunded'])->default('pending');
            $table->json('items');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('tax_amount');
            $table->unsignedInteger('total');
            $table->char('currency', 3)->default('EUR');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->json('shipping_address')->nullable();
            $table->enum('payment_gateway', ['stripe_connect', 'redsys', 'paypal', 'none'])->default('none');
            $table->string('payment_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('refund_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
