<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { Mail, Phone, MapPin, Clock, Send, CheckCircle2, MessageSquare } from 'lucide-vue-next'

const form = useForm({
    name: '', email: '', whatsapp: '', subject: '', message: '',
})

function submit() {
    form.post('/contact', { onSuccess: () => form.reset() })
}

const contacts = [
    { icon: Phone,   label: 'Téléphone / WhatsApp', value: '+225 0594429552',             href: 'https://wa.me/2250594429552' },
    { icon: Mail,    label: 'Email',                value: 'netspringbusiness@gmail.com', href: 'mailto:netspringbusiness@gmail.com' },
    { icon: MapPin,  label: 'Adresse',              value: 'Abidjan, Côte d\'Ivoire',      href: null },
    { icon: Clock,   label: 'Horaires',             value: 'Lun–Ven : 08h00–19h00',        href: null },
]
</script>

<template>
    <Head title="Contact — NETSPRING" />
    <AppLayout>

    <!-- HERO -->
    <section class="bg-[#0D0D0D] py-20" aria-labelledby="contact-heading">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">CONTACT</p>
            <h1 id="contact-heading" class="font-heading font-bold text-white mb-4" style="font-size:clamp(2rem,4vw,3rem);">
                Parlons de votre projet.
            </h1>
            <p class="text-gray-400 text-lg max-w-md mx-auto">Notre équipe vous répond sous 24h.</p>
        </div>
    </section>

    <div class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16">

                <!-- Formulaire -->
                <div>
                    <div v-if="form.wasSuccessful" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-[12px] flex items-center gap-3" role="alert">
                        <CheckCircle2 class="w-5 h-5 text-green-600 flex-shrink-0" aria-hidden="true" />
                        <p class="text-green-800 font-medium text-sm">Message envoyé ! Nous vous répondons sous 24h.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5" aria-label="Formulaire de contact">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label for="c-name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom <span class="text-[#F4620A]" aria-hidden="true">*</span></label>
                                <input id="c-name" v-model="form.name" type="text" required autocomplete="name"
                                       class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600" role="alert">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label for="c-email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-[#F4620A]" aria-hidden="true">*</span></label>
                                <input id="c-email" v-model="form.email" type="email" required autocomplete="email"
                                       class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600" role="alert">{{ form.errors.email }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="c-whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp</label>
                            <input id="c-whatsapp" v-model="form.whatsapp" type="tel" autocomplete="tel" placeholder="+225 0700000000"
                                   class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                        </div>

                        <div>
                            <label for="c-subject" class="block text-sm font-medium text-gray-700 mb-1.5">Sujet <span class="text-[#F4620A]" aria-hidden="true">*</span></label>
                            <input id="c-subject" v-model="form.subject" type="text" required placeholder="Demande de devis, Question, Partenariat..."
                                   class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                        </div>

                        <div>
                            <label for="c-message" class="block text-sm font-medium text-gray-700 mb-1.5">Message <span class="text-[#F4620A]" aria-hidden="true">*</span></label>
                            <textarea id="c-message" v-model="form.message" required rows="5"
                                      class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all resize-none"></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-xs text-red-600" role="alert">{{ form.errors.message }}</p>
                        </div>

                        <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-semibold px-8 py-4 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.3)] hover:-translate-y-0.5 cursor-pointer"
                                aria-label="Envoyer le message">
                            <Send class="w-5 h-5" aria-hidden="true" />
                            {{ form.processing ? 'Envoi...' : 'Envoyer' }}
                        </button>
                    </form>
                </div>

                <!-- Infos contact -->
                <div>
                    <h2 class="font-heading font-bold text-[#0D0D0D] text-2xl mb-8">Contactez-nous directement</h2>
                    <ul class="space-y-5 mb-10" role="list">
                        <li v-for="c in contacts" :key="c.label" class="flex items-start gap-4">
                            <div class="w-11 h-11 bg-orange-50 rounded-[12px] flex items-center justify-center flex-shrink-0">
                                <component :is="c.icon" class="w-5 h-5 text-[#F4620A]" aria-hidden="true" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-0.5">{{ c.label }}</p>
                                <a v-if="c.href" :href="c.href" target="_blank" rel="noopener"
                                   class="font-semibold text-[#0D0D0D] hover:text-[#F4620A] transition-colors">{{ c.value }}</a>
                                <p v-else class="font-semibold text-[#0D0D0D]">{{ c.value }}</p>
                            </div>
                        </li>
                    </ul>

                    <!-- WhatsApp direct -->
                    <a href="https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20j%27ai%20une%20question."
                       target="_blank" rel="noopener"
                       class="flex items-center gap-3 bg-[#25D366] hover:bg-[#1ebe5d] text-white font-semibold px-6 py-4 rounded-[14px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(37,211,102,0.4)] hover:-translate-y-0.5 cursor-pointer"
                       aria-label="Nous écrire directement sur WhatsApp">
                        <MessageSquare class="w-5 h-5" aria-hidden="true" />
                        Écrire sur WhatsApp maintenant
                    </a>
                </div>
            </div>
        </div>
    </div>

    </AppLayout>
</template>
