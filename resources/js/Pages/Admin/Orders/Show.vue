<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { ArrowLeft, Package, User, MapPin, CreditCard, Truck, CheckCircle2, XCircle, RotateCcw } from 'lucide-vue-next'

const props = defineProps({
    order: { type: Object, required: true },
})

const updatingStatus = ref(false)
const newStatus = ref(props.order.status)

const allStatuses = [
    { value: 'pending',    label: 'En attente',  color: 'bg-gray-100 text-gray-700' },
    { value: 'paid',       label: 'Payé',         color: 'bg-emerald-100 text-emerald-700' },
    { value: 'processing', label: 'En cours',     color: 'bg-blue-100 text-blue-700' },
    { value: 'shipped',    label: 'Expédié',      color: 'bg-purple-100 text-purple-700' },
    { value: 'delivered',  label: 'Livré',        color: 'bg-emerald-200 text-emerald-800' },
    { value: 'cancelled',  label: 'Annulé',       color: 'bg-red-100 text-red-600' },
    { value: 'refunded',   label: 'Remboursé',    color: 'bg-orange-100 text-orange-700' },
]

const paymentMap = {
    cinetpay:     'CinetPay',
    wave:         'Wave CI',
    orange_money: 'Orange Money',
    manual:       'Paiement manuel',
}

function currentStatusObj() {
    return allStatuses.find(s => s.value === props.order.status) ?? allStatuses[0]
}

function updateStatus() {
    if (newStatus.value === props.order.status) return
    updatingStatus.value = true
    router.patch(`/admin/commandes/${props.order.id}/statut`, { status: newStatus.value }, {
        onFinish: () => { updatingStatus.value = false }
    })
}

