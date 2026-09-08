<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import {
    Search, Handshake, ShieldCheck, Package, FileText, Users,
    ArrowRight, CheckCircle2, ChevronDown, Send,
    MapPin, Clock, Phone, Ship, Plane, Calculator, Info,
} from 'lucide-vue-next'

const props = defineProps({ openForm: { type: Boolean, default: false } })

const form = useForm({
    name: '', email: '', whatsapp: '', city: '',
    product_description: '', quantity: '', budget: '',
    source_country: 'Chine', experience_level: '',
    transport: '', message: '',
})

function submit() {
    form.post('/importer/devis', {
        onSuccess: () => form.reset(),
    })
}

// ── Simulateur de coûts ──────────────────────────────────────
const simTab = ref('maritime') // 'maritime' | 'aerien'

// MARITIME
const m = ref({ longueur: '', largeur: '', hauteur: '', quantite: 1, poids: '', valeur: '', taxe: '20' })
const tauxDouane = [
    { label: '0% — Intrants agricoles, médicaments', value: '0' },
    { label: '5% — Matières premières, équipements', value: '5' },
    { label: '10% — Produits semi-finis', value: '10' },
    { label: '20% — Produits finis de consommation', value: '20' },
]
// Tarifs maritimes estimatifs Chine → Abidjan 2024
const TARIF_CBM_FCFA   = 45000  // ~70$ × 640 FCFA/$ — fret + THC + frais docs
const TARIF_TRANSIT_CI = 15000  // frais transit/dédouanement de base par CBM
const TVA              = 0.18
const TAXES_DIVERSES   = 0.025  // PCS 1% + taxe stat 1% + CEDEAO 0.5%

const simMaritime = computed(() => {
    const l = parseFloat(m.value.longueur) || 0
    const w = parseFloat(m.value.largeur)  || 0
    const h = parseFloat(m.value.hauteur)  || 0
    const q = parseInt(m.value.quantite)   || 1
    const kg = parseFloat(m.value.poids)   || 0
    const val = parseFloat(m.value.valeur) || 0
    const taux = parseFloat(m.value.taxe)  / 100

    if (!l || !w || !h) return null

    const cbm = (l * w * h * q) / 1_000_000          // m³ réels
    const poidsTonne = kg / 1000                       // tonnes
    const cbmFacturable = Math.max(cbm, poidsTonne)    // maritime : max(volume, poids)

    const fretTotal   = Math.ceil(cbmFacturable) * TARIF_CBM_FCFA  // arrondi au CBM supérieur
    const transit     = Math.max(1, Math.ceil(cbmFacturable)) * TARIF_TRANSIT_CI

    // Droits & taxes CI sur valeur CIF (valeur marchandise + fret)
    const cif         = val + fretTotal
    const droits      = cif * taux
    const taxesDiv    = cif * TAXES_DIVERSES
    const tva         = (cif + droits + taxesDiv) * TVA
    const douaneTotal = droits + taxesDiv + tva

    return {
        cbm: cbm.toFixed(3),
        cbmFacturable: cbmFacturable.toFixed(3),
        fretTotal,
        transit,
        douaneTotal: val > 0 ? douaneTotal : null,
        total: val > 0 ? fretTotal + transit + douaneTotal : fretTotal + transit,
        showDouane: val > 0,
    }
})

// AÉRIEN
const a = ref({ poids: '', longueur: '', largeur: '', hauteur: '', valeur: '', taxe: '20' })
const TARIF_KG_AERIEN = 3500  // ~5.5$ × 640 FCFA/$

