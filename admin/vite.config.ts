import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
export default defineConfig({
  plugins: [react(), tailwindcss()],
  resolve: {
    alias: { '@': `${import.meta.dirname}/src` },
  },
  base: process.env.NODE_ENV === 'production' ? '/admin' : '/',
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
      },
      '/storage': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: '../public/admin',
    emptyOutDir: true,
    rollupOptions: {
      output: {
        manualChunks: (id: string) => {
          if (id.includes('recharts'))            return 'charts'
          if (id.includes('lucide-react'))        return 'icons'
          if (id.includes('@tanstack'))           return 'tanstack'
          if (id.includes('react-dom'))           return 'react-dom'
          if (id.includes('react-router-dom'))    return 'router'
        },
      },
    },
  },
})
