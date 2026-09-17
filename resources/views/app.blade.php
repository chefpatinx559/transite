<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- SEO base --}}
        <meta name="description" content="NETSPRING — Votre pont commercial entre la Chine et la Côte d'Ivoire. Importation, sourcing, formations et boutique.">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ url()->current() }}">

        {{-- Open Graph (surcharge possible depuis chaque page via @inertiaHead) --}}
        <meta property="og:site_name" content="NETSPRING">
        <meta property="og:locale" content="fr_CI">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="NETSPRING — Votre pont commercial Chine – Côte d'Ivoire">
        <meta property="og:description" content="Importation, sourcing, formations et boutique. Plus de 500 entrepreneurs accompagnés depuis 2022.">
        {{-- TODO: Remplacer og-image.png par une vraie photo (équipe, locaux, produits) 1200x630px --}}
        <meta property="og:image" content="{{ asset('og-image.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="NETSPRING — Commerce Chine vers Côte d'Ivoire">

        {{-- Twitter / X --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="NETSPRING — Chine – Côte d'Ivoire">
        <meta name="twitter:description" content="Importation, sourcing, formations et boutique. Plus de 500 entrepreneurs accompagnés.">
        <meta name="twitter:image" content="{{ asset('og-image.png') }}">

        {{-- Favicons --}}
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">

        <title inertia>NETSPRING</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                "https://wa.me/2250594429552"
            ]
        }
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia

        <!--Start of Tawk.to Script-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/6a9be43d729e44344cceb056/1k1of74uc';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <!--End of Tawk.to Script-->
    </body>
</html>
