<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug', 191)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();

            $table->enum('type', ['online', 'presentielle'])->default('online');
            $table->boolean('is_free')->default(false);
            $table->decimal('price', 12, 2)->default(0);

            $table->string('duration')->nullable();       // ex: "2 jours", "4 heures"
            $table->string('instructor_name')->nullable();

            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();

            $table->string('location')->nullable();       // présentielle — lieu
            $table->string('platform_link')->nullable();  // online — lien Zoom/Meet

            $table->unsignedInteger('max_participants')->nullable(); // null = illimité
            $table->unsignedInteger('participants_count')->default(0);

            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('formation_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained()->cascadeOnDelete();

            $table->string('name', 200);
            $table->string('email', 191);
            $table->string('phone', 30)->nullable();
            $table->text('notes')->nullable();

            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            $table->string('payment_ref')->nullable();
            $table->string('token', 60)->unique();

            $table->timestamp('registered_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_registrations');
        Schema::dropIfExists('formations');
    }
};
