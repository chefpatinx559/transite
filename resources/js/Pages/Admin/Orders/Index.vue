<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Eye } from 'lucide-vue-next'

const props = defineProps({
    orders:  { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
})

const statusFilter = ref(props.filters?.status ?? '')

const statusMap = {
    '':          'Tous',
    pending:     'En attente',
    paid:        'Payé',
    processing:  'En cours',
    shipped:     'Expédié',
    delivered:   'Livré',
    cancelled:   'Annulé',
}

const statusBadgeMap = {
    pending:    'bg-gray-100 text-gray-600',
    paid:       'bg-emerald-100 text-emerald-700',
    processing: 'bg-blue-100 text-blue-700',
    shipped:    'bg-purple-100 text-purple-700',
    delivered:  'bg-emerald-200 text-emerald-800',
    cancelled:  'bg-red-100 text-red-600',
}

const paymentBadgeMap = {
    pending: 'bg-amber-100 text-amber-700',
    paid:    'bg-emerald-100 text-emerald-700',
    failed:  'bg-red-100 text-red-600',
}

function statusBadge(s) { return statusBadgeMap[s] ?? 'bg-gray-100 text-gray-600' }
function statusLabel(s) { return statusMap[s] ?? s }
function paymentBadge(s) { return paymentBadgeMap[s] ?? 'bg-gray-100 text-gray-600' }
function paymentLabel(s) {
    const map = { pending: 'En attente', paid: 'Payé', failed: 'Échec' }
    return map[s] ?? s
}

function applyFilter(val) {
    statusFilter.value = val
    router.get('/admin/commandes', { status: val || undefined }, {
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
    <Head title="Commandes — Admin NETSPRING" />
    <AdminLayout>
        <template #page-title>Commandes</template>

        <!-- Filters -->
        <div class="flex flex-wrap gap-2 mb-6" role="group" aria-label="Filtrer par statut">
            <button
                v-for="(label, key) in statusMap"
                :key="key"
                class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all duration-[220ms] cursor-pointer"
                :class="statusFilter === key
                    ? 'bg-[#F4620A] text-white'
                    : 'bg-white border border-gray-600/20 text-gray-400 hover:text-white hover:border-white/30'"
                :aria-pressed="statusFilter === key"
                :aria-label="`Filtrer : ${label}`"
                @click="applyFilter(key)"
            >
                {{ label }}
            </button>
        </div>

        <div class="bg-white rounded-[14px] shadow-[0_4px_20px_rgba(0,0,0,0.07)] overflow-hidden">

            <div v-if="!orders.data || !orders.data.length" class="py-20 text-center" role="status">
                <p class="text-gray-400 text-sm">Aucune commande trouvée.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm" aria-label="Liste des commandes">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N° Commande</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Paiement</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Détail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 font-mono font-bold text-[#0D0D0D] text-xs whitespace-nowrap">
                                #{{ order.order_number ?? order.id }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-[#0D0D0D] text-sm">{{ order.customer_name }}</p>
                                <p class="text-gray-400 text-xs">{{ order.customer_phone }}</p>
                            </td>
                            <td class="px-5 py-4 font-bold text-[#F4620A] text-sm whitespace-nowrap">{{ formatPrice(order.total) }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full" :class="paymentBadge(order.payment_status)">
                                    {{ paymentLabel(order.payment_status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full" :class="statusBadge(order.status)">
                                    {{ statusLabel(order.status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-sm whitespace-nowrap">{{ formatDate(order.created_at) }}</td>
                            <td class="px-5 py-4">
                                <Link
                                    :href="`/admin/commandes/${order.id}`"
                                    class="w-8 h-8 flex items-center justify-center rounded-[7px] text-gray-400 hover:text-[#F4620A] hover:bg-[#F4620A]/10 transition-all duration-[220ms]"
                                    :aria-label="`Voir la commande #${order.order_number ?? order.id}`"
                                >
                                    <Eye class="w-3.5 h-3.5" aria-hidden="true" />
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav
            v-if="orders.links && orders.links.length > 3"
            class="mt-6 flex items-center justify-center gap-2"
            aria-label="Pagination des commandes"
        >
            <template v-for="link in orders.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="flex items-center justify-center w-8 h-8 rounded-[8px] text-xs font-medium transition-all"
                    :class="link.active ? 'bg-[#F4620A] text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#F4620A]'"
                    :aria-current="link.active ? 'page' : undefined"
                    v-html="link.label"
                />
                <span v-else class="flex items-center justify-center w-8 h-8 text-xs text-gray-300" aria-hidden="true" v-html="link.label" />
            </template>
        </nav>
    </AdminLayout>
</template>
