<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { XCircle, RotateCcw, MessageCircle } from 'lucide-vue-next'

const props = defineProps({
    order: { type: Object, required: true },
})
</script>

<template>
    <Head title="Paiement échoué — NETSPRING" />
    <AppLayout>
        <div class="max-w-2xl mx-auto px-6 py-20 text-center">

            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <XCircle class="w-14 h-14 text-red-500" aria-hidden="true" />
            </div>

            <h1 class="font-heading font-bold text-3xl text-[#0D0D0D] mb-3">
                Paiement non abouti
            </h1>
            <p class="text-gray-500 text-base mb-2 max-w-sm mx-auto">
                Votre commande <strong>#{{ order.order_number }}</strong> a bien été enregistrée mais le paiement n'a pas été complété.
            </p>
            <p class="text-gray-400 text-sm mb-10">
                Vous pouvez réessayer ou nous contacter sur WhatsApp.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <Link
                    href="/boutique"
                    class="flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-bold py-3.5 px-8 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] cursor-pointer"
                >
                    <RotateCcw class="w-4.5 h-4.5" aria-hidden="true" />
                    Retour à la boutique
                </Link>
                <a
                    :href="`https://wa.me/2250594429552?text=Bonjour NETSPRING, mon paiement pour la commande ${order.order_number} a échoué. Pouvez-vous m'aider ?`"
                    target="_blank"
                    rel="noopener"
                    class="flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-bold py-3.5 px-8 rounded-[12px] transition-all duration-[220ms] cursor-pointer"
                >
                    <MessageCircle class="w-4.5 h-4.5" aria-hidden="true" />
                    Aide sur WhatsApp
                </a>
            </div>
        </div>
    </AppLayout>
</template>
