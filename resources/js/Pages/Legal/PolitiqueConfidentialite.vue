<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Politique de confidentialité — NETSPRING" />
    <AppLayout>

    <!-- Hero -->
    <section class="bg-[#0D0D0D] py-16" aria-labelledby="pc-heading">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-widest uppercase mb-3">Juridique</p>
            <h1 id="pc-heading" class="font-heading font-bold text-white text-3xl md:text-4xl">Politique de confidentialité</h1>
            <p class="text-gray-400 text-sm mt-3">Dernière mise à jour : septembre 2026</p>
        </div>
    </section>

    <!-- Contenu -->
    <div class="max-w-3xl mx-auto px-6 py-16 space-y-12">

        <section aria-labelledby="pc-intro">
            <h2 id="pc-intro" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">1. Introduction</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                NETSPRING accorde une importance primordiale à la protection de vos données personnelles. La présente politique de confidentialité explique quelles données nous collectons, comment nous les utilisons et vos droits à leur égard, conformément aux lois ivoiriennes en vigueur.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="pc-collecte">
            <h2 id="pc-collecte" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">2. Données collectées</h2>
            <p class="text-gray-600 text-sm leading-relaxed mb-4">Nous collectons les données suivantes :</p>
            <ul class="space-y-2 text-gray-600 text-sm leading-relaxed list-disc list-inside">
                <li><strong class="text-[#0D0D0D]">Données d'identification :</strong> nom, prénom, adresse email, numéro de téléphone/WhatsApp.</li>
                <li><strong class="text-[#0D0D0D]">Données de commande :</strong> adresse de livraison, contenu du panier, historique des achats.</li>
                <li><strong class="text-[#0D0D0D]">Données de navigation :</strong> adresse IP, type de navigateur, pages visitées (via cookies techniques).</li>
                <li><strong class="text-[#0D0D0D]">Messages :</strong> contenu des formulaires de contact et de demande de devis.</li>
            </ul>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="pc-utilisation">
            <h2 id="pc-utilisation" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">3. Utilisation des données</h2>
            <p class="text-gray-600 text-sm leading-relaxed mb-4">Vos données sont utilisées pour :</p>
            <ul class="space-y-2 text-gray-600 text-sm leading-relaxed list-disc list-inside">
                <li>Traiter et suivre vos commandes.</li>
                <li>Vous contacter concernant votre projet d'importation.</li>
                <li>Améliorer nos services et l'expérience utilisateur sur le site.</li>
                <li>Vous envoyer des informations commerciales si vous y avez consenti (newsletter).</li>
            </ul>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="pc-partage">
            <h2 id="pc-partage" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">4. Partage des données</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                NETSPRING ne vend, ne loue ni ne partage vos données personnelles avec des tiers à des fins commerciales. Vos données peuvent être transmises à des prestataires techniques (hébergement, paiement via GeniusPay) dans le strict cadre de la fourniture de nos services, sous couvert d'accords de confidentialité.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="pc-conservation">
            <h2 id="pc-conservation" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">5. Conservation des données</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Vos données sont conservées pour la durée nécessaire à la réalisation des finalités pour lesquelles elles ont été collectées. Les données de commande sont conservées pendant 5 ans à des fins comptables et légales. Les données de compte sont supprimées sur demande.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="pc-droits">
            <h2 id="pc-droits" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">6. Vos droits</h2>
            <p class="text-gray-600 text-sm leading-relaxed mb-4">Vous disposez des droits suivants :</p>
            <ul class="space-y-2 text-gray-600 text-sm leading-relaxed list-disc list-inside">
                <li><strong class="text-[#0D0D0D]">Droit d'accès :</strong> obtenir une copie de vos données personnelles.</li>
                <li><strong class="text-[#0D0D0D]">Droit de rectification :</strong> corriger des données inexactes ou incomplètes.</li>
                <li><strong class="text-[#0D0D0D]">Droit à l'effacement :</strong> demander la suppression de vos données.</li>
                <li><strong class="text-[#0D0D0D]">Droit d'opposition :</strong> vous opposer au traitement de vos données à des fins commerciales.</li>
            </ul>
            <p class="text-gray-600 text-sm leading-relaxed mt-4">
                Pour exercer ces droits, contactez-nous à : <a href="mailto:netspringbusiness@gmail.com" class="text-[#F4620A] hover:underline">netspringbusiness@gmail.com</a>
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="pc-securite">
            <h2 id="pc-securite" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">7. Sécurité des données</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                NETSPRING met en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données contre tout accès non autorisé, perte, destruction ou divulgation. Les paiements sont traités par GeniusPay, un prestataire sécurisé, et NETSPRING ne stocke aucune donnée bancaire.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="pc-contact">
            <h2 id="pc-contact" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">8. Contact</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Pour toute question relative à cette politique de confidentialité ou à vos données personnelles, vous pouvez nous contacter :
            </p>
            <div class="mt-4 space-y-1 text-gray-600 text-sm">
                <p><strong class="text-[#0D0D0D]">Email :</strong> <a href="mailto:netspringbusiness@gmail.com" class="text-[#F4620A] hover:underline">netspringbusiness@gmail.com</a></p>
                <p><strong class="text-[#0D0D0D]">WhatsApp :</strong> +225 0594429552</p>
            </div>
        </section>

    </div>

    </AppLayout>
</template>
