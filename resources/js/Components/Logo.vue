<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import imgMain      from '../../images/netspring.png'
import imgFormation from '../../images/netspring_formation.png'
import imgBoutique  from '../../images/netspring_boutique.png'

const props = defineProps({
    dark: { type: Boolean, default: false },
    size: { type: Number,  default: 40   },
    src:  { type: String,  default: null },
})

const page = usePage()

const logoSrc = computed(() => {
    if (props.src) return props.src
    const url = page.url ?? ''
    if (url.startsWith('/formations')) return imgFormation
    if (url.startsWith('/boutique'))   return imgBoutique
    return imgMain
})
</script>

<template>
    <div
        v-if="dark"
        :style="{ display: 'inline-flex', background: 'white', borderRadius: '8px', padding: '4px' }"
    >
        <img
            :src="logoSrc"
            alt="NETSPRING"
            :style="{ height: `${size}px`, width: 'auto' }"
            class="object-contain"
        />
    </div>
    <img
        v-else
        :src="logoSrc"
        alt="NETSPRING"
        :style="{ height: `${size}px`, width: 'auto' }"
        class="object-contain"
    />
</template>
