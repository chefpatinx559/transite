import { useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { type ColumnDef } from '@tanstack/react-table'
import { ArrowLeft, Users } from 'lucide-react'
import { formationsApi } from '@/api/endpoints'
import { cn, formatDate } from '@/lib/utils'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { DataTable } from '@/components/ui/data-table'
import { useDebounce } from '@/hooks/useDebounce'
import { useToast } from '@/hooks/useToast'

interface Registration {
  id: number
  name: string
  email: string
  phone?: string
  status: string
  payment_status: string
  registered_at: string
  notes?: string
}

const STATUS_BADGE: Record<string, { label: string; variant: 'green' | 'gray' | 'red' }> = {
  confirmed: { label: 'Confirmé',  variant: 'green' },
  pending:   { label: 'En attente', variant: 'gray' },
  cancelled: { label: 'Annulé',    variant: 'red' },
}

const PAYMENT_BADGE: Record<string, { label: string; variant: 'green' | 'gray' | 'red' | 'amber' }> = {
  paid:   { label: 'Payé',      variant: 'green' },
  unpaid: { label: 'Non payé',  variant: 'amber' },
  failed: { label: 'Échoué',   variant: 'red' },
}

const STATUS_FILTERS = [
  { label: 'Tous',        value: '' },
  { label: 'Confirmés',   value: 'confirmed' },
  { label: 'En attente',  value: 'pending' },
  { label: 'Annulés',     value: 'cancelled' },
]

export default function FormationRegistrationsPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()

  const [search, setSearch]         = useState('')
  const [statusFilter, setStatus]   = useState('')
  const [page, setPage]             = useState(1)
  const debouncedSearch             = useDebounce(search, 400)

  // Formation info
  const { data: formationData } = useQuery({
    queryKey: ['formation', id],
    queryFn: () => formationsApi.get(Number(id)).then(r => r.data),
  })
  const formation = formationData?.data ?? formationData

  const params: Record<string, unknown> = { page, per_page: 20 }
  if (debouncedSearch) params.q      = debouncedSearch
  if (statusFilter)    params.status = statusFilter

  const { data, isLoading } = useQuery({
    queryKey: ['formation-registrations', id, params],
    queryFn: () => formationsApi.registrations(Number(id), params).then(r => r.data),
    enabled: !!id,
  })

  const registrations: Registration[] = data?.data ?? []
  const lastPage = data?.meta?.last_page ?? data?.last_page ?? 1
  const total    = data?.meta?.total    ?? data?.total    ?? 0

  const updateMutation = useMutation({
    mutationFn: ({ regId, status }: { regId: number; status: string }) =>
      formationsApi.updateRegistration(Number(id), regId, status),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['formation-registrations', id] })
      success('Statut mis à jour.')
    },
    onError: () => error('Impossible de modifier le statut.'),
  })

  const columns: ColumnDef<Registration>[] = [
    {
      id: 'participant',
      header: 'Participant',
      cell: ({ row }) => {
        const initials = row.original.name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
        return (
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-full bg-[#F4620A]/10 text-[#F4620A] flex items-center justify-center font-bold text-sm shrink-0">
              {initials}
            </div>
            <div>
              <p className="font-medium text-[#0D0D0D] dark:text-white text-sm">{row.original.name}</p>
              <p className="text-xs text-gray-400">{row.original.email}</p>
            </div>
          </div>
        )
      },
    },
    {
      accessorKey: 'phone',
      header: 'Téléphone',
      size: 140,
      cell: ({ row }) => <span className="text-sm text-gray-600 dark:text-gray-400">{row.original.phone || '—'}</span>,
    },
    {
      accessorKey: 'status',
      header: 'Statut',
      size: 160,
      cell: ({ row }) => {
        const info = STATUS_BADGE[row.original.status] ?? { label: row.original.status, variant: 'gray' as const }
        return (
          <div onClick={e => e.stopPropagation()}>
            <Select value={row.original.status}
              onValueChange={v => updateMutation.mutate({ regId: row.original.id, status: v })}
            >
              <SelectTrigger className="h-8 text-xs w-36">
                <SelectValue>
                  <Badge variant={info.variant}>{info.label}</Badge>
                </SelectValue>
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="pending">En attente</SelectItem>
                <SelectItem value="confirmed">Confirmé</SelectItem>
                <SelectItem value="cancelled">Annulé</SelectItem>
              </SelectContent>
            </Select>
          </div>
        )
      },
    },
    {
      accessorKey: 'payment_status',
      header: 'Paiement',
      size: 110,
      cell: ({ row }) => {
        const info = PAYMENT_BADGE[row.original.payment_status] ?? { label: row.original.payment_status, variant: 'gray' as const }
        return <Badge variant={info.variant}>{info.label}</Badge>
      },
    },
    {
      accessorKey: 'registered_at',
      header: 'Inscription',
      size: 130,
      cell: ({ row }) => <span className="text-xs text-gray-500">{formatDate(row.original.registered_at)}</span>,
    },
  ]

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-3 flex-wrap">
        <Button variant="outline" size="sm" onClick={() => navigate('/formations')}>
          <ArrowLeft size={15} /> Retour
        </Button>
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D] dark:text-white">
            Inscriptions — {formation?.title ?? '…'}
          </h1>
          {total > 0 && (
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
              {formation?.participants_count ?? 0}
              {formation?.max_participants ? `/${formation.max_participants}` : ''} inscrits
            </p>
          )}
        </div>
      </div>

      <div className="flex flex-wrap items-center gap-3">
        <div className="flex-1 min-w-[220px]">
          <Input placeholder="Rechercher par nom, email…" value={search}
            onChange={e => { setSearch(e.target.value); setPage(1) }} />
        </div>
        <div className="flex gap-1" role="group">
          {STATUS_FILTERS.map(f => (
            <button key={f.value} onClick={() => { setStatus(f.value); setPage(1) }}
              className={cn('px-3.5 py-2 text-xs font-semibold rounded-full transition-all duration-150 cursor-pointer',
                statusFilter === f.value ? 'bg-[#F4620A] text-white shadow-sm' : 'bg-gray-100 dark:bg-white/8 text-gray-600 hover:bg-gray-200'
              )}
            >{f.label}</button>
          ))}
        </div>
      </div>

      <DataTable
        columns={columns}
        data={registrations}
        isLoading={isLoading}
        page={page}
        pageCount={lastPage}
        total={total}
        onPageChange={setPage}
        emptyMessage="Aucune inscription trouvée."
        emptyIcon={<Users className="w-10 h-10" />}
      />
    </div>
  )
}
