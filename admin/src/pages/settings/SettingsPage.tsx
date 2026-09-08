import { useState } from 'react'
import { useMutation } from '@tanstack/react-query'
import { Eye, EyeOff, Sun, Moon, KeyRound, Palette } from 'lucide-react'
import { profileApi } from '@/api/endpoints'
import { useTheme } from '@/context/ThemeContext'
import { useAuth } from '@/context/AuthContext'
import { useToast } from '@/hooks/useToast'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { cn } from '@/lib/utils'

export default function SettingsPage() {
  const { user } = useAuth()
  const { theme, setTheme } = useTheme()
  const { success, error } = useToast()

  const [form, setForm] = useState({
    current_password: '',
    password: '',
    password_confirmation: '',
  })
  const [showCurrent, setShowCurrent] = useState(false)
  const [showNew, setShowNew]         = useState(false)
  const [showConfirm, setShowConfirm] = useState(false)
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({})

  const mutation = useMutation({
    mutationFn: () => profileApi.changePassword(form),
    onSuccess: () => {
      success('Mot de passe mis à jour.')
      setForm({ current_password: '', password: '', password_confirmation: '' })
      setFieldErrors({})
    },
    onError: (err: unknown) => {
      const data = (err as { response?: { data?: { errors?: Record<string, string[]> } } })?.response?.data
      if (data?.errors) {
        const flat: Record<string, string> = {}
        Object.entries(data.errors).forEach(([k, v]) => { flat[k] = v[0] })
        setFieldErrors(flat)
      } else {
        error('Impossible de mettre à jour le mot de passe.')
      }
    },
  })

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    setFieldErrors({})
    if (form.password !== form.password_confirmation) {
      setFieldErrors({ password_confirmation: 'Les mots de passe ne correspondent pas.' })
      return
    }
    mutation.mutate()
  }

  return (
    <div className="space-y-8 max-w-2xl">

      <div>
        <h1 className="font-sans font-bold text-2xl text-[#0D0D0D] dark:text-white">Paramètres</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
          Gérez votre compte et l'apparence du panel.
        </p>
      </div>

      {/* Profil */}
      <Card className="dark:bg-[#1e1e1e] dark:border-white/10">
        <CardHeader className="pb-2">
          <CardTitle className="flex items-center gap-2 text-base dark:text-white">
            <KeyRound className="w-4 h-4 text-[#F4620A]" />
            Changer le mot de passe
          </CardTitle>
          <p className="text-xs text-gray-500 dark:text-gray-400">{user?.email}</p>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Mot de passe actuel */}
            <div className="space-y-1.5">
              <Label htmlFor="current_password" className="dark:text-gray-200">Mot de passe actuel</Label>
              <div className="relative">
                <Input
                  id="current_password"
                  type={showCurrent ? 'text' : 'password'}
                  value={form.current_password}
                  onChange={e => setForm(f => ({ ...f, current_password: e.target.value }))}
                  autoComplete="current-password"
                  className={cn('pr-10', fieldErrors.current_password && 'border-red-500')}
                />
                <button
                  type="button"
                  onClick={() => setShowCurrent(s => !s)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  aria-label={showCurrent ? 'Masquer' : 'Afficher'}
                >
                  {showCurrent ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
              {fieldErrors.current_password && (
                <p className="text-xs text-red-600">{fieldErrors.current_password}</p>
              )}
            </div>

            {/* Nouveau mot de passe */}
            <div className="space-y-1.5">
              <Label htmlFor="password" className="dark:text-gray-200">Nouveau mot de passe</Label>
              <div className="relative">
                <Input
                  id="password"
                  type={showNew ? 'text' : 'password'}
                  value={form.password}
                  onChange={e => setForm(f => ({ ...f, password: e.target.value }))}
                  autoComplete="new-password"
                  className={cn('pr-10', fieldErrors.password && 'border-red-500')}
                  placeholder="8 caractères minimum"
                />
                <button
                  type="button"
                  onClick={() => setShowNew(s => !s)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  aria-label={showNew ? 'Masquer' : 'Afficher'}
                >
                  {showNew ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
              {fieldErrors.password && (
                <p className="text-xs text-red-600">{fieldErrors.password}</p>
              )}
            </div>

            {/* Confirmation */}
            <div className="space-y-1.5">
              <Label htmlFor="password_confirmation" className="dark:text-gray-200">Confirmer le nouveau mot de passe</Label>
              <div className="relative">
                <Input
                  id="password_confirmation"
                  type={showConfirm ? 'text' : 'password'}
                  value={form.password_confirmation}
                  onChange={e => setForm(f => ({ ...f, password_confirmation: e.target.value }))}
                  autoComplete="new-password"
                  className={cn('pr-10', fieldErrors.password_confirmation && 'border-red-500')}
                />
                <button
                  type="button"
                  onClick={() => setShowConfirm(s => !s)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  aria-label={showConfirm ? 'Masquer' : 'Afficher'}
                >
                  {showConfirm ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
              {fieldErrors.password_confirmation && (
                <p className="text-xs text-red-600">{fieldErrors.password_confirmation}</p>
              )}
            </div>

            <Button
              type="submit"
              loading={mutation.isPending}
              disabled={!form.current_password || !form.password || !form.password_confirmation}
            >
              Mettre à jour le mot de passe
            </Button>
          </form>
        </CardContent>
      </Card>

      {/* Apparence */}
      <Card className="dark:bg-[#1e1e1e] dark:border-white/10">
        <CardHeader className="pb-2">
          <CardTitle className="flex items-center gap-2 text-base dark:text-white">
            <Palette className="w-4 h-4 text-[#F4620A]" />
            Apparence
          </CardTitle>
          <p className="text-xs text-gray-500 dark:text-gray-400">Choisissez le thème du panel admin.</p>
        </CardHeader>
        <CardContent>
          <div className="flex gap-3">
            {/* Thème Clair */}
            <button
              type="button"
              onClick={() => setTheme('light')}
              className={cn(
                'flex-1 flex flex-col items-center gap-3 p-4 rounded-xl border-2 transition-all cursor-pointer',
                theme === 'light'
                  ? 'border-[#F4620A] bg-[#F4620A]/5'
                  : 'border-gray-200 hover:border-gray-300 dark:border-white/10 dark:hover:border-white/20',
              )}
              aria-pressed={theme === 'light'}
            >
              <div className="w-full h-16 rounded-lg bg-white border border-gray-200 shadow-sm flex flex-col gap-1.5 p-2 overflow-hidden">
                <div className="h-2 w-1/2 bg-gray-200 rounded" />
                <div className="h-1.5 w-3/4 bg-gray-100 rounded" />
                <div className="h-1.5 w-2/3 bg-gray-100 rounded" />
              </div>
              <div className="flex items-center gap-2">
                <Sun className="w-4 h-4 text-amber-500" />
                <span className={cn('text-sm font-medium', theme === 'light' ? 'text-[#F4620A]' : 'text-gray-600 dark:text-gray-300')}>
                  Clair
                </span>
                {theme === 'light' && (
                  <span className="text-[10px] font-semibold bg-[#F4620A] text-white px-1.5 py-0.5 rounded-full">Actif</span>
                )}
              </div>
            </button>

            {/* Thème Sombre */}
            <button
              type="button"
              onClick={() => setTheme('dark')}
              className={cn(
                'flex-1 flex flex-col items-center gap-3 p-4 rounded-xl border-2 transition-all cursor-pointer',
                theme === 'dark'
                  ? 'border-[#F4620A] bg-[#F4620A]/5'
                  : 'border-gray-200 hover:border-gray-300 dark:border-white/10 dark:hover:border-white/20',
              )}
              aria-pressed={theme === 'dark'}
            >
              <div className="w-full h-16 rounded-lg bg-[#1a1a1a] border border-white/10 shadow-sm flex flex-col gap-1.5 p-2 overflow-hidden">
                <div className="h-2 w-1/2 bg-white/20 rounded" />
                <div className="h-1.5 w-3/4 bg-white/10 rounded" />
                <div className="h-1.5 w-2/3 bg-white/10 rounded" />
              </div>
              <div className="flex items-center gap-2">
                <Moon className="w-4 h-4 text-indigo-400" />
                <span className={cn('text-sm font-medium', theme === 'dark' ? 'text-[#F4620A]' : 'text-gray-600 dark:text-gray-300')}>
                  Sombre
                </span>
                {theme === 'dark' && (
                  <span className="text-[10px] font-semibold bg-[#F4620A] text-white px-1.5 py-0.5 rounded-full">Actif</span>
                )}
              </div>
            </button>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