const simAerien = computed(() => {
    const kg  = parseFloat(a.value.poids)    || 0
    const l   = parseFloat(a.value.longueur) || 0
    const w   = parseFloat(a.value.largeur)  || 0
    const h   = parseFloat(a.value.hauteur)  || 0
    const val = parseFloat(a.value.valeur)   || 0
    const taux = parseFloat(a.value.taxe)    / 100

    if (!kg && !l) return null

    // Poids volumétrique aérien : L×W×H (cm) / 5000
    const poidsVol    = l && w && h ? (l * w * h) / 5000 : 0
    const poidsFacturable = Math.max(kg, poidsVol)

    const fretTotal   = poidsFacturable * TARIF_KG_AERIEN
    const transit     = 5000 + poidsFacturable * 500 // frais fixes + variable

    const cif         = val + fretTotal
    const droits      = cif * taux
    const taxesDiv    = cif * TAXES_DIVERSES
    const tva         = (cif + droits + taxesDiv) * TVA
    const douaneTotal = droits + taxesDiv + tva

    return {
        kg: kg.toFixed(1),
        poidsVol: poidsVol > 0 ? poidsVol.toFixed(1) : null,
        poidsFacturable: poidsFacturable.toFixed(1),
        fretTotal,
        transit,
        douaneTotal: val > 0 ? douaneTotal : null,
        total: val > 0 ? fretTotal + transit + douaneTotal : fretTotal + transit,
        showDouane: val > 0,
    }
})

function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' FCFA' }

const timeline = [
    { num: '01', title: 'Besoin',       desc: 'Vous décrivez le produit que vous souhaitez importer.' },
    { num: '02', title: 'Sourcing',     desc: 'Nous cherchons les meilleurs fournisseurs pour vous.' },
    { num: '03', title: 'Fournisseur',  desc: 'Présentation des 3 meilleurs fournisseurs avec devis.' },
    { num: '04', title: 'Négociation',  desc: 'Nous négocions les prix et conditions en votre nom.' },
    { num: '05', title: 'Achat',        desc: 'Validation et passation de la commande sécurisée.' },
    { num: '06', title: 'Transport',    desc: 'Expédition maritime ou aérienne selon votre choix.' },
    { num: '07', title: 'Transit',      desc: 'Dédouanement complet pris en charge par NETSPRING.' },
    { num: '08', title: 'Livraison',    desc: 'Réception de votre marchandise en Côte d\'Ivoire.' },
]
</script>

