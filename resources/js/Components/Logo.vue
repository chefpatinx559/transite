<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import imgMain      from '../../images/netspring-logo.webp'
import imgFormation from '../../images/netspring_formation-logo.webp'
import imgBoutique  from '../../images/netspring_boutique-logo.webp'

const props = defineProps({
    dark: { type: Boolean, default: false },
    size: { type: Number,  default: 40   },
    src:  { type: String,  default: null },
})

const page = usePage()

const logoMeta = computed(() => {
    if (props.src) {
        return { src: props.src, ratio: 273 / 104 }
    }
    const url = page.url ?? ''
    if (url.startsWith('/formations')) return { src: imgFormation, ratio: 286 / 104 }
    if (url.startsWith('/boutique'))   return { src: imgBoutique,  ratio: 256 / 104 }
    return { src: imgMain, ratio: 273 / 104 }
})

const computedWidth = computed(() => Math.round(props.size * logoMeta.value.ratio))
</script>

<template>
    <div
        v-if="dark"
        :style="{ display: 'inline-flex', background: 'white', borderRadius: '10px', padding: '8px' }"
    >
        <img
            :src="logoMeta.src"
            alt="NETSPRING"
            :width="computedWidth"
            :height="size"
            :style="{ height: `${size}px`, width: `${computedWidth}px` }"
            class="object-contain"
            decoding="async"
        />
    </div>
    <img
        v-else
        :src="logoMeta.src"
        alt="NETSPRING"
        :width="computedWidth"
        :height="size"
        :style="{ height: `${size}px`, width: `${computedWidth}px` }"
        class="object-contain"
        decoding="async"
    />
</template>
