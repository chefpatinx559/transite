<?php

namespace App\Services;

class SeoService
{
    private const BASE = 'https://netspring.business';
    private const DEFAULT_IMAGE = 'https://netspring.business/og-image.png';

    public static function forPage(array $page): array
    {
        $component = $page['component'] ?? '';
        $props = $page['props'] ?? [];

        return match ($component) {
            'Home' => self::home(),
            'Services' => self::services(),
            'About' => self::about(),
            'Contact' => self::contact(),
            'Faq' => self::faq(),
            'Importer' => self::importer(),
            'Boutique/Index' => self::boutiqueIndex(),
            'Boutique/Show' => self::boutiqueShow($props),
            'Blog/Index' => self::blogIndex(),
            'Blog/Show' => self::blogShow($props),
            'Formations/Index' => self::formationsIndex(),
            'Formations/Show' => self::formationsShow($props),
            'Legal/MentionsLegales' => self::meta(
                'Mentions légales — NETSPRING',
                'Mentions légales du site netspring.business.',
                self::BASE . '/mentions-legales',
            ),
            'Legal/PolitiqueConfidentialite' => self::meta(
                'Politique de confidentialité — NETSPRING',
                'Politique de confidentialité et protection des données personnelles — NETSPRING.',
                self::BASE . '/politique-de-confidentialite',
            ),
            'Auth/Login', 'Auth/Register', 'Panier', 'Checkout',
            'Formations/Confirmation' => self::noindex(),
            default => str_starts_with($component, 'Client/')
                ? self::noindex()
                : self::defaults(),
        };
    }

