<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ArrowLeft, ArrowRight, Clock, Eye, Tag, ChevronRight, Share2, Calendar, Image as ImageIcon } from 'lucide-vue-next'

const props = defineProps({
    post:    { type: Object, required: true },
    related: { type: Array,  default: () => [] },
})

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

function share() {
    if (navigator.share) {
        navigator.share({ title: props.post.title, url: window.location.href })
    } else {
        navigator.clipboard.writeText(window.location.href)
    }
}
</script>

<template>
    <Head :title="`${post.title} — Blog NETSPRING`">
        <meta name="description" :content="post.excerpt || `Article du blog NETSPRING : ${post.title}`" />
        <meta property="og:title" :content="`${post.title} — Blog NETSPRING`" />
        <meta property="og:description" :content="post.excerpt || post.title" />
        <meta property="og:type" content="article" />
        <meta property="og:image" :content="coverUrl(post.cover_image)" />
        <meta name="twitter:title" :content="post.title" />
        <meta name="twitter:description" :content="post.excerpt || post.title" />
        <meta name="twitter:image" :content="coverUrl(post.cover_image)" />
    </Head>
    <AppLayout>

    <!-- ── HERO ARTICLE ───────────────────────── -->
    <section class="bg-[#0D0D0D] py-16" aria-labelledby="article-heading">
        <div class="max-w-4xl mx-auto px-6">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8" aria-label="Fil d'Ariane">
                <Link href="/" class="hover:text-[#F4620A] transition-colors">Accueil</Link>
                <ChevronRight class="w-3.5 h-3.5" aria-hidden="true" />
                <Link href="/blog" class="hover:text-[#F4620A] transition-colors">Blog</Link>
                <ChevronRight class="w-3.5 h-3.5" aria-hidden="true" />
                <span class="text-gray-400 truncate max-w-xs">{{ post.title }}</span>
            </nav>

            <!-- Catégorie -->
            <span v-if="post.category"
                  class="inline-flex items-center gap-1.5 text-white text-xs font-semibold px-3 py-1.5 rounded-full mb-5"
                  :style="{ backgroundColor: post.category.color || '#F4620A' }"
            >
                <Tag class="w-3 h-3" aria-hidden="true" />
                {{ post.category.name }}
            </span>

            <!-- Titre -->
            <h1 id="article-heading" class="font-heading font-bold text-white mb-6 leading-tight" style="font-size:clamp(1.8rem,4vw,2.8rem);">
                {{ post.title }}
            </h1>

            <!-- Meta -->
            <div class="flex flex-wrap items-center gap-5 text-sm text-gray-400">
                <span class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-[#F4620A] rounded-full flex items-center justify-center text-white text-xs font-bold" aria-hidden="true">
                        {{ post.author?.first_name?.charAt(0) ?? 'N' }}
                    </div>
                    {{ post.author?.first_name }} {{ post.author?.last_name }}
                </span>
                <span class="flex items-center gap-1.5">
                    <Calendar class="w-3.5 h-3.5" aria-hidden="true" />
                    <time>{{ formatDate(post.published_at) }}</time>
                </span>
                <span class="flex items-center gap-1.5">
                    <Clock class="w-3.5 h-3.5" aria-hidden="true" />
                    {{ post.reading_time }} min de lecture
                </span>
                <span class="flex items-center gap-1.5">
                    <Eye class="w-3.5 h-3.5" aria-hidden="true" />
                    {{ post.views_count?.toLocaleString('fr') }} vues
                </span>
                <button
                    class="flex items-center gap-1.5 hover:text-[#F4620A] transition-colors cursor-pointer ml-auto"
                    aria-label="Partager cet article"
                    @click="share"
                >
                    <Share2 class="w-4 h-4" aria-hidden="true" />
                    Partager
                </button>
            </div>
        </div>
    </section>

    <!-- ── CONTENU ────────────────────────────── -->
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-[1fr_320px] gap-12">

                <!-- Article principal -->
                <article aria-labelledby="article-heading">
                    <!-- Image à la une -->
                    <div class="aspect-[16/9] rounded-[16px] overflow-hidden mb-10">
                        <img
                            :src="coverUrl(post.cover_image)"
                            :alt="post.title"
                            class="w-full h-full object-cover"
                        />
                    </div>

                    <!-- Chapô -->
                    <p v-if="post.excerpt" class="text-lg text-gray-600 leading-[1.8] mb-8 font-medium border-l-4 border-[#F4620A] pl-5 italic">
                        {{ post.excerpt }}
                    </p>

                    <!-- Corps de l'article -->
                    <div
                        class="prose prose-lg max-w-none
                               prose-headings:font-heading prose-headings:font-bold prose-headings:text-[#0D0D0D]
                               prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                               prose-p:text-gray-600 prose-p:leading-[1.8]
                               prose-a:text-[#F4620A] prose-a:no-underline hover:prose-a:underline
                               prose-strong:text-[#0D0D0D]
                               prose-ul:text-gray-600 prose-ol:text-gray-600
                               prose-li:marker:text-[#F4620A]"
                        v-html="post.content"
                        aria-label="Contenu de l'article"
                    ></div>

                    <!-- CTA inline -->
                    <div class="mt-14 p-8 bg-orange-50 rounded-[16px] border border-[#F4620A]/20 text-center">
                        <h3 class="font-heading font-bold text-[#0D0D0D] text-xl mb-3">
                            Prêt à importer depuis la Chine ?
                        </h3>
                        <p class="text-gray-600 mb-6">
                            NETSPRING vous accompagne de la recherche fournisseur à la livraison en Côte d'Ivoire.
                        </p>
                        <a
                            href="https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20demander%20un%20devis."
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-7 py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.3)] hover:-translate-y-0.5 active:translate-y-0 cursor-pointer"
                            aria-label="Demander un devis sur WhatsApp"
                        >
                            Demander un devis gratuit
                            <ArrowRight class="w-4 h-4" aria-hidden="true" />
                        </a>
                    </div>

                    <!-- Navigation entre articles -->
                    <div class="mt-12 pt-8 border-t border-[#E5E7EB]">
                        <Link
                            href="/blog"
                            class="inline-flex items-center gap-2 text-gray-500 hover:text-[#F4620A] font-medium text-sm transition-colors"
                            aria-label="Retour à la liste du blog"
                        >
                            <ArrowLeft class="w-4 h-4" aria-hidden="true" />
                            Tous les articles
                        </Link>
                    </div>
                </article>

                <!-- Sidebar -->
                <aside class="space-y-8" aria-label="Sidebar — À propos et articles similaires">

                    <!-- À propos de NETSPRING -->
                    <div class="bg-[#0D0D0D] rounded-[16px] p-6 text-white">
                        <div class="w-10 h-10 bg-[#F4620A] rounded-[10px] flex items-center justify-center mb-4 font-heading font-black text-xl">N</div>
                        <h3 class="font-heading font-semibold text-base mb-2">NETSPRING</h3>
                        <p class="text-gray-400 text-sm leading-[1.7] mb-5">
                            Votre partenaire pour importer depuis la Chine en toute sécurité. Sourcing, transport, dédouanement.
                        </p>
                        <a
                            href="https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20souhaite%20demander%20un%20devis."
                            target="_blank"
                            rel="noopener"
                            class="flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white text-sm font-semibold py-2.5 px-5 rounded-[10px] transition-colors cursor-pointer"
                            aria-label="Démarrer mon projet sur WhatsApp"
                        >
                            Démarrer mon projet
                            <ArrowRight class="w-4 h-4" aria-hidden="true" />
                        </a>
                    </div>

                    <!-- Articles similaires -->
                    <div v-if="related.length">
                        <h3 class="font-heading font-semibold text-[#0D0D0D] text-base mb-5">
                            Articles similaires
                        </h3>
                        <ul class="space-y-5" role="list">
                            <li v-for="r in related" :key="r.id">
                                <Link
                                    :href="`/blog/${r.slug}`"
                                    class="group flex gap-3 items-start"
                                    :aria-label="`Lire : ${r.title}`"
                                >
                                    <!-- Mini image -->
                                    <div class="w-16 h-16 flex-shrink-0 rounded-[10px] overflow-hidden">
                                        <img
                                            :src="coverUrl(r.cover_image)"
                                            :alt="r.title"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-heading font-medium text-[#0D0D0D] text-sm leading-snug mb-1 line-clamp-2 group-hover:text-[#F4620A] transition-colors duration-[150ms]">
                                            {{ r.title }}
                                        </h4>
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <Clock class="w-3 h-3" aria-hidden="true" />
                                            {{ r.reading_time }} min
                                        </span>
                                    </div>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Catégories -->
                    <div>
                        <h3 class="font-heading font-semibold text-[#0D0D0D] text-base mb-4">Catégories</h3>
                        <nav aria-label="Catégories du blog">
                            <ul class="space-y-2" role="list">
                                <li>
                                    <Link href="/blog" class="flex items-center justify-between text-sm py-2 px-3 rounded-lg text-gray-600 hover:bg-orange-50 hover:text-[#F4620A] transition-colors">
                                        Tous les articles
                                        <ChevronRight class="w-4 h-4 text-gray-300" aria-hidden="true" />
                                    </Link>
                                </li>
                                <li v-if="post.category">
                                    <Link
                                        :href="`/blog?categorie=${post.category.slug}`"
                                        class="flex items-center justify-between text-sm py-2 px-3 rounded-lg font-medium"
                                        :style="{ backgroundColor: (post.category.color ?? '#F4620A') + '15', color: post.category.color ?? '#F4620A' }"
                                        :aria-current="'page'"
                                    >
                                        {{ post.category.name }}
                                        <ChevronRight class="w-4 h-4" aria-hidden="true" />
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    </AppLayout>
</template>
