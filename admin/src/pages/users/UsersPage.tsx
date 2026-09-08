import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { type ColumnDef } from '@tanstack/react-table'
import { Users, Plus, Eye, EyeOff } from 'lucide-react'
import { usersApi } from '@/api/endpoints'
import { cn, formatDate } from '@/lib/utils'
import { useAuth } from '@/context/AuthContext'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { DataTable } from '@/components/ui/data-table'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { useDebounce } from '@/hooks/useDebounce'
import { useToast } from '@/hooks/useToast'
import { useConfirm } from '@/components/ui/confirm-dialog'

interface User {
  id: number
  first_name?: string
  last_name?: string
  email: string
  whatsapp?: string
  role: string
  created_at: string
}

const ROLE_FILTERS = [
  { label: 'Tous',         value: '' },
  { label: 'Super Admins', value: 'super_admin' },
  { label: 'Admins',       value: 'admin' },
  { label: 'Clients',      value: 'client' },
]

const ROLE_BADGE: Record<string, { label: string; variant: 'default' | 'secondary' | 'purple' | 'gray' }> = {
  super_admin: { label: 'Super Admin', variant: 'default' },
  admin:       { label: 'Admin',       variant: 'purple' },
  client:      { label: 'Client',      variant: 'gray' },
}

const emptyForm = { first_name: '', last_name: '', email: '', password: '', role: 'admin' }