function formatPrice(n) { return Number(n ?? 0).toLocaleString('fr-FR') + ' FCFA' }
function formatDate(d) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('fr-FR', { day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' })
}
</script>

<template>
    <Head :title="`Commande ${order.order_number} — Admin NETSPRING`" />
    <AdminLayout>
        <template #page-title>Commande {{ order.order_number }}</template>
        <template #header-actions>
            <Link href="/admin/commandes"
                  class="flex items-center gap-2 text-gray-400 hover:text-white text-sm transition-colors">
                <ArrowLeft class="w-4 h-4" aria-hidden="true" />
                Retour aux commandes
            </Link>
        </template>

        <div class="grid lg:grid-cols-3 gap-6">

            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Articles commandés -->
                <section class="bg-white rounded-[16px] border border-[#E5E7EB] overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#E5E7EB] flex items-center gap-2">
                        <Package class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        <h2 class="font-heading font-semibold text-[#0D0D0D] text-sm">Articles commandés</h2>
                    </div>
                    <div class="divide-y divide-[#E5E7EB]">
                        <div v-for="item in order.items" :key="item.id" class="flex items-center gap-4 px-6 py-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-[10px] flex items-center justify-center flex-shrink-0">
                                <Package class="w-6 h-6 text-gray-300" aria-hidden="true" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-[#0D0D0D] text-sm truncate">{{ item.name }}</p>
                                <p class="text-gray-400 text-xs capitalize">{{ item.item_type }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-sm font-medium text-[#0D0D0D]">{{ item.quantity }} × {{ formatPrice(item.price) }}</p>
                                <p class="text-sm font-bold text-[#F4620A]">{{ formatPrice(item.subtotal) }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Totaux -->
                    <div class="px-6 py-4 bg-[#FAFAFA] border-t border-[#E5E7EB] space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Sous-total</span>
                            <span>{{ formatPrice(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Frais de livraison</span>
                            <span>{{ formatPrice(order.shipping_cost) }}</span>
                        </div>
                        <div v-if="order.discount_amount > 0" class="flex justify-between text-sm text-green-600">
                            <span>Remise</span>
                            <span>-{{ formatPrice(order.discount_amount) }}</span>
                        </div>
                        <div class="flex justify-between font-heading font-bold text-[#0D0D0D] border-t border-[#E5E7EB] pt-2">
                            <span>Total</span>
                            <span class="text-[#F4620A]">{{ formatPrice(order.total) }}</span>
                        </div>
                    </div>
                </section>

                <!-- Informations client -->
                <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-6 space-y-5">
                    <div class="flex items-center gap-2 mb-1">
                        <User class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        <h2 class="font-heading font-semibold text-[#0D0D0D] text-sm">Informations client</h2>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Nom</p>
                            <p class="font-medium text-[#0D0D0D]">{{ order.customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Email</p>
                            <a :href="`mailto:${order.customer_email}`" class="text-[#F4620A] hover:underline">{{ order.customer_email }}</a>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Téléphone</p>
                            <a v-if="order.customer_phone" :href="`tel:${order.customer_phone}`" class="text-[#0D0D0D]">{{ order.customer_phone }}</a>
                            <span v-else class="text-gray-400">—</span>
                        </div>
                        <div v-if="order.user">
                            <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Compte client</p>
                            <p class="text-[#0D0D0D]">{{ order.user.first_name }} {{ order.user.last_name }}</p>
                        </div>
                    </div>
                </section>

                <!-- Adresse de livraison -->
                <section v-if="order.shipping_address" class="bg-white rounded-[16px] border border-[#E5E7EB] p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <MapPin class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        <h2 class="font-heading font-semibold text-[#0D0D0D] text-sm">Adresse de livraison</h2>
                    </div>
                    <address class="not-italic text-sm text-gray-700 space-y-1">
                        <p v-if="order.shipping_address.address">{{ order.shipping_address.address }}</p>
                        <p v-if="order.shipping_address.city">{{ order.shipping_address.city }}</p>
                        <p v-if="order.shipping_address.country">{{ order.shipping_address.country }}</p>
                    </address>
                    <p v-if="order.notes" class="mt-3 text-sm text-gray-500 italic">"{{ order.notes }}"</p>
                </section>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">

                <!-- Statut & mise à jour -->
                <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-6 space-y-4">
                    <h2 class="font-heading font-semibold text-[#0D0D0D] text-sm flex items-center gap-2">
                        <Truck class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        Statut commande
                    </h2>

                    <!-- Statut actuel -->
                    <span :class="['text-sm font-semibold px-3 py-1.5 rounded-full', currentStatusObj().color]">
                        {{ currentStatusObj().label }}
                    </span>

                    <!-- Changer statut -->
                    <div>
                        <label for="new-status" class="block text-xs text-gray-500 mb-1.5">Changer le statut</label>
                        <select id="new-status" v-model="newStatus"
                                class="w-full border border-gray-200 rounded-[10px] px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#F4620A]">
                            <option v-for="s in allStatuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </div>
                    <button
                        @click="updateStatus"
                        :disabled="updatingStatus || newStatus === order.status"
                        class="w-full bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-50 text-white font-semibold py-2.5 rounded-[10px] text-sm transition-all cursor-pointer"
                        aria-label="Enregistrer le nouveau statut"
                    >
                        {{ updatingStatus ? 'Mise à jour…' : 'Enregistrer' }}
                    </button>
                </section>

                <!-- Paiement -->
                <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-6 space-y-3">
                    <h2 class="font-heading font-semibold text-[#0D0D0D] text-sm flex items-center gap-2">
                        <CreditCard class="w-4 h-4 text-[#F4620A]" aria-hidden="true" />
                        Paiement
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Méthode</span>
                            <span class="font-medium text-[#0D0D0D]">{{ paymentMap[order.payment_method] ?? order.payment_method ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Statut</span>
                            <span :class="order.payment_status === 'paid' ? 'text-emerald-600 font-semibold' : 'text-red-500 font-semibold'">
                                {{ order.payment_status === 'paid' ? 'Payé' : order.payment_status === 'unpaid' ? 'Non payé' : order.payment_status }}
                            </span>
                        </div>
                        <div v-if="order.payment_ref" class="flex justify-between text-gray-600">
                            <span>Réf. transaction</span>
                            <span class="font-mono text-xs text-[#0D0D0D]">{{ order.payment_ref }}</span>
                        </div>
                    </div>
                </section>

                <!-- Dates -->
                <section class="bg-white rounded-[16px] border border-[#E5E7EB] p-6 space-y-3">
                    <h2 class="font-heading font-semibold text-[#0D0D0D] text-sm">Dates</h2>
                    <div class="space-y-2 text-xs text-gray-500">
                        <div class="flex justify-between">
                            <span>Créée le</span>
                            <span class="text-[#0D0D0D]">{{ formatDate(order.created_at) }}</span>
                        </div>
                        <div v-if="order.shipped_at" class="flex justify-between">
                            <span>Expédiée le</span>
                            <span class="text-purple-700">{{ formatDate(order.shipped_at) }}</span>
                        </div>
                        <div v-if="order.delivered_at" class="flex justify-between">
                            <span>Livrée le</span>
                            <span class="text-emerald-700">{{ formatDate(order.delivered_at) }}</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>
