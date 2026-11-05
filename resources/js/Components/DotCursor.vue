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
let resizeRaf = null
let mouse = { x: -100, y: -100 }
let head = { x: -100, y: -100, vx: 0, vy: 0 }
let currentSize = props.size
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

const headRgb  = hexToRgb(props.headColor)
const trailRgb = hexToRgb(props.trailColor)

// Pure DOM tag/selector check — zero window.getComputedStyle() to avoid forced style recalc
function isInteractive(el) {
    if (!el || typeof el.closest !== 'function') return false
    const tag = el.tagName
    if (tag === 'A' || tag === 'BUTTON' || tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || tag === 'LABEL' || tag === 'SUMMARY') return true
    return Boolean(el.closest('a, button, [role="button"], [data-cursor-hover], .cursor-pointer'))
}

function scheduleFrame() {
    if (raf === null && ctx && !isTouchDevice) {
        raf = requestAnimationFrame(animate)
    }
}

function onMouseMove(e) {
    mouse.x = e.clientX
    mouse.y = e.clientY
    if (!visible) visible = true
    hovering = isInteractive(e.target)
    scheduleFrame()
}

function onMouseLeave() {
    visible = false
    if (ctx && canvas.value) {
        ctx.clearRect(0, 0, canvas.value.width, canvas.value.height)
    }
}

function onMouseEnter() {
    visible = true
    scheduleFrame()
}

function onTouchStart() {
    isTouchDevice = true
    if (raf) {
        cancelAnimationFrame(raf)
        raf = null
    }
}

function resize() {
    if (resizeRaf) return
    resizeRaf = requestAnimationFrame(() => {
        resizeRaf = null
        if (!canvas.value) return
        const w = window.innerWidth
        const h = window.innerHeight
        canvas.value.width = w
        canvas.value.height = h
    })
}

function animate() {
    raf = null
    if (!ctx || isTouchDevice || !canvas.value) return

    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height)

    const dx = mouse.x - head.x
    const dy = mouse.y - head.y
    const ax = dx * SPRING
    const ay = dy * SPRING
    head.vx = (head.vx + ax) * DAMPING
    head.vy = (head.vy + ay) * DAMPING
    head.x += head.vx
    head.y += head.vy

    trail.unshift({ x: head.x, y: head.y })
    if (trail.length > props.trailLength) trail.length = props.trailLength

    const targetSize = hovering ? props.size * 1.8 : props.size
    currentSize = currentSize + (targetSize - currentSize) * 0.15

    if (visible && trail.length > 1) {
        for (let i = trail.length - 1; i >= 1; i--) {
            const t = 1 - i / trail.length
            const thickness = props.trailThickness * t
            const alpha = t * 0.5
            ctx.beginPath()
            ctx.moveTo(trail[i].x, trail[i].y)
            ctx.lineTo(trail[i - 1].x, trail[i - 1].y)
            ctx.strokeStyle = `rgba(${trailRgb.r},${trailRgb.g},${trailRgb.b},${alpha})`
            ctx.lineWidth = thickness
            ctx.lineCap = 'round'
            ctx.stroke()
        }
    }

    if (visible) {
        if (hovering) {
            ctx.beginPath()
            ctx.arc(head.x, head.y, currentSize / 2, 0, Math.PI * 2)
            ctx.strokeStyle = `rgba(${headRgb.r},${headRgb.g},${headRgb.b},0.6)`
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
            ctx.arc(head.x, head.y, currentSize / 2, 0, Math.PI * 2)
            ctx.fillStyle = `rgba(${headRgb.r},${headRgb.g},${headRgb.b},0.85)`
            ctx.fill()
            ctx.closePath()

            ctx.beginPath()
            ctx.arc(head.x, head.y, currentSize / 2 + 6, 0, Math.PI * 2)
            ctx.strokeStyle = `rgba(${headRgb.r},${headRgb.g},${headRgb.b},0.15)`
            ctx.lineWidth = 1
            ctx.stroke()
            ctx.closePath()
        }
    }

    // Only keep rAF alive while the cursor is still moving or resizing
    const moving = Math.abs(dx) > 0.15 || Math.abs(dy) > 0.15 ||
                   Math.abs(head.vx) > 0.05 || Math.abs(head.vy) > 0.05 ||
                   Math.abs(targetSize - currentSize) > 0.2
    if (visible && moving) {
        raf = requestAnimationFrame(animate)
    }
}

onMounted(() => {
    if (typeof window === 'undefined') return
    if (
        ('ontouchstart' in window && navigator.maxTouchPoints > 0 && !window.matchMedia('(pointer: fine)').matches) ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        isTouchDevice = true
        return
    }

    ctx = canvas.value?.getContext('2d')
    if (!ctx) return

    const w = window.innerWidth
    const h = window.innerHeight
    canvas.value.width = w
    canvas.value.height = h

    window.addEventListener('mousemove', onMouseMove, { passive: true })
    window.addEventListener('resize', resize, { passive: true })
    document.addEventListener('mouseleave', onMouseLeave)
    document.addEventListener('mouseenter', onMouseEnter)
    window.addEventListener('touchstart', onTouchStart, { once: true, passive: true })
})

onUnmounted(() => {
    if (raf) cancelAnimationFrame(raf)
    if (resizeRaf) cancelAnimationFrame(resizeRaf)
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
