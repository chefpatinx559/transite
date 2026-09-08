<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // ── Catégories produits ────────────────────────────────────────────

        $cats = [
            ['name' => 'Électronique',        'slug' => 'electronique',       'color' => '#0066CC'],
            ['name' => 'Vêtements & Mode',    'slug' => 'vetements-mode',     'color' => '#7C3AED'],
            ['name' => 'Maison & Décoration', 'slug' => 'maison-decoration',  'color' => '#059669'],
            ['name' => 'Cosmétiques',         'slug' => 'cosmetiques',        'color' => '#EC4899'],
            ['name' => 'Matériaux',           'slug' => 'materiaux',          'color' => '#D97706'],
            ['name' => 'Autre',               'slug' => 'autre',              'color' => '#6B7280'],
            ['name' => 'Formation',           'slug' => 'formation',          'color' => '#7C3AED'],
        ];

        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug'], 'type' => 'produit'],
                array_merge($cat, ['type' => 'produit'])
            );
        }

        // ── Produits ───────────────────────────────────────────────────────

        $products = [
            // Électronique
            [
                'name' => 'Écouteurs Bluetooth Sans Fil',
                'description' => 'Écouteurs TWS avec réduction de bruit active, autonomie 24h, étanches IPX5. Son haute fidélité pour le quotidien.',
                'price' => 8500,
                'compare_price' => 12000,
                'stock' => 45,
                'type' => 'physical',
                'category' => 'electronique',
                'is_featured' => true,
                'sales_count' => 87,
            ],
            [
                'name' => 'Montre Connectée Sport',
                'description' => 'Smartwatch avec suivi GPS, cardiofréquencemètre, notifications WhatsApp. Compatible Android et iOS.',
                'price' => 22000,
                'compare_price' => 30000,
                'stock' => 20,
                'type' => 'physical',
                'category' => 'electronique',
                'is_featured' => false,
                'sales_count' => 43,
            ],
            [
                'name' => 'Câble de Charge Magnétique 3-en-1',
                'description' => 'Câble universel compatible USB-C, Micro-USB et Lightning. 1,2 m, charge rapide 60W.',
                'price' => 2500,
                'compare_price' => 3500,
                'stock' => 200,
                'type' => 'physical',
                'category' => 'electronique',
                'is_featured' => false,
                'sales_count' => 312,
            ],
            // Vêtements & Mode
            [
                'name' => 'Ensemble Blazer Femme – Taille unique',
                'description' => 'Blazer élégant et pantalon droit, tissu polyester de qualité, lavable en machine. Disponible en noir, blanc, camel.',
                'price' => 14500,
                'compare_price' => 18000,
                'stock' => 60,
                'type' => 'physical',
                'category' => 'vetements-mode',
                'is_featured' => true,
                'sales_count' => 56,
            ],
            [
                'name' => 'Baskets Fashion Légères',
                'description' => 'Sneakers unisexe en maille respirante, semelle antidérapante, tailles 38 à 45.',
                'price' => 9800,
                'compare_price' => 13000,
                'stock' => 80,
                'type' => 'physical',
                'category' => 'vetements-mode',
                'is_featured' => false,
                'sales_count' => 128,
            ],
            // Maison & Décoration
            [
                'name' => 'Lampe de Bureau LED Rechargeable',
                'description' => 'Lampe tactile 3 niveaux de luminosité, port USB intégré, idéale pour le bureau ou la chambre.',
                'price' => 6500,
                'compare_price' => 9000,
                'stock' => 35,
                'type' => 'physical',
                'category' => 'maison-decoration',
                'is_featured' => false,
                'sales_count' => 74,
            ],
            [
                'name' => 'Ensemble Coussins Décoratifs (Lot de 4)',
                'description' => 'Coussins en velours doux 45x45 cm, housse amovible et lavable. Motifs géométriques modernes.',
                'price' => 11000,
                'compare_price' => 15000,
                'stock' => 25,
                'type' => 'physical',
                'category' => 'maison-decoration',
                'is_featured' => true,
                'sales_count' => 39,
            ],
            // Cosmétiques
            [
                'name' => 'Huile de Soin Capillaire à l\'Argan',
                'description' => 'Huile végétale pure, hydratation intense, brillance et force pour tous types de cheveux. Flacon 100 ml.',
                'price' => 3500,
                'compare_price' => 5000,
                'stock' => 150,
                'type' => 'physical',
                'category' => 'cosmetiques',
                'is_featured' => false,
                'sales_count' => 203,
            ],
            [
                'name' => 'Crème Éclat Teint Unifié',
                'description' => 'Crème de jour nourrissante et unifiante, enrichie en vitamine C et en karité, SPF 15. 50 g.',
                'price' => 4800,
                'compare_price' => 6500,
                'stock' => 90,
                'type' => 'physical',
                'category' => 'cosmetiques',
                'is_featured' => true,
                'sales_count' => 167,
            ],
            // Matériaux
            [
                'name' => 'Carrelage Sol Marbre Blanc (m²)',
                'description' => 'Carrelage intérieur en grès émaillé effet marbre blanc, 60x60 cm. Vendu au m², pose facile.',
                'price' => 7500,
                'compare_price' => 10000,
                'stock' => 500,
                'type' => 'physical',
                'category' => 'materiaux',
                'is_featured' => false,
                'sales_count' => 28,
            ],
            [
                'name' => 'Peinture Façade Extérieure 20L',
                'description' => 'Peinture acrylique hydrofuge pour extérieur, haute résistance UV et intempéries. Couvre 100 m².',
                'price' => 28000,
                'compare_price' => 35000,
                'stock' => 40,
                'type' => 'physical',
                'category' => 'materiaux',
                'is_featured' => false,
                'sales_count' => 15,
            ],
            [
                'name' => 'Kit Outillage Électrique 12 pièces',
                'description' => 'Perceuse-visseuse sans fil + accessoires (12 mèches, coffret rigide). Batterie 18V lithium.',
                'price' => 85000,
                'compare_price' => null,
                'stock' => 10,
                'type' => 'physical',
                'category' => 'materiaux',
                'is_featured' => true,
                'sales_count' => 7,
            ],
        ];

        foreach ($products as $data) {
            $catSlug = $data['category'];
            unset($data['category']);

            $slug = Str::slug($data['name']);
            // Unicité du slug
            $suffix = 1;
            $baseSlug = $slug;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$suffix++;
            }

            Product::firstOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'slug' => $slug,
                    'is_published' => true,
                    'category_id' => $categories[$catSlug]->id,
                ])
            );
        }

        // ── Coupon ─────────────────────────────────────────────────────────

        Coupon::firstOrCreate(
            ['code' => 'NETSPRING10'],
            [
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order_amount' => 5000,
                'max_uses' => null,
                'used_count' => 0,
                'expires_at' => null,
                'is_active' => true,
            ]
        );
    }
}
