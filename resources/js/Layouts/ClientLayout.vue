<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import {
    LayoutDashboard, ShoppingBag, BookOpen, User, LogOut,
    Menu, X, ChevronRight
} from 'lucide-vue-next'

const sidebarOpen = ref(false)

const nav = [
    { label: 'Tableau de bord', href: '/client/dashboard', icon: LayoutDashboard },
    { label: 'Mes commandes',   href: '/client/commandes',  icon: ShoppingBag },
    { label: 'Mes formations',  href: '/client/formations', icon: BookOpen },
    { label: 'Mon profil',      href: '/client/profil',     icon: User },
]

function logout() {
    router.post('/deconnexion')
}

function closeSidebar() {
    sidebarOpen.value = false
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">

        <!-- Overlay mobile -->
        <Transition name="fade">
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 bg-black/50 z-30 lg:hidden"
                aria-hidden="true"
                @click="closeSidebar"
            />
        </Transition>

        <!-- SIDEBAR -->
        <aside
            :class="[
                'fixed top-0 left-0 h-full w-64 bg-[#0D0D0D] z-40 flex flex-col transition-transform duration-[220ms]',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            aria-label="Navigation client"
        >
            <!-- Logo -->
            <div class="px-6 py-6 border-b border-white/10 flex-shrink-0">
                <Link
                    href="/"
                    class="flex items-center gap-2.5 group"
                    aria-label="NETSPRING — Retour à l'accueil"
                    @click="closeSidebar"
                >
                    <div class="w-9 h-9 bg-[#F4620A] rounded-[10px] flex items-center justify-center shadow-[0_2px_8px_rgba(244,98,10,0.4)]">
                        <span class="text-white font-black text-lg leading-none font-heading" aria-hidden="true">N</span>
                    </div>
                    <span class="font-heading font-bold text-xl text-white tracking-tight">
                        NET<span class="text-[#F4620A]">SPRING</span>
                    </span>
                </Link>
            </div>

            <!-- Nav links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto" aria-label="Menu client">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] text-gray-400 hover:text-white hover:bg-white/8 transition-all duration-[220ms] text-sm font-medium group"
                    :aria-label="item.label"
                    @click="closeSidebar"
                >
                    <component :is="item.icon" class="w-4.5 h-4.5 flex-shrink-0" aria-hidden="true" />
                    {{ item.label }}
                    <ChevronRight class="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-60 transition-opacity" aria-hidden="true" />
                </Link>
            </nav>

            <!-- Logout -->
            <div class="px-4 py-5 border-t border-white/10 flex-shrink-0">
                <button
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-[10px] text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-all duration-[220ms] text-sm font-medium cursor-pointer"
                    aria-label="Se déconnecter"
                    @click="logout"
                >
                    <LogOut class="w-4.5 h-4.5 flex-shrink-0" aria-hidden="true" />
                    Déconnexion
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

            <!-- Top bar mobile -->
            <header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3 sticky top-0 z-20">
                <button
                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer text-gray-700"
                    :aria-expanded="sidebarOpen"
                    aria-controls="client-sidebar"
                    :aria-label="sidebarOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <X v-if="sidebarOpen" class="w-5 h-5" aria-hidden="true" />
                    <Menu v-else class="w-5 h-5" aria-hidden="true" />
                </button>
                <span class="font-heading font-bold text-[#0D0D0D]">
                    NET<span class="text-[#F4620A]">SPRING</span>
                </span>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-6 md:p-8" id="client-main-content" tabindex="-1">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
