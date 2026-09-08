<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    Globe, Menu, X, ArrowRight,
    Mail, MapPin, Phone, FileText, ShoppingCart, CheckCircle2,
} from 'lucide-vue-next'
import Logo from '@/Components/Logo.vue'

const page       = usePage()
const isScrolled = ref(false)
const mobileOpen = ref(false)

// Panier — données partagées depuis HandleInertiaRequests
const cartCount = computed(() => page.props.cartCount ?? 0)

// Toast flash
const toast      = ref(null)
const toastTimer = ref(null)

watch(() => page.props.flash?.success, (msg) => {
    if (!msg) return
    toast.value = msg
    clearTimeout(toastTimer.value)
    toastTimer.value = setTimeout(() => { toast.value = null }, 3500)
}, { immediate: true })

function onScroll() { isScrolled.value = window.scrollY > 60 }
onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => { window.removeEventListener('scroll', onScroll); clearTimeout(toastTimer.value) })

const nav = [
    { label: 'Accueil',      href: '/' },
    { label: 'Nos services', href: '/services' },
    { label: 'Importer',     href: '/importer' },
    { label: 'Formations',   href: '/formations' },
    { label: 'Boutique',     href: '/boutique' },
    { label: 'Blog',         href: '/blog' },
    { label: 'À propos',     href: '/a-propos' },
    { label: 'Contact',      href: '/contact' },
]

const whatsappUrl   = 'https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20avoir%20plus%20d%27informations.'
const whatsappDevis = 'https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20demander%20un%20devis%20pour%20mon%20projet%20d%27importation.'
</script>

