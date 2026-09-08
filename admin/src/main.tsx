import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { AuthProvider } from '@/context/AuthContext'
import { ThemeProvider } from '@/context/ThemeContext'
import { Toaster } from '@/components/ui/sonner'
import { ConfirmProvider } from '@/components/ui/confirm-dialog'
import App from './App'
import './index.css'

const queryClient = new QueryClient({
  defaultOptions: {
    queries: { retry: 1, staleTime: 30_000 },
  },
})

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <BrowserRouter basename={import.meta.env.BASE_URL.replace(/\/$/, '') || ''}>
      <QueryClientProvider client={queryClient}>
        <ThemeProvider>
          <AuthProvider>
            <ConfirmProvider>
              <App />
              <Toaster />
            </ConfirmProvider>
          </AuthProvider>
        </ThemeProvider>
      </QueryClientProvider>
    </BrowserRouter>
  </StrictMode>,
)
