<template>
  <div ref="el" style="cursor:grab"></div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import {
  Vector3,
  BufferGeometry,
  Float32BufferAttribute,
  LineBasicMaterial,
  LineSegments,
  Line,
  MeshBasicMaterial,
  MeshPhongMaterial,
  Mesh,
  RingGeometry,
  SphereGeometry,
  DoubleSide,
  BackSide,
  Clock,
  Scene,
  PerspectiveCamera,
  WebGLRenderer,
  AmbientLight,
  PointLight,
  Group,
  TextureLoader,
  Color,
  QuadraticBezierCurve3,
} from 'three'
import { feature } from 'topojson-client'
import topo from 'world-atlas/countries-110m.json'
import earthLightsUrl from '../../images/earth-lights-1024.webp'

const el = ref(null)

let renderer, scene, camera, globeGroup, travelDot, curve, clock
let animationId = null
let resizeObs = null
let visObs = null
let isInView = true
let isPageVisible = true
let t = 0

// ── Drag / inertia state ─────────────────────────────────────
let mode = 'auto'   // 'auto' | 'drag' | 'inertia'
let velY = 0, velX = 0
let smoothVelY = 0, smoothVelX = 0
let prev = { x: 0, y: 0 }

// ── Pulse rings (populated in onMounted) ─────────────────────
const pulseRings = []

const clampX = x => Math.max(-Math.PI / 2, Math.min(Math.PI / 2, x))

// Lat/lon → 3D point on sphere of radius r
function latVec(lat, lon, r = 1) {
  const phi   = (90 - lat) * (Math.PI / 180)
  const theta = (lon + 180) * (Math.PI / 180)
  return new Vector3(
    -r * Math.sin(phi) * Math.cos(theta),
     r * Math.cos(phi),
     r * Math.sin(phi) * Math.sin(theta),
  )
}

// Country border lines from topojson — merged into a SINGLE LineSegments (1 draw call instead of ~300)
function buildCountries(group) {
  const geojson = feature(topo, topo.objects.countries)
  const positions = []

  geojson.features.forEach(f => {
    const geom = f.geometry
    if (!geom) return
    const polys = geom.type === 'Polygon' ? [geom.coordinates] : geom.coordinates
    polys.forEach(poly =>
      poly.forEach(ring => {
        const len = ring.length
        if (len < 2) return
        const pts = ring.map(([lon, lat]) => latVec(lat, lon, 1.002))
        for (let i = 0; i < len; i++) {
          const a = pts[i]
          const b = pts[(i + 1) % len]
          positions.push(a.x, a.y, a.z, b.x, b.y, b.z)
        }
      }),
    )
  })

  const geom = new BufferGeometry()
  geom.setAttribute('position', new Float32BufferAttribute(positions, 3))
  const mat = new LineBasicMaterial({ color: 0x4466cc, transparent: true, opacity: 0.22 })
  group.add(new LineSegments(geom, mat))
}

// 3 staggered radar rings at a surface position
function addPulseRings(position, group) {
  const ringGeo = new RingGeometry(0.035, 0.052, 24)
  for (let i = 0; i < 3; i++) {
    const mat = new MeshBasicMaterial({
      color: 0xF4620A, transparent: true, opacity: 0, side: DoubleSide,
    })
    const mesh = new Mesh(ringGeo, mat)
    mesh.position.copy(position)
    mesh.lookAt(new Vector3(0, 0, 0))
    mesh.userData.phase = i / 3
    group.add(mesh)
    pulseRings.push(mesh)
  }
}

// ── Drag handlers ────────────────────────────────────────────
function startDrag(x, y) {
  mode = 'drag'
  smoothVelY = 0
  smoothVelX = 0
  prev = { x, y }
  if (el.value) el.value.style.cursor = 'grabbing'
}

function moveDrag(x, y) {
  if (mode !== 'drag') return
  const dx = x - prev.x
  const dy = y - prev.y
  // Exponential moving average for velocity (used by inertia on release)
  smoothVelY = smoothVelY * 0.4 + (dx * 0.005) * 0.6
  smoothVelX = smoothVelX * 0.4 + (dy * 0.005) * 0.6
  globeGroup.rotation.y += dx * 0.005
  globeGroup.rotation.x = clampX(globeGroup.rotation.x + dy * 0.005)
  prev = { x, y }
}

