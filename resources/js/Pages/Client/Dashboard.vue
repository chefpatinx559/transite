<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ShoppingBag, TrendingUp, Clock, Hand } from 'lucide-vue-next'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ orders_count: 0, total_spent: 0, pending_count: 0 }),
    },
    recentOrders: { type: Array, default: () => [] },
    user: { type: Object, default: () => ({}) },
})

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function formatDate(val) {
    if (!val) return '—'
    return new Date(val).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })
}

const statusMap = {
    pending:    { label: 'En attente',  cls: 'bg-gray-100 text-gray-600' },
    paid:       { label: 'Payé',        cls: 'bg-emerald-100 text-emerald-700' },
    processing: { label: 'En cours',    cls: 'bg-blue-100 text-blue-700' },
    shipped:    { label: 'Expédié',     cls: 'bg-purple-100 text-purple-700' },
    delivered:  { label: 'Livré',       cls: 'bg-emerald-200 text-emerald-800' },
    cancelled:  { label: 'Annulé',      cls: 'bg-red-100 text-red-600' },
}

function statusBadge(status) {
    return statusMap[status] ?? { label: status, cls: 'bg-gray-100 text-gray-600' }
}

const statCards = [
    {
        label: 'Commandes',
        valueKey: 'orders_count',
        icon: ShoppingBag,
        color: 'bg-[#F4620A]/10 text-[#F4620A]',
    },
    {
        label: 'Total dépensé',
        valueKey: 'total_spent',
        icon: TrendingUp,
        color: 'bg-emerald-50 text-emerald-600',
        isPrice: true,
    },
    {
        label: 'Devis en attente',
        valueKey: 'pending_count',
        icon: Clock,
        color: 'bg-amber-50 text-amber-600',
    },
]
</script>

<template>
    <Head title="Mon tableau de bord — NETSPRING" />
    <ClientLayout>

        <!-- Header -->
        <div class="mb-8">
            <h1 class="font-heading font-bold text-2xl md:text-3xl text-[#0D0D0D] flex items-center gap-2.5">
                Bonjour {{ user?.first_name ?? 'vous' }}
                <Hand class="w-6 h-6 text-[#F4620A]" aria-hidden="true" />
            </h1>
            <p class="text-gray-500 text-sm mt-1">Bienvenue dans votre espace personnel NETSPRING.</p>
        </div>

        <!-- Stats cards -->
        <section aria-labelledby="stats-title" class="mb-10">
            <h2 id="stats-title" class="sr-only">Statistiques</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div
                    v-for="card in statCards"
                    :key="card.label"
                    class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-6 flex items-center gap-4"
                >
                    <div :class="['w-12 h-12 rounded-[12px] flex items-center justify-center flex-shrink-0', card.color]">
                        <component :is="card.icon" class="w-5.5 h-5.5" aria-hidden="true" />
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs font-medium uppercase tracking-wider mb-0.5">{{ card.label }}</p>
                        <p class="font-heading font-bold text-xl text-[#0D0D0D]">
                            {{ card.isPrice ? formatPrice(stats[card.valueKey] ?? 0) : (stats[card.valueKey] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent orders -->
        <section aria-labelledby="recent-orders-title">
            <div class="flex items-center justify-between mb-5">
                <h2 id="recent-orders-title" class="font-heading font-bold text-lg text-[#0D0D0D]">
                    Dernières commandes
                </h2>
                <a href="/client/commandes" class="text-[#F4620A] text-sm font-semibold hover:underline">
                    Tout voir
                </a>
            </div>

            <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] overflow-hidden">
                <div
                    v-if="!recentOrders.length"
                    class="py-16 text-center"
                    role="status"
                >
                    <ShoppingBag class="w-10 h-10 text-gray-200 mx-auto mb-3" aria-hidden="true" />
                    <p class="text-gray-400 text-sm">Aucune commande pour l'instant.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm" aria-label="Dernières commandes">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N° Commande</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="order in recentOrders"
                                :key="order.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-5 py-4 font-mono font-semibold text-[#0D0D0D] text-xs">
                                    #{{ order.order_number ?? order.id }}
                                </td>
                                <td class="px-5 py-4 text-gray-500">{{ formatDate(order.created_at) }}</td>
                                <td class="px-5 py-4 font-bold text-[#F4620A]">{{ formatPrice(order.total) }}</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full"
                                        :class="statusBadge(order.status).cls"
                                    >{{ statusBadge(order.status).label }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </ClientLayout>
</template>
