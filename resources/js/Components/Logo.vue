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
    const url = page.url ?? ''
    if (url.startsWith('/formations')) return '/netspring_formation.png'
    if (url.startsWith('/boutique'))   return '/netspring_boutique.png'
    return '/netspring.png'
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
