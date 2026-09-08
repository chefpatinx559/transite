<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL : modifier l'ENUM pour ajouter super_admin
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','client','super_admin') NOT NULL DEFAULT 'client'");
        }
        // SQLite & PostgreSQL : la colonne est déjà un string, aucune modification nécessaire
        // La validation applicative gère les valeurs autorisées
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET role = 'admin' WHERE role = 'super_admin'");
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','client') NOT NULL DEFAULT 'client'");
        }
    }
};
