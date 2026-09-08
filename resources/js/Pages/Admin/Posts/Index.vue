<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { PlusCircle, Pencil, Trash2, Eye } from 'lucide-vue-next'

const props = defineProps({
    posts: { type: Object, default: () => ({ data: [], links: [] }) },
})

function formatDate(val) {
    if (!val) return '—'
    return new Date(val).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' })
}

const statusMap = {
    published: { label: 'Publié',   cls: 'bg-emerald-100 text-emerald-700' },
    draft:     { label: 'Brouillon',cls: 'bg-gray-100 text-gray-600' },
    archived:  { label: 'Archivé', cls: 'bg-amber-100 text-amber-700' },
}

function statusBadge(s) { return statusMap[s] ?? { label: s, cls: 'bg-gray-100 text-gray-600' } }

function confirmDelete(post) {
    if (confirm(`Supprimer l'article "${post.title}" ? Cette action est irréversible.`)) {
        router.delete(`/admin/articles/${post.id}`, { preserveScroll: true })
    }
}
</script>

<template>
    <Head title="Articles — Admin NETSPRING" />
    <AdminLayout>
        <template #page-title>Articles</template>
        <template #header-actions>
            <Link
                href="/admin/articles/creer"
                class="flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white text-sm font-semibold px-4 py-2 rounded-[9px] transition-all duration-[220ms]"
                aria-label="Créer un nouvel article"
            >
                <PlusCircle class="w-4 h-4" aria-hidden="true" />
                Nouvel article
            </Link>
        </template>

        <div class="bg-white rounded-[14px] shadow-[0_4px_20px_rgba(0,0,0,0.07)] overflow-hidden">

            <!-- Empty -->
            <div v-if="!posts.data || !posts.data.length" class="py-20 text-center" role="status">
                <p class="text-gray-400 text-sm">Aucun article pour le moment.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm" aria-label="Liste des articles">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Titre</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vues</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[#0D0D0D] text-sm leading-snug max-w-xs truncate">{{ post.title }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-sm">{{ post.category?.name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full" :class="statusBadge(post.status).cls">
                                    {{ statusBadge(post.status).label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-sm whitespace-nowrap">{{ formatDate(post.created_at) }}</td>
                            <td class="px-5 py-4 text-gray-500 text-sm">{{ post.views_count ?? 0 }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="`/blog/${post.slug}`"
                                        class="w-8 h-8 flex items-center justify-center rounded-[7px] text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-[220ms]"
                                        :aria-label="`Voir l'article ${post.title}`"
                                        target="_blank"
                                    >
                                        <Eye class="w-3.5 h-3.5" aria-hidden="true" />
                                    </Link>
                                    <Link
                                        :href="`/admin/articles/${post.id}/modifier`"
                                        class="w-8 h-8 flex items-center justify-center rounded-[7px] text-gray-400 hover:text-[#F4620A] hover:bg-[#F4620A]/10 transition-all duration-[220ms]"
                                        :aria-label="`Modifier l'article ${post.title}`"
                                    >
                                        <Pencil class="w-3.5 h-3.5" aria-hidden="true" />
                                    </Link>
                                    <button
                                        class="w-8 h-8 flex items-center justify-center rounded-[7px] text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-[220ms] cursor-pointer"
                                        :aria-label="`Supprimer l'article ${post.title}`"
                                        @click="confirmDelete(post)"
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
            v-if="posts.links && posts.links.length > 3"
            class="mt-6 flex items-center justify-center gap-2"
            aria-label="Pagination des articles"
        >
            <template v-for="link in posts.links" :key="link.label">
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
