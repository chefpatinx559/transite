import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { type ColumnDef } from '@tanstack/react-table'
import { MessageSquare, Eye } from 'lucide-react'
import { quotesApi } from '@/api/endpoints'
import { cn, formatDate, truncate } from '@/lib/utils'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { DataTable } from '@/components/ui/data-table'
import { useDebounce } from '@/hooks/useDebounce'

type BadgeVariant = 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning' | 'info' | 'purple' | 'gray' | 'green' | 'red' | 'blue' | 'amber' | 'emerald' | 'orange'

interface Quote {
  id: number
  name: string
  email: string
  whatsapp?: string
  product_description?: string
  budget?: string | null
  status: string
  created_at: string
}

const STATUS_FILTERS = [
  { label: 'Tous',         value: '' },
  { label: 'Nouveau',      value: 'new' },
  { label: 'En révision',  value: 'in_review' },
  { label: 'Devis envoyé', value: 'quoted' },
  { label: 'Accepté',      value: 'won' },
  { label: 'Perdu',        value: 'lost' },
]

export default function QuotesPage() {
  const navigate = useNavigate()

  const [search, setSearch]             = useState('')
  const [statusFilter, setStatusFilter] = useState('')
  const [page, setPage]                 = useState(1)

  const debouncedSearch = useDebounce(search, 400)

  const params: Record<string, unknown> = { page, per_page: 15 }
  if (debouncedSearch) params.q = debouncedSearch
  if (statusFilter) params.status = statusFilter

  const { data, isLoading } = useQuery({
    queryKey: ['quotes', params],
    queryFn: () => quotesApi.list(params).then(r => r.data),
  })

  const quotes: Quote[] = data?.data ?? []
  const lastPage = data?.meta?.last_page ?? data?.last_page ?? 1
  const total    = data?.meta?.total    ?? data?.total    ?? 0

  const columns: ColumnDef<Quote>[] = [
    {
      id: 'contact',
      header: 'Contact',
      cell: ({ row }) => (
        <div>
          <p className="font-medium text-[#0D0D0D] text-sm">{row.original.name}</p>
          <p className="text-xs text-gray-400">{row.original.whatsapp}</p>
        </div>
      ),
    },
    {
      accessorKey: 'email',
      header: 'Email',
      size: 180,
      cell: ({ row }) => (
        <span className="text-sm text-gray-600 truncate max-w-[160px] block">
          {row.getValue<string>('email')}
        </span>
      ),
    },
    {
      accessorKey: 'product_description',
      header: 'Produit demandé',
      cell: ({ row }) => (
        <span className="text-sm text-gray-600">
          {truncate(row.getValue<string>('product_description') ?? '', 55)}
        </span>
      ),
    },
    {
      accessorKey: 'budget',
      header: 'Budget',
      size: 120,
      cell: ({ row }) => (
        <span className="text-sm text-gray-600">{row.getValue<string>('budget') || '—'}</span>
      ),
    },
    {
      accessorKey: 'status',
      header: 'Statut',
      size: 130,
      cell: ({ row }) => {
        const v = row.getValue<string>('status')
        const map: Record<string, { variant: BadgeVariant; label: string }> = {
          new:       { variant: 'blue',   label: 'Nouveau' },
          in_review: { variant: 'amber',  label: 'En révision' },
          quoted:    { variant: 'purple', label: 'Devis envoyé' },
          won:       { variant: 'green',  label: 'Accepté' },
          lost:      { variant: 'red',    label: 'Perdu' },
        }
        const { variant, label } = map[v] ?? { variant: 'gray' as BadgeVariant, label: v }
        return <Badge variant={variant}>{label}</Badge>
      },
    },
    {
      accessorKey: 'created_at',
      header: 'Date',
      size: 120,
      cell: ({ row }) => (
        <span className="text-xs text-gray-500">
          {formatDate(row.getValue<string>('created_at'))}
        </span>
      ),
    },
    {
      id: 'actions',
      size: 60,
      cell: ({ row }) => (
        <Button
          size="sm"
          variant="ghost"
          onClick={e => { e.stopPropagation(); navigate(`/devis/${row.original.id}`) }}
          aria-label="Voir le devis"
        >
          <Eye className="w-4 h-4" />
        </Button>
      ),
    },
  ]

  return (
    <div className="space-y-6">

      {/* En-tête */}
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D]">Demandes de devis</h1>
          {total > 0 && (
            <p className="text-sm text-gray-500 mt-0.5">
              {total} demande{total > 1 ? 's' : ''}
            </p>
          )}
        </div>
      </div>

      {/* Filtres */}
      <div className="flex flex-wrap items-center gap-3">
        <div className="flex-1 min-w-[220px]">
          <Input
            placeholder="Rechercher par nom, email…"
            value={search}
            onChange={e => { setSearch(e.target.value); setPage(1) }}
            aria-label="Rechercher une demande de devis"
          />
        </div>
        <div className="flex flex-wrap gap-1.5" role="group" aria-label="Filtrer par statut">
          {STATUS_FILTERS.map(f => (
            <button
              key={f.value}
              onClick={() => { setStatusFilter(f.value); setPage(1) }}
              aria-label={`Filtrer par statut : ${f.label}`}
              className={cn(
                'px-3.5 py-2 text-xs font-semibold rounded-full transition-all duration-150 cursor-pointer',
                statusFilter === f.value
                  ? 'bg-[#F4620A] text-white shadow-sm'
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
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
        data={quotes}
        isLoading={isLoading}
        page={page}
        pageCount={lastPage}
        total={total}
        onPageChange={setPage}
        onRowClick={row => navigate(`/devis/${row.id}`)}
        emptyMessage="Aucune demande de devis trouvée."
        emptyIcon={<MessageSquare className="w-10 h-10" />}
      />
    </div>
  )
}
