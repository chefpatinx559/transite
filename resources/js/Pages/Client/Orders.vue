<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ShoppingBag, Filter } from 'lucide-vue-next'

const props = defineProps({
    orders:  { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
})

const statusFilter = ref(props.filters?.status ?? '')

const statusMap = {
    '':          { label: 'Tous',       cls: '' },
    pending:     { label: 'En attente', cls: 'bg-gray-100 text-gray-600' },
    paid:        { label: 'Payé',       cls: 'bg-emerald-100 text-emerald-700' },
    processing:  { label: 'En cours',   cls: 'bg-blue-100 text-blue-700' },
    shipped:     { label: 'Expédié',    cls: 'bg-purple-100 text-purple-700' },
    delivered:   { label: 'Livré',      cls: 'bg-emerald-200 text-emerald-800' },
    cancelled:   { label: 'Annulé',     cls: 'bg-red-100 text-red-600' },
}

function statusBadge(status) {
    return statusMap[status] ?? { label: status, cls: 'bg-gray-100 text-gray-600' }
}

function applyFilter(val) {
    statusFilter.value = val
    router.get('/client/commandes', { status: val || undefined }, {
        preserveState: true, replace: true
    })
}

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function formatDate(val) {
    if (!val) return '—'
    return new Date(val).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })
}
</script>

<template>
    <Head title="Mes commandes — NETSPRING" />
    <ClientLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
            <div>
                <h1 class="font-heading font-bold text-2xl text-[#0D0D0D]">Mes commandes</h1>
                <p class="text-gray-500 text-sm mt-0.5">Retrouvez l'historique de toutes vos commandes.</p>
            </div>
        </div>

        <!-- Status filters -->
        <div class="flex flex-wrap gap-2 mb-6" role="group" aria-label="Filtrer les commandes par statut">
            <button
                v-for="(info, key) in statusMap"
                :key="key"
                class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-[220ms] cursor-pointer"
                :class="statusFilter === key
                    ? 'bg-[#F4620A] text-white shadow-[0_4px_12px_rgba(244,98,10,0.3)]'
                    : 'bg-white border border-gray-200 text-gray-600 hover:border-[#F4620A] hover:text-[#F4620A]'"
                :aria-pressed="statusFilter === key"
                :aria-label="`Filtrer : ${info.label}`"
                @click="applyFilter(key)"
            >
                {{ info.label }}
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] overflow-hidden">

            <!-- Empty -->
            <div
                v-if="!orders.data || !orders.data.length"
                class="py-20 text-center"
                role="status"
                aria-live="polite"
            >
                <ShoppingBag class="w-12 h-12 text-gray-200 mx-auto mb-4" aria-hidden="true" />
                <h2 class="font-heading font-bold text-lg text-[#0D0D0D] mb-2">Aucune commande</h2>
                <p class="text-gray-400 text-sm mb-6">Vous n'avez pas encore passé de commande.</p>
                <Link
                    href="/boutique"
                    class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-6 py-2.5 rounded-[10px] transition-all duration-[220ms]"
                    aria-label="Aller à la boutique"
                >
                    Voir la boutique
                </Link>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm" aria-label="Liste de mes commandes">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th scope="col" class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N° Commande</th>
                            <th scope="col" class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Articles</th>
                            <th scope="col" class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="order in orders.data"
                            :key="order.id"
                            class="hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-5 py-4 font-mono font-bold text-[#0D0D0D] text-xs whitespace-nowrap">
                                #{{ order.order_number ?? order.id }}
                            </td>
                            <td class="px-5 py-4 text-gray-500 whitespace-nowrap">{{ formatDate(order.created_at) }}</td>
                            <td class="px-5 py-4 text-gray-500">{{ order.items_count ?? '—' }} article(s)</td>
                            <td class="px-5 py-4 font-bold text-[#F4620A] whitespace-nowrap">{{ formatPrice(order.total) }}</td>
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

        <!-- Pagination -->
        <nav
            v-if="orders.links && orders.links.length > 3"
            class="mt-8 flex items-center justify-center gap-2"
            aria-label="Pagination des commandes"
        >
            <template v-for="link in orders.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="flex items-center justify-center w-9 h-9 rounded-[9px] text-sm font-medium transition-all duration-[220ms]"
                    :class="link.active
                        ? 'bg-[#F4620A] text-white'
                        : 'bg-white border border-gray-200 text-gray-600 hover:border-[#F4620A] hover:text-[#F4620A]'"
                    :aria-current="link.active ? 'page' : undefined"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="flex items-center justify-center w-9 h-9 text-sm text-gray-300"
                    aria-hidden="true"
                    v-html="link.label"
                />
            </template>
        </nav>
    </ClientLayout>
</template>
