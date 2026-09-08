<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('reviewable');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->longText('answer');
            $table->enum('category', ['importation', 'formation', 'boutique', 'general'])->default('general');
            $table->unsignedTinyInteger('order_index')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->longText('value')->nullable();
            $table->enum('type', ['text', 'json', 'boolean', 'integer'])->default('text');
            $table->string('group', 50)->default('general');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('reviews');
    }
};
