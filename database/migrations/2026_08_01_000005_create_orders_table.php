<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->default(0.00);
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 30)->unique();
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total', 12, 2);
            $table->string('currency', 5)->default('XOF');
            $table->enum('payment_method', ['cinetpay', 'wave', 'orange_money', 'manual'])->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'refunded'])->default('unpaid');
            $table->string('payment_ref')->nullable();
            $table->string('customer_name', 200);
            $table->string('customer_email', 191);
            $table->string('customer_phone', 25)->nullable();
            $table->json('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('item_type', ['product', 'course']);
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
    }
};
