import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { type ColumnDef } from '@tanstack/react-table'
import { GraduationCap, Plus, Pencil, Trash2, Users, RefreshCw } from 'lucide-react'
import { formationsApi } from '@/api/endpoints'
import { cn, formatPrice, storageUrl } from '@/lib/utils'
import { useDebounce } from '@/hooks/useDebounce'
import { useToast } from '@/hooks/useToast'
import { useConfirm } from '@/components/ui/confirm-dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import { Switch } from '@/components/ui/switch'
import { DataTable } from '@/components/ui/data-table'

interface Formation {
  id: number
  title: string
  type: 'online' | 'presentielle'
  is_free: boolean
  price: number
  date_start?: string | null
  is_published: boolean
  cover_image?: string | null
  participants_count: number
  max_participants?: number | null
  registrations_count?: number
}

const TYPE_FILTERS = [
  { label: 'Toutes',        value: '' },
  { label: 'En ligne',      value: 'online' },
  { label: 'Présentielle',  value: 'presentielle' },
]

export default function FormationsPage() {
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()
  const confirm = useConfirm()

  const [search, setSearch]       = useState('')
  const [typeFilter, setType]     = useState('')
  const [page, setPage]           = useState(1)
  const debouncedSearch           = useDebounce(search, 400)

  const params: Record<string, unknown> = { page, per_page: 15 }
  if (debouncedSearch) params.q    = debouncedSearch
  if (typeFilter)      params.type = typeFilter

  const { data, isLoading, isFetching, refetch } = useQuery({
    queryKey: ['formations', params],
    queryFn: () => formationsApi.list(params).then(r => r.data),
  })

  const formations: Formation[] = data?.data ?? []
  const lastPage = data?.meta?.last_page ?? data?.last_page ?? 1
  const total    = data?.meta?.total    ?? data?.total    ?? 0

  const toggleMutation = useMutation({
    mutationFn: ({ id, val }: { id: number; val: boolean }) => formationsApi.toggle(id, val),
    onSuccess: (_, { val }) => {
      queryClient.invalidateQueries({ queryKey: ['formations'] })
      success(val ? 'Formation publiée.' : 'Formation dépubliée.')
    },
    onError: () => error('Impossible de modifier la visibilité.'),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => formationsApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['formations'] })
      success('Formation supprimée.')
    },
    onError: () => error('Impossible de supprimer cette formation.'),
  })

  async function handleDelete(id: number, title: string) {
    const ok = await confirm({
      title: 'Supprimer la formation',
      description: `Voulez-vous vraiment supprimer "${title}" ? Toutes les inscriptions seront perdues.`,
      confirmLabel: 'Supprimer',
      variant: 'danger',
    })
    if (ok) deleteMutation.mutate(id)
  }

  function formatDate(d?: string | null) {
    if (!d) return '—'
    return new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })
  }

  const columns: ColumnDef<Formation>[] = [
    {
      accessorKey: 'title',
      header: 'Formation',
      cell: ({ row }) => {
        const img = row.original.cover_image ? storageUrl(row.original.cover_image) : null
        return (
          <div className="flex items-center gap-3">
            {img ? (
              <img src={img} alt={row.original.title} className="w-11 h-11 rounded-[10px] object-cover shrink-0 border border-[#E5E7EB]" loading="lazy" />
            ) : (
              <div className="w-11 h-11 rounded-[10px] bg-gray-100 flex items-center justify-center shrink-0">
                <GraduationCap className="w-5 h-5 text-gray-300" />
              </div>
            )}
            <div className="min-w-0">
              <p className="font-semibold text-[#0D0D0D] dark:text-white truncate max-w-[220px]">{row.original.title}</p>
              <div className="flex items-center gap-2 mt-0.5">
                <Badge variant={row.original.type === 'online' ? 'blue' : 'purple'} className="text-[10px]">
                  {row.original.type === 'online' ? 'En ligne' : 'Présentielle'}
                </Badge>
              </div>
            </div>
          </div>
        )
      },
    },
    {
      accessorKey: 'price',
      header: 'Prix',
      size: 130,
      cell: ({ row }) => row.original.is_free
        ? <Badge variant="green">Gratuite</Badge>
        : <span className="font-semibold text-[#F4620A]">{formatPrice(row.original.price)}</span>,
    },
    {
      accessorKey: 'date_start',
      header: 'Date',
      size: 140,
      cell: ({ row }) => <span className="text-sm text-gray-600 dark:text-gray-400">{formatDate(row.original.date_start)}</span>,
    },
    {
      id: 'inscrits',
      header: 'Inscrits',
      size: 100,
      cell: ({ row }) => (
        <button
          className="flex items-center gap-1.5 text-sm font-medium text-[#F4620A] hover:underline cursor-pointer"
          onClick={e => { e.stopPropagation(); navigate(`/formations/${row.original.id}/inscriptions`) }}
          aria-label="Voir les inscriptions"
        >
          <Users className="w-3.5 h-3.5" />
          {row.original.participants_count}
          {row.original.max_participants ? `/${row.original.max_participants}` : ''}
        </button>
      ),
    },
    {
      accessorKey: 'is_published',
      header: 'Publiée',
      size: 80,
      cell: ({ row }) => (
        <Switch
          checked={row.original.is_published}
          onCheckedChange={val => toggleMutation.mutate({ id: row.original.id, val })}
          disabled={toggleMutation.isPending}
          aria-label={row.original.is_published ? `Dépublier ${row.original.title}` : `Publier ${row.original.title}`}
        />
      ),
    },
    {
      id: 'actions',
      size: 120,
      cell: ({ row }) => (
        <div className="flex items-center justify-end gap-2" onClick={e => e.stopPropagation()}>
          <Button size="sm" variant="outline"
            onClick={() => navigate(`/formations/${row.original.id}`)}
            aria-label="Modifier"
          >
            <Pencil className="w-3.5 h-3.5" />
            Modifier
          </Button>
          <Button size="sm" variant="destructive"
            loading={deleteMutation.isPending && deleteMutation.variables === row.original.id}
            onClick={() => handleDelete(row.original.id, row.original.title)}
            aria-label="Supprimer"
          >
            <Trash2 className="w-3.5 h-3.5" />
          </Button>
        </div>
      ),
    },
  ]

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D] dark:text-white">Formations</h1>
          {total > 0 && <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{total} formation{total > 1 ? 's' : ''}</p>}
        </div>
        <div className="flex gap-2">
          <Button variant="outline" size="sm" onClick={() => refetch()} aria-label="Actualiser">
            <RefreshCw className={cn('w-4 h-4', isFetching && 'animate-spin')} />
          </Button>
          <Button onClick={() => navigate('/formations/creer')}>
            <Plus className="w-4 h-4" />
            Nouvelle formation
          </Button>
        </div>
      </div>

      <div className="flex flex-wrap items-center gap-3">
        <div className="flex-1 min-w-[220px]">
          <Input placeholder="Rechercher une formation…" value={search}
            onChange={e => { setSearch(e.target.value); setPage(1) }} />
        </div>
        <div className="flex gap-1" role="group" aria-label="Filtrer par type">
          {TYPE_FILTERS.map(f => (
            <button key={f.value}
              onClick={() => { setType(f.value); setPage(1) }}
              className={cn('px-3.5 py-2 text-xs font-semibold rounded-full transition-all duration-150 cursor-pointer',
                typeFilter === f.value ? 'bg-[#F4620A] text-white shadow-sm' : 'bg-gray-100 dark:bg-white/8 text-gray-600 dark:text-gray-400 hover:bg-gray-200'
              )}
            >{f.label}</button>
          ))}
        </div>
      </div>

      <DataTable
        columns={columns}
        data={formations}
        isLoading={isLoading}
        page={page}
        pageCount={lastPage}
        total={total}
        onPageChange={setPage}
        emptyMessage="Aucune formation trouvée."
        emptyIcon={<GraduationCap className="w-10 h-10" />}
      />
    </div>
  )
}
