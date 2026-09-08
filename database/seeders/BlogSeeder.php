<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@netspring.ci'],
            [
                'first_name' => 'Elysée',
                'last_name' => 'Ouattara',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Catégories blog
        $cats = [
            ['name' => 'Achat en Chine',   'slug' => 'achat-en-chine',   'color' => '#F4620A'],
            ['name' => 'Importation',       'slug' => 'importation',      'color' => '#0066CC'],
            ['name' => 'Logistique',        'slug' => 'logistique',       'color' => '#059669'],
            ['name' => 'Conseils',          'slug' => 'conseils',         'color' => '#7C3AED'],
            ['name' => 'Commerce',          'slug' => 'commerce',         'color' => '#D97706'],
        ];

        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug'], 'type' => 'blog'],
                array_merge($cat, ['type' => 'blog'])
            );
        }

        $articles = [
            [
                'slug' => 'comment-trouver-fournisseur-fiable-chine',
                'title' => 'Comment trouver un fournisseur fiable en Chine ?',
                'excerpt' => 'Découvrez les meilleures plateformes et stratégies pour identifier des fournisseurs sérieux sur Alibaba, Made-in-China et 1688.',
                'content' => '<p>Trouver un fournisseur fiable en Chine est l\'une des étapes les plus importantes de votre projet d\'importation. Que vous soyez débutant ou expérimenté, voici les méthodes éprouvées.</p><h2>1. Utiliser les plateformes reconnues</h2><p>Les plateformes comme <strong>Alibaba</strong>, <strong>Made-in-China</strong> et <strong>1688</strong> (version locale) sont les plus utilisées. Alibaba est recommandée pour les débutants car elle propose des fournisseurs vérifiés (Gold Supplier).</p><h2>2. Vérifier le profil du fournisseur</h2><ul><li>Ancienneté de l\'entreprise (minimum 3 ans)</li><li>Certification Trade Assurance d\'Alibaba</li><li>Nombre d\'évaluations et note moyenne</li><li>Réactivité aux messages</li></ul><h2>3. Demander un échantillon</h2><p>Avant toute commande en volume, exigez toujours un échantillon physique. Les frais d\'échantillon (généralement 20 à 100 $) sont un investissement indispensable.</p><h2>4. Faire appel à NETSPRING</h2><p>Notre équipe basée en Côte d\'Ivoire connaît le marché chinois et peut vous aider à identifier les meilleurs fournisseurs pour votre produit, négocier les prix et contrôler la qualité avant expédition.</p>',
                'reading_time' => 5,
                'category' => 'achat-en-chine',
                'published_at' => now()->subDays(3),
            ],
            [
                'slug' => 'documents-pour-importer-cote-ivoire',
                'title' => 'Liste complète des documents pour importer en Côte d\'Ivoire',
                'excerpt' => 'Facture commerciale, liste de colisage, connaissement, certificat d\'origine... voici tous les documents douaniers requis pour importer depuis la Chine.',
                'content' => '<p>L\'importation en Côte d\'Ivoire nécessite un ensemble de documents précis. Voici la liste exhaustive pour éviter tout blocage en douane.</p><h2>Documents obligatoires</h2><ul><li><strong>Facture commerciale</strong> — en double exemplaire, mentionnant valeur, quantité, description</li><li><strong>Liste de colisage (Packing List)</strong> — détail de chaque colis</li><li><strong>Connaissement (Bill of Lading)</strong> — pour le maritime, ou LTA pour l\'aérien</li><li><strong>Certificat d\'origine</strong> — Form A ou certificat de la chambre de commerce</li><li><strong>Déclaration d\'importation (DI)</strong> — à soumettre à la DGSIE</li></ul><h2>Documents spécifiques selon le produit</h2><p>Certaines marchandises nécessitent des autorisations supplémentaires : alimentaire (ANSSA), pharmaceutique (ONPPC), électronique (ARTCI).</p><h2>Conseil NETSPRING</h2><p>Notre équipe gère l\'ensemble de votre dossier douanier. Contactez-nous pour un accompagnement personnalisé.</p>',
                'reading_time' => 7,
                'category' => 'importation',
                'published_at' => now()->subDays(5),
            ],
            [
                'slug' => '5-erreurs-premiere-importation',
                'title' => '5 erreurs à éviter lors de votre première importation',
                'excerpt' => 'Payer sans Trade Assurance, négliger le contrôle qualité, sous-estimer les frais... ces erreurs coûtent cher. Découvrez comment les éviter.',
                'content' => '<p>La première importation est souvent la plus risquée. Voici les 5 erreurs les plus fréquentes et comment les éviter.</p><h2>Erreur #1 : Payer directement sans protection</h2><p>Ne jamais envoyer d\'argent via Western Union ou virement direct sans protection. Utilisez Trade Assurance sur Alibaba ou passez par NETSPRING pour sécuriser vos paiements.</p><h2>Erreur #2 : Ignorer le contrôle qualité</h2><p>Commandez toujours un échantillon et faites inspecter votre marchandise avant expédition. Une inspection coûte entre 200 et 400 $ — bien moins qu\'un conteneur de produits défectueux.</p><h2>Erreur #3 : Sous-estimer les coûts totaux</h2><p>Les frais de douane, de transit et de livraison locale peuvent représenter 30 à 40% du coût total. Calculez toujours le "landed cost" avant de commander.</p><h2>Erreur #4 : Mauvaise communication avec le fournisseur</h2><p>Soyez précis dans vos spécifications techniques. Utilisez des photos, des mesures exactes et des exemples de produits similaires.</p><h2>Erreur #5 : Choisir le transport sans comparer</h2><p>Le transport aérien est 3 à 5 fois plus cher que le maritime. Pour des volumes importants, le maritime reste la meilleure option malgré les délais plus longs.</p>',
                'reading_time' => 6,
                'category' => 'conseils',
                'published_at' => now()->subDays(8),
            ],
            [
                'slug' => 'transport-maritime-vs-aerien',
                'title' => 'Transport maritime vs aérien : lequel choisir pour votre importation ?',
                'excerpt' => 'Délais, coûts, contraintes de volume — comparatif complet pour choisir le bon mode de transport selon votre marchandise.',
                'content' => '<p>Le choix entre transport maritime et aérien dépend de plusieurs facteurs : urgence, volume, type de produit et budget. Voici notre analyse complète.</p><h2>Transport Maritime</h2><p><strong>Avantages :</strong> Coût 3 à 5 fois moins cher que l\'aérien, adapté aux gros volumes, FCL (conteneur entier) ou LCL (groupage).<br><strong>Inconvénients :</strong> Délai de 25 à 40 jours depuis la Chine vers Abidjan, plus de formalités.</p><h2>Transport Aérien</h2><p><strong>Avantages :</strong> Délai de 5 à 10 jours, idéal pour les produits à haute valeur ajoutée ou urgents.<br><strong>Inconvénients :</strong> Coût élevé, limites de poids et de volume.</p><h2>Notre recommandation</h2><p>Pour des produits standards (vêtements, accessoires, électronique) en volume, privilégiez le maritime. Pour des pièces détachées urgentes ou des produits périssables, optez pour l\'aérien.</p><p>NETSPRING négocie les meilleurs tarifs avec ses partenaires logistiques pour vous offrir le meilleur rapport qualité/prix.</p>',
                'reading_time' => 4,
                'category' => 'logistique',
                'published_at' => now()->subDays(10),
            ],
            [
                'slug' => 'guide-alibaba-debutant',
                'title' => 'Guide complet Alibaba pour débutants africains',
                'excerpt' => 'Comment naviguer sur Alibaba, lire les profils fournisseurs, utiliser Trade Assurance et passer sa première commande en toute sécurité.',
                'content' => '<p>Alibaba est la plus grande plateforme B2B mondiale, avec plus de 150 000 fournisseurs chinois. Ce guide vous aidera à faire vos premiers pas.</p><h2>Créer votre compte</h2><p>Inscrivez-vous sur alibaba.com avec votre email professionnel. Complétez votre profil acheteur pour être pris au sérieux par les fournisseurs.</p><h2>Recherche efficace</h2><p>Utilisez des mots-clés précis en anglais. Filtrez par "Trade Assurance", "Verified Supplier" et notes minimum 4.5/5.</p><h2>Contacter les fournisseurs</h2><p>Envoyez un message structuré : présentez votre entreprise, spécifiez votre besoin, demandez le MOQ (minimum order quantity) et le prix unitaire pour différentes quantités.</p><h2>Trade Assurance</h2><p>Payez toujours via Trade Assurance — Alibaba protège votre paiement jusqu\'à réception et validation de la marchandise conforme.</p>',
                'reading_time' => 8,
                'category' => 'achat-en-chine',
                'published_at' => now()->subDays(14),
            ],
            [
                'slug' => 'negociation-fournisseur-chinois',
                'title' => 'Comment négocier efficacement avec un fournisseur chinois',
                'excerpt' => 'Techniques de négociation culturelles, prix, délais, conditions de paiement — tout ce qu\'il faut savoir pour obtenir le meilleur deal.',
                'content' => '<p>La négociation avec les fournisseurs chinois obéit à des règles culturelles spécifiques. Maîtriser ces codes peut faire économiser 15 à 30% sur vos achats.</p><h2>Comprendre la culture de négociation</h2><p>Les Chinois apprécient les relations de long terme ("guanxi"). Ne vous précipitez pas — prenez le temps de construire une relation de confiance.</p><h2>Négocier le prix</h2><p>Demandez toujours un prix pour plusieurs quantités : 100 unités, 500, 1000. La dégressivité de volume peut être significative. Comparez au moins 5 fournisseurs différents.</p><h2>Les conditions de paiement</h2><p>Le schéma standard est 30% d\'acompte à la commande, 70% avant expédition. Pour les fournisseurs de confiance après plusieurs commandes, vous pouvez négocier des conditions plus favorables.</p><h2>La barrière de la langue</h2><p>L\'anglais basique suffit pour communiquer. NETSPRING dispose d\'une équipe bilingue français/chinois pour vous assister dans vos négociations.</p>',
                'reading_time' => 6,
                'category' => 'achat-en-chine',
                'published_at' => now()->subDays(18),
            ],
            [
                'slug' => 'calcul-rentabilite-importation',
                'title' => 'Comment calculer la rentabilité de votre projet d\'importation',
                'excerpt' => 'Landed cost, marge brute, seuil de rentabilité — apprenez à calculer si votre projet d\'importation est viable avant d\'investir.',
                'content' => '<p>Avant d\'importer, il faut impérativement calculer le "landed cost" — le coût total de votre marchandise une fois livrée en Côte d\'Ivoire.</p><h2>Les composantes du landed cost</h2><ul><li>Prix d\'achat fournisseur (FOB ou EXW)</li><li>Frais de transport international</li><li>Assurance maritime (0,5 à 1% de la valeur)</li><li>Droits de douane (selon nomenclature tarifaire)</li><li>TVA à l\'importation (18% en CI)</li><li>Frais de transit et livraison locale</li></ul><h2>Exemple pratique</h2><p>Produit à 5 USD/pièce, 500 unités, transport maritime : le landed cost peut atteindre 9 à 11 USD/pièce. Pour une rentabilité, votre prix de vente doit être supérieur à 15 USD minimum.</p><h2>Outil de calcul NETSPRING</h2><p>Contactez-nous pour obtenir un devis précis et un calcul de rentabilité personnalisé pour votre projet.</p>',
                'reading_time' => 7,
                'category' => 'commerce',
                'published_at' => now()->subDays(22),
            ],
            [
                'slug' => 'dossier-dedouanement-abidjan',
                'title' => 'Dédouanement à Abidjan : tout ce qu\'il faut savoir',
                'excerpt' => 'Port d\'Abidjan, procédures GUCE, régimes douaniers, délais et coûts — le guide complet pour réceptionner votre marchandise sans accroc.',
                'content' => '<p>Le port d\'Abidjan est le principal point d\'entrée des marchandises importées en Côte d\'Ivoire. Voici tout ce qu\'il faut savoir pour un dédouanement rapide.</p><h2>Le GUCE (Guichet Unique)</h2><p>Depuis 2014, toutes les procédures se font via le GUCE — le portail électronique qui centralise les démarches douanières. Vous devez être enregistré comme importateur.</p><h2>La procédure en 5 étapes</h2><ol><li>Obtention du Bon à Délivrer (BL)</li><li>Dépôt de la Déclaration d\'Importation (DI)</li><li>Liquidation et paiement des droits et taxes</li><li>Inspection physique (si sélectionné)</li><li>Mainlevée et enlèvement</li></ol><h2>Délais habituels</h2><p>Avec tous les documents en ordre : 3 à 7 jours ouvrables. Sans accompagnement spécialisé, les délais peuvent s\'allonger significativement.</p>',
                'reading_time' => 8,
                'category' => 'importation',
                'published_at' => now()->subDays(28),
            ],
            [
                'slug' => 'produits-rentables-importer-chine',
                'title' => 'Les 10 produits les plus rentables à importer de Chine en CI',
                'excerpt' => 'Vêtements, électronique, cosmétiques, matériaux de construction... analyse des niches les plus porteuses pour les importateurs ivoiriens.',
                'content' => '<p>Certaines catégories de produits offrent des marges particulièrement attractives pour les importateurs en Côte d\'Ivoire. Voici notre sélection.</p><h2>1. Vêtements et textile</h2><p>Marge typique : 200 à 400%. Forte demande locale, rotation rapide des stocks.</p><h2>2. Électronique grand public</h2><p>Smartphones reconditionnés, accessoires, câbles. Marché en forte croissance.</p><h2>3. Cosmétiques et soins capillaires</h2><p>Segment premium en plein essor, avec une clientèle sensible aux marques.</p><h2>4. Matériaux de construction</h2><p>Carrelage, luminaires, sanitaires. Fort potentiel B2B avec les promoteurs immobiliers.</p><h2>5. Articles de cuisine</h2><p>Ustensiles, électroménager petit format. Bons volumes, peu de concurrence formelle.</p><h2>Notre accompagnement</h2><p>NETSPRING analyse votre marché cible et vous aide à trouver les fournisseurs les plus adaptés pour chaque niche.</p>',
                'reading_time' => 9,
                'category' => 'commerce',
                'published_at' => now()->subDays(35),
            ],
        ];

        foreach ($articles as $data) {
            $cat = $categories[$data['category']];
            Post::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'user_id' => $admin->id,
                    'category_id' => $cat->id,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'reading_time' => $data['reading_time'],
                    'status' => 'published',
                    'published_at' => $data['published_at'],
                    'views_count' => rand(120, 2400),
                ]
            );
        }
    }
}
