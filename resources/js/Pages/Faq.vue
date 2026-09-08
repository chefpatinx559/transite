<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { ChevronDown, ArrowRight, MessageSquare } from 'lucide-vue-next'

const props = defineProps({
    faqs: { type: Array, default: () => [] },
})

// FAQ statiques si pas de données BDD
const defaultFaqs = [
    { id: 1, category: 'importation', question: 'Comment fonctionne le service d\'importation de NETSPRING ?', answer: 'NETSPRING prend en charge l\'intégralité de votre projet : recherche de fournisseurs en Chine, négociation, achat, contrôle qualité, transport, dédouanement et livraison en Côte d\'Ivoire. Vous n\'avez qu\'à nous décrire votre besoin et nous gérons tout le reste.' },
    { id: 2, category: 'importation', question: 'Quels types de produits pouvez-vous importer ?', answer: 'Nous importons tous types de produits : textile et vêtements, électronique grand public, cosmétiques, matériaux de construction, équipements industriels, accessoires, jouets, et bien plus. Contactez-nous pour valider la faisabilité de votre projet.' },
    { id: 3, category: 'importation', question: 'Quel est le budget minimum pour commencer ?', answer: 'Il n\'y a pas de budget minimum fixe — cela dépend du type de produit et de la quantité souhaitée. En général, nous recommandons un budget minimum de 500 000 FCFA pour un premier projet afin d\'assurer une marge suffisante après les frais logistiques.' },
    { id: 4, category: 'transport',   question: 'Quels sont les délais d\'importation depuis la Chine ?', answer: 'Le transport maritime prend entre 25 et 40 jours depuis les principaux ports chinois jusqu\'à Abidjan. Le transport aérien prend 5 à 10 jours. À cela s\'ajoutent les délais de production chez le fournisseur (7 à 30 jours selon les produits).' },
    { id: 5, category: 'transport',   question: 'Quelle est la différence entre transport maritime et aérien ?', answer: 'Le maritime est 3 à 5 fois moins cher que l\'aérien, mais plus lent (25-40 jours). Il est idéal pour les gros volumes. L\'aérien (5-10 jours) est recommandé pour les marchandises urgentes, légères ou à haute valeur ajoutée. NETSPRING vous conseille la meilleure option selon votre produit.' },
    { id: 6, category: 'paiement',    question: 'Comment se passe le paiement aux fournisseurs chinois ?', answer: 'Le schéma standard est 30% d\'acompte à la commande et 70% avant expédition après validation de l\'inspection qualité. NETSPRING utilise exclusivement Trade Assurance d\'Alibaba ou des virements Swift sécurisés pour protéger vos fonds.' },
    { id: 7, category: 'documents',   question: 'Quels documents sont nécessaires pour importer en Côte d\'Ivoire ?', answer: 'Les documents obligatoires sont : la facture commerciale, la liste de colisage (Packing List), le connaissement (Bill of Lading) ou la LTA pour l\'aérien, le certificat d\'origine, et la Déclaration d\'Importation (DI) via le GUCE. NETSPRING prépare et gère l\'ensemble de ce dossier.' },
    { id: 8, category: 'documents',   question: 'Combien coûte le dédouanement en Côte d\'Ivoire ?', answer: 'Les droits et taxes à l\'importation en Côte d\'Ivoire varient selon la nomenclature tarifaire du produit. Ils incluent généralement les droits de douane (5 à 20% selon le produit), la TVA (18%), et divers prélèvements. NETSPRING calcule le coût total avant votre commande.' },
    { id: 9, category: 'general',     question: 'NETSPRING propose-t-il des formations à l\'importation ?', answer: 'Oui ! NETSPRING propose des formations vidéo complètes sur l\'importation depuis la Chine : recherche fournisseurs, négociation, logistique, dédouanement. Consultez notre page Formations pour découvrir le programme complet.' },
]

const displayedFaqs = props.faqs.length ? props.faqs : defaultFaqs

const categories = [
    { key: null,         label: 'Toutes' },
    { key: 'importation', label: 'Importation' },
    { key: 'transport',   label: 'Transport' },
    { key: 'paiement',    label: 'Paiement' },
    { key: 'documents',   label: 'Documents' },
    { key: 'general',     label: 'Général' },
]

const activeCategory = ref(null)
const openId = ref(null)

