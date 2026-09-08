<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cinetpay','wave','orange_money','manual','geniuspay') NULL");
        }
        // SQLite/PostgreSQL : colonne string, aucune modification nécessaire
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE orders SET payment_method = 'manual' WHERE payment_method = 'geniuspay'");
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cinetpay','wave','orange_money','manual') NULL");
        }
    }
};
