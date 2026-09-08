<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ShoppingCart, Package, Trash2, ArrowLeft, CreditCard } from 'lucide-vue-next'

const props = defineProps({
    items:    { type: Array,  default: () => [] },
    total:    { type: Number, default: 0 },
    subtotal: { type: Number, default: 0 },
    shipping: { type: Number, default: 0 },
})

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

const quantities = ref(
    Object.fromEntries((props.items ?? []).map(item => [item.product_id, item.quantity]))
)

function updateQty(item) {
    const qty = Number(quantities.value[item.product_id])
    if (qty < 1) return
    router.patch(`/panier/${item.product_id}`, { quantity: qty }, {
        preserveScroll: true,
    })
}

function removeItem(item) {
    router.delete(`/panier/${item.product_id}`, { preserveScroll: true })
}
</script>

<template>
    <Head title="Mon panier — NETSPRING" />
    <AppLayout>

        <!-- Page header -->
        <section class="bg-[#0D0D0D] py-12" aria-labelledby="panier-title">
            <div class="max-w-6xl mx-auto px-6">
                <p class="text-[#F4620A] text-sm font-semibold uppercase tracking-widest mb-2">Boutique</p>
                <h1 id="panier-title" class="font-heading font-bold text-3xl md:text-4xl text-white">Mon panier</h1>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-6 py-12">

            <!-- EMPTY STATE -->
            <div
                v-if="!items || items.length === 0"
                class="flex flex-col items-center justify-center py-24 text-center"
                role="status"
                aria-live="polite"
            >
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <ShoppingCart class="w-10 h-10 text-gray-300" aria-hidden="true" />
                </div>
                <h2 class="font-heading font-bold text-2xl text-[#0D0D0D] mb-3">Votre panier est vide</h2>
                <p class="text-gray-500 text-base max-w-sm mb-8">
                    Vous n'avez aucun article dans votre panier. Découvrez nos produits.
                </p>
                <Link
                    href="/boutique"
                    class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-bold px-8 py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)]"
                    aria-label="Retourner à la boutique"
                >
                    <ArrowLeft class="w-4 h-4" aria-hidden="true" />
                    Voir la boutique
                </Link>
            </div>

            <!-- CART CONTENT -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10 items-start">

                <!-- Résumé — affiché en PREMIER sur mobile (order-first lg:order-last) -->
                <div class="order-first lg:order-last">
                    <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-6 lg:sticky lg:top-24">
                        <h2 class="font-heading font-bold text-[#0D0D0D] text-lg mb-6">Récapitulatif</h2>

                        <dl class="space-y-3 text-sm mb-6">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Sous-total</dt>
                                <dd class="font-medium text-[#0D0D0D]">{{ formatPrice(subtotal) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Livraison</dt>
                                <dd class="font-medium text-[#0D0D0D]">
                                    {{ shipping > 0 ? formatPrice(shipping) : 'À calculer' }}
                                </dd>
                            </div>
                            <div class="h-px bg-gray-100 my-3" role="separator" />
                            <div class="flex justify-between items-baseline pt-1">
                                <dt class="font-bold text-[#0D0D0D] text-base">Total</dt>
                                <dd class="font-bold text-[#F4620A] text-xl">{{ formatPrice(total) }}</dd>
                            </div>
                        </dl>

                        <Link
                            href="/checkout"
                            class="w-full flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-bold py-4 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_24px_rgba(244,98,10,0.45)] hover:-translate-y-px"
                            aria-label="Passer à la page de paiement"
                        >
                            <CreditCard class="w-4.5 h-4.5" aria-hidden="true" />
                            PASSER AU PAIEMENT
                        </Link>
                    </div>
                </div>

                <!-- Items table -->
                <div class="lg:col-span-2 order-last lg:order-first">
                    <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="font-heading font-bold text-[#0D0D0D] text-lg">
                                Articles ({{ items.length }})
                            </h2>
                        </div>

                        <ul role="list" aria-label="Articles dans le panier">
                            <li
                                v-for="item in items"
                                :key="item.product_id"
                                class="flex items-center gap-4 px-6 py-5 border-b border-gray-100 last:border-0"
                                role="listitem"
                            >
                                <!-- Image -->
                                <div class="w-16 h-16 bg-gray-100 rounded-[10px] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    <img
                                        v-if="item.image"
                                        :src="item.image"
                                        :alt="item.name"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                    <Package v-else class="w-7 h-7 text-gray-300" aria-hidden="true" />
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-[#0D0D0D] text-sm leading-snug line-clamp-2">{{ item.name }}</h3>
                                    <p class="text-[#F4620A] font-bold text-sm mt-0.5">{{ formatPrice(item.price) }}</p>
                                </div>

                                <!-- Quantity -->
                                <div class="flex items-center gap-0 border border-gray-200 rounded-[9px] overflow-hidden flex-shrink-0" role="group" :aria-label="`Quantité de ${item.name}`">
                                    <button
                                        class="w-11 h-11 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors cursor-pointer text-base font-bold"
                                        :disabled="quantities[item.product_id] <= 1"
                                        :aria-label="`Diminuer la quantité de ${item.name}`"
                                        @click="quantities[item.product_id] = Math.max(1, quantities[item.product_id] - 1); updateQty(item)"
                                    >−</button>
                                    <input
                                        v-model.number="quantities[item.product_id]"
                                        type="number"
                                        min="1"
                                        max="99"
                                        class="w-10 h-11 text-center text-[#0D0D0D] font-bold text-sm border-0 focus:outline-none focus:ring-0 bg-transparent"
                                        :aria-label="`Quantité de ${item.name}`"
                                        @change="updateQty(item)"
                                    />
                                    <button
                                        class="w-11 h-11 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors cursor-pointer text-base font-bold"
                                        :aria-label="`Augmenter la quantité de ${item.name}`"
                                        @click="quantities[item.product_id]++; updateQty(item)"
                                    >+</button>
                                </div>

                                <!-- Line total -->
                                <span class="font-bold text-[#0D0D0D] text-sm w-28 text-right flex-shrink-0 hidden sm:block">
                                    {{ formatPrice(item.price * quantities[item.product_id]) }}
                                </span>

                                <!-- Remove -->
                                <button
                                    class="w-11 h-11 flex items-center justify-center rounded-[8px] text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all duration-[220ms] cursor-pointer flex-shrink-0"
                                    :aria-label="`Supprimer ${item.name} du panier`"
                                    @click="removeItem(item)"
                                >
                                    <Trash2 class="w-4 h-4" aria-hidden="true" />
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Continue shopping -->
                    <div class="mt-5">
                        <Link
                            href="/boutique"
                            class="inline-flex items-center gap-2 text-gray-500 hover:text-[#F4620A] text-sm font-medium transition-colors duration-[220ms]"
                            aria-label="Continuer les achats"
                        >
                            <ArrowLeft class="w-4 h-4" aria-hidden="true" />
                            Continuer les achats
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
