<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,  // Admins en premier (toujours exactement 2)
            BlogSeeder::class,
            ShopSeeder::class,
            CrmSeeder::class,
        ]);
    }
}
