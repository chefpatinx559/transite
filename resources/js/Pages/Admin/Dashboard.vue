<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Users, ShoppingBag, TrendingUp, FileQuestion } from 'lucide-vue-next'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({ users_count: 0, orders_today: 0, revenue_month: 0, quotes_new: 0 }),
    },
    recentOrders: { type: Array, default: () => [] },
    recentQuotes: { type: Array, default: () => [] },
})

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function formatDate(val) {
    if (!val) return '—'
    return new Date(val).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })
}

const orderStatusMap = {
    pending:    { label: 'En attente',  cls: 'bg-gray-100 text-gray-600' },
    paid:       { label: 'Payé',        cls: 'bg-emerald-100 text-emerald-700' },
    processing: { label: 'En cours',    cls: 'bg-blue-100 text-blue-700' },
    shipped:    { label: 'Expédié',     cls: 'bg-purple-100 text-purple-700' },
    delivered:  { label: 'Livré',       cls: 'bg-emerald-200 text-emerald-800' },
    cancelled:  { label: 'Annulé',      cls: 'bg-red-100 text-red-600' },
}

const quoteStatusMap = {
    new:       { label: 'Nouveau',      cls: 'bg-blue-100 text-blue-700' },
    in_review: { label: 'En révision',  cls: 'bg-amber-100 text-amber-700' },
    quoted:    { label: 'Devis envoyé', cls: 'bg-purple-100 text-purple-700' },
    won:       { label: 'Accepté',      cls: 'bg-emerald-100 text-emerald-700' },
    lost:      { label: 'Perdu',        cls: 'bg-red-100 text-red-600' },
}

function orderBadge(s) { return orderStatusMap[s] ?? { label: s, cls: 'bg-gray-100 text-gray-600' } }
function quoteBadge(s) { return quoteStatusMap[s] ?? { label: s, cls: 'bg-gray-100 text-gray-600' } }

const statCards = [
    { label: 'Utilisateurs',       key: 'users_count',    icon: Users,         color: 'text-blue-500 bg-blue-50' },
    { label: 'Commandes auj.',     key: 'orders_today',   icon: ShoppingBag,   color: 'text-[#F4620A] bg-[#F4620A]/10' },
    { label: "Revenus du mois",    key: 'revenue_month',  icon: TrendingUp,    color: 'text-emerald-600 bg-emerald-50', isPrice: true },
    { label: 'Devis nouveaux',     key: 'quotes_new',     icon: FileQuestion,  color: 'text-amber-600 bg-amber-50' },
]
</script>

<template>
    <Head title="Admin Dashboard — NETSPRING" />
    <AdminLayout>
        <template #page-title>Dashboard</template>

        <!-- Stats cards -->
        <section aria-labelledby="admin-stats-title" class="mb-8">
            <h2 id="admin-stats-title" class="sr-only">Statistiques globales</h2>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    v-for="card in statCards"
                    :key="card.key"
                    class="bg-white rounded-[14px] shadow-[0_4px_20px_rgba(0,0,0,0.07)] p-5 flex items-center gap-4"
                >
                    <div :class="['w-11 h-11 rounded-[10px] flex items-center justify-center flex-shrink-0', card.color]">
                        <component :is="card.icon" class="w-5 h-5" aria-hidden="true" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-0.5 truncate">{{ card.label }}</p>
                        <p class="font-heading font-bold text-lg text-[#0D0D0D] truncate">
                            {{ card.isPrice ? formatPrice(stats[card.key] ?? 0) : (stats[card.key] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-7">

            <!-- Recent orders -->
            <section aria-labelledby="admin-recent-orders">
                <h2 id="admin-recent-orders" class="font-heading font-bold text-base text-white mb-4">
                    Dernières commandes
                </h2>
                <div class="bg-white rounded-[14px] shadow-[0_4px_20px_rgba(0,0,0,0.07)] overflow-hidden">
                    <div v-if="!recentOrders.length" class="py-12 text-center text-gray-400 text-sm">
                        Aucune commande récente.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm" aria-label="Dernières commandes">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">N°</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Montant</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono font-bold text-[#0D0D0D] text-xs">#{{ order.order_number ?? order.id }}</td>
                                    <td class="px-4 py-3 text-gray-700 text-xs">{{ order.customer_name }}</td>
                                    <td class="px-4 py-3 font-bold text-[#F4620A] text-xs whitespace-nowrap">{{ formatPrice(order.total) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full" :class="orderBadge(order.status).cls">
                                            {{ orderBadge(order.status).label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ formatDate(order.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Recent quotes -->
            <section aria-labelledby="admin-recent-quotes">
                <h2 id="admin-recent-quotes" class="font-heading font-bold text-base text-white mb-4">
                    Derniers devis
                </h2>
                <div class="bg-white rounded-[14px] shadow-[0_4px_20px_rgba(0,0,0,0.07)] overflow-hidden">
                    <div v-if="!recentQuotes.length" class="py-12 text-center text-gray-400 text-sm">
                        Aucun devis récent.
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm" aria-label="Derniers devis">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nom</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produit</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Statut</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="quote in recentQuotes" :key="quote.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-semibold text-[#0D0D0D] text-xs">{{ quote.name }}</td>
                                    <td class="px-4 py-3 text-gray-500 text-xs max-w-[140px] truncate">{{ quote.product }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full" :class="quoteBadge(quote.status).cls">
                                            {{ quoteBadge(quote.status).label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ formatDate(quote.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
