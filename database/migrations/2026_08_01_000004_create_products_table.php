<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->string('sku', 100)->unique()->nullable();
            $table->integer('stock')->default(0);
            $table->enum('type', ['physical', 'digital', 'course'])->default('physical');
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('images')->nullable();
            $table->json('attributes')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sales_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
