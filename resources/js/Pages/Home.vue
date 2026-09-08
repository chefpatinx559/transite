<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { useReveal } from '@/composables/useReveal'

useReveal()

const props = defineProps({
    latestPosts: { type: Array, default: () => [] },
})
import {
    Globe, Ship, Newspaper, ArrowRight, ChevronRight,
    CheckCircle2, CircleCheck,
    Search, Handshake, ShieldCheck, Package, FileText, Users,
    Target, Shield, Eye, Zap,
    Star, MapPin,
} from 'lucide-vue-next'

const whatsappDevis = 'https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20demander%20un%20devis%20pour%20mon%20projet%20d%27importation.'

// ── Stats animés ──────────────────────────────────────────────
const stats = [
    { target: 500,  suffix: '+',  label: 'Entrepreneurs\naccompagnés' },
    { target: 1000, suffix: '+',  label: 'Commandes\nréalisées' },
    { target: 15,   suffix: '+',  label: 'Pays\ndesservis' },
    { target: 4.9,  suffix: '/5', label: 'Satisfaction\nclients' },
]
const displayed = ref(stats.map(() => 0))

function animateCounters() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        stats.forEach((s, i) => { displayed.value[i] = s.target })
        return
    }
    const duration = 1800
    const start = performance.now()
    function tick(now) {
        const p = Math.min((now - start) / duration, 1)
        const ease = 1 - Math.pow(1 - p, 4)
        stats.forEach((s, i) => {
            displayed.value[i] = s.target % 1 !== 0
                ? parseFloat((s.target * ease).toFixed(1))
                : Math.floor(s.target * ease)
        })
        if (p < 1) requestAnimationFrame(tick)
        else stats.forEach((s, i) => { displayed.value[i] = s.target })
    }
    requestAnimationFrame(tick)
}

onMounted(() => {
    const el = document.getElementById('stats-section')
    if (!el) return
    const obs = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) { animateCounters(); obs.disconnect() }
    }, { threshold: 0.3 })
    obs.observe(el)
})

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
    { title: 'Expertise Chine – CI',      desc: 'Connaissance approfondie des marchés, réglementations et culture d\'affaires.', icon: Target     },
    { title: 'Sécurité des transactions', desc: 'Paiements sécurisés, fournisseurs vérifiés, contrats en ordre.',                 icon: Shield     },
    { title: 'Transparence totale',       desc: 'Suivi en temps réel de votre commande, du sourcing à la livraison.',            icon: Eye        },
    { title: 'Rapidité & réactivité',     desc: 'Réponse sous 24h, équipe disponible du lundi au vendredi.',                    icon: Zap        },
]

// ── Témoignages ───────────────────────────────────────────────
const testimonials = [
    { name: 'Kouamé D.',  role: 'Entrepreneur, Abidjan',     rating: 5, text: 'Grâce à NETSPRING, j\'ai pu lancer ma marque de vêtements avec les meilleurs fournisseurs de Chine. Le service est impeccable, je recommande !' },
    { name: 'Aissata B.', role: 'Importatrice, Bouaké',      rating: 5, text: 'Ils s\'occupent de tout, de la recherche à la livraison. Un gain de temps incroyable. Je recommande à 100%.' },
    { name: 'Bakary S.',  role: "Chef d'entreprise, Daloa",  rating: 5, text: 'Une équipe professionnelle, réactive et très à l\'écoute. Merci NETSPRING pour votre accompagnement !' },
]

// ── Articles blog ─────────────────────────────────────────────
const DEFAULT_COVER = 'https://static.vecteezy.com/system/resources/previews/027/484/654/large_2x/global-business-logistic-and-transportation-import-export-goods-container-cargo-freight-ship-at-international-port-cargo-plane-flying-above-truck-shipping-container-logistic-industry-generative-ai-photo.jpg'

function coverUrl(path) {
    if (!path) return DEFAULT_COVER
    if (path.startsWith('http')) return path
    return '/storage/' + path
}

