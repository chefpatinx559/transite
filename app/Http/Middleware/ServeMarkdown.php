<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServeMarkdown
{
    private const BASE = 'https://netspring.business';

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->wantsMarkdown($request)) {
            return $next($request);
        }

        $markdown = $this->renderMarkdown($request);

        if ($markdown === null) {
            return $next($request);
        }

        return response($markdown, 200, ['Content-Type' => 'text/markdown; charset=utf-8']);
    }

    private function wantsMarkdown(Request $request): bool
    {
        $accept = $request->header('Accept', '');
        return str_contains($accept, 'text/markdown');
    }

    private function renderMarkdown(Request $request): ?string
    {
        $path = rtrim($request->getPathInfo(), '/') ?: '/';

        return match ($path) {
            '/' => $this->home(),
            '/services' => $this->services(),
            '/a-propos' => $this->about(),
            '/contact' => $this->contact(),
            '/faq' => $this->faq(),
            '/boutique' => $this->boutique(),
            '/formations' => $this->formations(),
            '/blog' => $this->blog(),
            '/importer' => $this->importer(),
            default => null,
        };
    }

    private function home(): string
    {
        return <<<'MD'
# NETSPRING — Pont commercial Chine–Côte d'Ivoire

NETSPRING accompagne les entrepreneurs ivoiriens dans l'importation depuis la Chine : sourcing, négociation, transport, dédouanement. Plus de 500 clients accompagnés depuis 2022.

## Nos services

- **Recherche de fournisseurs** — Fournisseurs fiables et vérifiés en Chine
- **Achat & négociation** — Meilleurs prix et conditions sécurisées
- **Contrôle qualité** — Inspection rigoureuse avant expédition
- **Transport & logistique** — Maritime ou aérien jusqu'à destination
- **Dédouanement** — Formalités douanières en Côte d'Ivoire
- **Accompagnement personnalisé** — Un conseiller dédié à chaque étape

## Comment ça marche

1. Prenez contact (WhatsApp ou formulaire)
2. Analyse de votre besoin
3. Recherche des meilleures solutions
4. Validation de l'offre
5. Réception de vos marchandises en CI

## Pourquoi NETSPRING

- Expertise Chine–CI depuis 2022
- Sécurité des transactions
- Transparence totale
- Réponse sous 24h

## Liens

- [Services](/services)
- [Boutique](/boutique)
- [Formations](/formations)
- [Blog](/blog)
- [À propos](/a-propos)
- [Contact](/contact)
- [FAQ](/faq)
- [Demander un devis](/importer)
MD;
    }

    private function services(): string
    {
        return <<<'MD'
# Services Import Chine–Côte d'Ivoire — NETSPRING

NETSPRING gère votre importation de A à Z : recherche fournisseurs, négociation, contrôle qualité, transport maritime/aérien, dédouanement CI.

## Nos services

### Recherche de fournisseurs
Nous trouvons les meilleurs fournisseurs fiables et vérifiés en Chine pour vos produits.

### Achat & négociation
Négociation des meilleurs prix et conditions pour sécuriser vos achats.

### Contrôle qualité
Inspection et contrôle rigoureux de vos produits avant toute expédition.

### Transport & logistique
Organisation du transport maritime ou aérien jusqu'à votre destination.

### Dédouanement
Gestion complète des formalités douanières en Côte d'Ivoire.

### Accompagnement personnalisé
Un conseiller dédié vous guide à chaque étape de votre projet d'importation.

## Contact

- WhatsApp : +225 0712328489
- Email : netspringbusiness@gmail.com
MD;
    }

    private function about(): string
    {
        return <<<'MD'
# À propos de NETSPRING

NETSPRING est votre pont commercial entre la Chine et la Côte d'Ivoire depuis 2022. Plus de 500 clients et 1000+ commandes traitées.

## Notre mission

Simplifier l'accès aux opportunités commerciales entre la Chine et la Côte d'Ivoire pour les entrepreneurs africains.

## Localisation

Bouaké, Côte d'Ivoire

## Contact

- WhatsApp : +225 0712328489
- Email : netspringbusiness@gmail.com
- Site : https://netspring.business
MD;
    }

    private function contact(): string
    {
        return <<<'MD'
# Contact NETSPRING

Contactez NETSPRING pour vos projets d'importation Chine–Côte d'Ivoire. Réponse sous 24h.

## Coordonnées

- **WhatsApp** : +225 0712328489
- **Email** : netspringbusiness@gmail.com
- **Adresse** : Bouaké, Côte d'Ivoire
- **Horaires** : Lundi–Vendredi 08h00–18h00

## Formulaire

Rendez-vous sur [netspring.business/contact](https://netspring.business/contact) pour nous écrire directement.
MD;
    }

    private function faq(): string
    {
        return <<<'MD'
# FAQ — Import Chine–Côte d'Ivoire

Réponses aux questions fréquentes sur l'importation avec NETSPRING.

## Questions fréquentes

Consultez notre FAQ complète sur [netspring.business/faq](https://netspring.business/faq) pour les détails sur les délais, coûts, documents douaniers et paiements.
MD;
    }

    private function boutique(): string
    {
        $products = \App\Models\Product::where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['name', 'slug', 'short_description', 'price']);

        $lines = ["# Boutique NETSPRING — Produits importés de Chine\n"];
        $lines[] = "Produits importés directement de Chine, livrés en Côte d'Ivoire. Paiement sécurisé.\n";
        $lines[] = "## Produits\n";

        foreach ($products as $product) {
            $price = number_format($product->price, 0, ',', ' ');
            $lines[] = "- **[{$product->name}](/boutique/{$product->slug})** — {$price} FCFA";
            if ($product->short_description) {
                $lines[] = "  {$product->short_description}";
            }
        }

        return implode("\n", $lines);
    }

    private function formations(): string
    {
        $formations = \App\Models\Formation::where('is_published', true)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['title', 'slug', 'excerpt', 'price']);

        $lines = ["# Formations NETSPRING — Import Chine–CI\n"];
        $lines[] = "Formations pratiques pour apprendre à importer depuis la Chine.\n";
        $lines[] = "## Formations disponibles\n";

        foreach ($formations as $formation) {
            $price = number_format($formation->price, 0, ',', ' ');
            $lines[] = "- **[{$formation->title}](/formations/{$formation->slug})** — {$price} FCFA";
            if ($formation->excerpt) {
                $lines[] = "  {$formation->excerpt}";
            }
        }

        return implode("\n", $lines);
    }

    private function blog(): string
    {
        $posts = \App\Models\Post::published()
            ->orderByDesc('published_at')
            ->limit(20)
            ->get(['title', 'slug', 'reading_time', 'published_at']);

        $lines = ["# Blog NETSPRING — Import Chine–CI\n"];
        $lines[] = "Guides et conseils sur l'importation Chine–Côte d'Ivoire.\n";
        $lines[] = "## Articles récents\n";

        foreach ($posts as $post) {
            $date = $post->published_at?->format('d/m/Y') ?? '';
            $lines[] = "- **[{$post->title}](/blog/{$post->slug})** ({$date}, {$post->reading_time} min)";
        }

        return implode("\n", $lines);
    }

    private function importer(): string
    {
        return <<<'MD'
# Importer depuis la Chine vers la Côte d'Ivoire — NETSPRING

Guide d'importation Chine–CI. Simulateur de coûts, devis gratuit, accompagnement complet.

## Étapes d'importation

1. Définissez votre produit et vos quantités
2. NETSPRING recherche les meilleurs fournisseurs
3. Négociation et validation des conditions
4. Commande, production et contrôle qualité
5. Transport (maritime ou aérien)
6. Dédouanement et livraison en Côte d'Ivoire

## Demander un devis

Contactez-nous via [WhatsApp](https://wa.me/2250712328489) ou notre [formulaire de devis](/importer/devis).
MD;
    }
}
