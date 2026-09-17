<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Catégories boutique
            ['name' => 'Électronique',        'slug' => 'electronique',       'type' => 'produit', 'color' => '#0066CC'],
            ['name' => 'Vêtements & Mode',    'slug' => 'vetements-mode',     'type' => 'produit', 'color' => '#7C3AED'],
            ['name' => 'Maison & Décoration', 'slug' => 'maison-decoration',  'type' => 'produit', 'color' => '#059669'],
            ['name' => 'Cosmétiques',         'slug' => 'cosmetiques',        'type' => 'produit', 'color' => '#EC4899'],
            ['name' => 'Matériaux',           'slug' => 'materiaux',          'type' => 'produit', 'color' => '#D97706'],
            ['name' => 'Autre',               'slug' => 'autre',              'type' => 'produit', 'color' => '#6B7280'],
            ['name' => 'Formation',           'slug' => 'formation',          'type' => 'produit', 'color' => '#7C3AED'],

            // Catégories blog
            ['name' => 'Achat en Chine', 'slug' => 'achat-en-chine', 'type' => 'blog', 'color' => '#F4620A'],
            ['name' => 'Importation',    'slug' => 'importation',    'type' => 'blog', 'color' => '#0066CC'],
            ['name' => 'Logistique',     'slug' => 'logistique',     'type' => 'blog', 'color' => '#059669'],
            ['name' => 'Conseils',       'slug' => 'conseils',       'type' => 'blog', 'color' => '#7C3AED'],
            ['name' => 'Commerce',       'slug' => 'commerce',       'type' => 'blog', 'color' => '#D97706'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug'], 'type' => $cat['type']],
                $cat
            );
        }
    }
}
