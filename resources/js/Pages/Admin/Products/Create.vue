<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ArrowLeft, Save, Upload, X } from 'lucide-vue-next'

const props = defineProps({
    categories: { type: Array, default: () => [] },
})

const form = useForm({
    category_id:   null,
    name:          '',
    description:   '',
    content:       '',
    price:         '',
    compare_price: '',
    sku:           '',
    stock:         0,
    type:          'physical',
    weight:        '',
    is_published:  false,
    is_featured:   false,
    photo:         null,
})

const previewUrl = ref(null)

function onPhotoChange(e) {
    const file = e.target.files[0]
    if (!file) return
    form.photo = file
    previewUrl.value = URL.createObjectURL(file)
}

function removePhoto() {
    form.photo = null
    previewUrl.value = null
}

function submit() {
    form.post('/admin/produits', { forceFormData: true })
}
</script>

<template>
    <Head title="Nouveau produit — Admin NETSPRING" />
    <AdminLayout>
        <template #page-title>Nouveau produit</template>
        <template #header-actions>
            <Link href="/admin/produits"
                  class="flex items-center gap-2 text-gray-400 hover:text-white text-sm transition-colors">
                <ArrowLeft class="w-4 h-4" aria-hidden="true" />
                Retour
            </Link>
        </template>

        <form @submit.prevent="submit" class="max-w-3xl space-y-6">

            <!-- Infos principales -->
            <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-7 space-y-5">
                <h2 class="font-heading font-semibold text-[#0D0D0D] text-base">Informations générales</h2>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom du produit <span class="text-[#F4620A]">*</span></label>
                    <input id="name" v-model="form.name" type="text" required
                           class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent" />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600" role="alert">{{ form.errors.name }}</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                        <select id="category" v-model="form.category_id"
                                class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]">
                            <option :value="null">— Aucune catégorie —</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-[#F4620A]">*</span></label>
                        <select id="type" v-model="form.type" required
                                class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]">
                            <option value="physical">Physique</option>
                            <option value="digital">Numérique</option>
                            <option value="course">Formation</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Description courte</label>
                    <textarea id="description" v-model="form.description" rows="3"
                              class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] resize-none"></textarea>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1.5">Description complète (HTML)</label>
                    <textarea id="content" v-model="form.content" rows="6"
                              class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#F4620A] resize-y"></textarea>
                </div>
            </section>

            <!-- Prix & stock -->
            <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-7 space-y-5">
                <h2 class="font-heading font-semibold text-[#0D0D0D] text-base">Prix & stock</h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">Prix (FCFA) <span class="text-[#F4620A]">*</span></label>
                        <input id="price" v-model="form.price" type="number" min="0" step="100" required placeholder="15 000"
                               class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]" />
                        <p v-if="form.errors.price" class="mt-1 text-xs text-red-600" role="alert">{{ form.errors.price }}</p>
                    </div>
                    <div>
                        <label for="compare_price" class="block text-sm font-medium text-gray-700 mb-1.5">Prix barré (FCFA) <span class="text-gray-400 text-xs">— optionnel</span></label>
                        <input id="compare_price" v-model="form.compare_price" type="number" min="0" step="100" placeholder="20 000"
                               class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]" />
                    </div>
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1.5">Stock <span class="text-[#F4620A]">*</span></label>
                        <input id="stock" v-model="form.stock" type="number" min="-1" required
                               class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]" />
                        <p class="text-xs text-gray-400 mt-1">-1 = illimité</p>
                    </div>
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1.5">SKU / Réf.</label>
                        <input id="sku" v-model="form.sku" type="text" placeholder="NS-001"
                               class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]" />
                        <p v-if="form.errors.sku" class="mt-1 text-xs text-red-600" role="alert">{{ form.errors.sku }}</p>
                    </div>
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-1.5">Poids (kg)</label>
                        <input id="weight" v-model="form.weight" type="number" min="0" step="0.1" placeholder="1.5"
                               class="w-full border border-gray-200 rounded-[10px] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]" />
                    </div>
                </div>
            </section>

            <!-- Photo principale -->
            <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-7 space-y-4">
                <h2 class="font-heading font-semibold text-[#0D0D0D] text-base">Photo du produit</h2>

                <!-- Preview -->
                <div v-if="previewUrl" class="relative w-40 h-40">
                    <img :src="previewUrl" alt="Aperçu" class="w-full h-full object-cover rounded-[12px] border border-[#E5E7EB]" />
                    <button type="button" @click="removePhoto"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center cursor-pointer"
                            aria-label="Supprimer la photo">
                        <X class="w-3.5 h-3.5" aria-hidden="true" />
                    </button>
                </div>

                <!-- Zone d'upload -->
                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-[12px] cursor-pointer hover:border-[#F4620A]/50 hover:bg-orange-50/30 transition-all">
                    <Upload class="w-7 h-7 text-gray-300 mb-2" aria-hidden="true" />
                    <span class="text-sm text-gray-500">Cliquer pour ajouter une photo</span>
                    <span class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — max 5 Mo</span>
                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onPhotoChange" aria-label="Choisir une photo du produit" />
                </label>
                <p v-if="form.errors.photo" class="text-xs text-red-600" role="alert">{{ form.errors.photo }}</p>
            </section>

            <!-- Visibilité -->
            <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-7 space-y-4">
                <h2 class="font-heading font-semibold text-[#0D0D0D] text-base">Visibilité</h2>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" v-model="form.is_published" class="w-4 h-4 accent-[#F4620A] cursor-pointer" />
                    <span class="text-sm text-gray-700">Publié — visible dans la boutique</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" v-model="form.is_featured" class="w-4 h-4 accent-[#F4620A] cursor-pointer" />
                    <span class="text-sm text-gray-700">Mis en avant — affiché sur la page d'accueil</span>
                </label>
            </section>

            <!-- Actions -->
            <div class="flex items-center gap-4">
                <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-semibold px-7 py-3 rounded-[11px] transition-all cursor-pointer shadow-[0_4px_16px_rgba(244,98,10,0.3)]">
                    <Save class="w-4 h-4" aria-hidden="true" />
                    {{ form.processing ? 'Enregistrement…' : 'Créer le produit' }}
                </button>
                <Link href="/admin/produits" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">Annuler</Link>
            </div>
        </form>
    </AdminLayout>
</template>