function endDrag() {
  if (mode !== 'drag') return
  mode = 'inertia'
  velY = smoothVelY
  velX = smoothVelX
  if (el.value) el.value.style.cursor = 'grab'
}

const onMouseDown  = e => startDrag(e.clientX, e.clientY)
const onMouseMove  = e => moveDrag(e.clientX, e.clientY)
const onMouseUp    = () => endDrag()
const onTouchStart = e => startDrag(e.touches[0].clientX, e.touches[0].clientY)
const onTouchMove  = e => { if (e.touches[0]) moveDrag(e.touches[0].clientX, e.touches[0].clientY) }
const onTouchEnd   = () => endDrag()

function shouldAnimate() {
  return isInView && isPageVisible && renderer
}

function startLoop() {
  if (animationId !== null || !shouldAnimate()) return
  clock?.start()
  animationId = requestAnimationFrame(tick)
}

function stopLoop() {
  if (animationId !== null) {
    cancelAnimationFrame(animationId)
    animationId = null
  }
}

const DECAY          = 0.92
const STOP_THRESHOLD = 0.00006

function tick() {
  if (!shouldAnimate()) {
    animationId = null
    return
  }
  animationId = requestAnimationFrame(tick)
  const elapsed = clock.getElapsedTime()

  // Traveling dot
  t = (t + 0.004) % 1
  travelDot.position.copy(curve.getPoint(t))

  // Globe rotation / inertia
  if (mode === 'auto') {
    globeGroup.rotation.y += 0.0015
  } else if (mode === 'inertia') {
    velY *= DECAY
    velX *= DECAY
    globeGroup.rotation.y += velY
    globeGroup.rotation.x = clampX(globeGroup.rotation.x + velX)
    if (Math.abs(velY) < STOP_THRESHOLD && Math.abs(velX) < STOP_THRESHOLD) {
      mode = 'auto'
    }
  }

  // Radar pulse rings
  pulseRings.forEach(ring => {
    const tp = (elapsed * 0.55 + ring.userData.phase) % 1
    const scale = 1 + tp * 4
    ring.scale.set(scale, scale, scale)
    ring.material.opacity = Math.max(0, (1 - tp) * 0.55)
  })

  renderer.render(scene, camera)
}

function onVisibilityChange() {
  isPageVisible = !document.hidden
  if (isPageVisible) startLoop()
  else stopLoop()
}

