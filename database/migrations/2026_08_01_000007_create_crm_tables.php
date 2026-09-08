<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 200);
            $table->string('email', 191);
            $table->string('whatsapp', 25);
            $table->string('city', 100)->nullable();
            $table->text('product_description');
            $table->string('quantity', 100)->nullable();
            $table->string('budget', 100)->nullable();
            $table->string('source_country', 50)->default('Chine');
            $table->enum('experience_level', ['debutant', 'intermediaire', 'avance'])->nullable();
            $table->json('services')->nullable();
            $table->enum('status', ['new', 'in_review', 'quoted', 'won', 'lost'])->default('new')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->unique();
            $table->string('name', 100)->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->string('token')->nullable();
            $table->enum('source', ['footer', 'popup', 'blog', 'checkout'])->default('footer');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('email', 191);
            $table->string('phone', 25)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('quote_requests');
    }
};