    private static function meta(
        string $title,
        string $description,
        string $canonical,
        ?string $ogTitle = null,
        ?string $ogDescription = null,
        string $ogType = 'website',
        ?string $ogImage = null,
    ): array {
        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $ogTitle ?? $title,
            'og_description' => $ogDescription ?? $description,
            'og_type' => $ogType,
            'og_url' => $canonical,
            'og_image' => $ogImage ?? self::DEFAULT_IMAGE,
            'noindex' => false,
        ];
    }

    private static function noindex(): array
    {
        return [
            'title' => 'NETSPRING',
            'description' => '',
            'canonical' => '',
            'og_title' => '',
            'og_description' => '',
            'og_type' => 'website',
            'og_url' => '',
            'og_image' => self::DEFAULT_IMAGE,
            'noindex' => true,
        ];
    }

    private static function defaults(): array
    {
        return self::meta(
            'NETSPRING — Commerce Chine–Côte d\'Ivoire',
            'NETSPRING, votre pont commercial entre la Chine et la Côte d\'Ivoire.',
            self::BASE,
        );
    }

    private static function home(): array
    {
        return self::meta(
            'NETSPRING — Pont commercial Chine–Côte d\'Ivoire',
            'NETSPRING accompagne les entrepreneurs ivoiriens dans l\'importation depuis la Chine : sourcing, négociation, transport, dédouanement. 500+ clients.',
            self::BASE . '/',
            'NETSPRING — Votre pont commercial Chine – Côte d\'Ivoire',
            'Importation, sourcing, formations et boutique. Plus de 20+ entrepreneurs accompagnés depuis 2022.',
        );
    }

    private static function services(): array
    {
        return self::meta(
            'Services Import Chine-CI — NETSPRING',
            'Services NETSPRING : recherche fournisseurs, négociation, contrôle qualité, transport maritime/aérien, dédouanement CI. Import clé en main.',
            self::BASE . '/services',
            'Nos Services — Importation Chine-CI | NETSPRING',
            'Sourcing, négociation, contrôle qualité, transport et dédouanement. NETSPRING gère votre importation de A à Z.',
        );
    }

    private static function about(): array
    {
        return self::meta(
            'À propos — NETSPRING | Chine–Côte d\'Ivoire',
            'NETSPRING, votre pont commercial Chine-CI depuis 2022. 500+ clients, 1000+ commandes. Découvrez notre équipe, mission et vision.',
            self::BASE . '/a-propos',
            'À propos — NETSPRING | Commerce Chine – Côte d\'Ivoire',
            'Depuis 2022, NETSPRING accompagne les entrepreneurs ivoiriens dans l\'importation depuis la Chine. Découvrez notre histoire.',
        );
    }

    private static function contact(): array
    {
        return self::meta(
            'Contact NETSPRING — Import Chine-CI',
            'Contactez NETSPRING pour vos projets import Chine-CI. WhatsApp, email, formulaire. Réponse sous 24h. Bouaké, Côte d\'Ivoire.',
            self::BASE . '/contact',
            'Contactez-nous — NETSPRING',
            'Besoin d\'aide pour importer depuis la Chine ? Contactez NETSPRING : WhatsApp, email ou formulaire. Réponse sous 24h.',
        );
    }

    private static function faq(): array
    {
        return self::meta(
            'FAQ Import Chine-CI — NETSPRING',
            'Réponses aux questions sur l\'importation Chine-CI : délais, coûts, documents douaniers, paiements, dédouanement. FAQ NETSPRING.',
            self::BASE . '/faq',
            'FAQ — Questions fréquentes | NETSPRING',
            'Réponses à vos questions sur l\'importation Chine–CI : délais, coûts, documents douaniers, paiements sécurisés.',
        );
    }

    private static function importer(): array
    {
        return self::meta(
            'Importer depuis la Chine vers CI — NETSPRING',
            'Guide import Chine-CI. Simulateur coûts, devis gratuit, accompagnement complet. NETSPRING gère fournisseurs, transport, dédouanement.',
            self::BASE . '/importer',
            'Importer depuis la Chine — NETSPRING',
            'Estimez vos coûts d\'importation et demandez un devis gratuit. NETSPRING gère tout : fournisseurs, transport, dédouanement.',
        );
    }

    private static function boutiqueIndex(): array
    {
        return self::meta(
            'Boutique NETSPRING — Produits Chine-CI',
            'Boutique NETSPRING : produits importés de Chine, livrés en Côte d\'Ivoire. Textile, électronique, accessoires. Paiement sécurisé.',
            self::BASE . '/boutique',
            'Boutique — Produits importés de Chine | NETSPRING',
            'Découvrez notre sélection de produits importés directement de Chine. Livraison en Côte d\'Ivoire, paiement sécurisé.',
        );
    }

    private static function boutiqueShow(array $props): array
    {
        $product = $props['product'] ?? [];
        $name = $product['name'] ?? 'Produit';
        $slug = $product['slug'] ?? '';
        $shortDesc = $product['short_description'] ?? '';
        $image = self::resolveImage($product['images'][0] ?? null);

        return self::meta(
            "{$name} — Boutique NETSPRING",
            $shortDesc ?: "Achetez {$name} sur NETSPRING — Livraison en Côte d'Ivoire depuis la Chine.",
            self::BASE . "/boutique/{$slug}",
            "{$name} — NETSPRING",
            $shortDesc ?: "{$name} disponible sur NETSPRING.",
            'product',
            $image,
        );
    }

    private static function blogIndex(): array
    {
        return self::meta(
            'Blog Import Chine-CI — NETSPRING',
            'Blog NETSPRING : guides et conseils sur l\'importation Chine-CI, commerce international, entrepreneuriat. Actualités et astuces pratiques.',
            self::BASE . '/blog',
            'Blog — Conseils Import Chine-CI | NETSPRING',
            'Articles et guides sur l\'importation depuis la Chine, le commerce international et l\'entrepreneuriat en Côte d\'Ivoire.',
        );
    }

    private static function blogShow(array $props): array
    {
        $post = $props['post'] ?? [];
        $title = $post['title'] ?? 'Article';
        $slug = $post['slug'] ?? '';
        $excerpt = $post['excerpt'] ?? '';
        $coverImage = $post['cover_image'] ?? null;
        $image = self::resolveCoverImage($coverImage);

        return self::meta(
            "{$title} — Blog NETSPRING",
            $excerpt ?: "Article du blog NETSPRING : {$title}",
            self::BASE . "/blog/{$slug}",
            "{$title} — Blog NETSPRING",
            $excerpt ?: $title,
            'article',
            $image,
        );
    }

    private static function formationsIndex(): array
    {
        return self::meta(
            'Formations Import Chine — NETSPRING CI',
            'Formations NETSPRING : import Chine, négociation fournisseurs, logistique internationale. En ligne et présentiel. Devenez expert.',
            self::BASE . '/formations',
            'Formations Import Chine — NETSPRING Côte d\'Ivoire',
            'Apprenez à importer depuis la Chine : e-commerce, sourcing, négociation, logistique. Formations en ligne et en présentiel.',
        );
    }

    private static function formationsShow(array $props): array
    {
        $formation = $props['formation'] ?? [];
        $title = $formation['title'] ?? 'Formation';
        $slug = $formation['slug'] ?? '';
        $excerpt = $formation['excerpt'] ?? '';
        $coverImage = $formation['cover_image'] ?? null;
        $image = self::resolveCoverImage($coverImage);

        return self::meta(
            "{$title} — Formations NETSPRING",
            $excerpt ?: "Inscrivez-vous à la formation {$title} — NETSPRING Côte d'Ivoire.",
            self::BASE . "/formations/{$slug}",
            "{$title} — NETSPRING",
            $excerpt ?: $title,
            'article',
            $image,
        );
    }

    private static function resolveImage(mixed $image): string
    {
        if (!$image) {
            return self::DEFAULT_IMAGE;
        }

        $url = is_array($image) ? ($image['url'] ?? null) : $image;

        if (!$url) {
            return self::DEFAULT_IMAGE;
        }

        return self::toAbsoluteUrl($url);
    }

    private static function resolveCoverImage(?string $path): string
    {
        if (!$path) {
            return self::DEFAULT_IMAGE;
        }

        return self::toAbsoluteUrl($path);
    }

    private static function toAbsoluteUrl(string $path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return self::BASE . '/' . $path;
        }

        return self::BASE . '/storage/' . $path;
    }
}
