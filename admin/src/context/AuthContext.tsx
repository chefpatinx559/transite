import { createContext, useContext, useState, useEffect, ReactNode } from 'react'
import { authApi } from '@/api/endpoints'

interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  role: string
}

interface AuthContextType {
  user: User | null
  token: string | null
  isLoading: boolean
  login: (email: string, password: string) => Promise<void>
  logout: () => Promise<void>
  isAuthenticated: boolean
}

const AuthContext = createContext<AuthContextType | null>(null)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser]       = useState<User | null>(null)
  const [token, setToken]     = useState<string | null>(localStorage.getItem('admin_token'))
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    if (token) {
      authApi.me()
        .then(res => setUser(res.data))
        .catch(() => { setToken(null); localStorage.removeItem('admin_token') })
        .finally(() => setIsLoading(false))
    } else {
      setIsLoading(false)
    }
  }, [token])

  async function login(email: string, password: string) {
    const res = await authApi.login(email, password)
    const { token: newToken, user: newUser } = res.data
    localStorage.setItem('admin_token', newToken)
    setToken(newToken)
    setUser(newUser)
  }

  async function logout() {
    try { await authApi.logout() } catch {}
    localStorage.removeItem('admin_token')
    setToken(null)
    setUser(null)
  }

  return (
    <AuthContext.Provider value={{ user, token, isLoading, login, logout, isAuthenticated: !!token && !!user }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  const ctx = useContext(AuthContext)
  if (!ctx) throw new Error('useAuth must be used inside AuthProvider')
  return ctx
}
