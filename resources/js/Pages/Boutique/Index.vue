<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Search, Package, ShoppingCart, Eye, ChevronLeft, ChevronRight, SlidersHorizontal } from 'lucide-vue-next'

const props = defineProps({
    products:   { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
    categories: { type: Array,  default: () => [] },
    filters:    { type: Object, default: () => ({}) },
})

const search          = ref(props.filters?.search ?? '')
const activeCategory  = ref(props.filters?.categorie ?? '')

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function applyFilters() {
    router.get('/boutique', {
        search:    search.value || undefined,
        categorie: activeCategory.value || undefined,
    }, { preserveState: true, replace: true })
}

function selectCategory(slug) {
    activeCategory.value = activeCategory.value === slug ? '' : slug
    applyFilters()
}

function addToCart(product) {
    router.post('/panier/ajouter', { product_id: product.id, quantity: 1 }, {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Boutique — NETSPRING" />
    <AppLayout>

        <!-- HERO -->
        <section class="bg-[#0D0D0D] pt-16 pb-14 relative overflow-hidden" aria-labelledby="boutique-hero-title">
            <div class="absolute inset-0 bg-gradient-to-br from-[#F4620A]/10 via-transparent to-transparent pointer-events-none" aria-hidden="true" />
            <div class="max-w-5xl mx-auto px-6 text-center relative">
                <p class="text-[#F4620A] text-sm font-semibold uppercase tracking-widest mb-3">Boutique</p>
                <h1
                    id="boutique-hero-title"
                    class="font-heading font-bold text-4xl md:text-5xl text-white leading-tight mb-6"
                >
                    Découvrez nos produits
                </h1>
                <p class="text-gray-400 text-base max-w-xl mx-auto mb-10">
                    Produits sélectionnés directement depuis la Chine, livrés en Côte d'Ivoire.
                </p>

                <!-- Search bar -->
                <form
                    class="flex items-center gap-3 max-w-xl mx-auto bg-white/8 border border-white/15 rounded-[14px] px-4 py-3"
                    aria-label="Rechercher un produit"
                    @submit.prevent="applyFilters"
                >
                    <Search class="w-5 h-5 text-gray-400 flex-shrink-0" aria-hidden="true" />
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Rechercher un produit…"
                        class="flex-1 bg-transparent text-white placeholder-gray-500 text-sm focus:outline-none"
                        aria-label="Rechercher"
                    />
                    <button
                        type="submit"
                        class="bg-[#F4620A] hover:bg-[#d45208] text-white text-sm font-semibold px-4 py-2.5 min-h-[44px] rounded-[10px] transition-all duration-[220ms] cursor-pointer"
                        aria-label="Lancer la recherche"
                    >
                        Chercher
                    </button>
                </form>
            </div>
        </section>

        <!-- FILTERS & GRID -->
        <section class="max-w-7xl mx-auto px-6 py-12" aria-label="Produits et filtres">

            <!-- Category pills -->
            <div
                v-if="categories.length"
                class="flex flex-wrap gap-2 mb-10"
                role="group"
                aria-label="Filtrer par catégorie"
            >
                <button
                    class="flex items-center gap-2 px-4 py-3 min-h-[44px] rounded-full text-sm font-medium transition-all duration-[220ms] cursor-pointer"
                    :class="!activeCategory ? 'bg-[#F4620A] text-white shadow-[0_4px_12px_rgba(244,98,10,0.35)]' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#F4620A] hover:text-[#F4620A]'"
                    aria-label="Toutes les catégories"
                    @click="selectCategory('')"
                >
                    <SlidersHorizontal class="w-3.5 h-3.5" aria-hidden="true" />
                    Tous
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat.slug"
                    class="px-4 py-3 min-h-[44px] rounded-full text-sm font-medium transition-all duration-[220ms] cursor-pointer"
                    :class="activeCategory === cat.slug ? 'bg-[#F4620A] text-white shadow-[0_4px_12px_rgba(244,98,10,0.35)]' : 'bg-white border border-gray-200 text-gray-600 hover:border-[#F4620A] hover:text-[#F4620A]'"
                    :aria-label="`Catégorie ${cat.name}`"
                    :aria-pressed="activeCategory === cat.slug"
                    @click="selectCategory(cat.slug)"
                >
                    {{ cat.name }}
                </button>
            </div>

            <!-- Empty state -->
            <div
                v-if="!products.data || !products.data.length"
                class="flex flex-col items-center justify-center py-24 text-center"
                role="status"
                aria-live="polite"
            >
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-5">
                    <Package class="w-9 h-9 text-gray-300" aria-hidden="true" />
                </div>
                <h2 class="font-heading font-bold text-xl text-[#0D0D0D] mb-2">Aucun produit trouvé</h2>
                <p class="text-gray-500 text-sm max-w-xs">Essayez d'autres filtres ou revenez bientôt — nous ajoutons de nouveaux produits régulièrement.</p>
            </div>

            <!-- Products grid -->
            <div
                v-else
                class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-5"
                role="list"
                aria-label="Liste des produits"
            >
                <article
                    v-for="product in products.data"
                    :key="product.id"
                    class="card-lift bg-white rounded-[16px] shadow-[0_2px_12px_rgba(0,0,0,0.06)] overflow-hidden group"
                    role="listitem"
                >
                    <!-- Image / placeholder -->
                    <div class="relative aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                        <img
                            v-if="product.images && product.images[0]"
                            :src="product.images[0].url ?? product.images[0]"
                            :alt="product.images[0].alt ?? product.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[300ms]"
                            loading="lazy"
                        />
                        <Package v-else class="w-12 h-12 text-gray-300" aria-hidden="true" />

                        <!-- Badges -->
                        <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5" aria-label="Étiquettes produit">
                            <span
                                v-if="product.is_new"
                                class="bg-[#0D0D0D] text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider"
                            >NOUVEAU</span>
                            <span
                                v-if="product.compare_price"
                                class="bg-[#F4620A] text-white text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider"
                            >PROMO</span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-4">
                        <h3 class="font-heading font-bold text-[#0D0D0D] text-sm leading-snug mb-2 line-clamp-2">
                            {{ product.name }}
                        </h3>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-[#F4620A] font-bold text-base">{{ formatPrice(product.price) }}</span>
                            <span
                                v-if="product.compare_price"
                                class="text-gray-400 text-sm line-through"
                                aria-label="Prix barré"
                            >{{ formatPrice(product.compare_price) }}</span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button
                                class="btn-press flex-1 flex items-center justify-center gap-1.5 bg-[#F4620A] hover:bg-[#d45208] text-white text-xs font-semibold py-3 min-h-[44px] rounded-[10px] transition-colors duration-[220ms] cursor-pointer"
                                :aria-label="`Ajouter ${product.name} au panier`"
                                @click="addToCart(product)"
                            >
                                <ShoppingCart class="w-3.5 h-3.5" aria-hidden="true" />
                                Ajouter
                            </button>
                            <Link
                                :href="`/boutique/${product.slug}`"
                                class="w-11 h-11 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-[10px] transition-all duration-[220ms] flex-shrink-0"
                                :aria-label="`Voir les détails de ${product.name}`"
                            >
                                <Eye class="w-4 h-4 text-gray-600" aria-hidden="true" />
                            </Link>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Pagination -->
            <nav
                v-if="products.links && products.links.length > 3"
                class="mt-12 flex items-center justify-center gap-2"
                aria-label="Pagination des produits"
            >
                <template v-for="link in products.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="flex items-center justify-center w-9 h-9 rounded-[9px] text-sm font-medium transition-all duration-[220ms]"
                        :class="link.active
                            ? 'bg-[#F4620A] text-white shadow-[0_4px_12px_rgba(244,98,10,0.35)]'
                            : 'bg-white border border-gray-200 text-gray-600 hover:border-[#F4620A] hover:text-[#F4620A]'"
                        :aria-label="`Page ${link.label}`"
                        :aria-current="link.active ? 'page' : undefined"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="flex items-center justify-center w-9 h-9 rounded-[9px] text-sm text-gray-300 bg-gray-50"
                        aria-hidden="true"
                        v-html="link.label"
                    />
                </template>
            </nav>
        </section>
    </AppLayout>
</template>
