import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function formatPrice(value: number | string): string {
  return Number(value).toLocaleString('fr-FR') + ' FCFA'
}

export function formatDate(dateStr: string | null | undefined): string {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'short', year: 'numeric',
  })
}

export function formatDateTime(dateStr: string | null | undefined): string {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

/**
 * Construit l'URL complète d'une image storage.
 *
 * Gère deux formats de chemin :
 *   - '/storage/products/xxx.jpg'  (déjà préfixé — format produits)
 *   - 'formations/xxx.jpg'         (relatif au disk public — format formations/posts)
 *
 * Base URL :
 *   - Dev  → VITE_STORAGE_URL depuis .env.development (ex: http://127.0.0.1:8000)
 *   - Prod → déduite depuis window.location (fonctionne sur WAMP ET VPS)
 */
export function storageUrl(path: string | null | undefined): string | null {
  if (!path) return null
  if (path.startsWith('http')) return path

  // Normaliser : s'assurer que le chemin commence par /storage/
  const normalized = path.startsWith('/storage/') ? path : `/storage/${path}`

  // Base URL
  const envBase = (import.meta.env.VITE_STORAGE_URL as string | undefined)?.trim()
  if (envBase) return `${envBase}${normalized}`

  // Détection dynamique en production : retirer /admin et ce qui suit
  if (typeof window !== 'undefined') {
    const base = window.location.href.replace(/\/admin.*$/, '').replace(/\/$/, '')
    return `${base}${normalized}`
  }

  return normalized
}

export function truncate(str: string | null | undefined, n = 60): string {
  if (!str) return '—'
  return str.length > n ? str.slice(0, n) + '…' : str
}
