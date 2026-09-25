<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { useReveal } from '@/composables/useReveal'
import SocialProof from '@/Components/SocialProof.vue'
import BlogPreview from '@/Components/BlogPreview.vue'
import GlobeAnimation from '@/Components/GlobeAnimation.vue'

useReveal()

const props = defineProps({
    latestPosts:  { type: Array, default: () => [] },
    testimonials: { type: Array, default: () => [] },
})

import {
    Globe, Ship, ArrowRight, ChevronRight,
    CheckCircle2, CircleCheck,
    Search, Handshake, ShieldCheck, Package, FileText, Users,
    Target, Shield, Eye, Zap,
    MapPin,
} from 'lucide-vue-next'

const whatsappDevis = 'https://wa.me/2250712328489?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20demander%20un%20devis%20pour%20mon%20projet%20d%27importation.'

// ── Services ──────────────────────────────────────────────────
const services = [
    { title: 'Recherche de fournisseurs',    desc: 'Nous trouvons pour vous les meilleurs fournisseurs fiables et vérifiés en Chine.',    icon: Search    },
    { title: 'Achat & négociation',          desc: 'Nous négocions les meilleurs prix et conditions pour sécuriser vos achats.',           icon: Handshake  },
    { title: 'Contrôle qualité',             desc: 'Inspection et contrôle rigoureux de vos produits avant toute expédition.',            icon: ShieldCheck },
    { title: 'Transport & logistique',       desc: 'Organisation du transport maritime ou aérien jusqu\'à votre destination.',            icon: Package    },
    { title: 'Dédouanement',                 desc: 'Gestion complète des formalités douanières en Côte d\'Ivoire pour vous.',             icon: FileText   },
    { title: 'Accompagnement personnalisé',  desc: 'Un conseiller dédié vous guide à chaque étape de votre projet d\'importation.',      icon: Users      },
]

// ── Steps ─────────────────────────────────────────────────────
const steps = [
    { num: '01', title: 'Prenez contact',           desc: 'Par WhatsApp ou via notre formulaire en ligne.' },
    { num: '02', title: 'Analyse de votre besoin',  desc: 'Nous comprenons votre projet et vos objectifs commerciaux.' },
    { num: '03', title: 'Meilleures solutions',     desc: 'Fournisseurs, prix, qualité, logistique — tout est vérifié.' },
    { num: '04', title: "Validation de l'offre",    desc: 'Vous validez, nous lançons la commande et gérons tout.' },
    { num: '05', title: 'Réception marchandises',   desc: 'Livraison sécurisée à votre destination en Côte d\'Ivoire.' },
]

// ── Avantages ─────────────────────────────────────────────────
const advantages = [
    { title: 'Expertise Chine – CI',      desc: 'Connaissance approfondie des marchés, réglementations et culture d\'affaires.', icon: Target  },
    { title: 'Sécurité des transactions', desc: 'Paiements sécurisés, fournisseurs vérifiés, contrats en ordre.',                 icon: Shield  },
    { title: 'Transparence totale',       desc: 'Suivi en temps réel de votre commande, du sourcing à la livraison.',            icon: Eye     },
    { title: 'Rapidité & réactivité',     desc: 'Réponse sous 24h, équipe disponible du lundi au vendredi.',                    icon: Zap     },
]

const importChecklist = [
    'Recherche de produits', 'Recherche de fournisseurs', 'Négociation des prix',
    'Achat & paiement', 'Transport maritime/aérien', 'Transit & dédouanement', 'Livraison en CI',
]
const formationChecklist = [
    'E-commerce & vente en ligne', 'Importation A à Z depuis la Chine',
    'Négociation avec les fournisseurs', 'Logistique internationale',
]
</script>

