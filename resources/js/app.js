import { createSSRApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
    title: (title) => title ? `${title} — NETSPRING` : 'NETSPRING',
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue'),
    ),
    setup({ el, App, props, plugin }) {
        const app = createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
        document.getElementById('ssr-heading')?.remove();
        registerWebMcpTools();
        return app;
    },
    progress: {
        color: '#F4620A',
    },
});

function registerWebMcpTools() {
    if (!navigator.modelContext?.registerTool) return;

    navigator.modelContext.registerTool({
        name: 'search_products',
        description: 'Search the NETSPRING product catalog by keyword.',
        inputSchema: {
            type: 'object',
            properties: {
                query: { type: 'string', description: 'Search keywords' },
            },
        },
        async execute({ query }) {
            const res = await fetch(`/api/products?search=${encodeURIComponent(query || '')}`);
            return res.ok ? await res.json() : { error: 'Search failed' };
        },
    });

    navigator.modelContext.registerTool({
        name: 'request_quote',
        description: 'Request an import quote from NETSPRING. Navigates to the quote request page.',
        inputSchema: {
            type: 'object',
            properties: {
                product: { type: 'string', description: 'Product to import' },
            },
        },
        async execute({ product }) {
            window.location.href = `/importer/devis${product ? '?product=' + encodeURIComponent(product) : ''}`;
            return { status: 'navigating', url: '/importer/devis' };
        },
    });

    navigator.modelContext.registerTool({
        name: 'contact_netspring',
        description: 'Navigate to the NETSPRING contact page to send a message.',
        inputSchema: {
            type: 'object',
            properties: {},
        },
        async execute() {
            window.location.href = '/contact';
            return { status: 'navigating', url: '/contact' };
        },
    });
}
