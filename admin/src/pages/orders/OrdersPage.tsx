import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { type ColumnDef } from '@tanstack/react-table'
import { ShoppingCart, Eye } from 'lucide-react'
import { ordersApi } from '@/api/endpoints'
import { cn, formatPrice, formatDate } from '@/lib/utils'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { DataTable } from '@/components/ui/data-table'
import { useDebounce } from '@/hooks/useDebounce'

type BadgeVariant = 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning' | 'info' | 'purple' | 'gray' | 'green' | 'red' | 'blue' | 'amber' | 'emerald' | 'orange'

interface Order {
  id: number
  order_number: string
  customer_name: string
  customer_email: string
  total: number | string
  payment_status: string
  status: string
  created_at: string
}

const orderStatusVariant: Record<string, BadgeVariant> = {
  pending:    'gray',
  paid:       'green',
  processing: 'blue',
  shipped:    'purple',
  delivered:  'emerald',
  cancelled:  'red',
  refunded:   'orange',
}

const orderStatusLabel: Record<string, string> = {
  pending:    'En attente',
  paid:       'Payé',
  processing: 'En cours',
  shipped:    'Expédié',
  delivered:  'Livré',
  cancelled:  'Annulé',
  refunded:   'Remboursé',
}

const STATUS_FILTERS = [
  { label: 'Tous',       value: '' },
  { label: 'En attente', value: 'pending' },
  { label: 'Payé',       value: 'paid' },
  { label: 'En cours',   value: 'processing' },
  { label: 'Expédié',    value: 'shipped' },
  { label: 'Livré',      value: 'delivered' },
  { label: 'Annulé',     value: 'cancelled' },
]

export default function OrdersPage() {
  const navigate = useNavigate()

  const [search, setSearch]             = useState('')
  const [statusFilter, setStatusFilter] = useState('')
  const [page, setPage]                 = useState(1)

  const debouncedSearch = useDebounce(search, 400)

  const params: Record<string, unknown> = { page, per_page: 15 }
  if (debouncedSearch) params.q = debouncedSearch
  if (statusFilter) params.status = statusFilter

  const { data, isLoading } = useQuery({
    queryKey: ['orders', params],
    queryFn: () => ordersApi.list(params).then(r => r.data),
  })

  const orders: Order[] = data?.data ?? []
  const lastPage = data?.meta?.last_page ?? data?.last_page ?? 1
  const total    = data?.meta?.total    ?? data?.total    ?? 0

  const columns: ColumnDef<Order>[] = [
    {
      accessorKey: 'order_number',
      header: 'N° Commande',
      size: 140,
      cell: ({ row }) => (
        <span className="font-medium text-[#F4620A] text-xs font-mono">
          {row.getValue<string>('order_number')}
        </span>
      ),
    },
    {
      id: 'customer',
      header: 'Client',
      cell: ({ row }) => (
        <div>
          <p className="font-medium text-[#0D0D0D] text-sm">{row.original.customer_name}</p>
          <p className="text-xs text-gray-400">{row.original.customer_email}</p>
        </div>
      ),
    },
    {
      accessorKey: 'total',
      header: 'Montant',
      size: 140,
      cell: ({ row }) => (
        <span className="font-semibold text-[#0D0D0D]">
          {formatPrice(row.getValue<number | string>('total'))}
        </span>
      ),
    },
    {
      accessorKey: 'payment_status',
      header: 'Paiement',
      size: 120,
      cell: ({ row }) => {
        const variants: Record<string, BadgeVariant> = {
          paid: 'green', unpaid: 'red', failed: 'red', refunded: 'orange',
        }
        const labels: Record<string, string> = {
          paid: 'Payé', unpaid: 'Non payé', failed: 'Échec', refunded: 'Remboursé',
        }
        const v = row.getValue<string>('payment_status')
        return <Badge variant={variants[v] ?? 'gray'}>{labels[v] ?? v}</Badge>
      },
    },
    {
      accessorKey: 'status',
      header: 'Statut',
      size: 130,
      cell: ({ row }) => {
        const v = row.getValue<string>('status')
        return (
          <Badge variant={orderStatusVariant[v] ?? 'gray'}>
            {orderStatusLabel[v] ?? v}
          </Badge>
        )
      },
    },
    {
      accessorKey: 'created_at',
      header: 'Date',
      size: 130,
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
          onClick={e => { e.stopPropagation(); navigate(`/commandes/${row.original.id}`) }}
          aria-label="Voir la commande"
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
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D]">Commandes</h1>
          {total > 0 && (
            <p className="text-sm text-gray-500 mt-0.5">
              {total} commande{total > 1 ? 's' : ''}
            </p>
          )}
        </div>
      </div>

      {/* Filtres */}
      <div className="flex flex-wrap items-center gap-3">
        <div className="flex-1 min-w-[220px]">
          <Input
            placeholder="Rechercher par N° commande ou client…"
            value={search}
            onChange={e => { setSearch(e.target.value); setPage(1) }}
            aria-label="Rechercher une commande"
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
        data={orders}
        isLoading={isLoading}
        page={page}
        pageCount={lastPage}
        total={total}
        onPageChange={setPage}
        onRowClick={row => navigate(`/commandes/${row.id}`)}
        emptyMessage="Aucune commande trouvée."
        emptyIcon={<ShoppingCart className="w-10 h-10" />}
      />
    </div>
  )
}
