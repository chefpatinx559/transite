<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Calendar, MapPin, Wifi, Users, Clock, ArrowRight, GraduationCap } from 'lucide-vue-next'

const props = defineProps({
    formations: { type: Array, default: () => [] },
})

function formatPrice(val) {
    return Number(val).toLocaleString('fr-FR') + ' FCFA'
}

function formatDate(d) {
    if (!d) return null
    return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
}

const DEFAULT_COVER = 'https://static.vecteezy.com/system/resources/previews/027/484/654/large_2x/global-business-logistic-and-transportation-import-export-goods-container-cargo-freight-ship-at-international-port-cargo-plane-flying-above-truck-shipping-container-logistic-industry-generative-ai-photo.jpg'

function coverUrl(path) {
    if (!path) return DEFAULT_COVER
    if (path.startsWith('http')) return path
    return '/storage/' + path
}
</script>

<template>
    <Head title="Formations — NETSPRING" />
    <AppLayout>

    <!-- HERO -->
    <section class="bg-[#0D0D0D] py-20 relative overflow-hidden" aria-labelledby="formations-heading">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 right-1/3 w-96 h-96 bg-[#F4620A]/6 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-widest uppercase mb-4" aria-hidden="true">Formations</p>
            <h1 id="formations-heading" class="font-heading font-bold text-white mb-5" style="font-size:clamp(2rem,4vw,3rem)">
                Montez en compétences.<br>Importez avec confiance.
            </h1>
            <p class="text-gray-400 text-lg max-w-xl mx-auto leading-[1.75]">
                Des formations pratiques pour maîtriser l'importation depuis la Chine — en ligne ou en présentiel à Abidjan.
            </p>
        </div>
    </section>

    <!-- FORMATIONS -->
    <div class="bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-6 py-16">

            <!-- Empty state -->
            <div v-if="!formations.length" class="text-center py-24">
                <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-5">
                    <GraduationCap class="w-10 h-10 text-[#F4620A]" aria-hidden="true" />
                </div>
                <h2 class="font-heading font-bold text-xl text-[#0D0D0D] mb-2">Bientôt disponible</h2>
                <p class="text-gray-500">Nos formations arrivent très prochainement. Inscrivez-vous à notre newsletter pour être notifié.</p>
            </div>

            <!-- Grid formations -->
            <ul v-else class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7" role="list">
                <li v-for="formation in formations" :key="formation.id">
                    <Link
                        :href="`/formations/${formation.slug}`"
                        class="group flex flex-col h-full bg-white rounded-[20px] overflow-hidden border border-[#E5E7EB] hover:border-[#F4620A]/20 hover:shadow-[0_12px_40px_rgba(0,0,0,0.1)] transition-all duration-[220ms]"
                        :aria-label="`Voir la formation : ${formation.title}`"
                    >
                        <!-- Image -->
                        <div class="aspect-[16/9] relative overflow-hidden flex-shrink-0">
                            <img
                                :src="coverUrl(formation.cover_image)"
                                :alt="formation.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                            <!-- Badges -->
                            <div class="absolute bottom-3 left-3 flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1 text-white text-xs font-semibold px-2.5 py-1 rounded-full"
                                    :class="formation.type === 'online' ? 'bg-blue-600' : 'bg-purple-600'"
                                >
                                    <Wifi v-if="formation.type === 'online'" class="w-3 h-3" aria-hidden="true" />
                                    <MapPin v-else class="w-3 h-3" aria-hidden="true" />
                                    {{ formation.type === 'online' ? 'En ligne' : 'Présentielle' }}
                                </span>
                                <span v-if="formation.is_free" class="bg-emerald-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full">
                                    Gratuite
                                </span>
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div class="p-6 flex flex-col flex-1">
                            <!-- Meta -->
                            <div class="flex items-center gap-4 text-xs text-gray-400 mb-3 flex-wrap">
                                <span v-if="formation.date_start" class="flex items-center gap-1">
                                    <Calendar class="w-3.5 h-3.5" aria-hidden="true" />
                                    {{ formatDate(formation.date_start) }}
                                </span>
                                <span v-if="formation.duration" class="flex items-center gap-1">
                                    <Clock class="w-3.5 h-3.5" aria-hidden="true" />
                                    {{ formation.duration }}
                                </span>
                                <span v-if="formation.max_participants" class="flex items-center gap-1">
                                    <Users class="w-3.5 h-3.5" aria-hidden="true" />
                                    {{ formation.participants_count }}/{{ formation.max_participants }} places
                                </span>
                            </div>

                            <!-- Titre -->
                            <h2 class="font-heading font-bold text-[#0D0D0D] text-lg leading-snug mb-3 flex-1 group-hover:text-[#F4620A] transition-colors duration-[150ms]">
                                {{ formation.title }}
                            </h2>

                            <p v-if="formation.excerpt" class="text-gray-500 text-sm leading-[1.7] mb-5 line-clamp-2">
                                {{ formation.excerpt }}
                            </p>

                            <!-- Prix + CTA -->
                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-[#E5E7EB]">
                                <div>
                                    <p v-if="formation.is_free" class="font-heading font-bold text-emerald-600 text-lg">Gratuite</p>
                                    <p v-else class="font-heading font-bold text-[#F4620A] text-lg">{{ formatPrice(formation.price) }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1.5 text-[#F4620A] font-semibold text-sm group-hover:gap-2.5 transition-all duration-[220ms]" aria-hidden="true">
                                    S'inscrire
                                    <ArrowRight class="w-4 h-4" />
                                </span>
                            </div>
                        </div>
                    </Link>
                </li>
            </ul>
        </div>
    </div>

    <!-- CTA -->
    <section class="py-16 bg-[#F4620A]" aria-label="Vous avez une question ?">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="font-heading font-bold text-white text-2xl mb-3">Une question sur nos formations ?</h2>
            <p class="text-orange-100 mb-7">Contactez-nous sur WhatsApp, nous répondons rapidement.</p>
            <a
                href="https://wa.me/2250594429552?text=Bonjour%20NETSPRING%2C%20j%27ai%20une%20question%20sur%20vos%20formations."
                target="_blank" rel="noopener"
                class="inline-flex items-center gap-2 bg-white text-[#F4620A] font-bold px-7 py-3.5 rounded-[12px] hover:bg-orange-50 transition-all duration-[220ms] shadow-[0_4px_20px_rgba(0,0,0,0.1)]"
            >
                Nous contacter
                <ArrowRight class="w-5 h-5" aria-hidden="true" />
            </a>
        </div>
    </section>

    </AppLayout>
</template>
