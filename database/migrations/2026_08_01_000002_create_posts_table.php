<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug', 191)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('cover_image')->nullable();
            $table->tinyInteger('reading_time')->unsigned()->default(5);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
