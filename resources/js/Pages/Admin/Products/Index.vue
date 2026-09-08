<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { PlusCircle, Pencil, Trash2, Package } from 'lucide-vue-next'

const props = defineProps({
    products: { type: Object, default: () => ({ data: [], links: [] }) },
})

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function togglePublished(product) {
    router.patch(`/admin/produits/${product.id}`, {
        is_published: !product.is_published,
    }, { preserveScroll: true })
}

function confirmDelete(product) {
    if (confirm(`Supprimer le produit "${product.name}" ? Cette action est irréversible.`)) {
        router.delete(`/admin/produits/${product.id}`, { preserveScroll: true })
    }
}
</script>

<template>
    <Head title="Produits — Admin NETSPRING" />
    <AdminLayout>
        <template #page-title>Produits</template>
        <template #header-actions>
            <Link
                href="/admin/produits/creer"
                class="flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white text-sm font-semibold px-4 py-2 rounded-[9px] transition-all duration-[220ms]"
                aria-label="Créer un nouveau produit"
            >
                <PlusCircle class="w-4 h-4" aria-hidden="true" />
                Nouveau produit
            </Link>
        </template>

        <div class="bg-white rounded-[14px] shadow-[0_4px_20px_rgba(0,0,0,0.07)] overflow-hidden">

            <!-- Empty -->
            <div v-if="!products.data || !products.data.length" class="py-20 text-center" role="status">
                <Package class="w-10 h-10 text-gray-200 mx-auto mb-3" aria-hidden="true" />
                <p class="text-gray-400 text-sm">Aucun produit pour le moment.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm" aria-label="Liste des produits">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produit</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Prix</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Publié</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 transition-colors">

                            <!-- Product with image -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-[8px] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                        <img
                                            v-if="product.image"
                                            :src="product.image"
                                            :alt="product.name"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        />
                                        <Package v-else class="w-4.5 h-4.5 text-gray-300" aria-hidden="true" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-[#0D0D0D] text-sm truncate max-w-[200px]">{{ product.name }}</p>
                                        <p class="text-gray-400 text-xs">{{ product.category?.name ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 font-bold text-[#F4620A] text-sm whitespace-nowrap">{{ formatPrice(product.price) }}</td>

                            <!-- Stock badge -->
                            <td class="px-5 py-4">
                                <span
                                    class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full"
                                    :class="product.stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600'"
                                >
                                    {{ product.stock > 0 ? `${product.stock} en stock` : 'Rupture' }}
                                </span>
                            </td>

                            <!-- Published toggle -->
                            <td class="px-5 py-4">
                                <button
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-[220ms] cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:ring-offset-1"
                                    :class="product.is_published ? 'bg-[#F4620A]' : 'bg-gray-200'"
                                    :aria-label="`${product.is_published ? 'Masquer' : 'Publier'} ${product.name}`"
                                    :aria-pressed="product.is_published"
                                    @click="togglePublished(product)"
                                >
                                    <span
                                        class="inline-block h-3.5 w-3.5 rounded-full bg-white shadow transform transition-transform duration-[220ms]"
                                        :class="product.is_published ? 'translate-x-4' : 'translate-x-0.5'"
                                        aria-hidden="true"
                                    />
                                </button>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="`/admin/produits/${product.id}/modifier`"
                                        class="w-8 h-8 flex items-center justify-center rounded-[7px] text-gray-400 hover:text-[#F4620A] hover:bg-[#F4620A]/10 transition-all duration-[220ms]"
                                        :aria-label="`Modifier ${product.name}`"
                                    >
                                        <Pencil class="w-3.5 h-3.5" aria-hidden="true" />
                                    </Link>
                                    <button
                                        class="w-8 h-8 flex items-center justify-center rounded-[7px] text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-[220ms] cursor-pointer"
                                        :aria-label="`Supprimer ${product.name}`"
                                        @click="confirmDelete(product)"
                                    >
                                        <Trash2 class="w-3.5 h-3.5" aria-hidden="true" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav
            v-if="products.links && products.links.length > 3"
            class="mt-6 flex items-center justify-center gap-2"
            aria-label="Pagination des produits"
        >
            <template v-for="link in products.links" :key="link.label">
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
