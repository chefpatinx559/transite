<script setup>
import { ref, onMounted } from 'vue'
import { Star } from 'lucide-vue-next'

// Témoignages passés depuis le controller Laravel via Inertia::render()
const props = defineProps({
    testimonials: {
        type: Array,
        default: () => [],
    },
})

// ── Données stats fixes ────────────────────────────────────────
const stats = [
    { target: 500,  suffix: '+',  label: 'Entrepreneurs\naccompagnés' },
    { target: 1000, suffix: '+',  label: 'Commandes\nréalisées' },
    { target: 15,   suffix: '+',  label: 'Pays\ndesservis' },
    { target: 4.9,  suffix: '/5', label: 'Satisfaction\nclients' },
]
const displayed = ref(stats.map(() => 0))

function animateCounters() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        stats.forEach((s, i) => { displayed.value[i] = s.target })
        return
    }
    const duration = 1800
    const start = performance.now()
    function tick(now) {
        const p = Math.min((now - start) / duration, 1)
        const ease = 1 - Math.pow(1 - p, 4)
        stats.forEach((s, i) => {
            displayed.value[i] = s.target % 1 !== 0
                ? parseFloat((s.target * ease).toFixed(1))
                : Math.floor(s.target * ease)
        })
        if (p < 1) requestAnimationFrame(tick)
        else stats.forEach((s, i) => { displayed.value[i] = s.target })
    }
    requestAnimationFrame(tick)
}

onMounted(() => {
    const el = document.getElementById('stats-section')
    if (!el) return
    const obs = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) { animateCounters(); obs.disconnect() }
    }, { threshold: 0.3 })
    obs.observe(el)
})
</script>

<template>
    <!-- ══════════════════════════════════════════════════
         CHIFFRES CLÉS
    ══════════════════════════════════════════════════ -->
    <section
        id="stats-section"
        aria-labelledby="stats-heading"
        class="noise-bg relative py-20 bg-[#0D0D0D]"
    >
        <div class="max-w-7xl mx-auto px-6">
            <h2 id="stats-heading" class="sr-only">Nos chiffres clés</h2>
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="(stat, i) in stats" :key="stat.label" class="text-center">
                    <dt class="sr-only">{{ stat.label.replace('\n', ' ') }}</dt>
                    <dd>
                        <p
                            class="font-heading font-bold text-[#F4620A] leading-none mb-2"
                            style="font-size: clamp(2.5rem, 5vw, 4rem);"
                            role="status"
                            :aria-label="`${displayed[i]}${stat.suffix} ${stat.label.replace('\n', ' ')}`"
                            aria-live="polite"
                        >
                            {{ displayed[i] }}<span class="text-2xl">{{ stat.suffix }}</span>
                        </p>
                        <p
                            class="text-gray-400 text-sm font-medium whitespace-pre-line leading-snug"
                            aria-hidden="true"
                        >{{ stat.label }}</p>
                    </dd>
                </div>
            </dl>
        </div>
    </section>

    <!-- ══════════════════════════════════════════════════
         TÉMOIGNAGES
    ══════════════════════════════════════════════════ -->
    <section
        v-if="testimonials.length"
        aria-labelledby="testimonials-heading"
        class="py-24 bg-white"
    >
        <div class="max-w-7xl mx-auto px-6">
            <header class="text-center mb-14">
                <p
                    class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3"
                    aria-hidden="true"
                >TÉMOIGNAGES</p>
                <h2
                    id="testimonials-heading"
                    class="font-heading font-bold text-[#0D0D0D]"
                    style="font-size:clamp(1.8rem,3.5vw,2.5rem);"
                >
                    Ils nous font confiance.
                </h2>
            </header>

            <ul class="grid md:grid-cols-3 gap-6" role="list">
                <li v-for="t in testimonials" :key="t.name">
                    <article
                        class="bg-[#FAFAFA] rounded-[16px] p-8 h-full flex flex-col hover:shadow-[0_8px_32px_rgba(0,0,0,0.08)] transition-all duration-[220ms] border border-transparent hover:border-[#F4620A]/10"
                        :aria-label="`Témoignage de ${t.name}, ${t.role}`"
                    >
                        <!-- Étoiles -->
                        <div
                            class="flex gap-1 mb-5"
                            :aria-label="`Note : ${t.rating} étoiles sur 5`"
                        >
                            <Star
                                v-for="n in t.rating"
                                :key="n"
                                class="w-4 h-4 text-[#F4620A] fill-[#F4620A]"
                                aria-hidden="true"
                            />
                        </div>

                        <!-- Citation -->
                        <blockquote class="text-gray-700 leading-[1.75] mb-6 flex-1 italic text-sm">
                            "{{ t.text }}"
                        </blockquote>

                        <!-- Auteur -->
                        <footer class="flex items-center gap-3">
                            <!-- Avatar : photo si disponible, sinon initiale -->
                            <div
                                v-if="t.avatar"
                                class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0"
                            >
                                <img
                                    :src="t.avatar"
                                    :alt="`Photo de ${t.name}`"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                />
                            </div>
                            <div
                                v-else
                                class="w-10 h-10 bg-[#F4620A] rounded-full flex items-center justify-center text-white font-heading font-bold text-sm flex-shrink-0"
                                aria-hidden="true"
                            >
                                {{ t.name.charAt(0) }}
                            </div>

                            <div>
                                <cite class="not-italic font-heading font-semibold text-[#0D0D0D] text-sm block">
                                    {{ t.name }}
                                </cite>
                                <span class="text-gray-400 text-xs">{{ t.role }}</span>
                            </div>
                        </footer>
                    </article>
                </li>
            </ul>
        </div>
    </section>
</template>
