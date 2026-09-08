import { Outlet, NavLink, useNavigate } from 'react-router-dom'
import { useAuth } from '@/context/AuthContext'
import {
  LayoutDashboard, Package, ShoppingCart, FileText,
  MessageSquare, Users, LogOut, Menu,
  ChevronRight, Settings, GraduationCap, ChevronUp,
} from 'lucide-react'
import { cn } from '@/lib/utils'
import { Sheet, SheetContent } from '@/components/ui/sheet'
import { Button } from '@/components/ui/button'
import { Logo } from '@/components/Logo'
import { useState, useRef, useEffect } from 'react'

const nav = [
  { to: '/dashboard',    label: 'Dashboard',    icon: LayoutDashboard },
  { to: '/produits',     label: 'Produits',      icon: Package },
  { to: '/commandes',    label: 'Commandes',     icon: ShoppingCart },
  { to: '/articles',     label: 'Articles',      icon: FileText },
  { to: '/devis',        label: 'Devis',         icon: MessageSquare },
  { to: '/utilisateurs', label: 'Utilisateurs',  icon: Users },
  { to: '/formations',   label: 'Formations',    icon: GraduationCap },
]

export default function AdminLayout() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const [dropupOpen, setDropupOpen]   = useState(false)
  const dropupRef = useRef<HTMLDivElement>(null)

  // Fermer le dropup au clic extérieur
  useEffect(() => {
    function handleClick(e: MouseEvent) {
      if (dropupRef.current && !dropupRef.current.contains(e.target as Node)) {
        setDropupOpen(false)
      }
    }
    document.addEventListener('mousedown', handleClick)
    return () => document.removeEventListener('mousedown', handleClick)
  }, [])

  async function handleLogout() {
    await logout()
    navigate('/login', { replace: true })
  }

  const Sidebar = () => (
    <aside className="flex flex-col h-full bg-[#0D0D0D] w-64">
      {/* Logo */}
      <div className="flex items-center px-5 py-4 border-b border-white/8 shrink-0">
        <Logo variant="full" dark className="scale-90 origin-left" />
        <span className="ml-auto text-[10px] font-semibold text-[#F4620A] bg-[#F4620A]/10 px-2 py-0.5 rounded-full">
          Admin
        </span>
      </div>

      {/* Nav */}
      <nav className="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto" aria-label="Navigation admin">
        {nav.map(({ to, label, icon: Icon }) => (
          <NavLink
            key={to}
            to={to}
            onClick={() => setSidebarOpen(false)}
            className={({ isActive }) => cn(
              'flex items-center gap-3 px-3 py-2.5 rounded-[10px] text-sm font-medium transition-all duration-150 group',
              isActive
                ? 'bg-[#F4620A] text-white shadow-[0_2px_8px_rgba(244,98,10,0.3)]'
                : 'text-gray-400 hover:text-white hover:bg-white/8',
            )}
          >
            <Icon className="w-4 h-4 shrink-0" aria-hidden="true" />
            {label}
            <ChevronRight className="w-3.5 h-3.5 ml-auto opacity-0 group-hover:opacity-50 transition-opacity" aria-hidden="true" />
          </NavLink>
        ))}
      </nav>

      {/* Footer — Avatar + Dropup */}
      <div className="px-3 py-3 border-t border-white/8 shrink-0" ref={dropupRef}>

        {/* Dropup menu */}
        {dropupOpen && (
          <div className="mb-2 bg-[#1a1a1a] border border-white/10 rounded-[12px] overflow-hidden shadow-[0_-8px_32px_rgba(0,0,0,0.4)]">
            <NavLink
              to="/parametres"
              onClick={() => { setDropupOpen(false); setSidebarOpen(false) }}
              className={({ isActive }) => cn(
                'flex items-center gap-3 px-4 py-3 text-sm font-medium transition-all duration-150',
                isActive ? 'text-[#F4620A] bg-white/5' : 'text-gray-300 hover:text-white hover:bg-white/8',
              )}
            >
              <Settings className="w-4 h-4 shrink-0" aria-hidden="true" />
              Paramètres
            </NavLink>
            <div className="h-px bg-white/8 mx-3" />
            <button
              onClick={() => { setDropupOpen(false); handleLogout() }}
              className="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all duration-150 cursor-pointer"
              aria-label="Se déconnecter"
            >
              <LogOut className="w-4 h-4 shrink-0" aria-hidden="true" />
              Déconnexion
            </button>
          </div>
        )}

        {/* Avatar cliquable */}
        <button
          onClick={() => setDropupOpen(v => !v)}
          className={cn(
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-[10px] transition-all duration-150 cursor-pointer group',
            dropupOpen ? 'bg-white/8' : 'hover:bg-white/8',
          )}
          aria-expanded={dropupOpen}
          aria-label="Menu utilisateur"
        >
          <div className="w-8 h-8 bg-[#F4620A]/20 rounded-full flex items-center justify-center text-[#F4620A] text-sm font-bold shrink-0">
            {user?.first_name?.charAt(0) ?? 'A'}
          </div>
          <div className="min-w-0 flex-1 text-left">
            <p className="text-white text-xs font-medium truncate">{user?.first_name} {user?.last_name}</p>
            <p className="text-gray-500 text-[10px] truncate">{user?.email}</p>
          </div>
          <ChevronUp className={cn(
            'w-3.5 h-3.5 text-gray-500 shrink-0 transition-transform duration-150',
            dropupOpen ? 'rotate-180' : '',
          )} aria-hidden="true" />
        </button>
      </div>
    </aside>
  )

  return (
    <div className="flex h-screen bg-[#F8F8F8] dark:bg-[#111111] overflow-hidden transition-colors duration-200">
      {/* Sidebar desktop */}
      <div className="hidden lg:flex shrink-0">
        <Sidebar />
      </div>

      {/* Drawer mobile — shadcn Sheet */}
      <Sheet open={sidebarOpen} onOpenChange={setSidebarOpen}>
        <SheetContent side="left" hideClose className="p-0 w-64">
          <Sidebar />
        </SheetContent>
      </Sheet>

      {/* Main */}
      <div className="flex-1 flex flex-col min-w-0 overflow-hidden">
        {/* Header mobile */}
        <header className="lg:hidden flex items-center gap-4 px-4 py-3 bg-[#0D0D0D] border-b border-white/8">
          <Button
            variant="ghost"
            size="icon"
            onClick={() => setSidebarOpen(true)}
            className="text-white hover:bg-white/10 hover:text-white"
            aria-label="Ouvrir le menu"
          >
            <Menu className="w-5 h-5" />
          </Button>
          <Logo variant="full" dark className="scale-75 origin-left" />
        </header>

        {/* Content */}
        <main className="flex-1 overflow-y-auto p-6 lg:p-8">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