<template>
    <!-- Skip link -->
    <a href="#main-content" class="skip-link">Aller au contenu principal</a>

    <!-- TOP BAR -->
    <div class="hidden md:flex bg-[#0D0D0D] text-gray-400 text-xs py-2" role="banner" aria-label="Informations de contact">
        <div class="max-w-7xl mx-auto px-6 w-full flex items-center justify-between">
            <span class="flex items-center gap-2 text-gray-300">
                <Globe class="w-3.5 h-3.5 text-[#F4620A]" aria-hidden="true" />
                <strong class="text-white font-semibold">Chine → Côte d'Ivoire</strong>
            </span>
            <div class="flex items-center gap-6">
                <a :href="whatsappUrl" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 hover:text-[#F4620A] transition-colors duration-[220ms]"
                   aria-label="Nous contacter sur WhatsApp : +225 0594429552">
                    <!-- WhatsApp — brand icon, pas dans Lucide -->
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.555 4.122 1.524 5.855L0 24l6.336-1.498A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.651-.502-5.178-1.381l-.371-.221-3.762.889.952-3.664-.242-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    +225 0594429552
                </a>
                <a href="mailto:netspringbusiness@gmail.com"
                   class="hover:text-[#F4620A] transition-colors duration-[220ms]"
                   aria-label="Envoyer un email à netspringbusiness@gmail.com">
                    netspringbusiness@gmail.com
                </a>
            </div>
        </div>
    </div>

    <!-- HEADER STICKY -->
    <header
        class="sticky top-0 z-50 transition-all duration-[220ms]"
        :class="isScrolled ? 'bg-[#0D0D0D]/95 backdrop-blur-md shadow-[0_2px_20px_rgba(0,0,0,0.4)] py-3' : 'bg-[#0D0D0D] py-4'"
        role="banner"
    >
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between gap-6">

            <!-- Logo -->
            <Link href="/" aria-label="NETSPRING — Retour à l'accueil" class="flex-shrink-0 hover:opacity-90 transition-opacity duration-150">
                <Logo :dark="true" :size="36" />
            </Link>

            <!-- Nav desktop -->
            <nav class="hidden lg:flex items-center gap-6" aria-label="Navigation principale">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="text-gray-300 hover:text-white text-sm font-medium transition-colors duration-[150ms] relative group py-1"
                >
                    {{ item.label }}
                    <span class="absolute inset-x-0 -bottom-0.5 h-px bg-[#F4620A] scale-x-0 group-hover:scale-x-100 transition-transform duration-[220ms] origin-left" aria-hidden="true"></span>
                </Link>
            </nav>

            <!-- CTA + panier + hamburger -->
            <div class="flex items-center gap-3">
                <a
                    :href="whatsappDevis"
                    target="_blank"
                    rel="noopener"
                    class="hidden sm:inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white text-sm font-semibold px-5 py-2.5 rounded-[10px] transition-all duration-[220ms] shadow-[0_2px_8px_rgba(244,98,10,0.35)] hover:shadow-[0_4px_16px_rgba(244,98,10,0.45)] hover:-translate-y-px active:translate-y-0 active:shadow-none"
                    aria-label="Demander un devis — ouvre WhatsApp"
                >
                    DEMANDER UN DEVIS
                </a>
                <!-- Icône panier -->
                <Link
                    href="/panier"
                    class="relative flex items-center justify-center w-11 h-11 rounded-[10px] text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-150"
                    aria-label="Mon panier"
                >
                    <ShoppingCart class="w-5 h-5" aria-hidden="true" />
                    <Transition name="pop" appear>
                    <span
                        v-if="cartCount > 0"
                        :key="cartCount"
                        class="badge-pop absolute -top-1 -right-1 w-5 h-5 bg-[#F4620A] text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none"
                        :aria-label="`${cartCount} article(s) dans le panier`"
                    >{{ cartCount > 9 ? '9+' : cartCount }}</span>
                    </Transition>
                </Link>
                <button
                    class="lg:hidden text-white w-11 h-11 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors cursor-pointer"
                    :aria-expanded="mobileOpen"
                    aria-controls="mobile-menu"
                    :aria-label="mobileOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
                    @click="mobileOpen = !mobileOpen"
                >
                    <X v-if="mobileOpen" class="w-6 h-6" aria-hidden="true" />
                    <Menu v-else class="w-6 h-6" aria-hidden="true" />
                </button>
            </div>
        </div>

        <!-- Menu mobile animé -->
        <Transition name="menu">
        <nav
            v-if="mobileOpen"
            id="mobile-menu"
            class="lg:hidden bg-[#0D0D0D] border-t border-white/10 px-6 py-6 flex flex-col gap-1"
            aria-label="Menu mobile"
        >
            <Link
                v-for="item in nav"
                :key="item.href"
                :href="item.href"
                class="text-gray-200 hover:text-white hover:bg-white/5 text-base font-medium px-3 py-3 rounded-lg transition-colors"
                @click="mobileOpen = false"
            >{{ item.label }}</Link>
            <hr class="border-white/10 my-3"/>
            <a :href="whatsappDevis" target="_blank" rel="noopener"
               class="bg-[#F4620A] text-white text-center font-bold py-3.5 rounded-[12px] mt-1 shadow-[0_4px_16px_rgba(244,98,10,0.4)] flex items-center justify-center gap-2 cursor-pointer"
               @click="mobileOpen = false">
                DEMANDER UN DEVIS
                <ArrowRight class="w-4 h-4" aria-hidden="true" />
            </a>
            <a :href="whatsappUrl" target="_blank" rel="noopener"
               class="border border-white/20 text-white text-center font-medium py-3 rounded-[12px] mt-2 cursor-pointer"
               @click="mobileOpen = false">
                Écrire sur WhatsApp
            </a>
        </nav>
        </Transition>
    </header>

    <!-- TOAST FLASH -->
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 translate-y-2 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-2 scale-95"
    >
        <div
            v-if="toast"
            class="fixed top-20 left-4 right-4 sm:left-auto sm:right-5 sm:max-w-xs z-[60] flex items-center gap-3 bg-[#0D0D0D] text-white text-sm font-medium px-5 py-3.5 rounded-[14px] shadow-[0_8px_32px_rgba(0,0,0,0.35)] border border-white/10"
            role="status"
            aria-live="polite"
        >
            <CheckCircle2 class="w-5 h-5 text-emerald-400 flex-shrink-0" aria-hidden="true" />
            {{ toast }}
            <button
                class="ml-2 text-gray-400 hover:text-white transition-colors cursor-pointer"
                aria-label="Fermer la notification"
                @click="toast = null"
            >×</button>
        </div>
    </Transition>

    <!-- PAGE CONTENT — pb-20 pour ne pas être caché par la CTA bar fixe sur mobile -->
    <main id="main-content" tabindex="-1" class="md:pb-0 pb-20">
        <slot />
    </main>

    <!-- FOOTER -->
    <footer class="bg-[#0D0D0D] text-gray-400 pt-16 pb-8" role="contentinfo">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                <!-- Col 1 — Marque -->
                <div>
                    <Link href="/" aria-label="NETSPRING — Accueil" class="inline-flex mb-5 hover:opacity-90 transition-opacity duration-150">
                        <Logo :dark="true" :size="32" />
                    </Link>
                    <p class="text-sm leading-relaxed mb-5 max-w-xs">
                        Votre pont commercial Chine-Côte d'Ivoire. Nous accompagnons les entrepreneurs africains dans leurs projets d'importation.
                    </p>
                    <!-- Réseaux sociaux -->
                    <div class="flex items-center gap-3" aria-label="Réseaux sociaux NETSPRING">
                        <!-- WhatsApp -->
                        <a :href="whatsappUrl" target="_blank" rel="noopener" class="w-11 h-11 rounded-full bg-white/5 hover:bg-[#F4620A] flex items-center justify-center transition-all duration-[220ms] text-gray-400 hover:text-white cursor-pointer" aria-label="WhatsApp NETSPRING">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.555 4.122 1.524 5.855L0 24l6.336-1.498A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.651-.502-5.178-1.381l-.371-.221-3.762.889.952-3.664-.242-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                        </a>
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/profile.php?id=61574430440509" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/5 hover:bg-[#F4620A] flex items-center justify-center transition-all duration-[220ms] text-gray-400 hover:text-white cursor-pointer" aria-label="Facebook NETSPRING">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <!-- TikTok -->
                        <a href="https://www.tiktok.com/@netspringbusiness" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/5 hover:bg-[#F4620A] flex items-center justify-center transition-all duration-[220ms] text-gray-400 hover:text-white cursor-pointer" aria-label="TikTok NETSPRING">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2 — Liens rapides -->
                <nav aria-label="Liens rapides du footer">
                    <h3 class="text-white font-semibold mb-5 text-sm uppercase tracking-widest">Liens rapides</h3>
                    <ul class="space-y-3 text-sm" role="list">
                        <li v-for="item in [
                            {label:'Accueil',     href:'/'},
                            {label:'Boutique',    href:'/boutique'},
                            {label:'Blog',        href:'/blog'},
                            {label:'À propos',    href:'/a-propos'},
                            {label:'Importer',    href:'/importer'},
                            {label:'Formations',  href:'/formations'},
                            {label:'Contact',     href:'/contact'},
                        ]" :key="item.href">
                            <Link :href="item.href" class="hover:text-[#F4620A] transition-colors duration-[150ms]">
                                {{ item.label }}
                            </Link>
                        </li>
                    </ul>
                </nav>

                <!-- Col 3 — Services -->
                <nav aria-label="Services NETSPRING">
                    <h3 class="text-white font-semibold mb-5 text-sm uppercase tracking-widest">Nos services</h3>
                    <ul class="space-y-3 text-sm" role="list">
                        <li v-for="s in ['Achat & négociation','Contrôle qualité','Transport & logistique','Dédouanement','Accompagnement personnalisé']" :key="s">
                            <a href="/services" class="hover:text-[#F4620A] transition-colors duration-[150ms]">{{ s }}</a>
                        </li>
                    </ul>
                </nav>

                <!-- Col 4 — Contact + Newsletter -->
                <div>
                    <h3 class="text-white font-semibold mb-5 text-sm uppercase tracking-widest">Contact</h3>
                    <address class="not-italic space-y-3 text-sm mb-7">
                        <a :href="whatsappUrl" target="_blank" rel="noopener"
                           class="flex items-center gap-2.5 hover:text-[#F4620A] transition-colors">
                            <!-- WhatsApp brand -->
                            <svg class="w-4 h-4 text-[#F4620A] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.555 4.122 1.524 5.855L0 24l6.336-1.498A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.651-.502-5.178-1.381l-.371-.221-3.762.889.952-3.664-.242-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                            +225 0594429552
                        </a>
                        <a href="mailto:netspringbusiness@gmail.com"
                           class="flex items-center gap-2.5 hover:text-[#F4620A] transition-colors">
                            <Mail class="w-4 h-4 text-[#F4620A] flex-shrink-0" aria-hidden="true" />
                            netspringbusiness@gmail.com
                        </a>
                        <span class="flex items-start gap-2.5">
                            <MapPin class="w-4 h-4 text-[#F4620A] flex-shrink-0 mt-0.5" aria-hidden="true" />
                            <span>Abidjan, Côte d'Ivoire<br/><span class="text-xs text-gray-500">Lun–Ven : 08h00–19h00</span></span>
                        </span>
                    </address>

                    <h3 class="text-white font-semibold mb-3 text-sm uppercase tracking-widest">Newsletter</h3>
                    <form class="flex gap-2" @submit.prevent aria-label="Inscription à la newsletter">
                        <label for="newsletter-email" class="sr-only">Votre adresse email</label>
                        <input
                            id="newsletter-email"
                            type="email"
                            placeholder="Votre email"
                            autocomplete="email"
                            class="flex-1 bg-white/5 border border-white/10 text-white text-sm px-3 py-2.5 rounded-[9px] placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                        />
                        <button
                            type="submit"
                            class="bg-[#F4620A] hover:bg-[#d45208] text-white text-sm font-semibold px-4 py-2.5 rounded-[9px] transition-all duration-[220ms] whitespace-nowrap active:scale-95 cursor-pointer"
                        >
                            S'ABONNER
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                <p>© 2026 NETSPRING — Tous droits réservés.</p>
                <div class="flex gap-6">
                    <Link href="/mentions-legales" class="hover:text-[#F4620A] transition-colors">Mentions légales</Link>
                    <Link href="/politique-de-confidentialite" class="hover:text-[#F4620A] transition-colors">Politique de confidentialité</Link>
                </div>
            </div>
        </div>
    </footer>

    <!-- Tawk.to injecté via app.blade.php -->

    <!-- CTA MOBILE BAR -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 grid grid-cols-3" role="navigation" aria-label="Actions rapides">
        <a :href="whatsappUrl" target="_blank" rel="noopener"
           class="flex flex-col items-center justify-center py-3 text-green-600 text-xs font-semibold gap-1 min-h-[56px] cursor-pointer"
           aria-label="WhatsApp">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.555 4.122 1.524 5.855L0 24l6.336-1.498A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.651-.502-5.178-1.381l-.371-.221-3.762.889.952-3.664-.242-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            WhatsApp
        </a>
        <a :href="whatsappDevis" target="_blank" rel="noopener"
           class="flex flex-col items-center justify-center py-3 bg-[#F4620A] text-white text-xs font-bold gap-1 min-h-[56px] cursor-pointer"
           aria-label="Demander un devis">
            <FileText class="w-5 h-5" aria-hidden="true" />
            Devis
        </a>
        <a href="tel:+2250594429552"
           class="flex flex-col items-center justify-center py-3 text-gray-700 text-xs font-semibold gap-1 min-h-[56px] cursor-pointer"
           aria-label="Appeler NETSPRING : +225 0594429552">
            <Phone class="w-5 h-5" aria-hidden="true" />
            Appeler
        </a>
    </div>
</template>
