<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import {
    LayoutDashboard, FileText, Package, ShoppingBag,
    FileQuestion, Users, LogOut, Menu, X, ExternalLink, ChevronRight
} from 'lucide-vue-next'

const sidebarOpen = ref(false)

const nav = [
    { label: 'Dashboard',   href: '/admin/dashboard',  icon: LayoutDashboard },
    { label: 'Articles',    href: '/admin/articles',   icon: FileText },
    { label: 'Produits',    href: '/admin/produits',   icon: Package },
    { label: 'Commandes',   href: '/admin/commandes',  icon: ShoppingBag },
    { label: 'Devis',       href: '/admin/devis',      icon: FileQuestion },
    { label: 'Utilisateurs',href: '/admin/utilisateurs', icon: Users },
]

function logout() {
    router.post('/deconnexion')
}

function closeSidebar() {
    sidebarOpen.value = false
}
</script>

<template>
    <div class="min-h-screen bg-[#111] flex">

        <!-- Overlay mobile -->
        <Transition name="fade">
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 bg-black/60 z-30 lg:hidden"
                aria-hidden="true"
                @click="closeSidebar"
            />
        </Transition>

        <!-- SIDEBAR -->
        <aside
            :class="[
                'fixed top-0 left-0 h-full w-60 bg-[#0D0D0D] border-r border-white/8 z-40 flex flex-col transition-transform duration-[220ms]',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            aria-label="Navigation administration"
        >
            <!-- Logo -->
            <div class="px-5 py-5 border-b border-white/10 flex-shrink-0">
                <Link href="/admin/dashboard" class="flex items-center gap-2.5" aria-label="Admin NETSPRING" @click="closeSidebar">
                    <div class="w-8 h-8 bg-[#F4620A] rounded-[9px] flex items-center justify-center">
                        <span class="text-white font-black text-sm leading-none font-heading" aria-hidden="true">N</span>
                    </div>
                    <div>
                        <span class="font-heading font-bold text-base text-white tracking-tight leading-none block">
                            NET<span class="text-[#F4620A]">SPRING</span>
                        </span>
                        <span class="text-[10px] text-gray-500 font-medium uppercase tracking-widest">Administration</span>
                    </div>
                </Link>
            </div>

            <!-- Nav links -->
            <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto" aria-label="Menu administration">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-[9px] text-gray-400 hover:text-white hover:bg-white/8 transition-all duration-[220ms] text-sm font-medium group"
                    :aria-label="item.label"
                    @click="closeSidebar"
                >
                    <component :is="item.icon" class="w-4 h-4 flex-shrink-0" aria-hidden="true" />
                    {{ item.label }}
                    <ChevronRight class="w-3 h-3 ml-auto opacity-0 group-hover:opacity-50 transition-opacity" aria-hidden="true" />
                </Link>
            </nav>

            <!-- Bottom actions -->
            <div class="px-3 py-4 border-t border-white/10 space-y-0.5 flex-shrink-0">
                <Link
                    href="/"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-[9px] text-gray-400 hover:text-white hover:bg-white/8 transition-all duration-[220ms] text-sm font-medium"
                    aria-label="Voir le site public"
                    @click="closeSidebar"
                >
                    <ExternalLink class="w-4 h-4 flex-shrink-0" aria-hidden="true" />
                    Voir le site
                </Link>
                <button
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-[9px] text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-all duration-[220ms] text-sm font-medium cursor-pointer"
                    aria-label="Se déconnecter"
                    @click="logout"
                >
                    <LogOut class="w-4 h-4 flex-shrink-0" aria-hidden="true" />
                    Déconnexion
                </button>
            </div>
        </aside>

        <!-- MAIN WRAPPER -->
        <div class="flex-1 lg:ml-60 flex flex-col min-h-screen">

            <!-- Top header -->
            <header class="bg-[#0D0D0D] border-b border-white/8 px-5 py-3 flex items-center gap-4 sticky top-0 z-20">
                <button
                    class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition-colors cursor-pointer text-gray-400"
                    :aria-expanded="sidebarOpen"
                    :aria-label="sidebarOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <X v-if="sidebarOpen" class="w-5 h-5" aria-hidden="true" />
                    <Menu v-else class="w-5 h-5" aria-hidden="true" />
                </button>
                <h1 class="text-white font-heading font-bold text-base flex-1">
                    <slot name="page-title">Administration</slot>
                </h1>
                <slot name="header-actions" />
            </header>

            <!-- Page content -->
            <main class="flex-1 p-5 md:p-7" id="admin-main-content" tabindex="-1">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
