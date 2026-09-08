import { useState, FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '@/context/AuthContext'
import { Eye, EyeOff, LogIn } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Logo } from '@/components/Logo'

export default function LoginPage() {
  const { login, isAuthenticated } = useAuth()
  const navigate = useNavigate()

  const [email, setEmail]       = useState('admin@netspring.ci')
  const [password, setPassword] = useState('')
  const [showPwd, setShowPwd]   = useState(false)
  const [loading, setLoading]   = useState(false)
  const [error, setError]       = useState('')

  if (isAuthenticated) { navigate('/dashboard', { replace: true }); return null }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setLoading(true)
    setError('')
    try {
      await login(email, password)
      navigate('/dashboard', { replace: true })
    } catch (err: unknown) {
      const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
      setError(msg ?? 'Email ou mot de passe incorrect.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-[#0D0D0D] flex items-center justify-center px-4">
      {/* Décor fond */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-96 h-96 bg-[#F4620A]/8 rounded-full blur-3xl" />
        <div className="absolute -bottom-40 -left-40 w-96 h-96 bg-[#F4620A]/5 rounded-full blur-3xl" />
      </div>

      <div className="relative w-full max-w-md">
        {/* Logo */}
        <div className="flex justify-center mb-8">
          <Logo variant="full" dark />
        </div>

        {/* Card */}
        <div className="bg-white dark:bg-[#1a1a1a] rounded-[20px] shadow-[0_24px_60px_rgba(0,0,0,0.3)] dark:shadow-[0_24px_60px_rgba(0,0,0,0.6)] border dark:border-white/10 p-8">
          {error && (
            <div className="mb-5 p-3.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 rounded-[10px] text-sm text-red-700 dark:text-red-400" role="alert">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5" aria-label="Formulaire de connexion admin">
            <div className="space-y-1.5">
              <Label htmlFor="email">Adresse email</Label>
              <Input
                id="email"
                type="email"
                value={email}
                onChange={e => setEmail(e.target.value)}
                autoComplete="email"
                required
                placeholder="admin@netspring.ci"
              />
            </div>

            <div className="space-y-1.5">
              <Label htmlFor="password">Mot de passe</Label>
              <div className="relative">
                <Input
                  id="password"
                  type={showPwd ? 'text' : 'password'}
                  value={password}
                  onChange={e => setPassword(e.target.value)}
                  required
                  autoComplete="current-password"
                  placeholder="••••••••"
                  className="pr-10"
                />
                <button
                  type="button"
                  onClick={() => setShowPwd(v => !v)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer"
                  aria-label={showPwd ? 'Masquer le mot de passe' : 'Afficher le mot de passe'}
                >
                  {showPwd ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            <Button
              type="submit"
              loading={loading}
              size="lg"
              className="w-full mt-2"
              aria-label="Se connecter à l'admin"
            >
              <LogIn className="w-4 h-4" aria-hidden="true" />
              Se connecter
            </Button>
          </form>

          <p className="text-center text-xs text-gray-400 dark:text-gray-500 mt-6">
            Accès réservé aux administrateurs NETSPRING
          </p>
        </div>

        <a href="/" className="block text-center text-sm text-gray-600 dark:text-gray-500 hover:text-white mt-6 transition-colors">
          ← Retour au site
        </a>
      </div>
    </div>
  )
}
