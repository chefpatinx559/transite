import { onMounted, onUnmounted } from 'vue'

/**
 * Scroll-reveal via IntersectionObserver.
 * Ajoute .is-revealed aux éléments [data-reveal] quand ils entrent dans le viewport.
 * Usage : appeler useReveal() dans le setup() de chaque page.
 */
export function useReveal({ threshold = 0.1, rootMargin = '-40px' } = {}) {
    let observer

    onMounted(() => {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed')
                        observer.unobserve(entry.target)
                    }
                })
            },
            { threshold, rootMargin }
        )

        document.querySelectorAll('[data-reveal]').forEach(el => observer.observe(el))
    })

    onUnmounted(() => observer?.disconnect())
}
