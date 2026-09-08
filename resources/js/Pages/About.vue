<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ArrowRight, Target, Heart, Globe, Lightbulb, CheckCircle2, Users } from 'lucide-vue-next'

const values = [
    { icon: Target,     title: 'Excellence',       desc: 'Nous visons la qualité à chaque étape, du sourcing jusqu\'à la livraison.' },
    { icon: Heart,      title: 'Proximité',        desc: 'Nous traitons chaque client comme un partenaire à long terme.' },
    { icon: Globe,      title: 'International',    desc: 'Maîtrise du commerce Chine-Afrique et des codes culturels.' },
    { icon: Lightbulb,  title: 'Innovation',       desc: 'Nous simplifions des processus complexes avec des solutions pratiques.' },
]

const teamStats = [
    { value: '2018',  label: 'Année de création' },
    { value: '500+',  label: 'Clients accompagnés' },
    { value: '1000+', label: 'Commandes réalisées' },
    { value: '4.9/5', label: 'Satisfaction clients' },
]
</script>

<template>
    <Head title="À propos — NETSPRING" />
    <AppLayout>

    <!-- HERO -->
    <section class="bg-[#0D0D0D] py-24 relative overflow-hidden" aria-labelledby="about-heading">
        <div aria-hidden="true" class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#F4620A]/6 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-[#F4620A]/4 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-6 text-center">
            <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">À PROPOS</p>
            <h1 id="about-heading" class="font-heading font-bold text-white mb-5" style="font-size:clamp(2rem,4vw,3rem);">
                Construire des ponts.<br>Créer des opportunités.
            </h1>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto leading-[1.75]">
                NETSPRING est né d'une conviction simple : les entrepreneurs africains méritent un accès facile, sécurisé et accompagné au commerce international.
            </p>
        </div>
    </section>

    <!-- STATS -->
    <div class="bg-[#F4620A] py-5">
        <div class="max-w-7xl mx-auto px-6">
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-white text-center">
                <div v-for="s in teamStats" :key="s.label">
                    <dt class="sr-only">{{ s.label }}</dt>
                    <dd>
                        <p class="font-heading font-bold text-2xl">{{ s.value }}</p>
                        <p class="text-orange-100 text-xs">{{ s.label }}</p>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- HISTOIRE -->
    <section class="py-24 bg-white" aria-labelledby="story-heading">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-[#F4620A] font-semibold text-xs tracking-widest uppercase mb-4" aria-hidden="true">Notre histoire</p>
                    <h2 id="story-heading" class="font-heading font-bold text-[#0D0D0D] mb-6" style="font-size:clamp(1.8rem,3vw,2.5rem);">
                        Né de la frustration<br>des importateurs africains.
                    </h2>
                    <div class="space-y-4 text-gray-600 leading-[1.75] text-sm">
                        <p>NETSPRING a été fondé par <strong class="text-[#0D0D0D]">Elysée Ouattara</strong> après avoir observé les difficultés rencontrées par de nombreux entrepreneurs ivoiriens souhaitant importer depuis la Chine : barrière de la langue, risque de fraude, complexité douanière, manque de transparence.</p>
                        <p>Notre mission depuis 2018 : rendre l'importation accessible à tous les entrepreneurs africains, qu'ils soient débutants ou expérimentés, en prenant en charge l'intégralité du processus avec transparence et professionnalisme.</p>
                        <p>Aujourd'hui, NETSPRING a accompagné plus de 500 clients en Côte d'Ivoire et dans la sous-région, avec plus de 1000 commandes réussies.</p>
                    </div>
                </div>
                <!-- Visuel équipe réduite -->
                <div class="relative">
                    <div class="rounded-[24px] bg-gradient-to-br from-[#0D0D0D] to-[#1a1a1a] shadow-[0_16px_48px_rgba(0,0,0,0.15)] overflow-hidden p-8">
                        <p class="text-[#F4620A] font-semibold text-xs tracking-widest uppercase mb-6">L'équipe fondatrice</p>
                        <div class="space-y-5">
                            <!-- Elysée -->
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-[#F4620A] flex items-center justify-center flex-shrink-0 shadow-[0_4px_16px_rgba(244,98,10,0.4)]">
                                    <span class="font-heading font-bold text-white text-lg">EO</span>
                                </div>
                                <div>
                                    <p class="font-heading font-bold text-white">Elysée Ouattara</p>
                                    <p class="text-[#F4620A] text-xs font-semibold mt-0.5">Fondateur & Manager général</p>
                                </div>
                            </div>
                            <div class="h-px bg-white/8"></div>
                            <!-- Kassamba -->
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-white/10 border border-white/20 flex items-center justify-center flex-shrink-0">
                                    <span class="font-heading font-bold text-white text-lg">KB</span>
                                </div>
                                <div>
                                    <p class="font-heading font-bold text-white">Kassamba Ben Idriss Diaby</p>
                                    <p class="text-gray-400 text-xs font-semibold mt-0.5">Directeur Exécutif</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div aria-hidden="true" class="absolute -bottom-4 -right-4 w-24 h-24 bg-orange-100 rounded-[16px] -z-10 opacity-60"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ÉQUIPE -->
    <section class="py-24 bg-[#FAFAFA]" aria-labelledby="team-heading">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-[#F4620A] font-semibold text-xs tracking-widest uppercase mb-3" aria-hidden="true">Notre équipe</p>
                <h2 id="team-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    Les visages de NETSPRING.
                </h2>
            </div>

            <div class="grid sm:grid-cols-2 gap-8 max-w-2xl mx-auto">

                <!-- Elysée Ouattara -->
                <article class="bg-white rounded-[20px] p-8 border border-[#E5E7EB] hover:border-[#F4620A]/30 hover:shadow-[0_8px_32px_rgba(244,98,10,0.08)] transition-all duration-[220ms] text-center">
                    <div class="w-20 h-20 rounded-full bg-[#F4620A] flex items-center justify-center mx-auto mb-5 shadow-[0_4px_20px_rgba(244,98,10,0.35)]">
                        <span class="font-heading font-bold text-white text-2xl">EO</span>
                    </div>
                    <h3 class="font-heading font-bold text-[#0D0D0D] text-lg">Elysée Ouattara</h3>
                    <p class="text-[#F4620A] text-sm font-semibold mt-1 mb-4">Fondateur & Manager général</p>
                    <p class="text-gray-500 text-sm leading-[1.75]">
                        À l'origine du projet NETSPRING, Elysée a construit un pont concret entre les entrepreneurs ivoiriens et les marchés chinois depuis 2018.
                    </p>
                </article>

                <!-- Kassamba Ben Idriss Diaby -->
                <article class="bg-white rounded-[20px] p-8 border border-[#E5E7EB] hover:border-[#F4620A]/30 hover:shadow-[0_8px_32px_rgba(244,98,10,0.08)] transition-all duration-[220ms] text-center">
                    <div class="w-20 h-20 rounded-full bg-[#0D0D0D] border-2 border-[#F4620A] flex items-center justify-center mx-auto mb-5">
                        <span class="font-heading font-bold text-white text-2xl">KB</span>
                    </div>
                    <h3 class="font-heading font-bold text-[#0D0D0D] text-lg">Kassamba Ben Idriss Diaby</h3>
                    <p class="text-[#F4620A] text-sm font-semibold mt-1 mb-4">Directeur Exécutif</p>
                    <p class="text-gray-500 text-sm leading-[1.75]">
                        Kassamba pilote les opérations et la stratégie de NETSPRING, assurant l'excellence du service et l'expansion continue de l'activité.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- MISSION / VISION -->
    <section class="py-20 bg-[#FAFAFA]" aria-label="Mission et vision NETSPRING">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-8">
                <article class="bg-white rounded-[20px] p-8 border border-[#E5E7EB] hover:shadow-[0_8px_32px_rgba(0,0,0,0.08)] transition-all duration-[220ms]">
                    <div class="w-12 h-12 bg-orange-50 rounded-[14px] flex items-center justify-center mb-6">
                        <Target class="w-6 h-6 text-[#F4620A]" aria-hidden="true" />
                    </div>
                    <h3 class="font-heading font-bold text-[#0D0D0D] text-xl mb-3">Notre mission</h3>
                    <p class="text-gray-600 leading-[1.75] text-sm">Rendre le commerce international accessible à tous les entrepreneurs africains en proposant un service clé en main, transparent et sécurisé pour importer depuis la Chine.</p>
                </article>
                <article class="bg-[#0D0D0D] rounded-[20px] p-8 hover:shadow-[0_8px_32px_rgba(0,0,0,0.15)] transition-all duration-[220ms]">
                    <div class="w-12 h-12 bg-[#F4620A]/15 rounded-[14px] flex items-center justify-center mb-6">
                        <Globe class="w-6 h-6 text-[#F4620A]" aria-hidden="true" />
                    </div>
                    <h3 class="font-heading font-bold text-white text-xl mb-3">Notre vision</h3>
                    <p class="text-gray-400 leading-[1.75] text-sm">Devenir le partenaire commercial de référence pour les entrepreneurs d'Afrique subsaharienne qui souhaitent développer leur activité grâce au commerce international.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- VALEURS -->
    <section class="py-24 bg-white" aria-labelledby="values-heading">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14">
                <h2 id="values-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">Nos valeurs</h2>
            </div>
            <ul class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6" role="list">
                <li v-for="val in values" :key="val.title"
                    class="text-center p-7 rounded-[16px] border border-[#E5E7EB] hover:border-[#F4620A]/30 hover:shadow-[0_8px_24px_rgba(244,98,10,0.07)] transition-all duration-[220ms]">
                    <div class="w-12 h-12 bg-orange-50 rounded-[14px] flex items-center justify-center mx-auto mb-4">
                        <component :is="val.icon" class="w-6 h-6 text-[#F4620A]" aria-hidden="true" />
                    </div>
                    <h3 class="font-heading font-semibold text-[#0D0D0D] mb-2">{{ val.title }}</h3>
                    <p class="text-gray-500 text-sm leading-[1.7]">{{ val.desc }}</p>
                </li>
            </ul>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-[#F4620A]" aria-label="Travaillez avec NETSPRING">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="font-heading font-bold text-white text-3xl mb-4">Prêt à travailler ensemble ?</h2>
            <p class="text-orange-100 text-lg mb-8">Rejoignez les 500+ entrepreneurs qui font confiance à NETSPRING.</p>
            <Link href="/importer"
                  class="inline-flex items-center gap-2 bg-white text-[#F4620A] font-bold px-8 py-4 rounded-[12px] hover:bg-orange-50 transition-all duration-[220ms] shadow-lg">
                Démarrer mon projet
                <ArrowRight class="w-5 h-5" aria-hidden="true" />
            </Link>
        </div>
    </section>

    </AppLayout>
</template>
