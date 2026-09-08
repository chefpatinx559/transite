<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import {
    Search, Handshake, ShieldCheck, Package, FileText, Users,
    ArrowRight, CheckCircle2, ChevronRight,
    Clock, Globe, TrendingUp, Award,
} from 'lucide-vue-next'

const whatsappDevis = 'https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20demander%20un%20devis.'

const services = [
    {
        icon: Search,
        title: 'Recherche de fournisseurs',
        desc: 'Identification et vérification des meilleurs fournisseurs sur Alibaba, 1688 et Made-in-China selon vos critères (qualité, prix, MOQ).',
        features: ['Recherche sur 3 plateformes majeures', 'Vérification du profil Trade Assurance', 'Comparatif 5 fournisseurs minimum', 'Rapport détaillé livré sous 72h'],
    },
    {
        icon: Handshake,
        title: 'Achat & négociation',
        desc: 'Négociation des meilleurs tarifs, conditions de paiement et délais de livraison avec les fournisseurs en votre nom.',
        features: ['Négociation prix et conditions', 'Rédaction des contrats d\'achat', 'Gestion des paiements sécurisés', 'Suivi des commandes en temps réel'],
    },
    {
        icon: ShieldCheck,
        title: 'Contrôle qualité',
        desc: 'Inspection physique de vos marchandises avant expédition pour garantir la conformité avec vos spécifications.',
        features: ['Inspection avant production (PSI)', 'Contrôle pendant production (DUPRO)', 'Inspection finale avant expédition', 'Rapport photos + rapport écrit'],
    },
    {
        icon: Package,
        title: 'Transport & logistique',
        desc: 'Organisation du transport maritime ou aérien depuis la Chine jusqu\'à Abidjan, avec suivi en temps réel.',
        features: ['Transport maritime FCL & LCL', 'Transport aérien express', 'Suivi tracking en temps réel', 'Assurance marchandises incluse'],
    },
    {
        icon: FileText,
        title: 'Dédouanement',
        desc: 'Gestion complète des formalités douanières à l\'importation : déclaration, paiement des taxes, mainlevée.',
        features: ['Déclaration d\'importation (DI)', 'Paiement droits et taxes', 'Relation avec les services douaniers', 'Livraison locale en Côte d\'Ivoire'],
    },
    {
        icon: Users,
        title: 'Accompagnement personnalisé',
        desc: 'Un conseiller dédié vous guide à chaque étape, disponible par WhatsApp du lundi au vendredi.',
        features: ['Conseiller dédié tout au long du projet', 'Disponible WhatsApp Lun-Ven 8h-19h', 'Conseil sur la réglementation CI', 'Suivi après livraison'],
    },
]

const advantages = [
    { icon: Globe,     title: '+15 pays',        desc: 'Réseau de partenaires dans toute l\'Asie' },
    { icon: Clock,     title: '24h de réponse',  desc: 'Traitement rapide de chaque demande' },
    { icon: TrendingUp,title: '1000+ commandes', desc: 'Expérience acquise sur le terrain' },
    { icon: Award,     title: '4.9/5',           desc: 'Note de satisfaction clients' },
]

const steps = [
    { num: '01', title: 'Contact & brief',    desc: 'Décrivez votre besoin via WhatsApp ou notre formulaire.' },
    { num: '02', title: 'Sourcing',           desc: 'Nous cherchons et vérifions les meilleurs fournisseurs.' },
    { num: '03', title: 'Proposition',        desc: 'Vous recevez une offre détaillée avec prix et délais.' },
    { num: '04', title: 'Validation',         desc: 'Vous validez, nous commandons et suivons la production.' },
    { num: '05', title: 'Livraison',          desc: 'Transport, dédouanement, livraison à votre adresse en CI.' },
]
</script>

