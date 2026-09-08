<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Mentions légales — NETSPRING" />
    <AppLayout>

    <!-- Hero -->
    <section class="bg-[#0D0D0D] py-16" aria-labelledby="ml-heading">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-widest uppercase mb-3">Juridique</p>
            <h1 id="ml-heading" class="font-heading font-bold text-white text-3xl md:text-4xl">Mentions légales</h1>
            <p class="text-gray-400 text-sm mt-3">Dernière mise à jour : septembre 2026</p>
        </div>
    </section>

    <!-- Contenu -->
    <div class="max-w-3xl mx-auto px-6 py-16 space-y-12">

        <section aria-labelledby="ml-editeur">
            <h2 id="ml-editeur" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">1. Éditeur du site</h2>
            <div class="space-y-2 text-gray-600 text-sm leading-relaxed">
                <p><strong class="text-[#0D0D0D]">Raison sociale :</strong> NETSPRING</p>
                <p><strong class="text-[#0D0D0D]">Forme juridique :</strong> Entreprise individuelle</p>
                <p><strong class="text-[#0D0D0D]">Siège social :</strong> Abidjan, Côte d'Ivoire</p>
                <p><strong class="text-[#0D0D0D]">Directeur de la publication :</strong> Elysée Ouattara</p>
                <p><strong class="text-[#0D0D0D]">Email :</strong> <a href="mailto:netspringbusiness@gmail.com" class="text-[#F4620A] hover:underline">netspringbusiness@gmail.com</a></p>
                <p><strong class="text-[#0D0D0D]">WhatsApp :</strong> +225 0594429552</p>
            </div>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="ml-hebergement">
            <h2 id="ml-hebergement" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">2. Hébergement</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Ce site est hébergé sur un serveur VPS. Les informations relatives à l'hébergeur sont disponibles sur demande à l'adresse email mentionnée ci-dessus.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="ml-propriete">
            <h2 id="ml-propriete" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">3. Propriété intellectuelle</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                L'ensemble des contenus publiés sur ce site (textes, images, logos, graphismes, vidéos) est la propriété exclusive de NETSPRING ou de ses partenaires et est protégé par les lois ivoiriennes et internationales relatives à la propriété intellectuelle. Toute reproduction, représentation, modification ou adaptation, totale ou partielle, est strictement interdite sans l'accord écrit préalable de NETSPRING.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="ml-responsabilite">
            <h2 id="ml-responsabilite" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">4. Limitation de responsabilité</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                NETSPRING s'efforce d'assurer l'exactitude et la mise à jour des informations diffusées sur ce site. Cependant, NETSPRING ne peut garantir l'exhaustivité ou l'absence d'erreur des informations publiées et se réserve le droit de les modifier à tout moment sans préavis. NETSPRING ne saurait être tenu responsable de l'utilisation faite de ces informations.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="ml-liens">
            <h2 id="ml-liens" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">5. Liens hypertextes</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Ce site peut contenir des liens vers des sites tiers. NETSPRING n'exerce aucun contrôle sur ces sites et décline toute responsabilité quant à leur contenu. La présence de ces liens ne constitue pas une approbation de ces sites ou de leurs contenus.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="ml-cookies">
            <h2 id="ml-cookies" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">6. Cookies</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Ce site utilise des cookies à des fins techniques (gestion de session) et analytiques. En continuant à naviguer sur ce site, vous acceptez l'utilisation de ces cookies. Vous pouvez à tout moment désactiver les cookies dans les paramètres de votre navigateur.
            </p>
        </section>

        <div class="h-px bg-[#E5E7EB]"></div>

        <section aria-labelledby="ml-droit">
            <h2 id="ml-droit" class="font-heading font-bold text-[#0D0D0D] text-xl mb-4">7. Droit applicable</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Les présentes mentions légales sont soumises au droit ivoirien. Tout litige relatif à l'utilisation de ce site sera soumis à la compétence exclusive des tribunaux d'Abidjan, Côte d'Ivoire.
            </p>
        </section>

    </div>

    </AppLayout>
</template>
