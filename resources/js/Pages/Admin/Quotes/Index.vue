<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Eye } from 'lucide-vue-next'

const props = defineProps({
    quotes:  { type: Object, default: () => ({ data: [], links: [] }) },
    filters: { type: Object, default: () => ({}) },
})

const statusFilter = ref(props.filters?.status ?? '')

const statusMap = {
    '':        'Tous',
    new:       'Nouveau',
    in_review: 'En révision',
    quoted:    'Devis envoyé',
    won:       'Accepté',
    lost:      'Perdu',
}

const statusBadgeMap = {
    new:       'bg-blue-100 text-blue-700',
    in_review: 'bg-amber-100 text-amber-700',
    quoted:    'bg-purple-100 text-purple-700',
    won:       'bg-emerald-100 text-emerald-700',
    lost:      'bg-red-100 text-red-600',
}

function statusBadge(s) { return statusBadgeMap[s] ?? 'bg-gray-100 text-gray-600' }
function statusLabel(s) { return statusMap[s] ?? s }

function applyFilter(val) {
    statusFilter.value = val
    router.get('/admin/devis', { status: val || undefined }, {
        preserveState: true, replace: true
    })
}

function formatDate(val) {
    if (!val) return '—'
    return new Date(val).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })
}

function truncate(str, n = 50) {
    if (!str) return '—'
    return str.length > n ? str.slice(0, n) + '…' : str
}
</script>

<template>
    <Head title="Devis — Admin NETSPRING" />
    <AdminLayout>
        <template #page-title>Devis</template>

        <!-- Filters -->
        <div class="flex flex-wrap gap-2 mb-6" role="group" aria-label="Filtrer les devis par statut">
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

            <div v-if="!quotes.data || !quotes.data.length" class="py-20 text-center" role="status">
                <p class="text-gray-400 text-sm">Aucun devis trouvé.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm" aria-label="Liste des devis">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">WhatsApp</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produit</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Détail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="quote in quotes.data" :key="quote.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 font-semibold text-[#0D0D0D] text-sm whitespace-nowrap">{{ quote.name }}</td>
                            <td class="px-5 py-4 text-gray-600 text-sm">{{ quote.email }}</td>
                            <td class="px-5 py-4 text-gray-600 text-sm whitespace-nowrap">{{ quote.whatsapp }}</td>
                            <td class="px-5 py-4 text-gray-500 text-sm max-w-[180px]">
                                <span :title="quote.product" class="block truncate">{{ truncate(quote.product) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full" :class="statusBadge(quote.status)">
                                    {{ statusLabel(quote.status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-sm whitespace-nowrap">{{ formatDate(quote.created_at) }}</td>
                            <td class="px-5 py-4">
                                <Link
                                    :href="`/admin/devis/${quote.id}`"
                                    class="w-8 h-8 flex items-center justify-center rounded-[7px] text-gray-400 hover:text-[#F4620A] hover:bg-[#F4620A]/10 transition-all duration-[220ms]"
                                    :aria-label="`Voir le devis de ${quote.name}`"
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
            v-if="quotes.links && quotes.links.length > 3"
            class="mt-6 flex items-center justify-center gap-2"
            aria-label="Pagination des devis"
        >
            <template v-for="link in quotes.links" :key="link.label">
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
