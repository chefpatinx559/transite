<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { Calendar, MapPin, Wifi, Users, Clock, ArrowRight, CheckCircle2, User, Mail, Phone, FileText } from 'lucide-vue-next'

const props = defineProps({
    formation: { type: Object, required: true },
})

const showForm = ref(false)

const form = useForm({
    name:  '',
    email: '',
    phone: '',
    notes: '',
})

function submit() {
    form.post(`/formations/${props.formation.slug}/inscription`, {
        onSuccess: () => { /* redirect handled server-side */ },
    })
}

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function formatDate(d) {
    if (!d) return null
    return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const DEFAULT_COVER = 'https://static.vecteezy.com/system/resources/previews/027/484/654/large_2x/global-business-logistic-and-transportation-import-export-goods-container-cargo-freight-ship-at-international-port-cargo-plane-flying-above-truck-shipping-container-logistic-industry-generative-ai-photo.jpg'

function coverUrl(path) {
    if (!path) return DEFAULT_COVER
    if (path.startsWith('http')) return path
    return '/storage/' + path
}

const isFull = computed(() =>
    props.formation.max_participants &&
    props.formation.participants_count >= props.formation.max_participants
)
</script>

<template>
    <Head :title="`${formation.title} — Formations NETSPRING`">
        <meta name="description" :content="formation.excerpt || `Inscrivez-vous à la formation ${formation.title} — NETSPRING Côte d'Ivoire.`" />
        <meta property="og:title" :content="`${formation.title} — NETSPRING`" />
        <meta property="og:description" :content="formation.excerpt || formation.title" />
        <meta property="og:type" content="article" />
        <meta property="og:image" :content="coverUrl(formation.cover_image)" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="formation.title" />
        <meta name="twitter:image" :content="coverUrl(formation.cover_image)" />
    </Head>
    <AppLayout>

    <!-- HERO -->
    <section class="bg-[#0D0D0D] py-14" aria-labelledby="formation-heading">
        <div class="max-w-5xl mx-auto px-6">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8" aria-label="Fil d'Ariane">
                <a href="/" class="hover:text-[#F4620A] transition-colors">Accueil</a>
                <span class="text-gray-600">/</span>
                <a href="/formations" class="hover:text-[#F4620A] transition-colors">Formations</a>
                <span class="text-gray-600">/</span>
                <span class="text-gray-400 truncate max-w-xs">{{ formation.title }}</span>
            </nav>

            <!-- Badges -->
            <div class="flex items-center gap-2 mb-5 flex-wrap">
                <span class="inline-flex items-center gap-1 text-white text-xs font-semibold px-3 py-1.5 rounded-full"
                    :class="formation.type === 'online' ? 'bg-blue-600' : 'bg-purple-600'">
                    <Wifi v-if="formation.type === 'online'" class="w-3 h-3" aria-hidden="true" />
                    <MapPin v-else class="w-3 h-3" aria-hidden="true" />
                    {{ formation.type === 'online' ? 'En ligne' : 'Présentielle' }}
                </span>
                <span v-if="formation.is_free" class="bg-emerald-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full">Gratuite</span>
                <span v-if="isFull" class="bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full">Complet</span>
            </div>

            <h1 id="formation-heading" class="font-heading font-bold text-white mb-5 leading-tight" style="font-size:clamp(1.8rem,4vw,2.8rem)">
                {{ formation.title }}
            </h1>

            <!-- Meta -->
            <div class="flex flex-wrap items-center gap-6 text-sm text-gray-400">
                <span v-if="formation.instructor_name" class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-[#F4620A] rounded-full flex items-center justify-center text-white text-xs font-bold" aria-hidden="true">
                        {{ formation.instructor_name.charAt(0) }}
                    </div>
                    {{ formation.instructor_name }}
                </span>
                <span v-if="formation.date_start" class="flex items-center gap-1.5">
                    <Calendar class="w-3.5 h-3.5" aria-hidden="true" />
                    {{ formatDate(formation.date_start) }}
                </span>
                <span v-if="formation.duration" class="flex items-center gap-1.5">
                    <Clock class="w-3.5 h-3.5" aria-hidden="true" />
                    {{ formation.duration }}
                </span>
                <span v-if="formation.max_participants" class="flex items-center gap-1.5">
                    <Users class="w-3.5 h-3.5" aria-hidden="true" />
                    {{ formation.participants_count }}/{{ formation.max_participants }} places
                </span>
                <span v-if="formation.location" class="flex items-center gap-1.5">
                    <MapPin class="w-3.5 h-3.5" aria-hidden="true" />
                    {{ formation.location }}
                </span>
            </div>
        </div>
    </section>

    <!-- CONTENU -->
    <div class="bg-white py-12">
        <div class="max-w-5xl mx-auto px-6">
            <div class="grid lg:grid-cols-[1fr_360px] gap-12 items-start">

                <!-- Colonne gauche — Description -->
                <div>
                    <!-- Image -->
                    <div class="aspect-[16/9] rounded-[16px] overflow-hidden mb-8">
                        <img :src="coverUrl(formation.cover_image)" :alt="formation.title" class="w-full h-full object-cover" />
                    </div>

                    <!-- Contenu HTML -->
                    <div v-if="formation.description"
                        class="prose prose-lg max-w-none
                               prose-headings:font-heading prose-headings:font-bold prose-headings:text-[#0D0D0D]
                               prose-h2:text-xl prose-h2:mt-8 prose-h2:mb-3
                               prose-p:text-gray-600 prose-p:leading-[1.8]
                               prose-a:text-[#F4620A] prose-a:no-underline hover:prose-a:underline
                               prose-strong:text-[#0D0D0D]
                               prose-ul:text-gray-600 prose-li:marker:text-[#F4620A]"
                        v-html="formation.description"
                        aria-label="Description de la formation"
                    ></div>
                    <p v-else-if="formation.excerpt" class="text-gray-600 text-lg leading-[1.8]">{{ formation.excerpt }}</p>
                </div>

                <!-- Colonne droite — Card inscription -->
                <aside class="sticky top-24">
                    <div class="bg-white rounded-[20px] border border-[#E5E7EB] shadow-[0_8px_40px_rgba(0,0,0,0.08)] overflow-hidden">
                        <!-- Prix -->
                        <div class="p-6 border-b border-[#E5E7EB]">
                            <p v-if="formation.is_free" class="font-heading font-bold text-3xl text-emerald-600">Gratuite</p>
                            <p v-else class="font-heading font-bold text-3xl text-[#F4620A]">{{ formatPrice(formation.price) }}</p>
                            <p class="text-gray-400 text-sm mt-1">par participant</p>
                        </div>

                        <!-- Détails -->
                        <div class="p-6 space-y-3 border-b border-[#E5E7EB]">
                            <div v-if="formation.date_start" class="flex items-center gap-2.5 text-sm text-gray-600">
                                <Calendar class="w-4 h-4 text-[#F4620A] shrink-0" aria-hidden="true" />
                                {{ formatDate(formation.date_start) }}
                            </div>
                            <div v-if="formation.duration" class="flex items-center gap-2.5 text-sm text-gray-600">
                                <Clock class="w-4 h-4 text-[#F4620A] shrink-0" aria-hidden="true" />
                                {{ formation.duration }}
                            </div>
                            <div v-if="formation.type === 'presentielle' && formation.location" class="flex items-center gap-2.5 text-sm text-gray-600">
                                <MapPin class="w-4 h-4 text-[#F4620A] shrink-0" aria-hidden="true" />
                                {{ formation.location }}
                            </div>
                            <div v-if="formation.type === 'online'" class="flex items-center gap-2.5 text-sm text-gray-600">
                                <Wifi class="w-4 h-4 text-[#F4620A] shrink-0" aria-hidden="true" />
                                Formation en ligne
                            </div>
                            <div v-if="formation.max_participants" class="flex items-center gap-2.5 text-sm" :class="isFull ? 'text-red-500' : 'text-gray-600'">
                                <Users class="w-4 h-4 shrink-0" :class="isFull ? 'text-red-500' : 'text-[#F4620A]'" aria-hidden="true" />
                                {{ isFull ? 'Complet' : `${formation.participants_count}/${formation.max_participants} places` }}
                            </div>
                        </div>

                        <!-- CTA / Formulaire -->
                        <div class="p-6">
                            <div v-if="isFull" class="text-center py-3 text-sm text-red-500 font-semibold">
                                Cette formation est complète.
                            </div>

                            <div v-else-if="!showForm">
                                <button
                                    class="w-full flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-bold py-4 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_24px_rgba(244,98,10,0.45)] hover:-translate-y-px active:translate-y-0 cursor-pointer"
                                    @click="showForm = true"
                                    :aria-label="`S'inscrire à ${formation.title}`"
                                >
                                    <CheckCircle2 class="w-5 h-5" aria-hidden="true" />
                                    {{ formation.is_free ? 'S\'inscrire gratuitement' : 'S\'inscrire et payer' }}
                                </button>
                            </div>

                            <!-- Formulaire d'inscription -->
                            <form v-else @submit.prevent="submit" class="space-y-4" aria-label="Formulaire d'inscription">
                                <div class="space-y-1.5">
                                    <label for="reg_name" class="block text-sm font-semibold text-[#0D0D0D]">
                                        Nom complet <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <div class="relative">
                                        <User class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" aria-hidden="true" />
                                        <input id="reg_name" v-model="form.name" type="text" required autocomplete="name"
                                            placeholder="Votre nom complet"
                                            class="w-full border border-gray-200 rounded-[10px] pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent"
                                            :class="{ 'border-red-400': form.errors.name }" />
                                    </div>
                                    <p v-if="form.errors.name" class="text-red-500 text-xs">{{ form.errors.name }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label for="reg_email" class="block text-sm font-semibold text-[#0D0D0D]">
                                        Email <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <div class="relative">
                                        <Mail class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" aria-hidden="true" />
                                        <input id="reg_email" v-model="form.email" type="email" required autocomplete="email"
                                            placeholder="votre@email.com"
                                            class="w-full border border-gray-200 rounded-[10px] pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent"
                                            :class="{ 'border-red-400': form.errors.email }" />
                                    </div>
                                    <p v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label for="reg_phone" class="block text-sm font-semibold text-[#0D0D0D]">
                                        Téléphone <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <div class="relative">
                                        <Phone class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" aria-hidden="true" />
                                        <input id="reg_phone" v-model="form.phone" type="tel" required
                                            placeholder="+225 07 XX XX XX XX"
                                            class="w-full border border-gray-200 rounded-[10px] pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent"
                                            :class="{ 'border-red-400': form.errors.phone }" />
                                    </div>
                                    <p v-if="form.errors.phone" class="text-red-500 text-xs">{{ form.errors.phone }}</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label for="reg_notes" class="block text-sm font-semibold text-[#0D0D0D]">
                                        Message (optionnel)
                                    </label>
                                    <div class="relative">
                                        <FileText class="absolute left-3 top-3 w-4 h-4 text-gray-400" aria-hidden="true" />
                                        <textarea id="reg_notes" v-model="form.notes" rows="2"
                                            placeholder="Questions ou remarques…"
                                            class="w-full border border-gray-200 rounded-[10px] pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent resize-none"
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="flex gap-2 pt-1">
                                    <button type="button" @click="showForm = false"
                                        class="flex-1 border border-gray-200 text-gray-600 font-semibold py-3 rounded-[10px] text-sm hover:bg-gray-50 transition-colors cursor-pointer">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                        :disabled="form.processing"
                                        class="flex-1 flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-bold py-3 rounded-[10px] text-sm transition-all duration-[220ms] cursor-pointer">
                                        {{ form.processing ? 'Envoi…' : (formation.is_free ? 'Confirmer' : 'Payer') }}
                                        <ArrowRight v-if="!form.processing" class="w-4 h-4" aria-hidden="true" />
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    </AppLayout>
</template>
