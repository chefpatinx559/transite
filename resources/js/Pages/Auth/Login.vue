<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Eye, EyeOff, LogIn } from 'lucide-vue-next'
import { ref } from 'vue'

const showPassword = ref(false)

const form = useForm({
    email:    '',
    password: '',
    remember: false,
})

function submit() {
    form.post('/connexion', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Se connecter — NETSPRING" />

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
                <h1 class="font-heading font-bold text-2xl text-[#0D0D0D] mb-1 text-center">Se connecter</h1>
                <p class="text-gray-500 text-sm text-center mb-8">Accédez à votre espace personnel NETSPRING.</p>

                <form aria-label="Formulaire de connexion" @submit.prevent="submit">

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
                            class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                            :class="{ 'border-red-400': form.errors.email }"
                            aria-required="true"
                            :aria-describedby="form.errors.email ? 'err-email' : undefined"
                        />
                        <p v-if="form.errors.email" id="err-email" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-semibold text-[#0D0D0D]">
                                Mot de passe <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <a
                                href="#"
                                class="text-xs text-[#F4620A] hover:underline"
                                aria-label="Réinitialiser le mot de passe (non disponible)"
                                tabindex="0"
                            >Mot de passe oublié ?</a>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                placeholder="Votre mot de passe"
                                class="w-full border border-gray-200 rounded-[10px] px-3.5 py-3 pr-11 text-sm text-[#0D0D0D] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#F4620A] focus:border-transparent transition-all"
                                :class="{ 'border-red-400': form.errors.password }"
                                aria-required="true"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
                                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                                @click="showPassword = !showPassword"
                            >
                                <EyeOff v-if="showPassword" class="w-4.5 h-4.5" aria-hidden="true" />
                                <Eye v-else class="w-4.5 h-4.5" aria-hidden="true" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1" role="alert">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-2 mb-6">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="w-4 h-4 accent-[#F4620A] rounded cursor-pointer"
                            aria-label="Se souvenir de moi"
                        />
                        <label for="remember" class="text-sm text-gray-600 cursor-pointer">Se souvenir de moi</label>
                    </div>

                    <!-- General error -->
                    <div v-if="form.errors.general" class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-[10px] px-4 py-3 mb-5" role="alert">
                        {{ form.errors.general }}
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 bg-[#F4620A] hover:bg-[#d45208] disabled:opacity-60 text-white font-bold py-3.5 rounded-[12px] transition-all duration-[220ms] shadow-[0_4px_16px_rgba(244,98,10,0.35)] hover:shadow-[0_6px_24px_rgba(244,98,10,0.45)] hover:-translate-y-px active:translate-y-0 cursor-pointer"
                        aria-label="Se connecter à mon compte"
                    >
                        <LogIn v-if="!form.processing" class="w-4.5 h-4.5" aria-hidden="true" />
                        {{ form.processing ? 'Connexion…' : 'Se connecter' }}
                    </button>
                </form>

                <!-- Register link -->
                <p class="text-center text-sm text-gray-500 mt-6">
                    Pas encore de compte ?
                    <Link href="/inscription" class="text-[#F4620A] font-semibold hover:underline">
                        Créer un compte
                    </Link>
                </p>
            </div>
        </div>
    </div>
</template>
