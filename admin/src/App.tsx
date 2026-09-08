import { Routes, Route, Navigate } from 'react-router-dom'
import { useAuth } from '@/context/AuthContext'
import AdminLayout from '@/layouts/AdminLayout'
import LoginPage from '@/pages/auth/LoginPage'
import DashboardPage from '@/pages/dashboard/DashboardPage'
import ProductsPage from '@/pages/products/ProductsPage'
import ProductFormPage from '@/pages/products/ProductFormPage'
import OrdersPage from '@/pages/orders/OrdersPage'
import OrderDetailPage from '@/pages/orders/OrderDetailPage'
import PostsPage from '@/pages/posts/PostsPage'
import PostFormPage from '@/pages/posts/PostFormPage'
import QuotesPage from '@/pages/quotes/QuotesPage'
import QuoteDetailPage from '@/pages/quotes/QuoteDetailPage'
import UsersPage from '@/pages/users/UsersPage'
import SettingsPage from '@/pages/settings/SettingsPage'
import FormationsPage from '@/pages/formations/FormationsPage'
import FormationFormPage from '@/pages/formations/FormationFormPage'
import FormationRegistrationsPage from '@/pages/formations/FormationRegistrationsPage'

function RequireAuth({ children }: { children: React.ReactNode }) {
  const { isAuthenticated, isLoading } = useAuth()
  if (isLoading) return (
    <div className="min-h-screen bg-[#0D0D0D] flex items-center justify-center">
      <div className="w-8 h-8 border-2 border-[#F4620A] border-t-transparent rounded-full animate-spin" />
    </div>
  )
  return isAuthenticated ? <>{children}</> : <Navigate to="/login" replace />
}

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route path="/" element={
        <RequireAuth>
          <AdminLayout />
        </RequireAuth>
      }>
        <Route index element={<Navigate to="/dashboard" replace />} />
        <Route path="dashboard"          element={<DashboardPage />} />
        <Route path="produits"           element={<ProductsPage />} />
        <Route path="produits/creer"     element={<ProductFormPage />} />
        <Route path="produits/:id"       element={<ProductFormPage />} />
        <Route path="commandes"          element={<OrdersPage />} />
        <Route path="commandes/:id"      element={<OrderDetailPage />} />
        <Route path="articles"           element={<PostsPage />} />
        <Route path="articles/creer"     element={<PostFormPage />} />
        <Route path="articles/:id"       element={<PostFormPage />} />
        <Route path="devis"              element={<QuotesPage />} />
        <Route path="devis/:id"          element={<QuoteDetailPage />} />
        <Route path="utilisateurs"       element={<UsersPage />} />
        <Route path="parametres"                        element={<SettingsPage />} />
        <Route path="formations"                        element={<FormationsPage />} />
        <Route path="formations/creer"                  element={<FormationFormPage />} />
        <Route path="formations/:id"                    element={<FormationFormPage />} />
        <Route path="formations/:id/inscriptions"       element={<FormationRegistrationsPage />} />
      </Route>
      <Route path="*" element={<Navigate to="/dashboard" replace />} />
    </Routes>
  )
}
