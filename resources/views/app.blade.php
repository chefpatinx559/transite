<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Google Tag Manager --}}
        <script async src="https://www.googletagmanager.com/gtm.js?id=GTM-5TXQTH8M"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        </script>

        @php
            $seo = \App\Services\SeoService::forPage($page);
        @endphp

        @if($seo['noindex'])
        <meta name="robots" content="noindex, nofollow">
        @else
        <meta name="robots" content="index, follow">
        <meta name="description" content="{{ $seo['description'] }}">
        <link rel="canonical" href="{{ $seo['canonical'] }}">
        @endif

        {{-- OG / Twitter — dynamiques par page --}}
        <meta property="og:site_name" content="NETSPRING">
        <meta property="og:locale" content="fr_CI">
        @unless($seo['noindex'])
        <meta property="og:type" content="{{ $seo['og_type'] }}">
        <meta property="og:title" content="{{ $seo['og_title'] }}">
        <meta property="og:description" content="{{ $seo['og_description'] }}">
        <meta property="og:url" content="{{ $seo['og_url'] }}">
        <meta property="og:image" content="{{ $seo['og_image'] }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="NETSPRING — Commerce Chine vers Côte d'Ivoire">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo['og_title'] }}">
        <meta name="twitter:description" content="{{ $seo['og_description'] }}">
        <meta name="twitter:image" content="{{ $seo['og_image'] }}">
        @endunless

        {{-- Agent-discoverable about / contact / legal links --}}
        <link rel="about" href="https://netspring.business/a-propos">
        <link rel="contact" href="https://netspring.business/contact">
        <link rel="terms-of-service" href="https://netspring.business/mentions-legales">
        <link rel="privacy-policy" href="https://netspring.business/politique-de-confidentialite">

        {{-- Favicons --}}
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <title inertia>{{ $seo['title'] }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"></noscript>

        @vite(['resources/css/app.css', 'resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        {{-- Schema.org LocalBusiness --}}
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "LocalBusiness",
            "name": "NETSPRING",
            "description": "Pont commercial entre la Chine et la Côte d'Ivoire. Importation, sourcing, contrôle qualité, transport et formations.",
            "url": "https://netspring.business",
            "logo": "{{ asset('android-chrome-512x512.png') }}",
            "image": "{{ asset('og-image.png') }}",
            "telephone": "+2250594429552",
            "email": "contact@netspring.business",
            "priceRange": "$$",
            "currenciesAccepted": "XOF",
            "address": {
                "@@type": "PostalAddress",
                "streetAddress": "Bouaké",
                "addressLocality": "Bouaké",
                "addressCountry": "CI"
            },
            "areaServed": [
                { "@@type": "Country", "name": "Côte d'Ivoire" },
                { "@@type": "Country", "name": "China" }
            ],
            "openingHoursSpecification": {
                "@@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
                "opens": "08:00",
                "closes": "18:00"
            },
            "sameAs": [
                "https://wa.me/2250712328489",
                "https://www.facebook.com/profile.php?id=61574430440509",
                "https://www.tiktok.com/@@netspringbusiness"
            ]
        }
        </script>

        {{-- Schema.org WebSite with SearchAction --}}
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebSite",
            "name": "NETSPRING",
            "url": "https://netspring.business",
            "description": "Plateforme d'importation Chine–Côte d'Ivoire : sourcing, transport, formations et boutique en ligne.",
            "inLanguage": "fr",
            "potentialAction": {
                "@@type": "SearchAction",
                "target": "https://netspring.business/boutique?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
        </script>

        {{-- Schema.org Service --}}
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Service",
            "serviceType": "Import-export facilitation",
            "name": "Importation Chine–Côte d'Ivoire",
            "description": "Accompagnement complet pour l'importation depuis la Chine : recherche de fournisseurs, négociation, contrôle qualité, transport maritime/aérien et dédouanement en Côte d'Ivoire.",
            "provider": {
                "@@type": "LocalBusiness",
                "name": "NETSPRING",
                "url": "https://netspring.business"
            },
            "areaServed": [
                { "@@type": "Country", "name": "Côte d'Ivoire" },
                { "@@type": "Country", "name": "China" }
            ],
            "url": "https://netspring.business/services"
        }
        </script>
    </head>
    <body class="font-sans antialiased">
        @unless($seo['noindex'])
        <h1 id="ssr-heading" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap">{{ $seo['title'] }}</h1>
        @endunless
        @inertia

        {{-- Server-rendered nav for non-JS agents and crawlers --}}
        <nav aria-label="Pages principales" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap">
            <a href="https://netspring.business/a-propos">À propos</a>
            <a href="https://netspring.business/contact">Contact</a>
            <a href="https://netspring.business/mentions-legales">Mentions légales</a>
            <a href="https://netspring.business/politique-de-confidentialite">Politique de confidentialité</a>
            <a href="https://netspring.business/services">Services</a>
            <a href="https://netspring.business/boutique">Boutique</a>
            <a href="https://netspring.business/formations">Formations</a>
            <a href="https://netspring.business/blog">Blog</a>
            <a href="https://netspring.business/faq">FAQ</a>
        </nav>

        <!--Start of Tawk.to Script (deferred until user interaction / idle to prevent main-thread blocking)-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var loaded = false;
            function loadTawk() {
                if (loaded) return;
                loaded = true;
                ['scroll', 'mousemove', 'touchstart', 'keydown', 'click'].forEach(function(evt) {
                    window.removeEventListener(evt, loadTawk, { passive: true });
                });
                var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                s1.async=true;
                s1.src='https://embed.tawk.to/6a9be43d729e44344cceb056/1k1of74uc';
                s1.charset='UTF-8';
                s1.setAttribute('crossorigin','*');
                s0.parentNode.insertBefore(s1,s0);
            }
            ['scroll', 'mousemove', 'touchstart', 'keydown', 'click'].forEach(function(evt) {
                window.addEventListener(evt, loadTawk, { once: true, passive: true });
            });
            window.addEventListener('load', function() {
                setTimeout(loadTawk, 5000);
            }, { once: true });
        })();
        </script>
        <!--End of Tawk.to Script-->
    </body>
</html>
