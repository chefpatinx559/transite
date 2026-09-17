<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,   // Admins en premier (toujours exactement 2)
            CategorySeeder::class, // Catégories de référence (blog + produit)
            BlogSeeder::class,
            ShopSeeder::class,
            CrmSeeder::class,
        ]);
    }
}