onMounted(() => {
  const container = el.value
  if (!container) return

  // Read layout metrics BEFORE any DOM writes to avoid forced reflow
  const rect = container.getBoundingClientRect()
  const W = rect.width  || 400
  const H = rect.height || 300
  const isMobile = W < 640

  clock  = new Clock()
  scene  = new Scene()
  camera = new PerspectiveCamera(42, W / H, 0.1, 100)
  camera.position.z = 2.8

  try {
    renderer = new WebGLRenderer({
      antialias: !isMobile,
      alpha: true,
      powerPreference: 'high-performance',
    })
  } catch {
    return
  }
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, isMobile ? 1.25 : 1.75))
  renderer.setSize(W, H)
  renderer.domElement.style.cssText = 'display:block;width:100%;height:100%'
  container.appendChild(renderer.domElement)

  // ── Lighting — night scene ───────────────────────────────
  scene.add(new AmbientLight(0xffffff, 0.08))
  const accent = new PointLight(0xF4620A, 1.0, 6)
  accent.position.set(-2, 1, 3)
  scene.add(accent)

  // ── Globe group — pre-rotated so route faces camera ──────
  globeGroup = new Group()
  globeGroup.rotation.y = -2.55
  scene.add(globeGroup)

  // Earth night texture (local WebP asset, 29 KB vs 410 KB remote PNG)
  const tex = new TextureLoader().load(earthLightsUrl, () => {
    if (renderer && !animationId) renderer.render(scene, camera)
  })
  const segs = isMobile ? 32 : 48
  globeGroup.add(new Mesh(
    new SphereGeometry(1, segs, segs),
    new MeshPhongMaterial({
      map: tex,
      emissiveMap: tex,
      emissive: new Color(1.0, 0.85, 0.55),
      emissiveIntensity: 0.38,
      shininess: 5,
      specular: new Color(0.08, 0.08, 0.18),
    }),
  ))

  // Country borders (single merged LineSegments)
  buildCountries(globeGroup)

  // Atmospheric orange halo
  globeGroup.add(new Mesh(
    new SphereGeometry(1.08, 24, 24),
    new MeshBasicMaterial({
      color: 0xF4620A, transparent: true, opacity: 0.05, side: BackSide,
    }),
  ))

  // ── City markers + radar rings ───────────────────────────
  const pChina = latVec(39.9, 116.4, 1.01)
  const pCIV   = latVec(5.3,  -4.0,  1.01)
  const mkMat  = new MeshBasicMaterial({ color: 0xF4620A })
  const mkGeo  = new SphereGeometry(0.026, 10, 10)

  ;[pChina, pCIV].forEach(p => {
    const marker = new Mesh(mkGeo, mkMat)
    marker.position.copy(p)
    globeGroup.add(marker)
    addPulseRings(p, globeGroup)
  })

  // ── Trade arc Chine → CIV ────────────────────────────────
  const mid = pChina.clone().lerp(pCIV, 0.5).normalize().multiplyScalar(1.65)
  curve = new QuadraticBezierCurve3(pChina, mid, pCIV)

  globeGroup.add(new Line(
    new BufferGeometry().setFromPoints(curve.getPoints(64)),
    new LineBasicMaterial({ color: 0xF4620A, transparent: true, opacity: 0.85 }),
  ))

  // Traveling dot along arc
  travelDot = new Mesh(
    new SphereGeometry(0.032, 10, 10),
    new MeshBasicMaterial({ color: 0xffffff }),
  )
  globeGroup.add(travelDot)

  // Initial static render
  renderer.render(scene, camera)

  // Pause animation when scrolled out of view
  if ('IntersectionObserver' in window) {
    visObs = new IntersectionObserver(entries => {
      isInView = entries[0]?.isIntersecting ?? true
      if (isInView) startLoop()
      else stopLoop()
    }, { threshold: 0.05 })
    visObs.observe(container)
  }

  document.addEventListener('visibilitychange', onVisibilityChange)

  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    startLoop()
  }

  // ── Responsive (reads entry.contentRect — zero forced reflow) ─
  resizeObs = new ResizeObserver(entries => {
    const cr = entries[0]?.contentRect
    const w = cr?.width
    const h = cr?.height
    if (!w || !h || !renderer) return
    camera.aspect = w / h
    camera.updateProjectionMatrix()
    renderer.setSize(w, h)
  })
  resizeObs.observe(container)

  // ── Interaction events ───────────────────────────────────
  const canvas = renderer.domElement
  canvas.addEventListener('mousedown',  onMouseDown)
  canvas.addEventListener('touchstart', onTouchStart, { passive: true })
  window.addEventListener('mousemove',  onMouseMove, { passive: true })
  window.addEventListener('mouseup',    onMouseUp)
  window.addEventListener('touchmove',  onTouchMove, { passive: true })
  window.addEventListener('touchend',   onTouchEnd)
})

onBeforeUnmount(() => {
  stopLoop()
  visObs?.disconnect()
  resizeObs?.disconnect()
  document.removeEventListener('visibilitychange', onVisibilityChange)
  const canvas = renderer?.domElement
  if (canvas) {
    canvas.removeEventListener('mousedown',  onMouseDown)
    canvas.removeEventListener('touchstart', onTouchStart)
  }
  window.removeEventListener('mousemove', onMouseMove)
  window.removeEventListener('mouseup',   onMouseUp)
  window.removeEventListener('touchmove', onTouchMove)
  window.removeEventListener('touchend',  onTouchEnd)
  renderer?.dispose()
})
</script>
