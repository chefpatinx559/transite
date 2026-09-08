<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { Package, User, MapPin, ShieldCheck } from 'lucide-vue-next'

const props = defineProps({
    items:    { type: Array,  default: () => [] },
    total:    { type: Number, default: 0 },
    subtotal: { type: Number, default: 0 },
})

const steps = [
    { num: 1, label: 'Infos' },
    { num: 2, label: 'Livraison' },
    { num: 3, label: 'Paiement' },
]

const form = useForm({
    customer_name:    '',
    customer_email:   '',
    customer_phone:   '',
    shipping_address: {
        address: '',
        city:    '',
        country: 'Côte d\'Ivoire',
    },
    notes:            '',
})

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function submit() {
    form.post('/checkout')
}
</script>

<template>
    <Head title="Checkout — NETSPRING" />
    <AppLayout>

        <!-- Header -->
        <section class="bg-[#0D0D0D] py-12" aria-labelledby="checkout-title">
            <div class="max-w-5xl mx-auto px-6">
                <h1 id="checkout-title" class="font-heading font-bold text-3xl text-white mb-8">Finaliser ma commande</h1>

                <!-- Steps indicator -->
                <nav aria-label="Étapes de commande">
                    <ol class="flex items-center gap-0" role="list">
                        <li
                            v-for="(step, idx) in steps"
                            :key="step.num"
                            class="flex items-center"
                            :aria-current="step.num === 1 ? 'step' : undefined"
                        >
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-[220ms]"
                                    :class="step.num === 1
                                        ? 'bg-[#F4620A] text-white'
                                        : 'bg-white/10 text-gray-400'"
                                    :aria-label="`Étape ${step.num} : ${step.label}`"
                                >
                                    {{ step.num }}
                                </div>
                                <span
                                    class="text-xs sm:text-sm font-medium"
                                    :class="step.num === 1 ? 'text-white' : 'text-gray-500'"
                                >{{ step.label }}</span>
                            </div>
                            <div
                                v-if="idx < steps.length - 1"
                                class="w-8 md:w-16 h-px bg-white/15 mx-2"
                                aria-hidden="true"
                            />
                        </li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Main content -->
        <div class="max-w-5xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10 items-start">

                <!-- Résumé — visible en PREMIER sur mobile -->
                <div class="order-first lg:order-last">
                    <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-6 lg:sticky lg:top-24">
                        <h2 class="font-heading font-bold text-[#0D0D0D] text-base mb-5">Résumé de commande</h2>

                        <ul class="space-y-3 mb-5" role="list" aria-label="Articles commandés">
                            <li
                                v-for="item in items"
                                :key="item.id"
                                class="flex items-center gap-3"
                            >
                                <div class="w-10 h-10 bg-gray-100 rounded-[8px] flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    <img v-if="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                                    <Package v-else class="w-4.5 h-4.5 text-gray-300" aria-hidden="true" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-[#0D0D0D] truncate">{{ item.name }}</p>
                                    <p class="text-xs text-gray-500">x{{ item.quantity }}</p>
                                </div>
                                <span class="text-xs font-bold text-[#0D0D0D] flex-shrink-0">{{ formatPrice(item.price * item.quantity) }}</span>
                            </li>
                        </ul>

                        <div class="h-px bg-gray-100 mb-4" role="separator" />
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Sous-total</dt>
                                <dd class="font-medium text-[#0D0D0D]">{{ formatPrice(subtotal) }}</dd>
                            </div>
                            <div class="flex justify-between items-baseline pt-1 mt-1 border-t border-gray-100">
                                <dt class="font-bold text-[#0D0D0D]">Total</dt>
                                <dd class="font-bold text-[#F4620A] text-lg">{{ formatPrice(total) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Form -->
                <div class="lg:col-span-2 space-y-6 order-last lg:order-first">
                    <form aria-label="Formulaire de commande" @submit.prevent="submit">

                        <!-- Section 1 — Informations personnelles -->
                        <fieldset class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-6 mb-6">
                            <legend class="flex items-center gap-2.5 font-heading font-bold text-[#0D0D0D] text-base mb-6">
                                <User class="w-5 h-5 text-[#F4620A]" aria-hidden="true" />
                                Informations personnelles
                            </legend>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_name" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                        Nom complet <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="customer_name"
                                        v-model="form.customer_name"
                                        type="text"
                                        autocomplete="name"
                                        required
                                        placeholder="Votre nom complet"
                                        class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 min-h-[48px] text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                        :class="{ 'border-red-400': form.errors.customer_name }"
                                        aria-required="true"
                                        :aria-describedby="form.errors.customer_name ? 'err-name' : undefined"
                                    />
                                    <p v-if="form.errors.customer_name" id="err-name" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.customer_name }}</p>
                                </div>

                                <div>
                                    <label for="customer_email" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                        Email <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="customer_email"
                                        v-model="form.customer_email"
                                        type="email"
                                        autocomplete="email"
                                        required
                                        placeholder="votre@email.com"
                                        class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 min-h-[48px] text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                        :class="{ 'border-red-400': form.errors.customer_email }"
                                        aria-required="true"
                                    />
                                    <p v-if="form.errors.customer_email" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.customer_email }}</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="customer_phone" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                        Téléphone / WhatsApp <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="customer_phone"
                                        v-model="form.customer_phone"
                                        type="tel"
                                        autocomplete="tel"
                                        required
                                        placeholder="+225 07 XX XX XX XX"
                                        class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 min-h-[48px] text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                        :class="{ 'border-red-400': form.errors.customer_phone }"
                                        aria-required="true"
                                    />
                                    <p v-if="form.errors.customer_phone" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.customer_phone }}</p>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Section 2 — Livraison -->
                        <fieldset class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-6 mb-6">
                            <legend class="flex items-center gap-2.5 font-heading font-bold text-[#0D0D0D] text-base mb-6">
                                <MapPin class="w-5 h-5 text-[#F4620A]" aria-hidden="true" />
                                Adresse de livraison
                            </legend>

                            <div class="space-y-4">
                                <div>
                                    <label for="city" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                        Ville <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <input
                                        id="city"
                                        v-model="form.shipping_address.city"
                                        type="text"
                                        autocomplete="address-level2"
                                        required
                                        placeholder="Abidjan, Bouaké…"
                                        class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 min-h-[48px] text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                        :class="{ 'border-red-400': form.errors['shipping_address.city'] }"
                                        aria-required="true"
                                    />
                                    <p v-if="form.errors['shipping_address.city']" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors['shipping_address.city'] }}</p>
                                </div>
                                <div>
                                    <label for="address" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                        Adresse complète <span class="text-red-500" aria-hidden="true">*</span>
                                    </label>
                                    <textarea
                                        id="address"
                                        v-model="form.shipping_address.address"
                                        rows="2"
                                        required
                                        autocomplete="street-address"
                                        placeholder="Quartier, rue, numéro…"
                                        class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 min-h-[48px] text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all resize-none"
                                        :class="{ 'border-red-400': form.errors['shipping_address.address'] }"
                                        aria-required="true"
                                    />
                                    <p v-if="form.errors['shipping_address.address']" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors['shipping_address.address'] }}</p>
                                </div>
                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                        Notes de livraison (optionnel)
                                    </label>
                                    <textarea
                                        id="notes"
                                        v-model="form.notes"
                                        rows="2"
                                        placeholder="Instructions spéciales pour la livraison…"
                                        class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 min-h-[48px] text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all resize-none"
                                    />
                                </div>
                            </div>
                        </fieldset>

                        <!-- Section 3 — Paiement GeniusPay -->
                        <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-6 mb-6">
                            <div class="flex items-center gap-2.5 font-heading font-bold text-[#0D0D0D] text-base mb-5">
                                <ShieldCheck class="w-5 h-5 text-[#F4620A]" aria-hidden="true" />
                                Paiement sécurisé
                            </div>
                            <div class="flex items-center gap-4 p-4 rounded-[12px] border-2 border-[#F4620A] bg-[#F4620A]/5">
                                <div class="w-10 h-10 bg-[#F4620A] rounded-[10px] flex items-center justify-center flex-shrink-0">
                                    <ShieldCheck class="w-5 h-5 text-white" aria-hidden="true" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-[#0D0D0D] text-sm">GeniusPay</p>
                                    <p class="text-gray-500 text-xs mt-0.5">Wave · Orange Money · MTN · Moov · Carte bancaire</p>
                                </div>
                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full">Sécurisé</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-3 text-center">
                                Vous choisissez votre moyen de paiement sur la page sécurisée GeniusPay.
                            </p>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-bold py-4 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_24px_rgba(244,98,10,0.45)] hover:-translate-y-px active:translate-y-0 cursor-pointer text-base"
                            aria-label="Confirmer et passer la commande"
                        >
                            {{ form.processing ? 'Redirection vers GeniusPay…' : 'PAYER SUR GENIUSPAY' }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
