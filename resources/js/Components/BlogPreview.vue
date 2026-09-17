<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowRight, ChevronRight } from 'lucide-vue-next'

// 3 ou 4 derniers articles passés depuis le controller via Inertia::render()
defineProps({
    latestPosts: {
        type: Array,
        default: () => [],
    },
})

// Retourne l'URL de couverture ou null si absente (on affiche alors un gradient)
function coverUrl(path) {
    if (!path) return null
    if (path.startsWith('http')) return path
    return '/storage/' + path
}

function formatDate(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: 'numeric', month: 'long', year: 'numeric',
    })
}
</script>

<template>
    <!-- ══════════════════════════════════════════════════
         BLOG — Aperçu des derniers articles
    ══════════════════════════════════════════════════ -->
    <section
        v-if="latestPosts.length"
        aria-labelledby="blog-heading"
        class="py-24 bg-[#FAFAFA]"
    >
        <div class="max-w-7xl mx-auto px-6">

            <!-- En-tête + lien desktop -->
            <div class="flex items-end justify-between mb-14 flex-wrap gap-4">
                <div>
                    <p
                        class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3"
                        aria-hidden="true"
                    >BLOG</p>
                    <h2
                        id="blog-heading"
                        class="font-heading font-bold text-[#0D0D0D]"
                        style="font-size:clamp(1.8rem,3.5vw,2.5rem);"
                    >
                        Nos conseils pour développer<br class="hidden sm:block">votre business.
                    </h2>
                </div>
                <Link
                    href="/blog"
                    class="hidden md:inline-flex items-center gap-2 text-[#F4620A] font-semibold text-sm hover:gap-3 transition-all duration-[220ms]"
                    aria-label="Voir tous les articles du blog NETSPRING"
                >
                    Voir tous les articles
                    <ArrowRight class="w-4 h-4" aria-hidden="true" />
                </Link>
            </div>

            <!-- Grille d'articles -->
            <ul class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5" role="list">
                <li
                    v-for="(post, i) in latestPosts"
                    :key="post.id"
                    data-reveal
                    :data-reveal-delay="i + 1"
                >
                    <article
                        class="card-lift group bg-white rounded-[16px] overflow-hidden border border-[#E5E7EB] hover:border-[#F4620A]/20 h-full flex flex-col"
                    >
                        <!-- Couverture : image réelle ou gradient NETSPRING -->
                        <div class="aspect-[16/9] relative overflow-hidden flex-shrink-0">
                            <img
                                v-if="coverUrl(post.cover_image)"
                                :src="coverUrl(post.cover_image)"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy"
                            />
                            <!--
                                TODO: Remplacer ce gradient par une vraie image de couverture
                                par article (format 800x450px WebP, stockée dans storage/app/public/posts/)
                            -->
                            <div
                                v-else
                                class="w-full h-full bg-gradient-to-br from-[#0D0D0D] via-[#1a1a1a] to-[#2d1a0a] flex items-center justify-center"
                                aria-hidden="true"
                            >
                                <span class="font-heading font-bold text-[#F4620A] text-2xl tracking-widest select-none">
                                    NETSPRING
                                </span>
                            </div>

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"
                                aria-hidden="true"
                            />

                            <!-- Badge catégorie -->
                            <div class="absolute bottom-3 left-3">
                                <span
                                    v-if="post.category"
                                    class="text-white text-xs font-semibold px-2.5 py-1 rounded-full"
                                    :style="{ backgroundColor: post.category.color || '#F4620A' }"
                                >{{ post.category.name }}</span>
                                <span
                                    v-else
                                    class="bg-[#F4620A] text-white text-xs font-semibold px-2.5 py-1 rounded-full"
                                >Blog</span>
                            </div>
                        </div>

                        <!-- Corps -->
                        <div class="p-5 flex flex-col flex-1">
                            <div class="flex items-center gap-3 text-xs text-gray-400 mb-2.5">
                                <time :datetime="post.published_at">{{ formatDate(post.published_at) }}</time>
                                <span aria-hidden="true">·</span>
                                <span>{{ post.reading_time }} min de lecture</span>
                            </div>
                            <h3
                                class="font-heading font-semibold text-[#0D0D0D] text-sm leading-snug mb-4 flex-1 group-hover:text-[#F4620A] transition-colors duration-[150ms]"
                            >
                                {{ post.title }}
                            </h3>
                            <Link
                                :href="`/blog/${post.slug}`"
                                class="text-[#F4620A] text-xs font-semibold flex items-center gap-1.5 group-hover:gap-2.5 transition-all duration-[220ms]"
                                :aria-label="`Lire : ${post.title}`"
                            >
                                Lire l'article
                                <ChevronRight class="w-3.5 h-3.5" aria-hidden="true" />
                            </Link>
                        </div>
                    </article>
                </li>
            </ul>

            <!-- Lien mobile -->
            <div class="mt-10 text-center md:hidden">
                <Link href="/blog" class="inline-flex items-center gap-2 text-[#F4620A] font-semibold">
                    Voir tous les articles
                    <ArrowRight class="w-4 h-4" aria-hidden="true" />
                </Link>
            </div>

        </div>
    </section>
</template>
