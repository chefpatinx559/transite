<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Package, ShoppingCart, MessageCircle, CheckCircle, AlertCircle, ChevronLeft, Eye } from 'lucide-vue-next'

const props = defineProps({
    product: { type: Object, required: true },
    related: { type: Array,  default: () => [] },
})

const quantity    = ref(1)
const addingCart  = ref(false)
const activeImage = ref(props.product.images?.[0] ?? null)

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function increment() { if (quantity.value < 10) quantity.value++ }
function decrement() { if (quantity.value > 1)  quantity.value-- }

function addToCart() {
    addingCart.value = true
    router.post('/panier/ajouter', { product_id: props.product.id, quantity: quantity.value }, {
        preserveScroll: true,
        onFinish: () => { addingCart.value = false },
    })
}

function whatsappOrder() {
    const text = encodeURIComponent(
        `Bonjour NETSPRING, je souhaite commander : ${props.product.name} (x${quantity.value}) — ${formatPrice(props.product.price * quantity.value)}`
    )
    window.open(`https://wa.me/2250594429552?text=${text}`, '_blank', 'noopener')
}

function addRelatedToCart(product) {
    router.post('/panier/ajouter', { product_id: product.id, quantity: 1 }, { preserveScroll: true })
}
</script>

<template>
    <Head :title="`${product.name} — Boutique NETSPRING`">
        <meta name="description" :content="product.short_description || `Achetez ${product.name} sur NETSPRING — Livraison en Côte d'Ivoire depuis la Chine.`" />
        <meta property="og:title" :content="`${product.name} — NETSPRING`" />
        <meta property="og:description" :content="product.short_description || `${product.name} disponible sur NETSPRING.`" />
        <meta property="og:type" content="product" />
        <meta property="og:image" :content="product.images?.[0]?.url ?? product.images?.[0] ?? ''" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="product.name" />
        <meta name="twitter:image" :content="product.images?.[0]?.url ?? product.images?.[0] ?? ''" />
    </Head>
    <AppLayout>

        <!-- Breadcrumb -->
        <nav class="max-w-7xl mx-auto px-6 py-4" aria-label="Fil d'Ariane">
            <ol class="flex items-center gap-2 text-sm text-gray-500">
                <li><Link href="/" class="hover:text-[#F4620A] transition-colors">Accueil</Link></li>
                <li aria-hidden="true" class="text-gray-300">/</li>
                <li><Link href="/boutique" class="hover:text-[#F4620A] transition-colors">Boutique</Link></li>
                <li aria-hidden="true" class="text-gray-300">/</li>
                <li class="text-[#0D0D0D] font-medium truncate max-w-[200px]" aria-current="page">{{ product.name }}</li>
            </ol>
        </nav>

        <!-- MAIN PRODUCT -->
        <section class="max-w-7xl mx-auto px-6 pb-16" aria-labelledby="product-title">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

                <!-- LEFT — Image gallery -->
                <div class="space-y-4">
                    <!-- Image principale avec crossfade -->
                    <div class="aspect-square bg-gray-100 rounded-[16px] overflow-hidden shadow-[0_8px_32px_rgba(0,0,0,0.09)] relative">
                        <Transition name="img">
                            <img
                                v-if="activeImage"
                                :key="activeImage.url ?? activeImage"
                                :src="activeImage.url ?? activeImage"
                                :alt="activeImage.alt ?? product.name"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                        </Transition>
                        <Package v-if="!activeImage" class="absolute inset-0 m-auto w-20 h-20 text-gray-300" aria-hidden="true" />
                    </div>

                    <!-- Thumbnails interactives -->
                    <div v-if="product.images && product.images.length > 1" class="flex gap-3 overflow-x-auto pb-1">
                        <button
                            v-for="(img, idx) in product.images"
                            :key="idx"
                            class="flex-shrink-0 w-16 h-16 rounded-[10px] overflow-hidden border-2 transition-all duration-[220ms] cursor-pointer"
                            :class="activeImage === img
                                ? 'border-[#F4620A] shadow-[0_0_0_2px_rgba(244,98,10,0.2)]'
                                : 'border-transparent hover:border-[#F4620A]/60'"
                            :aria-label="`Image produit ${idx + 1}`"
                            @click="activeImage = img"
                        >
                            <img :src="img.url ?? img" :alt="img.alt ?? `${product.name} vue ${idx + 1}`" class="w-full h-full object-cover" loading="lazy" />
                        </button>
                    </div>
                </div>

                <!-- RIGHT — Product info -->
                <div class="space-y-6">

                    <!-- Category badge -->
                    <span
                        v-if="product.category"
                        class="inline-block bg-[#F4620A]/10 text-[#F4620A] text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider"
                    >
                        {{ product.category.name }}
                    </span>

                    <!-- Title -->
                    <h1
                        id="product-title"
                        class="font-heading font-bold text-3xl md:text-4xl text-[#0D0D0D] leading-tight"
                    >
                        {{ product.name }}
                    </h1>

                    <!-- Prices -->
                    <div class="flex items-baseline gap-4">
                        <span class="text-[#F4620A] font-bold text-3xl">{{ formatPrice(product.price) }}</span>
                        <span
                            v-if="product.compare_price"
                            class="text-gray-400 text-xl line-through"
                            aria-label="Prix original avant remise"
                        >{{ formatPrice(product.compare_price) }}</span>
                        <span
                            v-if="product.compare_price"
                            class="bg-[#F4620A] text-white text-xs font-bold px-2 py-0.5 rounded-full"
                            aria-label="En promotion"
                        >PROMO</span>
                    </div>

                    <!-- Stock -->
                    <div class="flex items-center gap-2">
                        <CheckCircle v-if="product.stock > 0" class="w-4.5 h-4.5 text-emerald-500" aria-hidden="true" />
                        <AlertCircle v-else class="w-4.5 h-4.5 text-red-500" aria-hidden="true" />
                        <span
                            class="text-sm font-medium"
                            :class="product.stock > 0 ? 'text-emerald-600' : 'text-red-500'"
                        >
                            {{ product.stock > 0 ? `En stock (${product.stock} disponibles)` : 'Rupture de stock' }}
                        </span>
                    </div>

                    <!-- Short description -->
                    <p v-if="product.short_description" class="text-gray-600 text-base leading-relaxed">
                        {{ product.short_description }}
                    </p>

                    <!-- Quantity selector -->
                    <div>
                        <label class="block text-sm font-semibold text-[#0D0D0D] mb-2">Quantité</label>
                        <div class="flex items-center gap-0 border border-gray-200 rounded-[12px] w-fit overflow-hidden" role="group" aria-label="Sélectionner la quantité">
                            <button
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer font-bold text-lg"
                                :disabled="quantity <= 1"
                                aria-label="Diminuer la quantité"
                                @click="decrement"
                            >−</button>
                            <span
                                class="w-12 text-center text-[#0D0D0D] font-bold text-sm"
                                aria-live="polite"
                                :aria-label="`Quantité : ${quantity}`"
                            >{{ quantity }}</span>
                            <button
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer font-bold text-lg"
                                :disabled="quantity >= 10"
                                aria-label="Augmenter la quantité"
                                @click="increment"
                            >+</button>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button
                            class="flex-1 flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_24px_rgba(244,98,10,0.45)] hover:-translate-y-px active:translate-y-0 cursor-pointer"
                            :disabled="product.stock === 0 || addingCart"
                            :aria-label="`Ajouter ${quantity} exemplaire(s) de ${product.name} au panier`"
                            @click="addToCart"
                        >
                            <ShoppingCart class="w-4.5 h-4.5" aria-hidden="true" />
                            {{ addingCart ? 'Ajout…' : 'Ajouter au panier' }}
                        </button>
                        <button
                            class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] cursor-pointer"
                            aria-label="Commander via WhatsApp"
                            @click="whatsappOrder"
                        >
                            <MessageCircle class="w-4.5 h-4.5" aria-hidden="true" />
                            Commander via WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- DESCRIPTION LONGUE -->
        <section
            v-if="product.description"
            class="max-w-7xl mx-auto px-6 pb-16"
            aria-labelledby="product-desc-title"
        >
            <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-8">
                <h2 id="product-desc-title" class="font-heading font-bold text-xl text-[#0D0D0D] mb-6">
                    Description du produit
                </h2>
                <div
                    class="prose prose-gray max-w-none text-gray-600 leading-relaxed"
                    v-html="product.description"
                />
            </div>
        </section>

        <!-- PRODUITS SIMILAIRES -->
        <section
            v-if="related.length"
            class="max-w-7xl mx-auto px-6 pb-20"
            aria-labelledby="related-title"
        >
            <h2 id="related-title" class="font-heading font-bold text-2xl text-[#0D0D0D] mb-8">
                Produits similaires
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5" role="list">
                <article
                    v-for="item in related.slice(0, 4)"
                    :key="item.id"
                    class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] overflow-hidden group hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(0,0,0,0.14)] transition-all duration-[220ms]"
                    role="listitem"
                >
                    <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                        <img
                            v-if="item.image"
                            :src="item.image"
                            :alt="item.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[300ms]"
                            loading="lazy"
                        />
                        <Package v-else class="w-10 h-10 text-gray-300" aria-hidden="true" />
                    </div>
                    <div class="p-4">
                        <h3 class="font-heading font-bold text-[#0D0D0D] text-sm mb-2 line-clamp-2">{{ item.name }}</h3>
                        <div class="flex items-baseline gap-2 mb-3">
                            <span class="text-[#F4620A] font-bold text-sm">{{ formatPrice(item.price) }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="flex-1 flex items-center justify-center gap-1.5 bg-[#F4620A] hover:bg-[#d45208] text-white text-xs font-semibold py-2 rounded-[9px] transition-all duration-[220ms] cursor-pointer"
                                :aria-label="`Ajouter ${item.name} au panier`"
                                @click="addRelatedToCart(item)"
                            >
                                <ShoppingCart class="w-3.5 h-3.5" aria-hidden="true" />
                                Ajouter
                            </button>
                            <Link
                                :href="`/boutique/${item.slug}`"
                                class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-[9px] transition-all duration-[220ms]"
                                :aria-label="`Voir ${item.name}`"
                            >
                                <Eye class="w-3.5 h-3.5 text-gray-600" aria-hidden="true" />
                            </Link>
                        </div>
                    </div>
                </article>
            </div>
        </section>

    </AppLayout>
</template>