export default function UsersPage() {
  const queryClient = useQueryClient()
  const { success, error } = useToast()
  const confirm = useConfirm()
  const { user: currentUser } = useAuth()
  const isSuperAdmin = currentUser?.role === 'super_admin'

  const [search, setSearch]         = useState('')
  const [roleFilter, setRoleFilter] = useState('')
  const [page, setPage]             = useState(1)

  // Modal création
  const [open, setOpen]         = useState(false)
  const [form, setForm]         = useState(emptyForm)
  const [showPwd, setShowPwd]   = useState(false)
  const [formErrors, setFormErrors] = useState<Record<string, string>>({})

  const debouncedSearch = useDebounce(search, 400)

  const params: Record<string, unknown> = { page, per_page: 15 }
  if (debouncedSearch) params.q = debouncedSearch
  if (roleFilter) params.role = roleFilter

  const { data, isLoading } = useQuery({
    queryKey: ['users', params],
    queryFn: () => usersApi.list(params).then(r => r.data),
  })

  const users: User[] = data?.data ?? []
  const lastPage = data?.meta?.last_page ?? data?.last_page ?? 1
  const total    = data?.meta?.total    ?? data?.total    ?? 0

  const roleMutation = useMutation({
    mutationFn: ({ id, role }: { id: number; role: string }) => usersApi.updateRole(id, role),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['users'] })
      success(`Rôle mis à jour : ${variables.role === 'admin' ? 'administrateur' : 'client'}.`)
    },
    onError: () => error('Impossible de modifier le rôle.'),
  })

  const createMutation = useMutation({
    mutationFn: () => usersApi.create(form as typeof emptyForm),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] })
      success('Utilisateur créé avec succès.')
      setOpen(false)
      setForm(emptyForm)
      setFormErrors({})
    },
    onError: (err: unknown) => {
      const data = (err as { response?: { data?: { errors?: Record<string, string[]> } } })?.response?.data
      if (data?.errors) {
        const flat: Record<string, string> = {}
        Object.entries(data.errors).forEach(([k, v]) => { flat[k] = (v as string[])[0] })
        setFormErrors(flat)
      } else {
        error('Impossible de créer l\'utilisateur.')
      }
    },
  })

  async function handleRoleChange(id: number, firstName: string | undefined, currentRole: string) {
    const newRole = currentRole === 'admin' ? 'client' : 'admin'
    const name = firstName ?? 'cet utilisateur'
    const ok = await confirm({
      title: currentRole === 'admin' ? 'Retirer les droits admin' : 'Passer en administrateur',
      description: currentRole === 'admin'
        ? `Voulez-vous retirer les droits admin de ${name} ? Il deviendra client.`
        : `Voulez-vous donner les droits admin à ${name} ? Il aura accès au panel.`,
      confirmLabel: currentRole === 'admin' ? 'Retirer admin' : 'Passer admin',
      variant: currentRole === 'admin' ? 'warning' : 'default',
    })
    if (ok) roleMutation.mutate({ id, role: newRole })
  }

  function handleCreate(e: React.FormEvent) {
    e.preventDefault()
    setFormErrors({})
    createMutation.mutate()
  }

  const columns: ColumnDef<User>[] = [
    {
      id: 'user',
      header: 'Utilisateur',
      cell: ({ row }) => {
        const initials = `${row.original.first_name?.[0] ?? ''}${row.original.last_name?.[0] ?? ''}`.toUpperCase()
        return (
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-full bg-[#F4620A]/10 text-[#F4620A] flex items-center justify-center font-bold text-sm shrink-0">
              {initials || '?'}
            </div>
            <div className="min-w-0">
              <p className="font-medium text-[#0D0D0D] dark:text-white text-sm">
                {row.original.first_name} {row.original.last_name}
              </p>
              <p className="text-xs text-gray-400 truncate">{row.original.email}</p>
            </div>
          </div>
        )
      },
    },
    {
      accessorKey: 'whatsapp',
      header: 'WhatsApp',
      size: 150,
      cell: ({ row }) => (
        <span className="text-sm text-gray-600 dark:text-gray-400">{row.getValue<string>('whatsapp') || '—'}</span>
      ),
    },
    {
      accessorKey: 'role',
      header: 'Rôle',
      size: 130,
      cell: ({ row }) => {
        const r = row.getValue<string>('role')
        const info = ROLE_BADGE[r] ?? { label: r, variant: 'gray' as const }
        return <Badge variant={info.variant}>{info.label}</Badge>
      },
    },
    {
      accessorKey: 'created_at',
      header: 'Inscription',
      size: 130,
      cell: ({ row }) => (
        <span className="text-xs text-gray-500 dark:text-gray-400">
          {formatDate(row.getValue<string>('created_at'))}
        </span>
      ),
    },
    ...(isSuperAdmin ? [{
      id: 'actions',
      size: 150,
      cell: ({ row }: { row: { original: User } }) => {
        const role = row.original.role
        if (role === 'super_admin') return null
        const isAdmin = role === 'admin'
        return (
          <div onClick={(e: React.MouseEvent) => e.stopPropagation()}>
            <Button
              size="sm"
              variant={isAdmin ? 'outline' : 'default'}
              className={cn(isAdmin && 'text-red-600 border-red-200 hover:bg-red-50 dark:border-red-800/40 dark:hover:bg-red-900/20 hover:text-red-700')}
              loading={roleMutation.isPending && roleMutation.variables?.id === row.original.id}
              onClick={() => handleRoleChange(row.original.id, row.original.first_name, role)}
              aria-label={isAdmin ? 'Retirer les droits admin' : 'Passer en admin'}
            >
              {isAdmin ? 'Retirer admin' : 'Passer admin'}
            </Button>
          </div>
        )
      },
    } as ColumnDef<User>] : []),
  ]

  return (
    <div className="space-y-6">

      {/* En-tête */}
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D] dark:text-white">Utilisateurs</h1>
          {total > 0 && (
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
              {total} utilisateur{total > 1 ? 's' : ''}
            </p>
          )}
        </div>
        {isSuperAdmin && (
          <Button onClick={() => { setForm(emptyForm); setFormErrors({}); setOpen(true) }}>
            <Plus className="w-4 h-4" />
            Nouvel admin
          </Button>
        )}
      </div>

      {/* Filtres */}
      <div className="flex flex-wrap items-center gap-3">
        <div className="flex-1 min-w-[220px]">
          <Input
            placeholder="Rechercher un utilisateur…"
            value={search}
            onChange={e => { setSearch(e.target.value); setPage(1) }}
            aria-label="Rechercher un utilisateur"
          />
        </div>
        <div className="flex gap-1 flex-wrap" role="group" aria-label="Filtrer par rôle">
          {ROLE_FILTERS.map(f => (
            <button
              key={f.value}
              onClick={() => { setRoleFilter(f.value); setPage(1) }}
              aria-label={`Filtrer : ${f.label}`}
              className={cn(
                'px-3.5 py-2 text-xs font-semibold rounded-full transition-all duration-150 cursor-pointer',
                roleFilter === f.value
                  ? 'bg-[#F4620A] text-white shadow-sm'
                  : 'bg-gray-100 dark:bg-white/8 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-white/12',
              )}
            >
              {f.label}
            </button>
          ))}
        </div>
      </div>

      {/* DataTable */}
      <DataTable
        columns={columns}
        data={users}
        isLoading={isLoading}
        page={page}
        pageCount={lastPage}
        total={total}
        onPageChange={setPage}
        emptyMessage="Aucun utilisateur trouvé."
        emptyIcon={<Users className="w-10 h-10" />}
      />

      {/* Modal — Créer un admin */}
      <Dialog open={open} onOpenChange={setOpen}>
        <DialogContent className="dark:bg-[#1e1e1e] dark:border-white/10 max-w-md">
          <DialogHeader>
            <DialogTitle className="dark:text-white">Créer un administrateur</DialogTitle>
          </DialogHeader>

          <form onSubmit={handleCreate} className="space-y-4 py-2" id="create-user-form">
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-1.5">
                <Label htmlFor="first_name" className="dark:text-gray-200">Prénom</Label>
                <Input
                  id="first_name"
                  value={form.first_name}
                  onChange={e => setForm(f => ({ ...f, first_name: e.target.value }))}
                  placeholder="Kassamba"
                  required
                  error={formErrors.first_name}
                />
              </div>
              <div className="space-y-1.5">
                <Label htmlFor="last_name" className="dark:text-gray-200">Nom</Label>
                <Input
                  id="last_name"
                  value={form.last_name}
                  onChange={e => setForm(f => ({ ...f, last_name: e.target.value }))}
                  placeholder="Diaby"
                  required
                  error={formErrors.last_name}
                />
              </div>
            </div>

            <div className="space-y-1.5">
              <Label htmlFor="email" className="dark:text-gray-200">Email</Label>
              <Input
                id="email"
                type="email"
                value={form.email}
                onChange={e => setForm(f => ({ ...f, email: e.target.value }))}
                placeholder="admin@netspring.ci"
                required
                error={formErrors.email}
              />
            </div>

            <div className="space-y-1.5">
              <Label htmlFor="new_password" className="dark:text-gray-200">Mot de passe</Label>
              <div className="relative">
                <Input
                  id="new_password"
                  type={showPwd ? 'text' : 'password'}
                  value={form.password}
                  onChange={e => setForm(f => ({ ...f, password: e.target.value }))}
                  placeholder="8 caractères minimum"
                  required
                  className="pr-10"
                  error={formErrors.password}
                />
                <button
                  type="button"
                  onClick={() => setShowPwd(v => !v)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                >
                  {showPwd ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            <div className="space-y-1.5">
              <Label className="dark:text-gray-200">Rôle</Label>
              <Select value={form.role} onValueChange={v => setForm(f => ({ ...f, role: v }))}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="admin">Admin</SelectItem>
                  <SelectItem value="super_admin">Super Admin</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </form>

          <DialogFooter>
            <Button variant="outline" onClick={() => setOpen(false)} type="button">
              Annuler
            </Button>
            <Button
              form="create-user-form"
              type="submit"
              loading={createMutation.isPending}
              disabled={!form.first_name || !form.last_name || !form.email || !form.password}
            >
              Créer l'administrateur
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  )
}