<template>
    <Head title="Importer depuis la Chine — NETSPRING" />
    <AppLayout>

    <!-- HERO -->
    <section class="relative bg-[#0D0D0D] min-h-[60vh] flex items-center overflow-hidden" aria-labelledby="importer-heading">
        <div aria-hidden="true" class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0D0D0D] via-[#1a1a1a] to-[#2d1a0a]"></div>
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#F4620A]/8 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 py-24 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-4" aria-hidden="true">IMPORTATION</p>
                <h1 id="importer-heading" class="font-heading font-bold text-white mb-5 leading-tight" style="font-size:clamp(2rem,4vw,3rem);">
                    Importer depuis la Chine<br>devient plus simple.
                </h1>
                <p class="text-gray-400 text-lg leading-[1.75] mb-8 max-w-lg">
                    Nous vous accompagnons dans votre projet de la recherche du produit jusqu'à la réception de votre marchandise en Côte d'Ivoire.
                </p>
                <a href="#formulaire"
                   class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold px-7 py-4 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.4)] cursor-pointer">
                    Démarrer mon projet
                    <ArrowRight class="w-5 h-5" aria-hidden="true" />
                </a>
            </div>
            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4">
                <div v-for="stat in [{v:'500+',l:'Clients accompagnés'},{v:'8 ans',l:'D\'expérience'},{v:'1000+',l:'Commandes réalisées'},{v:'4.9/5',l:'Satisfaction'}]"
                     :key="stat.v"
                     class="bg-white/5 border border-white/10 rounded-[16px] p-6 text-center hover:bg-white/8 transition-colors">
                    <p class="font-heading font-bold text-[#F4620A] text-2xl mb-1">{{ stat.v }}</p>
                    <p class="text-gray-400 text-sm">{{ stat.l }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- PROCESSUS D'IMPORTATION -->
    <section class="py-24 bg-white" aria-labelledby="process-heading">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3" aria-hidden="true">LE PROCESSUS</p>
                <h2 id="process-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    De votre idée à la livraison<br>en 8 étapes.
                </h2>
            </div>
            <!-- Timeline -->
            <ol class="grid md:grid-cols-2 lg:grid-cols-4 gap-6" aria-label="Processus d'importation en 8 étapes">
                <li v-for="(step, i) in timeline" :key="step.num"
                    class="relative p-6 rounded-[16px] border border-[#E5E7EB] hover:border-[#F4620A]/30 hover:shadow-[0_4px_20px_rgba(244,98,10,0.07)] transition-all duration-[220ms]">
                    <div class="w-10 h-10 rounded-[10px] flex items-center justify-center text-sm font-heading font-bold mb-4"
                         :class="i < 4 ? 'bg-[#F4620A] text-white shadow-[0_2px_8px_rgba(244,98,10,0.3)]' : 'bg-gray-100 text-[#0D0D0D]'">
                        {{ step.num }}
                    </div>
                    <h3 class="font-heading font-semibold text-[#0D0D0D] mb-2">{{ step.title }}</h3>
                    <p class="text-gray-500 text-sm leading-[1.7]">{{ step.desc }}</p>
                </li>
            </ol>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════
         SIMULATEUR DE COÛTS
    ═══════════════════════════════════════════ -->
    <section id="simulateur" class="py-24 bg-[#0D0D0D]" aria-labelledby="sim-heading">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-10">
                <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3" aria-hidden="true">OUTIL GRATUIT</p>
                <h2 id="sim-heading" class="font-heading font-bold text-white mb-3" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    Simulateur de coût d'importation
                </h2>
                <p class="text-gray-400 max-w-lg mx-auto text-sm leading-[1.75]">
                    Estimez le coût total de votre import depuis la Chine jusqu'à Abidjan — fret, douanes et taxes inclus.
                </p>
            </div>

            <!-- Onglets Maritime / Aérien -->
            <div class="flex gap-3 mb-8 justify-center" role="tablist" aria-label="Mode de transport">
                <button
                    role="tab"
                    :aria-selected="simTab === 'maritime'"
                    class="flex items-center gap-2 px-6 py-3 rounded-[12px] font-semibold text-sm transition-all duration-[220ms] cursor-pointer"
                    :class="simTab === 'maritime' ? 'bg-[#F4620A] text-white shadow-[0_4px_16px_rgba(244,98,10,0.4)]' : 'bg-white/5 text-gray-400 hover:bg-white/10 border border-white/10'"
                    @click="simTab = 'maritime'"
                >
                    <Ship class="w-4 h-4" aria-hidden="true" />
                    Maritime (au m³)
                </button>
                <button
                    role="tab"
                    :aria-selected="simTab === 'aerien'"
                    class="flex items-center gap-2 px-6 py-3 rounded-[12px] font-semibold text-sm transition-all duration-[220ms] cursor-pointer"
                    :class="simTab === 'aerien' ? 'bg-[#F4620A] text-white shadow-[0_4px_16px_rgba(244,98,10,0.4)]' : 'bg-white/5 text-gray-400 hover:bg-white/10 border border-white/10'"
                    @click="simTab = 'aerien'"
                >
                    <Plane class="w-4 h-4" aria-hidden="true" />
                    Aérien (au kg)
                </button>
            </div>

            <!-- ── SIMULATEUR MARITIME ── -->
            <div v-if="simTab === 'maritime'" role="tabpanel" aria-label="Simulateur maritime">
                <div class="grid lg:grid-cols-2 gap-6">

                    <!-- Formulaire -->
                    <div class="bg-white/5 border border-white/10 rounded-[20px] p-7 space-y-5">
                        <h3 class="font-heading font-semibold text-white text-base flex items-center gap-2">
                            <Calculator class="w-5 h-5 text-[#F4620A]" aria-hidden="true" />
                            Dimensions du colis/palette
                        </h3>

                        <!-- Dimensions -->
                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide mb-2">Dimensions d'un colis (cm)</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="m-l" class="block text-xs text-gray-500 mb-1">Longueur</label>
                                    <input id="m-l" v-model="m.longueur" type="number" min="1" placeholder="100"
                                           class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                                </div>
                                <div>
                                    <label for="m-w" class="block text-xs text-gray-500 mb-1">Largeur</label>
                                    <input id="m-w" v-model="m.largeur" type="number" min="1" placeholder="80"
                                           class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                                </div>
                                <div>
                                    <label for="m-h" class="block text-xs text-gray-500 mb-1">Hauteur</label>
                                    <input id="m-h" v-model="m.hauteur" type="number" min="1" placeholder="60"
                                           class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                                </div>
                            </div>
                        </div>

                        <!-- Quantité + poids -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="m-q" class="block text-xs text-gray-500 mb-1">Nombre de colis</label>
                                <input id="m-q" v-model="m.quantite" type="number" min="1" placeholder="10"
                                       class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                            </div>
                            <div>
                                <label for="m-kg" class="block text-xs text-gray-500 mb-1">Poids total (kg)</label>
                                <input id="m-kg" v-model="m.poids" type="number" min="1" placeholder="500"
                                       class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                            </div>
                        </div>

                        <!-- Valeur marchandise (optionnel) -->
                        <div>
                            <label for="m-val" class="block text-xs text-gray-500 mb-1">
                                Valeur marchandise (FCFA) <span class="text-gray-600">— optionnel, pour estimer les taxes</span>
                            </label>
                            <input id="m-val" v-model="m.valeur" type="number" min="0" placeholder="1 500 000"
                                   class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                        </div>

                        <!-- Taux douane -->
                        <div v-if="m.valeur">
                            <label for="m-taxe" class="block text-xs text-gray-500 mb-1">Taux douane (TEC CEDEAO)</label>
                            <select id="m-taxe" v-model="m.taxe"
                                    class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]">
                                <option v-for="t in tauxDouane" :key="t.value" :value="t.value" class="bg-[#0D0D0D]">{{ t.label }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Résultat maritime -->
                    <div class="flex flex-col">
                        <div v-if="!simMaritime" class="flex-1 bg-white/3 border border-white/8 rounded-[20px] p-7 flex flex-col items-center justify-center text-center">
                            <Ship class="w-12 h-12 text-gray-700 mb-3" aria-hidden="true" />
                            <p class="text-gray-500 text-sm">Renseignez les dimensions<br>pour voir l'estimation</p>
                        </div>

                        <div v-else class="bg-white/5 border border-[#F4620A]/30 rounded-[20px] p-7 space-y-4 flex-1">
                            <h3 class="font-heading font-semibold text-white text-base">Estimation du coût total</h3>

                            <!-- Volume calculé -->
                            <div class="bg-white/5 rounded-[12px] p-4 space-y-1.5 text-sm">
                                <div class="flex justify-between text-gray-400">
                                    <span>Volume réel</span>
                                    <span class="text-white font-medium">{{ simMaritime.cbm }} m³</span>
                                </div>
                                <div class="flex justify-between text-gray-400">
                                    <span>Volume facturable <span class="text-xs">(max volume/poids)</span></span>
                                    <span class="text-[#F4620A] font-bold">{{ simMaritime.cbmFacturable }} m³</span>
                                </div>
                            </div>

                            <!-- Détail des coûts -->
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-gray-300">
                                    <span>Fret maritime Chine → Abidjan</span>
                                    <span>{{ fmt(simMaritime.fretTotal) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Transit & dédouanement CI</span>
                                    <span>{{ fmt(simMaritime.transit) }}</span>
                                </div>
                                <template v-if="simMaritime.showDouane">
                                    <div class="border-t border-white/10 my-2"></div>
                                    <div class="flex justify-between text-gray-300">
                                        <span>Droits & taxes douane CI</span>
                                        <span>{{ fmt(simMaritime.douaneTotal) }}</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Total -->
                            <div class="border-t border-[#F4620A]/30 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-white font-semibold text-sm">TOTAL ESTIMÉ</span>
                                    <span class="font-heading font-bold text-[#F4620A] text-xl">{{ fmt(simMaritime.total) }}</span>
                                </div>
                                <p v-if="!simMaritime.showDouane" class="text-gray-500 text-xs mt-1">
                                    Hors droits de douane — renseignez la valeur marchandise pour les inclure
                                </p>
                            </div>

                            <!-- CTA -->
                            <a href="#formulaire"
                               class="flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold py-3 rounded-[12px] text-sm transition-all cursor-pointer">
                                Obtenir un vrai devis
                                <ArrowRight class="w-4 h-4" aria-hidden="true" />
                            </a>
                        </div>

                        <!-- Note info -->
                        <div class="mt-4 flex gap-2 text-xs text-gray-600 items-start">
                            <Info class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" aria-hidden="true" />
                            <p>Tarif indicatif LCL (groupage) Chine→Abidjan. Basé sur ~45 000 FCFA/m³ tout compris. Les prix réels varient selon la saison, le port d'embarquement et le type de marchandise.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SIMULATEUR AÉRIEN ── -->
            <div v-if="simTab === 'aerien'" role="tabpanel" aria-label="Simulateur aérien">
                <div class="grid lg:grid-cols-2 gap-6">

                    <!-- Formulaire -->
                    <div class="bg-white/5 border border-white/10 rounded-[20px] p-7 space-y-5">
                        <h3 class="font-heading font-semibold text-white text-base flex items-center gap-2">
                            <Calculator class="w-5 h-5 text-[#F4620A]" aria-hidden="true" />
                            Poids & dimensions du colis
                        </h3>

                        <div>
                            <label for="a-kg" class="block text-xs text-gray-500 mb-1">Poids réel (kg) <span class="text-[#F4620A]">*</span></label>
                            <input id="a-kg" v-model="a.poids" type="number" min="0.1" step="0.1" placeholder="25"
                                   class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                        </div>

                        <div>
                            <p class="text-gray-400 text-xs font-medium uppercase tracking-wide mb-2">
                                Dimensions (cm) <span class="text-gray-600 normal-case">— pour calculer le poids volumétrique</span>
                            </p>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="a-l" class="block text-xs text-gray-500 mb-1">Longueur</label>
                                    <input id="a-l" v-model="a.longueur" type="number" min="1" placeholder="50"
                                           class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                                </div>
                                <div>
                                    <label for="a-w" class="block text-xs text-gray-500 mb-1">Largeur</label>
                                    <input id="a-w" v-model="a.largeur" type="number" min="1" placeholder="40"
                                           class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                                </div>
                                <div>
                                    <label for="a-h" class="block text-xs text-gray-500 mb-1">Hauteur</label>
                                    <input id="a-h" v-model="a.hauteur" type="number" min="1" placeholder="30"
                                           class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                                </div>
                            </div>
                            <p class="text-gray-600 text-xs mt-2">Formule aérien : L×W×H (cm) ÷ 5 000 = poids volumétrique (kg)</p>
                        </div>

                        <div>
                            <label for="a-val" class="block text-xs text-gray-500 mb-1">
                                Valeur marchandise (FCFA) <span class="text-gray-600">— optionnel</span>
                            </label>
                            <input id="a-val" v-model="a.valeur" type="number" min="0" placeholder="800 000"
                                   class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A] placeholder-gray-600" />
                        </div>

                        <div v-if="a.valeur">
                            <label for="a-taxe" class="block text-xs text-gray-500 mb-1">Taux douane</label>
                            <select id="a-taxe" v-model="a.taxe"
                                    class="w-full bg-white/5 border border-white/15 text-white rounded-[9px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]">
                                <option v-for="t in tauxDouane" :key="t.value" :value="t.value" class="bg-[#0D0D0D]">{{ t.label }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Résultat aérien -->
                    <div class="flex flex-col">
                        <div v-if="!simAerien" class="flex-1 bg-white/3 border border-white/8 rounded-[20px] p-7 flex flex-col items-center justify-center text-center">
                            <Plane class="w-12 h-12 text-gray-700 mb-3" aria-hidden="true" />
                            <p class="text-gray-500 text-sm">Renseignez le poids<br>pour voir l'estimation</p>
                        </div>

                        <div v-else class="bg-white/5 border border-[#F4620A]/30 rounded-[20px] p-7 space-y-4 flex-1">
                            <h3 class="font-heading font-semibold text-white text-base">Estimation du coût total</h3>

                            <!-- Poids calculés -->
                            <div class="bg-white/5 rounded-[12px] p-4 space-y-1.5 text-sm">
                                <div class="flex justify-between text-gray-400">
                                    <span>Poids réel</span>
                                    <span class="text-white font-medium">{{ simAerien.kg }} kg</span>
                                </div>
                                <div v-if="simAerien.poidsVol" class="flex justify-between text-gray-400">
                                    <span>Poids volumétrique</span>
                                    <span class="text-white font-medium">{{ simAerien.poidsVol }} kg</span>
                                </div>
                                <div class="flex justify-between text-gray-400">
                                    <span>Poids facturable <span class="text-xs">(le plus élevé)</span></span>
                                    <span class="text-[#F4620A] font-bold">{{ simAerien.poidsFacturable }} kg</span>
                                </div>
                            </div>

                            <!-- Détail -->
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-gray-300">
                                    <span>Fret aérien Chine → Abidjan</span>
                                    <span>{{ fmt(simAerien.fretTotal) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Transit & frais aéroportuaires</span>
                                    <span>{{ fmt(simAerien.transit) }}</span>
                                </div>
                                <template v-if="simAerien.showDouane">
                                    <div class="border-t border-white/10 my-2"></div>
                                    <div class="flex justify-between text-gray-300">
                                        <span>Droits & taxes douane CI</span>
                                        <span>{{ fmt(simAerien.douaneTotal) }}</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Total -->
                            <div class="border-t border-[#F4620A]/30 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-white font-semibold text-sm">TOTAL ESTIMÉ</span>
                                    <span class="font-heading font-bold text-[#F4620A] text-xl">{{ fmt(simAerien.total) }}</span>
                                </div>
                                <p v-if="!simAerien.showDouane" class="text-gray-500 text-xs mt-1">
                                    Hors droits de douane — renseignez la valeur marchandise pour les inclure
                                </p>
                            </div>

                            <a href="#formulaire"
                               class="flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] text-white font-semibold py-3 rounded-[12px] text-sm transition-all cursor-pointer">
                                Obtenir un vrai devis
                                <ArrowRight class="w-4 h-4" aria-hidden="true" />
                            </a>
                        </div>

                        <div class="mt-4 flex gap-2 text-xs text-gray-600 items-start">
                            <Info class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" aria-hidden="true" />
                            <p>Tarif indicatif fret aérien Chine→Abidjan. Basé sur ~3 500 FCFA/kg. Les tarifs varient selon la compagnie, le volume total et la nature des marchandises.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Récap taxes CI -->
            <div class="mt-10 bg-white/3 border border-white/8 rounded-[16px] p-6">
                <h3 class="text-white font-heading font-semibold text-sm mb-4 flex items-center gap-2">
                    <Info class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                    Taxes à l'importation en Côte d'Ivoire (sur valeur CIF)
                </h3>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <div v-for="tax in [
                        {label:'Droits de douane (TEC)',    taux:'0 – 20%',  desc:'Selon catégorie produit'},
                        {label:'TVA',                       taux:'18%',      desc:'Sur valeur + droits'},
                        {label:'Taxe statistique',          taux:'1%',       desc:'Sur valeur CIF'},
                        {label:'PCS + CEDEAO + UA',         taux:'~1,5%',    desc:'Prélèvements régionaux'},
                    ]" :key="tax.label" class="bg-white/5 rounded-[10px] p-3">
                        <p class="text-[#F4620A] font-bold text-base">{{ tax.taux }}</p>
                        <p class="text-gray-300 font-medium mt-0.5">{{ tax.label }}</p>
                        <p class="text-gray-500 mt-0.5">{{ tax.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FORMULAIRE DEVIS -->
    <section id="formulaire" class="py-24 bg-[#FAFAFA]" aria-labelledby="form-heading">
        <div class="max-w-5xl mx-auto px-6">
            <div class="text-center mb-12">
                <p class="text-[#F4620A] font-semibold text-xs tracking-[0.18em] uppercase mb-3" aria-hidden="true">DEVIS GRATUIT</p>
                <h2 id="form-heading" class="font-heading font-bold text-[#0D0D0D]" style="font-size:clamp(1.8rem,3.5vw,2.5rem);">
                    Parlons de votre projet.
                </h2>
                <p class="text-gray-500 mt-3">Remplissez ce formulaire et recevez un devis sous 24h.</p>
            </div>

            <!-- Success message -->
            <div v-if="form.wasSuccessful" class="mb-8 p-5 bg-green-50 border border-green-200 rounded-[12px] flex items-center gap-3" role="alert">
                <CheckCircle2 class="w-5 h-5 text-green-600 flex-shrink-0" aria-hidden="true" />
                <p class="text-green-800 font-medium">Votre demande a bien été envoyée ! Nous vous recontactons sous 24h.</p>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-[20px] p-8 lg:p-12 shadow-[0_4px_32px_rgba(0,0,0,0.06)] border border-[#E5E7EB]" aria-label="Formulaire de demande de devis">
                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Col gauche — Informations -->
                    <div class="space-y-5">
                        <h3 class="font-heading font-semibold text-[#0D0D0D] text-base mb-2">Vos informations</h3>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet <span class="text-[#F4620A]" aria-hidden="true">*</span></label>
                            <input id="name" v-model="form.name" type="text" required autocomplete="name" placeholder="Konan Kouamé"
                                   class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600" role="alert">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">Numéro WhatsApp <span class="text-[#F4620A]" aria-hidden="true">*</span></label>
                            <input id="whatsapp" v-model="form.whatsapp" type="tel" required autocomplete="tel" placeholder="+225 0700000000"
                                   class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                            <input id="email" v-model="form.email" type="email" autocomplete="email" placeholder="votre@email.com"
                                   class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1.5">Ville</label>
                            <input id="city" v-model="form.city" type="text" placeholder="Abidjan, Bouaké..."
                                   class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                        </div>
                    </div>

                    <!-- Col droite — Projet -->
                    <div class="space-y-5">
                        <h3 class="font-heading font-semibold text-[#0D0D0D] text-base mb-2">Votre projet</h3>

                        <div>
                            <label for="product" class="block text-sm font-medium text-gray-700 mb-1.5">Produit à importer <span class="text-[#F4620A]" aria-hidden="true">*</span></label>
                            <textarea id="product" v-model="form.product_description" required rows="3" placeholder="Décrivez le produit : type, spécifications, usage..."
                                      class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1.5">Quantité estimée</label>
                                <input id="quantity" v-model="form.quantity" type="text" placeholder="Ex: 500 unités"
                                       class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                            </div>
                            <div>
                                <label for="budget" class="block text-sm font-medium text-gray-700 mb-1.5">Budget (FCFA)</label>
                                <input id="budget" v-model="form.budget" type="text" placeholder="Ex: 500 000 FCFA"
                                       class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all" />
                            </div>
                        </div>

                        <div>
                            <fieldset>
                                <legend class="block text-sm font-medium text-gray-700 mb-2">Mode de transport souhaité</legend>
                                <div class="flex gap-4">
                                    <label v-for="opt in ['Aérien','Maritime','Je ne sais pas']" :key="opt" class="flex items-center gap-2 cursor-pointer text-sm text-gray-600">
                                        <input type="radio" :value="opt" v-model="form.transport" class="accent-[#F4620A]" />
                                        {{ opt }}
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1.5">Message complémentaire</label>
                    <textarea id="message" v-model="form.message" rows="3" placeholder="Informations supplémentaires, urgence, questions..."
                              class="w-full border border-[#E5E7EB] rounded-[10px] px-4 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all resize-none"></textarea>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-4 items-center">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-semibold px-8 py-4 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:-translate-y-0.5 active:translate-y-0 cursor-pointer text-base"
                        aria-label="Envoyer ma demande de devis"
                    >
                        <Send class="w-5 h-5" aria-hidden="true" />
                        {{ form.processing ? 'Envoi...' : 'ENVOYER MA DEMANDE' }}
                    </button>
                    <p class="text-gray-400 text-xs">Réponse sous 24h · Sans engagement</p>
                </div>
            </form>
        </div>
    </section>

    </AppLayout>
</template>
