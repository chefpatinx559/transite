<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cinetpay','wave','orange_money','manual','geniuspay') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cinetpay','wave','orange_money','manual') NULL");
    }
};
