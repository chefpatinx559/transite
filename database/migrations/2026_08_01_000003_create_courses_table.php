<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug', 191)->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('preview_video_url')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->smallInteger('total_videos')->unsigned()->default(0);
            $table->unsignedInteger('total_duration')->default(0);
            $table->enum('level', ['debutant', 'intermediaire', 'avance'])->default('debutant');
            $table->string('language', 10)->default('fr');
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('students_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->unsignedTinyInteger('order_index');
            $table->timestamps();
        });

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('course_sections')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->unsignedInteger('video_duration')->default(0);
            $table->unsignedSmallInteger('order_index');
            $table->boolean('is_free_preview')->default(false);
            $table->json('resources')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('courses');
    }
};
