<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        // ── FAQs ───────────────────────────────────────────────────────────

        $faqs = [
            [
                'question' => 'Comment fonctionne le service d\'importation de NETSPRING ?',
                'answer' => '<p>NETSPRING vous accompagne de A à Z dans votre projet d\'importation depuis la Chine. Notre équipe identifie les fournisseurs fiables selon votre cahier des charges, négocie les prix, contrôle la qualité avant expédition et gère toutes les formalités douanières jusqu\'à la livraison en Côte d\'Ivoire.</p><p>Il vous suffit de nous soumettre un devis en décrivant votre besoin, et nous vous rappelons sous 24h.</p>',
                'category' => 'importation',
                'order_index' => 1,
                'is_published' => true,
            ],
            [
                'question' => 'Quels modes de transport proposez-vous ?',
                'answer' => '<p>Nous proposons deux modes de transport :</p><ul><li><strong>Transport maritime</strong> : idéal pour les volumes importants (FCL ou LCL). Délai : 25 à 40 jours depuis la Chine. Coût très compétitif.</li><li><strong>Transport aérien</strong> : pour les envois urgents ou les produits de haute valeur. Délai : 5 à 10 jours.</li></ul><p>Nous vous conseillons le mode le plus adapté selon votre budget et votre délai.</p>',
                'category' => 'importation',
                'order_index' => 2,
                'is_published' => true,
            ],
            [
                'question' => 'Quels sont les délais habituels pour recevoir ma commande ?',
                'answer' => '<p>Les délais varient selon le mode de transport choisi :</p><ul><li><strong>Maritime</strong> : 30 à 45 jours au total (production + transport + dédouanement)</li><li><strong>Aérien</strong> : 10 à 15 jours au total</li></ul><p>Ces délais sont donnés à titre indicatif et peuvent varier selon la disponibilité du fournisseur et les formalités douanières.</p>',
                'category' => 'importation',
                'order_index' => 3,
                'is_published' => true,
            ],
            [
                'question' => 'Quels sont les moyens de paiement acceptés ?',
                'answer' => '<p>Pour les achats sur notre boutique en ligne, nous acceptons :</p><ul><li>CinetPay (cartes bancaires + Mobile Money)</li><li>Wave</li><li>Orange Money</li><li>Paiement manuel (virement / dépôt)</li></ul><p>Pour les services d\'importation, le règlement se fait par virement bancaire ou Mobile Money selon les modalités convenues avec votre chargé de compte.</p>',
                'category' => 'general',
                'order_index' => 4,
                'is_published' => true,
            ],
            [
                'question' => 'Quels documents sont nécessaires pour importer en Côte d\'Ivoire ?',
                'answer' => '<p>Les documents douaniers indispensables sont :</p><ul><li>Facture commerciale (Commercial Invoice)</li><li>Liste de colisage (Packing List)</li><li>Connaissement ou LTA</li><li>Certificat d\'origine</li><li>Déclaration d\'importation (DI)</li></ul><p>NETSPRING prend en charge la constitution et le suivi de votre dossier douanier. Des autorisations supplémentaires peuvent être requises selon la nature de la marchandise (alimentaire, pharmaceutique, électronique).</p>',
                'category' => 'importation',
                'order_index' => 5,
                'is_published' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }

        // ── Settings ───────────────────────────────────────────────────────

        $settings = [
            ['key' => 'site_name',       'value' => 'NETSPRING',                     'type' => 'text',    'group' => 'general'],
            ['key' => 'whatsapp_number', 'value' => '+225 07 07 07 07 07',           'type' => 'text',    'group' => 'contact'],
            ['key' => 'contact_email',   'value' => 'contact@netspring.ci',           'type' => 'text',    'group' => 'contact'],
            ['key' => 'address',         'value' => 'Abidjan, Cocody, Côte d\'Ivoire', 'type' => 'text',   'group' => 'contact'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