function formatDate(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

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
        <title>NETSPRING — Votre pont commercial Chine – Côte d'Ivoire</title>
        <meta name="description" content="NETSPRING accompagne les entrepreneurs ivoiriens dans l'importation depuis la Chine : sourcing, négociation, transport, dédouanement et formations." />
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

            <!-- Visuel droit -->
            <div class="relative hidden lg:block">
                <div
                    class="relative rounded-[20px] overflow-hidden shadow-[0_24px_60px_rgba(0,0,0,0.12)] aspect-[4/3] bg-[#0D0D0D]"
                    role="img"
                    aria-label="Illustration commerce Chine vers Côte d'Ivoire : port de conteneurs et logistique"
                >
                    <div class="absolute inset-0 bg-gradient-to-br from-[#1a1a1a] via-[#0D0D0D] to-[#2d1a0a] flex flex-col items-center justify-center p-10 text-white">
                        <Ship class="w-20 h-20 text-[#F4620A] mb-4" aria-hidden="true" />
                        <p class="font-heading text-2xl font-bold text-center leading-snug">Commerce<br>International</p>
                        <div class="flex items-center gap-4 mt-5 w-full max-w-xs">
                            <div class="flex items-center gap-2 text-gray-300 text-sm font-medium">
                                <MapPin class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                                Chine
                            </div>
                            <div class="flex-1 flex items-center gap-1" aria-hidden="true">
                                <div class="h-px flex-1 bg-gradient-to-r from-[#F4620A] to-transparent"></div>
                                <div class="w-2 h-2 bg-[#F4620A] rounded-full"></div>
                                <div class="h-px flex-1 bg-gradient-to-l from-[#F4620A] to-transparent"></div>
                            </div>
                            <div class="flex items-center gap-2 text-gray-300 text-sm font-medium">
                                <MapPin class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                                Côte d'Ivoire
                            </div>
                        </div>
                    </div>
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
         CHIFFRES CLÉS
    ══════════════════════════════════════════════════ -->
    <section id="stats-section" aria-labelledby="stats-heading" class="noise-bg relative py-20 bg-[#0D0D0D]">
        <div class="max-w-7xl mx-auto px-6">
            <h2 id="stats-heading" class="sr-only">Nos chiffres clés</h2>
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="(stat, i) in stats" :key="stat.label" class="text-center">
                    <dt class="sr-only">{{ stat.label.replace('\n', ' ') }}</dt>
                    <dd>
                        <p
                            class="font-heading font-bold text-[#F4620A] leading-none mb-2"
                            style="font-size: clamp(2.5rem, 5vw, 4rem);"
                            role="status"
                            :aria-label="`${displayed[i]}${stat.suffix} ${stat.label.replace('\n', ' ')}`"
                            aria-live="polite"
                        >
                            {{ displayed[i] }}<span class="text-2xl">{{ stat.suffix }}</span>
                        </p>
                        <p class="text-gray-400 text-sm font-medium whitespace-pre-line leading-snug" aria-hidden="true">{{ stat.label }}</p>
                    </dd>
                </div>
            </dl>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         TÉMOIGNAGES
    ══════════════════════════════════════════════════ -->
    <section aria-labelledby="testimonials-heading" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <header class="text-center mb-14">
                <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3" aria-hidden="true">TÉMOIGNAGES</p>
                <h2 id="testimonials-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    Ils nous font confiance.
                </h2>
            </header>

            <ul class="grid md:grid-cols-3 gap-6" role="list">
                <li v-for="t in testimonials" :key="t.name">
                    <article
                        class="bg-[#FAFAFA] rounded-[16px] p-8 h-full flex flex-col hover:shadow-[0_8px_32px_rgba(0,0,0,0.08)] transition-all duration-[220ms] border border-transparent hover:border-[#F4620A]/10"
                        :aria-label="`Témoignage de ${t.name}, ${t.role}`"
                    >
                        <div class="flex gap-1 mb-5" :aria-label="`Note : ${t.rating} étoiles sur 5`">
                            <Star v-for="n in t.rating" :key="n" class="w-4 h-4 text-[#F4620A] fill-[#F4620A]" aria-hidden="true" />
                        </div>
                        <blockquote class="text-gray-700 leading-[1.75] mb-6 flex-1 italic text-sm">
                            "{{ t.text }}"
                        </blockquote>
                        <footer class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#F4620A] rounded-full flex items-center justify-center text-white font-heading font-bold text-sm flex-shrink-0" aria-hidden="true">
                                {{ t.name.charAt(0) }}
                            </div>
                            <div>
                                <cite class="not-italic font-heading font-semibold text-[#0D0D0D] text-sm block">{{ t.name }}</cite>
                                <span class="text-gray-400 text-xs">{{ t.role }}</span>
                            </div>
                        </footer>
                    </article>
                </li>
            </ul>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         BLOG
    ══════════════════════════════════════════════════ -->
    <section aria-labelledby="blog-heading" class="py-24 bg-[#FAFAFA]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-end justify-between mb-14 flex-wrap gap-4">
                <div>
                    <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3" aria-hidden="true">BLOG</p>
                    <h2 id="blog-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                        Nos conseils pour développer<br class="hidden sm:block">votre business.
                    </h2>
                </div>
                <Link href="/blog"
                      class="hidden md:inline-flex items-center gap-2 text-[#F4620A] font-semibold text-sm hover:gap-3 transition-all duration-[220ms]"
                      aria-label="Voir tous les articles du blog NETSPRING">
                    Voir tous les articles
                    <ArrowRight class="w-4 h-4" aria-hidden="true" />
                </Link>
            </div>

            <ul class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" role="list">
                <li v-for="(post, i) in latestPosts" :key="post.id"
                    data-reveal :data-reveal-delay="i + 1"
                >
                    <article class="card-lift group bg-white rounded-[16px] overflow-hidden border border-[#E5E7EB] hover:border-[#F4620A]/20 h-full flex flex-col">
                        <!-- Image de couverture -->
                        <div class="aspect-[16/9] relative overflow-hidden flex-shrink-0">
                            <img
                                :src="coverUrl(post.cover_image)"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent" aria-hidden="true" />
                            <div class="absolute bottom-3 left-3">
                                <span
                                    v-if="post.category"
                                    class="text-white text-xs font-semibold px-2.5 py-1 rounded-full"
                                    :style="{ backgroundColor: post.category.color || '#F4620A' }"
                                >{{ post.category.name }}</span>
                                <span v-else class="bg-[#F4620A] text-white text-xs font-semibold px-2.5 py-1 rounded-full">
                                    Blog
                                </span>
                            </div>
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center gap-3 text-xs text-gray-400 mb-2.5">
                                <time :datetime="post.published_at">{{ formatDate(post.published_at) }}</time>
                                <span aria-hidden="true">·</span>
                                <span>{{ post.reading_time }} min de lecture</span>
                            </div>
                            <h3 class="font-heading font-semibold text-[#0D0D0D] text-sm leading-snug mb-4 flex-1 group-hover:text-[#F4620A] transition-colors duration-[150ms]">
                                {{ post.title }}
                            </h3>
                            <Link
                                :href="`/blog/${post.slug}`"
                                class="text-[#F4620A] text-xs font-semibold flex items-center gap-1.5 group-hover:gap-2.5 transition-all duration-[220ms]"
                                :aria-label="`Lire : ${post.title}`"
                            >
                                Lire l'article
                                <ChevronRight class="w-3.5 h-3.5" aria-hidden="true" />
                            </Link>
                        </div>
                    </article>
                </li>
            </ul>

            <div class="mt-10 text-center md:hidden">
                <Link href="/blog" class="inline-flex items-center gap-2 text-[#F4620A] font-semibold">
                    Voir tous les articles
                    <ArrowRight class="w-4 h-4" aria-hidden="true" />
                </Link>
            </div>
        </div>
    </section>

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
