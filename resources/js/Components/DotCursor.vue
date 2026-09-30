<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    headColor:      { type: String, default: '#F4620A' },
    trailColor:     { type: String, default: '#d45208' },
    size:           { type: Number, default: 24 },
    trailLength:    { type: Number, default: 12 },
    trailThickness: { type: Number, default: 10 },
})

const canvas = ref(null)
let ctx = null
let raf = null
let mouse = { x: -100, y: -100 }
let head = { x: -100, y: -100, vx: 0, vy: 0 }
let trail = []
let hovering = false
let visible = false
let isTouchDevice = false

const SPRING = 0.15
const DAMPING = 0.72

function hexToRgb(hex) {
    const r = parseInt(hex.slice(1, 3), 16)
    const g = parseInt(hex.slice(3, 5), 16)
    const b = parseInt(hex.slice(5, 7), 16)
    return { r, g, b }
}

function isInteractive(el) {
    if (!el) return false
    const tag = el.tagName
    if (tag === 'A' || tag === 'BUTTON' || tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return true
    if (el.closest('a, button, [role="button"], [data-cursor-hover]')) return true
    const style = window.getComputedStyle(el)
    return style.cursor === 'pointer'
}

function onMouseMove(e) {
    mouse.x = e.clientX
    mouse.y = e.clientY
    if (!visible) visible = true
    hovering = isInteractive(e.target)
}

function onMouseLeave() {
    visible = false
}

function onMouseEnter() {
    visible = true
}

function onTouchStart() {
    isTouchDevice = true
}

function resize() {
    if (!canvas.value) return
    canvas.value.width = window.innerWidth
    canvas.value.height = window.innerHeight
}

function animate() {
    if (!ctx || isTouchDevice) return

    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height)

    const ax = (mouse.x - head.x) * SPRING
    const ay = (mouse.y - head.y) * SPRING
    head.vx = (head.vx + ax) * DAMPING
    head.vy = (head.vy + ay) * DAMPING
    head.x += head.vx
    head.y += head.vy

    trail.unshift({ x: head.x, y: head.y })
    if (trail.length > props.trailLength) trail.length = props.trailLength

    if (visible && trail.length > 1) {
        const rgb = hexToRgb(props.trailColor)
        for (let i = trail.length - 1; i >= 1; i--) {
            const t = 1 - i / trail.length
            const thickness = props.trailThickness * t
            const alpha = t * 0.5
            ctx.beginPath()
            ctx.moveTo(trail[i].x, trail[i].y)
            ctx.lineTo(trail[i - 1].x, trail[i - 1].y)
            ctx.strokeStyle = `rgba(${rgb.r},${rgb.g},${rgb.b},${alpha})`
            ctx.lineWidth = thickness
            ctx.lineCap = 'round'
            ctx.stroke()
        }
    }

    if (visible) {
        const targetSize = hovering ? props.size * 1.8 : props.size
        const currentSize = parseFloat(canvas.value.dataset.currentSize || props.size)
        const newSize = currentSize + (targetSize - currentSize) * 0.15
        canvas.value.dataset.currentSize = newSize

        const rgb = hexToRgb(props.headColor)

        if (hovering) {
            ctx.beginPath()
            ctx.arc(head.x, head.y, newSize / 2, 0, Math.PI * 2)
            ctx.strokeStyle = `rgba(${rgb.r},${rgb.g},${rgb.b},0.6)`
            ctx.lineWidth = 2
            ctx.stroke()
            ctx.closePath()

            ctx.beginPath()
            ctx.arc(head.x, head.y, 4, 0, Math.PI * 2)
            ctx.fillStyle = props.headColor
            ctx.fill()
            ctx.closePath()
        } else {
            ctx.beginPath()
            ctx.arc(head.x, head.y, newSize / 2, 0, Math.PI * 2)
            ctx.fillStyle = `rgba(${rgb.r},${rgb.g},${rgb.b},0.85)`
            ctx.fill()
            ctx.closePath()

            ctx.beginPath()
            ctx.arc(head.x, head.y, newSize / 2 + 6, 0, Math.PI * 2)
            ctx.strokeStyle = `rgba(${rgb.r},${rgb.g},${rgb.b},0.15)`
            ctx.lineWidth = 1
            ctx.stroke()
            ctx.closePath()
        }
    }

    raf = requestAnimationFrame(animate)
}

onMounted(() => {
    if (typeof window === 'undefined') return
    if ('ontouchstart' in window && navigator.maxTouchPoints > 0 && !window.matchMedia('(pointer: fine)').matches) {
        isTouchDevice = true
        return
    }

    ctx = canvas.value?.getContext('2d')
    if (!ctx) return

    resize()
    window.addEventListener('mousemove', onMouseMove, { passive: true })
    window.addEventListener('resize', resize, { passive: true })
    document.addEventListener('mouseleave', onMouseLeave)
    document.addEventListener('mouseenter', onMouseEnter)
    window.addEventListener('touchstart', onTouchStart, { once: true, passive: true })

    raf = requestAnimationFrame(animate)
})

onUnmounted(() => {
    if (raf) cancelAnimationFrame(raf)
    window.removeEventListener('mousemove', onMouseMove)
    window.removeEventListener('resize', resize)
    document.removeEventListener('mouseleave', onMouseLeave)
    document.removeEventListener('mouseenter', onMouseEnter)
})
</script>

<template>
    <canvas
        ref="canvas"
        class="dot-cursor-canvas"
        aria-hidden="true"
    />
</template>

<style scoped>
.dot-cursor-canvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none;
    z-index: 9999;
}
</style>