<template>
    <Head title="Nos Services — Importation Chine-CI" />
    <AppLayout>

    <!-- HERO -->
    <section class="bg-[#0D0D0D] py-20 relative overflow-hidden" aria-labelledby="services-heading">
        <div aria-hidden="true" class="absolute -top-20 right-1/4 w-96 h-96 bg-[#F4620A]/6 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">NOS SERVICES</p>
            <h1 id="services-heading" class="font-heading font-bold text-white mb-5" style="font-size:clamp(2rem,4vw,3rem);">
                Des solutions pour importer,<br>apprendre et développer.
            </h1>
            <p class="text-gray-400 text-lg max-w-xl mx-auto leading-[1.75] mb-8">
                De la recherche de fournisseurs à la livraison en Côte d'Ivoire, NETSPRING gère tout pour vous.
            </p>
            <a :href="whatsappDevis" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-7 py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.4)] hover:-translate-y-0.5 cursor-pointer"
               aria-label="Demander un devis sur WhatsApp">
                Demander un devis gratuit
                <ArrowRight class="w-5 h-5" aria-hidden="true" />
            </a>
        </div>
    </section>

    <!-- CHIFFRES CLÉS -->
    <div class="bg-[#F4620A] py-4">
        <div class="max-w-7xl mx-auto px-6">
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-white text-center">
                <div v-for="adv in advantages" :key="adv.title">
                    <dt class="sr-only">{{ adv.title }}</dt>
                    <dd>
                        <component :is="adv.icon" class="w-6 h-6 mx-auto mb-1 opacity-80" aria-hidden="true" />
                        <p class="font-heading font-bold text-xl">{{ adv.title }}</p>
                        <p class="text-orange-100 text-xs">{{ adv.desc }}</p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- SERVICES DÉTAILLÉS -->
    <section class="py-24 bg-white" aria-labelledby="services-detail-heading">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 id="services-detail-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    Tout ce dont vous avez besoin<br>pour importer en toute sérénité.
                </h2>
            </div>
            <div class="space-y-8">
                <article v-for="(service, i) in services" :key="service.title"
                    class="grid lg:grid-cols-[auto_1fr_1fr] gap-8 items-start p-8 rounded-[20px] border border-[#E5E7EB] hover:border-[#F4620A]/20 hover:shadow-[0_8px_32px_rgba(0,0,0,0.07)] transition-all duration-[220ms]"
                >
                    <div class="w-16 h-16 bg-orange-50 rounded-[16px] flex items-center justify-center flex-shrink-0">
                        <component :is="service.icon" class="w-7 h-7 text-[#F4620A]" aria-hidden="true" />
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-[#0D0D0D] text-xl mb-3">{{ service.title }}</h3>
                        <p class="text-gray-500 leading-[1.75] text-sm">{{ service.desc }}</p>
                    </div>
                    <ul class="space-y-2" role="list" :aria-label="`Inclus dans ${service.title}`">
                        <li v-for="feat in service.features" :key="feat" class="flex items-start gap-2 text-sm text-gray-600">
                            <CheckCircle2 class="w-4 h-4 text-[#F4620A] flex-shrink-0 mt-0.5" aria-hidden="true" />
                            {{ feat }}
                        </li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <!-- PROCESSUS -->
    <section class="py-24 bg-[#FAFAFA]" aria-labelledby="process-heading">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14">
                <h2 id="process-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    Comment ça marche ?
                </h2>
            </div>
            <ol class="grid md:grid-cols-5 gap-6" aria-label="Processus en 5 étapes">
                <li v-for="(step, i) in steps" :key="step.num" class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-[14px] flex items-center justify-center font-heading font-bold text-lg mb-4 shadow-[0_4px_16px_rgba(244,98,10,0.2)]"
                         :class="i < 3 ? 'bg-[#F4620A] text-white' : 'bg-white text-[#0D0D0D] border-2 border-[#E5E7EB]'">
                        {{ step.num }}
                    </div>
                    <h3 class="font-heading font-semibold text-[#0D0D0D] text-sm mb-1">{{ step.title }}</h3>
                    <p class="text-gray-500 text-xs leading-[1.7]">{{ step.desc }}</p>
                </li>
            </ol>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-[#F4620A]" aria-label="Démarrer votre projet">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="font-heading font-bold text-white text-3xl mb-4">Prêt à démarrer ?</h2>
            <p class="text-orange-100 text-lg mb-8">Décrivez votre projet et obtenez un devis personnalisé sous 24h.</p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a :href="whatsappDevis" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-white text-[#F4620A] font-bold px-7 py-3.5 rounded-[12px] hover:bg-orange-50 transition-all duration-[220ms] shadow-lg cursor-pointer">
                    Demander un devis
                    <ArrowRight class="w-5 h-5" aria-hidden="true" />
                </a>
                <Link href="/importer" class="inline-flex items-center gap-2 border-2 border-white text-white font-semibold px-7 py-3.5 rounded-[12px] hover:bg-white hover:text-[#F4620A] transition-all duration-[220ms]">
                    En savoir plus
                    <ChevronRight class="w-5 h-5" aria-hidden="true" />
                </Link>
            </div>
        </div>
    </section>

    </AppLayout>
</template>