<template>
    <Head>
        <title>Votre pont commercial Chine – Côte d'Ivoire</title>
        <meta name="description" content="NETSPRING accompagne les entrepreneurs ivoiriens dans l'importation depuis la Chine : sourcing, négociation, transport, dédouanement et formations." />
        <link rel="canonical" href="https://netspring.business/" />
        <meta property="og:title" content="NETSPRING — Votre pont commercial Chine – Côte d'Ivoire" />
        <meta property="og:description" content="Importation, sourcing, formations et boutique. Plus de 500 entrepreneurs accompagnés depuis 2022." />
        <meta property="og:url" content="https://netspring.business/" />
        <meta name="twitter:title" content="NETSPRING — Votre pont commercial Chine – Côte d'Ivoire" />
        <meta name="twitter:description" content="Importation, sourcing, formations et boutique. Plus de 500 entrepreneurs accompagnés depuis 2022." />
    </Head>

    <AppLayout>

    <!-- ══════════════════════════════════════════════════
         HERO
    ══════════════════════════════════════════════════ -->
    <section aria-labelledby="hero-heading" class="relative bg-white overflow-hidden min-h-[calc(100svh-60px)] flex items-center">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none select-none">
            <div class="absolute top-0 right-0 w-[55%] h-full bg-gradient-to-l from-orange-50/70 to-transparent"></div>
            <div class="absolute -top-24 right-1/4 w-96 h-96 bg-orange-100/40 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-gray-50 rounded-full blur-3xl opacity-60"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 py-20 w-full grid lg:grid-cols-2 gap-14 items-center">

            <!-- Texte gauche -->
            <div>
                <p class="hero-anim-1 inline-flex items-center gap-2 text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-5 bg-orange-50 px-3.5 py-1.5 rounded-full">
                    <Globe class="w-3.5 h-3.5" aria-hidden="true" />
                    Commerce International · Importation · Formation
                </p>

                <h1
                    id="hero-heading"
                    class="hero-anim-2 font-heading font-bold text-[#0D0D0D] leading-[1.08] mb-6"
                    style="font-size: clamp(2.4rem, 5vw, 4rem);"
                >
                    Votre pont commercial<br>
                    <span class="text-[#F4620A]">Chine</span> –<br class="sm:hidden"> la Côte d'Ivoire.
                </h1>

                <p class="hero-anim-3 text-gray-600 leading-[1.75] mb-8 max-w-lg" style="font-size: clamp(1rem, 1.5vw, 1.125rem);">
                    De la recherche de fournisseurs à l'importation de vos marchandises,
                    NETSPRING vous accompagne pour transformer vos idées commerciales en
                    opportunités concrètes.
                </p>

                <div class="hero-anim-4 flex flex-col sm:flex-row gap-3 mb-10">
                    <a
                        :href="whatsappDevis" target="_blank" rel="noopener"
                        class="justify-center inline-flex items-center gap-2.5 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-7 py-4 rounded-[12px] text-base transition-all duration-[220ms] shadow-[0_4px_20px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_28px_rgba(244,98,10,0.45)] hover:-translate-y-0.5 active:translate-y-0 active:shadow-none cursor-pointer"
                        aria-label="Demander un devis — ouvre WhatsApp"
                    >
                        DEMANDER UN DEVIS
                        <ArrowRight class="w-5 h-5" aria-hidden="true" />
                    </a>
                    <Link
                        href="/services"
                        class="justify-center inline-flex items-center gap-2.5 border-2 border-[#0D0D0D] text-[#0D0D0D] hover:bg-[#0D0D0D] hover:text-white font-semibold px-7 py-4 rounded-[12px] text-base transition-all duration-[220ms]"
                    >
                        NOS SERVICES
                        <ArrowRight class="w-5 h-5" aria-hidden="true" />
                    </Link>
                </div>

                <ul class="flex flex-wrap gap-x-6 gap-y-3 text-sm text-gray-500" role="list" aria-label="Garanties NETSPRING">
                    <li v-for="badge in ['Accompagnement personnalisé','Sécurité & Fiabilité','Solutions sur mesure']" :key="badge" class="flex items-center gap-2">
                        <CheckCircle2 class="w-4 h-4 text-[#F4620A] flex-shrink-0" aria-hidden="true" />
                        {{ badge }}
                    </li>
                </ul>
            </div>

            <!-- Visuel droit — globe 3D animé Chine → Côte d'Ivoire -->
            <div class="relative">
                <div
                    class="relative rounded-[20px] overflow-hidden shadow-[0_24px_60px_rgba(0,0,0,0.12)] h-64 sm:h-80 lg:aspect-4/3 lg:h-auto bg-ink"
                    role="img"
                    aria-label="Globe animé — route commerciale Chine vers Côte d'Ivoire — NETSPRING"
                >
                    <GlobeAnimation class="absolute inset-0 w-full h-full" />

                    <div class="absolute bottom-5 left-5 bg-white rounded-[12px] px-4 py-3 shadow-[0_4px_20px_rgba(0,0,0,0.12)]">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Clients satisfaits</p>
                        <p class="text-lg font-heading font-bold text-[#0D0D0D]">
                            <span role="status" aria-label="Plus de 500 clients satisfaits">500+</span>
                            <span class="text-xs text-gray-400 font-normal ml-1">entreprises</span>
                        </p>
                    </div>
                    <div class="absolute top-5 right-5 bg-[#F4620A] text-white rounded-[10px] px-3 py-2">
                        <p class="text-xs font-bold leading-tight">4.9/5</p>
                        <p class="text-[10px] opacity-80 leading-tight">satisfaction</p>
                    </div>
                </div>
                <div aria-hidden="true" class="absolute -bottom-5 -left-5 w-28 h-28 bg-orange-100 rounded-[18px] -z-10 opacity-70"></div>
                <div aria-hidden="true" class="absolute -top-5 -right-5 w-20 h-20 border-2 border-[#F4620A]/20 rounded-full -z-10"></div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         TRUST MARQUEE
    ══════════════════════════════════════════════════ -->
    <div class="bg-[#F4620A] py-3 overflow-hidden" aria-label="Domaines d'expertise NETSPRING" aria-hidden="true">
        <div class="marquee-track select-none">
            <template v-for="_ in 2" :key="_">
                <span
                    v-for="item in ['Sourcing Chine','Importation','Contrôle qualité','Transport maritime','Dédouanement CI','Accompagnement dédié','500+ clients','Achat & négociation','Livraison sécurisée','Commerce Chine – CI']"
                    :key="item + _"
                    class="inline-flex items-center gap-3 px-5 text-white font-semibold text-sm whitespace-nowrap"
                >
                    <span class="w-1 h-1 bg-white/40 rounded-full flex-shrink-0" aria-hidden="true"></span>
                    {{ item }}
                </span>
            </template>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         SERVICES
    ══════════════════════════════════════════════════ -->
    <section aria-labelledby="services-heading" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-[1fr_2fr] gap-16 items-start">

                <!-- Texte gauche fixe -->
                <div class="lg:sticky lg:top-28" data-reveal>
                    <p class="text-[#F4620A] font-semibold text-xs tracking-widest uppercase mb-4">Ce que nous faisons</p>
                    <h2 id="services-heading" class="font-heading font-bold text-[#0D0D0D] leading-tight mb-5" style="font-size:clamp(1.8rem,3vw,2.4rem);">
                        De la Chine<br>à votre porte.
                    </h2>
                    <p class="text-gray-500 text-base leading-relaxed mb-8">
                        Chaque service est conçu pour retirer un obstacle sur la route entre vous et vos marchandises.
                    </p>
                    <Link
                        href="/services"
                        class="btn-press inline-flex items-center gap-2 bg-[#0D0D0D] hover:bg-[#1a1a1a] text-white font-semibold px-6 py-3 rounded-[10px] text-sm transition-colors duration-[220ms]"
                    >
                        Voir tous nos services
                        <ChevronRight class="w-4 h-4" aria-hidden="true" />
                    </Link>
                </div>

                <!-- Liste éditoriale droite -->
                <ul class="divide-y divide-[#E5E7EB]" role="list">
                    <li v-for="(service, i) in services" :key="service.title"
                        data-reveal :data-reveal-delay="Math.min(i + 1, 4)"
                    >
                        <Link
                            href="/services"
                            class="group flex items-center gap-6 py-7 transition-all duration-[220ms]"
                            :aria-label="`En savoir plus sur ${service.title}`"
                        >
                            <span class="text-gray-300 font-heading font-bold text-sm w-8 shrink-0 tabular-nums" aria-hidden="true">{{ String(i+1).padStart(2,'0') }}</span>
                            <div class="w-11 h-11 bg-orange-50 group-hover:bg-[#F4620A] rounded-[12px] flex items-center justify-center shrink-0 transition-colors duration-[220ms] shadow-brand">
                                <component :is="service.icon" class="w-5 h-5 text-[#F4620A] group-hover:text-white transition-colors duration-[220ms]" aria-hidden="true" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-heading font-semibold text-[#0D0D0D] group-hover:text-[#F4620A] text-base leading-snug transition-colors duration-[150ms]">{{ service.title }}</h3>
                                <p class="text-gray-500 text-sm mt-0.5 leading-relaxed">{{ service.desc }}</p>
                            </div>
                            <ChevronRight class="w-5 h-5 text-gray-300 group-hover:text-[#F4620A] group-hover:translate-x-1 transition-all duration-[220ms] shrink-0" aria-hidden="true" />
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         POURQUOI NETSPRING
    ══════════════════════════════════════════════════ -->
    <section aria-labelledby="why-heading" class="noise-bg relative py-24 bg-[#0D0D0D]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">POURQUOI NOUS ?</p>
                    <h2 id="why-heading" class="font-heading font-bold text-white mb-6 leading-tight" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                        Pourquoi choisir<br>NETSPRING ?
                    </h2>
                    <p class="text-gray-400 text-lg leading-[1.75] mb-8 max-w-md">
                        Nous simplifions l'accès aux opportunités commerciales entre la Chine
                        et la Côte d'Ivoire. Notre expertise et notre réseau vous permettent
                        d'importer en toute confiance.
                    </p>
                    <a
                        :href="whatsappDevis" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-2.5 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-7 py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_24px_rgba(244,98,10,0.45)] hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
                        aria-label="Démarrer mon projet d'importation via WhatsApp"
                    >
                        Démarrer mon projet
                        <ArrowRight class="w-5 h-5" aria-hidden="true" />
                    </a>
                </div>

                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4" role="list">
                    <li v-for="adv in advantages" :key="adv.title"
                        class="bg-white/5 hover:bg-white/8 border border-white/10 rounded-[16px] p-6 transition-colors duration-[220ms]"
                    >
                        <div class="w-10 h-10 bg-[#F4620A]/15 rounded-[10px] flex items-center justify-center mb-4">
                            <component :is="adv.icon" class="w-5 h-5 text-[#F4620A]" aria-hidden="true" />
                        </div>
                        <h3 class="font-heading font-semibold text-white mb-2 text-sm leading-snug">{{ adv.title }}</h3>
                        <p class="text-gray-400 text-xs leading-[1.7]">{{ adv.desc }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         COMMENT ÇA MARCHE
    ══════════════════════════════════════════════════ -->
    <section aria-labelledby="process-heading" class="py-24 bg-[#FAFAFA]">
        <div class="max-w-7xl mx-auto px-6">
            <header class="text-center mb-16">
                <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3" aria-hidden="true">LE PROCESSUS</p>
                <h2 id="process-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    De votre idée à votre marchandise.
                </h2>
            </header>

            <!-- Desktop -->
            <ol class="hidden lg:flex items-start gap-0 relative" aria-label="Processus d'importation en 5 étapes">
                <div class="absolute top-8 left-[8%] right-[8%] h-0.5 bg-[#E5E7EB] z-0" aria-hidden="true">
                    <div class="h-full bg-gradient-to-r from-[#F4620A] via-[#F4620A]/60 to-[#E5E7EB] w-3/4 rounded-full"></div>
                </div>
                <li v-for="(step, i) in steps" :key="step.num" class="flex-1 flex flex-col items-center text-center relative z-10 px-3">
                    <div class="w-16 h-16 rounded-[16px] flex items-center justify-center text-lg font-heading font-bold mb-5 shadow-[0_4px_20px_rgba(244,98,10,0.25)]"
                         :class="i < 3 ? 'bg-[#F4620A] text-white' : 'bg-white text-[#0D0D0D] border-2 border-[#E5E7EB]'"
                    >{{ step.num }}</div>
                    <h3 class="font-heading font-semibold text-[#0D0D0D] text-sm mb-2 leading-snug">{{ step.title }}</h3>
                    <p class="text-gray-500 text-xs leading-[1.7]">{{ step.desc }}</p>
                </li>
            </ol>

            <!-- Mobile -->
            <ol class="lg:hidden space-y-0" aria-label="Processus d'importation en 5 étapes">
                <li v-for="(step, i) in steps" :key="step.num" class="flex gap-5">
                    <div class="flex flex-col items-center">
                        <div class="w-11 h-11 rounded-[12px] flex items-center justify-center text-sm font-heading font-bold flex-shrink-0"
                             :class="i < 3 ? 'bg-[#F4620A] text-white shadow-[0_4px_12px_rgba(244,98,10,0.3)]' : 'bg-white text-[#0D0D0D] border-2 border-[#E5E7EB]'"
                        >{{ step.num }}</div>
                        <div v-if="i < steps.length-1" class="w-0.5 flex-1 my-1.5" :class="i < 2 ? 'bg-[#F4620A]/30' : 'bg-[#E5E7EB]'" aria-hidden="true"></div>
                    </div>
                    <div class="pt-1.5 pb-7">
                        <h3 class="font-heading font-semibold text-[#0D0D0D] text-base mb-1">{{ step.title }}</h3>
                        <p class="text-gray-500 text-sm leading-[1.7]">{{ step.desc }}</p>
                    </div>
                </li>
            </ol>

            <div class="text-center mt-14">
                <a :href="whatsappDevis" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 text-[#F4620A] font-semibold text-base hover:gap-3 transition-all duration-[220ms] cursor-pointer"
                   aria-label="Démarrer mon projet — ouvre WhatsApp">
                    Démarrer mon projet maintenant
                    <ArrowRight class="w-5 h-5" aria-hidden="true" />
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         IMPORTATION + FORMATIONS
    ══════════════════════════════════════════════════ -->
    <section aria-label="Services d'importation et de formation NETSPRING">
        <div class="grid lg:grid-cols-2">

            <!-- Importation -->
            <article aria-labelledby="import-heading" class="relative bg-[#0D0D0D] p-12 lg:p-16 flex flex-col justify-center overflow-hidden">
                <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-br from-[#1a1a1a] to-[#0D0D0D]"></div>
                <div aria-hidden="true" class="absolute top-0 right-0 w-72 h-72 bg-[#F4620A]/8 rounded-full blur-3xl"></div>
                <div class="relative">
                    <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">IMPORTATION</p>
                    <h2 id="import-heading" class="font-heading font-bold text-white mb-5 leading-tight" style="font-size:clamp(1.6rem,3vw,2.2rem);">
                        Vous voulez importer<br>depuis la <span class="text-[#F4620A]">Chine</span> ?
                    </h2>
                    <p class="text-gray-400 leading-[1.75] mb-7 max-w-md">
                        Que vous soyez débutant, commerçant ou entrepreneur, NETSPRING vous
                        accompagne à chaque étape de votre projet d'importation.
                    </p>
                    <ul class="space-y-3 mb-8" role="list" aria-label="Services d'importation inclus">
                        <li v-for="item in importChecklist" :key="item" class="flex items-center gap-3 text-gray-300 text-sm">
                            <CircleCheck class="w-4 h-4 text-[#F4620A] flex-shrink-0" aria-hidden="true" />
                            {{ item }}
                        </li>
                    </ul>
                    <a :href="whatsappDevis" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-6 py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
                       aria-label="Démarrer mon projet d'importation">
                        EN SAVOIR PLUS
                        <ArrowRight class="w-4 h-4" aria-hidden="true" />
                    </a>
                </div>
            </article>

            <!-- Formations -->
            <article aria-labelledby="formation-heading" class="relative bg-orange-50 p-12 lg:p-16 flex flex-col justify-center overflow-hidden">
                <div aria-hidden="true" class="absolute bottom-0 right-0 w-80 h-80 bg-orange-200/25 rounded-full blur-3xl"></div>
                <div class="relative">
                    <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">APPRENEZ À IMPORTER</p>
                    <h2 id="formation-heading" class="font-heading font-bold text-[#0D0D0D] mb-5 leading-tight" style="font-size:clamp(1.6rem,3vw,2.2rem);">
                        Apprenez.<br>Importez.<br><span class="text-[#F4620A]">Développez.</span>
                    </h2>
                    <p class="text-gray-600 leading-[1.75] mb-7 max-w-md">
                        Des formations pratiques pour comprendre l'importation, le commerce
                        international et développer votre activité commerciale.
                    </p>
                    <ul class="space-y-3 mb-8" role="list" aria-label="Sujets de formation disponibles">
                        <li v-for="item in formationChecklist" :key="item" class="flex items-center gap-3 text-gray-700 text-sm">
                            <CircleCheck class="w-4 h-4 text-[#F4620A] flex-shrink-0" aria-hidden="true" />
                            {{ item }}
                        </li>
                    </ul>
                    <Link href="/formations"
                          class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-6 py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.3)] hover:-translate-y-0.5 active:translate-y-0">
                        DÉCOUVRIR NOS FORMATIONS
                        <ArrowRight class="w-4 h-4" aria-hidden="true" />
                    </Link>
                </div>
            </article>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         PREUVE SOCIALE — Stats animés + Témoignages
         (composant SocialProof — données depuis controller)
    ══════════════════════════════════════════════════ -->
    <SocialProof :testimonials="testimonials" />

    <!-- ══════════════════════════════════════════════════
         BLOG — Aperçu des derniers articles
         (composant BlogPreview — données depuis controller)
    ══════════════════════════════════════════════════ -->
    <BlogPreview :latest-posts="latestPosts" />

    <!-- ══════════════════════════════════════════════════
         CTA FINAL
    ══════════════════════════════════════════════════ -->
    <section aria-labelledby="cta-heading" class="py-20 bg-[#F4620A] relative overflow-hidden">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-black/10 rounded-full blur-2xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-6 text-center">
            <div class="w-16 h-16 bg-white/20 rounded-[18px] flex items-center justify-center mx-auto mb-7" aria-hidden="true">
                <Zap class="w-8 h-8 text-white" />
            </div>
            <h2 id="cta-heading" class="font-heading font-bold text-white mb-5 leading-tight" style="font-size:clamp(1.8rem,4vw,3rem);">
                Prêt à développer votre business<br>avec la Chine ?
            </h2>
            <p class="text-orange-100 text-lg mb-10 max-w-xl mx-auto leading-[1.75]">
                Contactez-nous dès maintenant et obtenez un accompagnement personnalisé pour votre projet.
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a
                    :href="whatsappDevis" target="_blank" rel="noopener"
                    class="inline-flex items-center gap-2.5 bg-white text-[#F4620A] font-bold px-8 py-4 rounded-[14px] hover:bg-orange-50 transition-all duration-[220ms] text-base shadow-[0_8px_32px_rgba(0,0,0,0.15)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.18)] hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
                    aria-label="Demander un devis NETSPRING — ouvre WhatsApp"
                >
                    DEMANDER UN DEVIS MAINTENANT
                    <ArrowRight class="w-5 h-5" aria-hidden="true" />
                </a>
                <Link
                    href="/contact"
                    class="inline-flex items-center gap-2.5 border-2 border-white text-white hover:bg-white hover:text-[#F4620A] font-semibold px-8 py-4 rounded-[14px] transition-all duration-[220ms] text-base"
                >
                    NOUS CONTACTER
                </Link>
            </div>
        </div>
    </section>

    </AppLayout>
</template>
