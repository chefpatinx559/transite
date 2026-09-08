<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Eye, EyeOff, UserPlus } from 'lucide-vue-next'
import { ref } from 'vue'

const showPassword  = ref(false)
const showConfirm   = ref(false)

const form = useForm({
    first_name:            '',
    last_name:             '',
    email:                 '',
    whatsapp:              '',
    password:              '',
    password_confirmation: '',
    terms:                 false,
})

function submit() {
    form.post('/inscription', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Créer mon compte — NETSPRING" />

    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="text-center mb-8">
                <Link href="/" aria-label="NETSPRING — Retour à l'accueil" class="inline-flex items-center gap-2.5 justify-center">
                    <div class="w-10 h-10 bg-[#F4620A] rounded-[11px] flex items-center justify-center shadow-[0_2px_12px_rgba(244,98,10,0.4)]">
                        <span class="text-white font-black text-xl leading-none font-heading" aria-hidden="true">N</span>
                    </div>
                    <span class="font-heading font-bold text-2xl text-[#0D0D0D] tracking-tight">
                        NET<span class="text-[#F4620A]">SPRING</span>
                    </span>
                </Link>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-[16px] shadow-[0_8px_32px_rgba(0,0,0,0.09)] p-8">
                <h1 class="font-heading font-bold text-2xl text-[#0D0D0D] mb-1 text-center">Créer mon compte</h1>
                <p class="text-gray-500 text-sm text-center mb-8">Rejoignez NETSPRING et accédez à votre espace personnel.</p>

                <form aria-label="Formulaire d'inscription" @submit.prevent="submit">

                    <!-- Name row -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                Prénom <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="first_name"
                                v-model="form.first_name"
                                type="text"
                                autocomplete="given-name"
                                required
                                placeholder="Kofi"
                                class="w-full border border-gray-200 rounded-[10px] px-3.5 py-2.5 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                :class="{ 'border-red-400': form.errors.first_name }"
                                aria-required="true"
                            />
                            <p v-if="form.errors.first_name" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.first_name }}</p>
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                                Nom <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="last_name"
                                v-model="form.last_name"
                                type="text"
                                autocomplete="family-name"
                                required
                                placeholder="Kouassi"
                                class="w-full border border-gray-200 rounded-[10px] px-3.5 py-2.5 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                :class="{ 'border-red-400': form.errors.last_name }"
                                aria-required="true"
                            />
                            <p v-if="form.errors.last_name" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.last_name }}</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                            Adresse email <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            required
                            placeholder="votre@email.com"
                            class="w-full border border-gray-200 rounded-[10px] px-3.5 py-2.5 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                            :class="{ 'border-red-400': form.errors.email }"
                            aria-required="true"
                        />
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.email }}</p>
                    </div>

                    <!-- WhatsApp -->
                    <div class="mb-4">
                        <label for="whatsapp" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                            Numéro WhatsApp <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="whatsapp"
                            v-model="form.whatsapp"
                            type="tel"
                            autocomplete="tel"
                            required
                            placeholder="+225 07 XX XX XX XX"
                            class="w-full border border-gray-200 rounded-[10px] px-3.5 py-2.5 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                            :class="{ 'border-red-400': form.errors.whatsapp }"
                            aria-required="true"
                        />
                        <p v-if="form.errors.whatsapp" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.whatsapp }}</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                            Mot de passe <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                placeholder="Minimum 8 caractères"
                                class="w-full border border-gray-200 rounded-[10px] px-3.5 py-2.5 pr-11 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                :class="{ 'border-red-400': form.errors.password }"
                                aria-required="true"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
                                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" class="w-4 h-4" aria-hidden="true" />
                                <Eye v-else class="w-4 h-4" aria-hidden="true" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.password }}</p>
                    </div>

                    <!-- Password confirmation -->
                    <div class="mb-5">
                        <label for="password_confirmation" class="block text-sm font-semibold text-[#0D0D0D] mb-1.5">
                            Confirmer le mot de passe <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showConfirm ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                placeholder="Répétez le mot de passe"
                                class="w-full border border-gray-200 rounded-[10px] px-3.5 py-2.5 pr-11 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                :class="{ 'border-red-400': form.errors.password_confirmation }"
                                aria-required="true"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
                                :aria-label="showConfirm ? 'Masquer la confirmation' : 'Afficher la confirmation'"
                                @click="showConfirm = !showConfirm"
                            >
                                <EyeOff v-if="showConfirm" class="w-4 h-4" aria-hidden="true" />
                                <Eye v-else class="w-4 h-4" aria-hidden="true" />
                            </button>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.password_confirmation }}</p>
                    </div>

                    <!-- Terms checkbox -->
                    <div class="flex items-start gap-2.5 mb-6">
                        <input
                            id="terms"
                            v-model="form.terms"
                            type="checkbox"
                            required
                            class="w-4 h-4 mt-0.5 accent-[#F4620A] rounded cursor-pointer flex-shrink-0"
                            aria-required="true"
                        />
                        <label for="terms" class="text-sm text-gray-600 cursor-pointer leading-snug">
                            J'accepte les
                            <a href="#" class="text-[#F4620A] font-semibold hover:underline">conditions d'utilisation</a>
                            et la
                            <a href="#" class="text-[#F4620A] font-semibold hover:underline">politique de confidentialité</a>
                            de NETSPRING.
                        </label>
                    </div>
                    <p v-if="form.errors.terms" class="text-red-500 text-xs -mt-4 mb-4" role="alert">{{ form.errors.terms }}</p>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_24px_rgba(244,98,10,0.45)] hover:-translate-y-px active:translate-y-0 cursor-pointer"
                        aria-label="Créer mon compte NETSPRING"
                    >
                        <UserPlus v-if="!form.processing" class="w-4.5 h-4.5" aria-hidden="true" />
                        {{ form.processing ? 'Création en cours…' : 'Créer mon compte' }}
                    </button>
                </form>

                <!-- Login link -->
                <p class="text-center text-sm text-gray-500 mt-6">
                    Déjà un compte ?
                    <Link href="/connexion" class="text-[#F4620A] font-semibold hover:underline">
                        Se connecter
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>
