<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { CheckCircle2, Package, ShoppingCart, Home } from 'lucide-vue-next'

const props = defineProps({
    order: { type: Object, required: true },
})

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function formatDate(val) {
    if (!val) return '—'
    return new Date(val).toLocaleDateString('fr-FR', {
        year: 'numeric', month: 'long', day: 'numeric'
    })
}

const paymentStatusMap = {
    pending:    { label: 'En attente',     cls: 'bg-gray-100 text-gray-600' },
    paid:       { label: 'Payé',           cls: 'bg-emerald-100 text-emerald-700' },
    failed:     { label: 'Échec',          cls: 'bg-red-100 text-red-600' },
}

function paymentBadge(status) {
    return paymentStatusMap[status] ?? { label: status, cls: 'bg-gray-100 text-gray-600' }
}
</script>

<template>
    <Head title="Commande confirmée — NETSPRING" />
    <AppLayout>
        <div class="max-w-2xl mx-auto px-6 py-20">

            <!-- Success icon -->
            <div class="flex flex-col items-center text-center mb-10">
                <div class="w-24 h-24 bg-[#F4620A]/10 rounded-full flex items-center justify-center mb-6">
                    <CheckCircle2 class="w-14 h-14 text-[#F4620A]" aria-hidden="true" />
                </div>
                <h1 class="font-heading font-bold text-3xl md:text-4xl text-[#0D0D0D] mb-3">
                    Merci pour votre commande !
                </h1>
                <p class="text-gray-500 text-base max-w-sm">
                    Votre commande a bien été enregistrée. Vous recevrez une confirmation par email et WhatsApp.
                </p>
            </div>

            <!-- Order card -->
            <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5">Numéro de commande</p>
                        <p class="font-heading font-bold text-[#0D0D0D] text-lg">#{{ order.order_number ?? order.id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-0.5 text-right">Date</p>
                        <p class="font-medium text-[#0D0D0D] text-sm">{{ formatDate(order.created_at) }}</p>
                    </div>
                </div>

                <!-- Meta row -->
                <div class="px-6 py-4 border-b border-gray-100 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Montant total</p>
                        <p class="font-bold text-[#F4620A] text-xl">{{ formatPrice(order.total) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Statut paiement</p>
                        <span
                            class="inline-block text-xs font-semibold px-3 py-1 rounded-full"
                            :class="paymentBadge(order.payment_status).cls"
                        >{{ paymentBadge(order.payment_status).label }}</span>
                    </div>
                </div>

                <!-- Items -->
                <div class="px-6 py-4">
                    <h2 class="text-sm font-bold text-[#0D0D0D] mb-4">Articles commandés</h2>
                    <ul class="space-y-3" role="list" aria-label="Récapitulatif des articles">
                        <li
                            v-for="item in (order.items ?? [])"
                            :key="item.id"
                            class="flex items-center gap-3"
                        >
                            <div class="w-10 h-10 bg-gray-100 rounded-[8px] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                <img
                                    v-if="item.image"
                                    :src="item.image.startsWith('http') ? item.image : '/storage/' + item.image"
                                    :alt="item.name"
                                    class="w-full h-full object-cover"
                                />
                                <Package v-else class="w-4.5 h-4.5 text-gray-300" aria-hidden="true" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#0D0D0D] truncate">{{ item.name }}</p>
                                <p class="text-xs text-gray-500">x{{ item.quantity }}</p>
                            </div>
                            <span class="text-sm font-bold text-[#0D0D0D] flex-shrink-0">
                                {{ formatPrice(item.price * item.quantity) }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <Link
                    href="/boutique"
                    class="flex-1 flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] cursor-pointer"
                    aria-label="Continuer les achats"
                >
                    <ShoppingCart class="w-4.5 h-4.5" aria-hidden="true" />
                    Continuer les achats
                </Link>
                <Link
                    href="/"
                    class="flex-1 flex items-center justify-center gap-2 bg-white border-2 border-gray-200 hover:border-[#F4620A] hover:text-[#F4620A] text-[#0D0D0D] font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] cursor-pointer"
                    aria-label="Retour à l'accueil"
                >
                    <Home class="w-4.5 h-4.5" aria-hidden="true" />
                    Retour à l'accueil
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
