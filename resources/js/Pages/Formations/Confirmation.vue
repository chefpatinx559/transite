<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { CheckCircle2, Calendar, MapPin, Wifi, GraduationCap, MessageCircle } from 'lucide-vue-next'

const props = defineProps({
    registration: { type: Object, required: true },
    formation:    { type: Object, required: true },
})

function formatDate(d) {
    if (!d) return null
    return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}
</script>

<template>
    <Head title="Inscription confirmée — NETSPRING" />
    <AppLayout>
        <div class="max-w-2xl mx-auto px-6 py-20">

            <!-- Icône succès -->
            <div class="flex flex-col items-center text-center mb-10">
                <div class="w-24 h-24 bg-[#F4620A]/10 rounded-full flex items-center justify-center mb-6">
                    <CheckCircle2 class="w-14 h-14 text-[#F4620A]" aria-hidden="true" />
                </div>
                <h1 class="font-heading font-bold text-3xl md:text-4xl text-[#0D0D0D] mb-3">
                    Inscription
                    <span class="text-[#F4620A]">{{ registration.status === 'confirmed' ? 'confirmée' : 'enregistrée' }}</span>
                    !
                </h1>
                <p class="text-gray-500 text-base max-w-sm">
                    <span v-if="registration.payment_status === 'paid'">
                        Votre paiement a été accepté. Vous recevrez les détails par email.
                    </span>
                    <span v-else-if="formation.is_free">
                        Votre inscription gratuite est confirmée. À bientôt !
                    </span>
                    <span v-else>
                        Inscription enregistrée. Votre paiement sera vérifié et vous recevrez une confirmation.
                    </span>
                </p>
            </div>

            <!-- Récapitulatif -->
            <div class="bg-white rounded-[20px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] overflow-hidden mb-8">
                <!-- En-tête -->
                <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Formation</p>
                        <p class="font-heading font-bold text-[#0D0D0D] text-lg">{{ formation.title }}</p>
                    </div>
                    <span
                        class="text-xs font-semibold px-3 py-1.5 rounded-full"
                        :class="registration.status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                    >{{ registration.status === 'confirmed' ? 'Confirmé' : 'En attente' }}</span>
                </div>

                <!-- Détails -->
                <div class="px-6 py-5 space-y-3 border-b border-gray-100">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <span class="font-semibold w-24 shrink-0">Nom</span>
                        {{ registration.name }}
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <span class="font-semibold w-24 shrink-0">Email</span>
                        {{ registration.email }}
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <span class="font-semibold w-24 shrink-0">Téléphone</span>
                        {{ registration.phone || '—' }}
                    </div>
                    <div v-if="!formation.is_free" class="flex items-center gap-3 text-sm text-gray-600">
                        <span class="font-semibold w-24 shrink-0">Montant</span>
                        <span class="text-[#F4620A] font-bold">{{ formatPrice(formation.price) }}</span>
                    </div>
                </div>

                <!-- Info formation -->
                <div class="px-6 py-5 space-y-2">
                    <p v-if="formation.date_start" class="flex items-center gap-2.5 text-sm text-gray-600">
                        <Calendar class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        {{ formatDate(formation.date_start) }}
                    </p>
                    <p v-if="formation.type === 'presentielle' && formation.location" class="flex items-center gap-2.5 text-sm text-gray-600">
                        <MapPin class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        {{ formation.location }}
                    </p>
                    <p v-if="formation.type === 'online'" class="flex items-center gap-2.5 text-sm text-gray-600">
                        <Wifi class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        Formation en ligne — le lien vous sera envoyé par email
                    </p>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <Link href="/formations"
                    class="flex-1 flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] cursor-pointer">
                    <GraduationCap class="w-4.5 h-4.5" aria-hidden="true" />
                    Voir d'autres formations
                </Link>
                <a
                    :href="`https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20je%20viens%20de%20m%27inscrire%20à%20la%20formation%20${encodeURIComponent(formation.title)}.`"
                    target="_blank" rel="noopener"
                    class="flex-1 flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] cursor-pointer">
                    <MessageCircle class="w-4.5 h-4.5" aria-hidden="true" />
                    Confirmer sur WhatsApp
                </a>
            </div>
        </div>
    </AppLayout>
</template>
