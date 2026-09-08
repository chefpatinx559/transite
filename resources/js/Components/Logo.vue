<script setup>
const props = defineProps({
    variant: { type: String, default: 'full' }, // 'mark' | 'full'
    dark:    { type: Boolean, default: false  },
    size:    { type: Number,  default: 40     },
})

const strokeColor = props.dark ? '#ffffff' : '#0D0D0D'
</script>

<template>
    <div :class="variant === 'full' ? 'flex items-center gap-2.5' : ''">
        <!-- Mark SVG — hexagone C-shape + poignée de main -->
        <svg
            :width="size"
            :height="size"
            viewBox="0 0 64 64"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <!-- Hexagone ouvert à droite (C-shape) -->
            <path
                d="M56 20 L32 6 L8 20 L8 44 L32 58 L56 44"
                :stroke="strokeColor"
                stroke-width="4"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
            />
            <!-- Poignée de main — mains qui se rejoignent au centre -->
            <g stroke="#F4620A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none">
                <!-- Bras gauche -->
                <path d="M14 34 L22 28" />
                <!-- Bras droit -->
                <path d="M50 34 L42 28" />
                <!-- Doigts gauche -->
                <path d="M22 28 L26 24 M22 28 L24 32" />
                <!-- Doigts droit -->
                <path d="M42 28 L38 24 M42 28 L40 32" />
                <!-- Mains jointes — fond -->
                <path
                    d="M26 24 C28 22 30 22 32 23 C34 22 36 22 38 24 L40 28 C40 30 38 32 36 32 L28 32 C26 32 24 30 24 28 Z"
                    fill="#F4620A"
                    opacity="0.18"
                />
                <!-- Mains jointes — contour -->
                <path
                    d="M26 24 C28 22 30 22 32 23 C34 22 36 22 38 24 L40 28 C40 30 38 32 36 32 L28 32 C26 32 24 30 24 28 Z"
                />
                <!-- Poignets -->
                <path d="M24 32 L18 36 M40 32 L46 36" />
            </g>
        </svg>

        <!-- Wordmark -->
        <span
            v-if="variant === 'full'"
            class="font-heading font-bold tracking-wide leading-none select-none"
            :style="{ fontSize: `${size * 0.45}px`, color: dark ? '#ffffff' : '#0D0D0D' }"
        >
            NET<span style="color: #F4620A">SPRING</span>
        </span>
    </div>
</template>