const filtered = computed(() => {
    if (!activeCategory.value) return displayedFaqs
    return displayedFaqs.filter(f => f.category === activeCategory.value)
})

function toggle(id) {
    openId.value = openId.value === id ? null : id
}
</script>

<template>
    <Head title="FAQ — Questions fréquentes — NETSPRING" />
    <AppLayout>

    <!-- HERO -->
    <section class="bg-[#0D0D0D] py-20" aria-labelledby="faq-heading">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">FAQ</p>
            <h1 id="faq-heading" class="font-heading font-bold text-white mb-4" style="font-size:clamp(2rem,4vw,3rem);">
                Questions fréquentes.
            </h1>
            <p class="text-gray-400 text-lg">Tout ce que vous devez savoir sur nos services d'importation.</p>
        </div>
    </section>

    <div class="bg-white py-16">
        <div class="max-w-3xl mx-auto px-6">

            <!-- Filtres catégories -->
            <div class="flex flex-wrap gap-2 mb-10" role="group" aria-label="Filtrer les questions par catégorie">
                <button
                    v-for="cat in categories"
                    :key="cat.key ?? 'all'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-[150ms] cursor-pointer"
                    :class="activeCategory === cat.key
                        ? 'bg-[#F4620A] text-white shadow-sm'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    :aria-pressed="activeCategory === cat.key"
                    @click="activeCategory = cat.key"
                >
                    {{ cat.label }}
                </button>
            </div>

            <!-- Accordéons -->
            <div class="space-y-3" role="list">
                <div
                    v-for="faq in (activeCategory ? displayedFaqs.filter(f => f.category === activeCategory) : displayedFaqs)"
                    :key="faq.id"
                    class="border border-[#E5E7EB] rounded-[14px] overflow-hidden transition-all duration-[220ms]"
                    :class="openId === faq.id ? 'border-[#F4620A]/30 shadow-[0_4px_16px_rgba(244,98,10,0.08)]' : 'hover:border-gray-300'"
                    role="listitem"
                >
                    <button
                        class="w-full flex items-start justify-between gap-4 p-5 text-left cursor-pointer"
                        :aria-expanded="openId === faq.id"
                        :aria-controls="`faq-answer-${faq.id}`"
                        @click="toggle(faq.id)"
                    >
                        <span class="font-heading font-semibold text-[#0D0D0D] text-sm leading-snug">{{ faq.question }}</span>
                        <ChevronDown
                            class="w-5 h-5 text-[#F4620A] flex-shrink-0 mt-0.5 transition-transform duration-[220ms]"
                            :class="openId === faq.id ? 'rotate-180' : ''"
                            aria-hidden="true"
                        />
                    </button>
                    <div
                        v-if="openId === faq.id"
                        :id="`faq-answer-${faq.id}`"
                        class="px-5 pb-5 text-gray-600 text-sm leading-[1.8] border-t border-[#E5E7EB] pt-4"
                        role="region"
                    >
                        {{ faq.answer }}
                    </div>
                </div>
            </div>

            <!-- CTA pas de réponse -->
            <div class="mt-14 p-8 bg-[#FAFAFA] rounded-[20px] text-center border border-[#E5E7EB]">
                <MessageSquare class="w-10 h-10 text-[#F4620A] mx-auto mb-4" aria-hidden="true" />
                <h2 class="font-heading font-bold text-[#0D0D0D] text-xl mb-2">Vous ne trouvez pas votre réponse ?</h2>
                <p class="text-gray-500 mb-6">Notre équipe répond à toutes vos questions par WhatsApp ou email.</p>
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="https://wa.me/2250594429552" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-6 py-3 rounded-[12px] transition-all duration-[220ms] cursor-pointer"
                       aria-label="Poser une question sur WhatsApp">
                        Poser une question
                        <ArrowRight class="w-4 h-4" aria-hidden="true" />
                    </a>
                    <Link href="/contact"
                          class="inline-flex items-center gap-2 border-2 border-[#0D0D0D] text-[#0D0D0D] hover:bg-[#0D0D0D] hover:text-white font-semibold px-6 py-3 rounded-[12px] transition-all duration-[220ms]">
                        Formulaire de contact
                    </Link>
                </div>
            </div>
        </div>
    </div>

    </AppLayout>
</template>
