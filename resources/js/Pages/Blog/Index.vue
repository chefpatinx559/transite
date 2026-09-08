<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ArrowRight, ChevronRight, Clock, Eye, Tag, Image as ImageIcon } from 'lucide-vue-next'

const props = defineProps({
    posts:          { type: Object,  required: true },
    featured:       { type: Object,  default: null  },
    categories:     { type: Array,   default: () => [] },
    activeCategory: { type: String,  default: null  },
})

function filterByCategory(slug) {
    router.get('/blog', slug ? { categorie: slug } : {}, { preserveState: true, replace: true })
}

function formatDate(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: 'numeric', month: 'long', year: 'numeric',
    })
}

const DEFAULT_COVER = 'https://static.vecteezy.com/system/resources/previews/027/484/654/large_2x/global-business-logistic-and-transportation-import-export-goods-container-cargo-freight-ship-at-international-port-cargo-plane-flying-above-truck-shipping-container-logistic-industry-generative-ai-photo.jpg'

function coverUrl(path) {
    if (!path) return DEFAULT_COVER
    if (path.startsWith('http')) return path
    return '/storage/' + path
}
</script>

<template>
    <Head title="Blog — Conseils importation & commerce Chine-CI" />
    <AppLayout>

    <!-- ── HERO ──────────────────────────────────── -->
    <section class="bg-[#0D0D0D] py-20 relative overflow-hidden" aria-labelledby="blog-page-heading">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 right-1/4 w-96 h-96 bg-[#F4620A]/6 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">BLOG NETSPRING</p>
            <h1 id="blog-page-heading" class="font-heading font-bold text-white mb-5" style="font-size:clamp(2rem,4vw,3rem);">
                Conseils, stratégies et opportunités.
            </h1>
            <p class="text-gray-400 text-lg max-w-xl mx-auto leading-[1.75]">
                Tout ce qu'il faut savoir pour réussir votre importation depuis la Chine vers la Côte d'Ivoire.
            </p>
        </div>
    </section>

    <div class="bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 py-16">

            <!-- ── ARTICLE À LA UNE ─────────────────── -->
            <section v-if="featured && !activeCategory" aria-labelledby="featured-heading" class="mb-16">
                <h2 id="featured-heading" class="font-heading font-semibold text-xs text-[#F4620A] tracking-[0.18em] uppercase mb-6">
                    À LA UNE
                </h2>
                <Link
                    :href="`/blog/${featured.slug}`"
                    class="group grid lg:grid-cols-2 gap-0 rounded-[20px] overflow-hidden border border-[#E5E7EB] hover:border-[#F4620A]/20 hover:shadow-[0_12px_40px_rgba(0,0,0,0.1)] transition-all duration-[220ms]"
                    :aria-label="`Lire l'article à la une : ${featured.title}`"
                >
                    <!-- Image -->
                    <div class="aspect-[16/9] lg:aspect-auto lg:min-h-[320px] bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                        <img
                            :src="coverUrl(featured.cover_image)"
                            :alt="featured.title"
                            class="absolute inset-0 w-full h-full object-cover"
                            loading="lazy"
                        />
                        <!-- Overlay + badge catégorie -->
                        <div class="absolute inset-0 bg-gradient-to-br from-[#0D0D0D]/60 to-transparent flex flex-col justify-end p-8">
                            <span v-if="featured.category"
                                  class="inline-flex items-center gap-1.5 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-3 w-fit"
                                  :style="{ backgroundColor: featured.category.color || '#F4620A' }"
                            >
                                <Tag class="w-3 h-3" aria-hidden="true" />
                                {{ featured.category.name }}
                            </span>
                        </div>
                    </div>
                    <!-- Contenu -->
                    <div class="p-10 flex flex-col justify-center bg-white">
                        <div class="flex items-center gap-4 text-xs text-gray-400 mb-4">
                            <span class="flex items-center gap-1.5">
                                <Clock class="w-3.5 h-3.5" aria-hidden="true" />
                                {{ featured.reading_time }} min de lecture
                            </span>
                            <span class="flex items-center gap-1.5">
                                <Eye class="w-3.5 h-3.5" aria-hidden="true" />
                                {{ featured.views_count }} vues
                            </span>
                            <time>{{ formatDate(featured.published_at) }}</time>
                        </div>
                        <h3 class="font-heading font-bold text-[#0D0D0D] text-2xl leading-snug mb-4 group-hover:text-[#F4620A] transition-colors duration-[150ms]">
                            {{ featured.title }}
                        </h3>
                        <p class="text-gray-500 leading-[1.75] mb-6 text-sm line-clamp-3">{{ featured.excerpt }}</p>
                        <span class="inline-flex items-center gap-2 text-[#F4620A] font-semibold text-sm group-hover:gap-3 transition-all duration-[220ms]" aria-hidden="true">
                            Lire l'article
                            <ArrowRight class="w-4 h-4" />
                        </span>
                    </div>
                </Link>
            </section>

            <!-- ── FILTRES CATÉGORIES ───────────────── -->
            <div class="mb-10 flex flex-wrap gap-2" role="group" aria-label="Filtrer les articles par catégorie">
                <button
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-[150ms] cursor-pointer"
                    :class="!activeCategory
                        ? 'bg-[#0D0D0D] text-white shadow-sm'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    :aria-pressed="!activeCategory"
                    @click="filterByCategory(null)"
                >
                    Tous les articles
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat.slug"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-[150ms] cursor-pointer"
                    :class="activeCategory === cat.slug
                        ? 'text-white shadow-sm'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    :style="activeCategory === cat.slug ? { backgroundColor: cat.color } : {}"
                    :aria-pressed="activeCategory === cat.slug"
                    @click="filterByCategory(cat.slug)"
                >
                    {{ cat.name }}
                    <span v-if="cat.posts_count" class="ml-1 opacity-70 text-xs">({{ cat.posts_count }})</span>
                </button>
            </div>

            <!-- ── GRILLE ARTICLES ─────────────────── -->
            <section aria-labelledby="articles-heading">
                <h2 id="articles-heading" class="sr-only">
                    {{ activeCategory ? 'Articles filtrés' : 'Derniers articles' }}
                </h2>

                <!-- Empty state -->
                <div v-if="posts.data.length === 0" class="text-center py-20">
                    <p class="text-gray-400 text-lg">Aucun article dans cette catégorie pour le moment.</p>
                    <button @click="filterByCategory(null)" class="mt-4 text-[#F4620A] font-semibold hover:underline cursor-pointer">
                        Voir tous les articles
                    </button>
                </div>

                <ul v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" role="list">
                    <li v-for="post in posts.data" :key="post.id">
                        <article class="group bg-white rounded-[16px] overflow-hidden border border-[#E5E7EB] hover:border-[#F4620A]/20 hover:shadow-[0_8px_32px_rgba(0,0,0,0.09)] transition-all duration-[220ms] h-full flex flex-col">
                            <!-- Image -->
                            <div class="aspect-[16/9] bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden flex-shrink-0">
                                <img
                                    :src="coverUrl(post.cover_image)"
                                    :alt="post.title"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                <div class="absolute bottom-3 left-3">
                                    <span v-if="post.category"
                                          class="text-white text-xs font-semibold px-2.5 py-1 rounded-full"
                                          :style="{ backgroundColor: post.category.color || '#F4620A' }"
                                    >{{ post.category.name }}</span>
                                </div>
                            </div>
                            <!-- Contenu -->
                            <div class="p-6 flex flex-col flex-1">
                                <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                                    <time>{{ formatDate(post.published_at) }}</time>
                                    <span aria-hidden="true">·</span>
                                    <span class="flex items-center gap-1">
                                        <Clock class="w-3 h-3" aria-hidden="true" />
                                        {{ post.reading_time }} min
                                    </span>
                                </div>
                                <h3 class="font-heading font-semibold text-[#0D0D0D] text-base leading-snug mb-3 flex-1 group-hover:text-[#F4620A] transition-colors duration-[150ms]">
                                    {{ post.title }}
                                </h3>
                                <p class="text-gray-500 text-sm leading-[1.7] mb-5 line-clamp-2">{{ post.excerpt }}</p>
                                <Link
                                    :href="`/blog/${post.slug}`"
                                    class="text-[#F4620A] text-sm font-semibold flex items-center gap-1.5 group-hover:gap-2.5 transition-all duration-[220ms]"
                                    :aria-label="`Lire : ${post.title}`"
                                >
                                    Lire l'article
                                    <ChevronRight class="w-4 h-4" aria-hidden="true" />
                                </Link>
                            </div>
                        </article>
                    </li>
                </ul>

                <!-- Pagination -->
                <nav v-if="posts.last_page > 1" class="mt-12 flex items-center justify-center gap-2" aria-label="Pagination">
                    <Link
                        v-if="posts.prev_page_url"
                        :href="posts.prev_page_url"
                        class="px-4 py-2 rounded-lg border border-[#E5E7EB] text-sm font-medium text-gray-700 hover:border-[#F4620A]/30 hover:text-[#F4620A] transition-all"
                        aria-label="Page précédente"
                    >
                        ← Précédent
                    </Link>
                    <span class="text-sm text-gray-500 px-3">
                        Page {{ posts.current_page }} / {{ posts.last_page }}
                    </span>
                    <Link
                        v-if="posts.next_page_url"
                        :href="posts.next_page_url"
                        class="px-4 py-2 rounded-lg border border-[#E5E7EB] text-sm font-medium text-gray-700 hover:border-[#F4620A]/30 hover:text-[#F4620A] transition-all"
                        aria-label="Page suivante"
                    >
                        Suivant →
                    </Link>
                </nav>
            </section>
        </div>
    </div>

    <!-- ── CTA ──────────────────────────────────── -->
    <section class="py-16 bg-[#F4620A]" aria-label="Démarrez votre projet avec NETSPRING">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="font-heading font-bold text-white text-2xl mb-3">
                Prêt à passer à l'action ?
            </h2>
            <p class="text-orange-100 mb-7">NETSPRING vous accompagne de la recherche fournisseur à la livraison en CI.</p>
            <a
                href="https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20demander%20un%20devis."
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-2 bg-white text-[#F4620A] font-bold px-7 py-3.5 rounded-[12px] hover:bg-orange-50 transition-all duration-[220ms] shadow-[0_4px_20px_rgba(0,0,0,0.1)] cursor-pointer"
                aria-label="Demander un devis sur WhatsApp"
            >
                Demander un devis
                <ArrowRight class="w-5 h-5" aria-hidden="true" />
            </a>
        </div>
    </section>

    </AppLayout>
</template>
