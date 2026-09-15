<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    dark: { type: Boolean, default: false },
    size: { type: Number,  default: 40   },
    src:  { type: String,  default: null },
})

const page = usePage()

const logoSrc = computed(() => {
    if (props.src) return props.src
    const base = (page.props.assetUrl ?? '').replace(/\/$/, '')
    const url  = page.url ?? ''
    if (url.startsWith('/formations')) return `${base}/netspring_formation.png`
    if (url.startsWith('/boutique'))   return `${base}/netspring_boutique.png`
    return `${base}/netspring.png`
})
</script>

<template>
    <img
        :src="logoSrc"
        alt="NETSPRING"
        :style="{ height: `${size}px`, width: 'auto' }"
        class="object-contain"
    />
</template>
