import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { type ColumnDef } from '@tanstack/react-table'
import { Package, Plus, Pencil, Trash2, RefreshCw, Search } from 'lucide-react'
import { productsApi } from '@/api/endpoints'
import { cn, formatPrice, storageUrl } from '@/lib/utils'
import { useDebounce } from '@/hooks/useDebounce'
import { useToast } from '@/hooks/useToast'
import { useConfirm } from '@/components/ui/confirm-dialog'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Switch } from '@/components/ui/switch'
import { DataTable } from '@/components/ui/data-table'

type BadgeVariant = 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning' | 'info' | 'purple' | 'gray' | 'green' | 'red' | 'blue' | 'amber' | 'emerald' | 'orange'

interface ProductImage { url: string; alt?: string }

interface Product {
  id: number
  name: string
  category?: { id: number; name: string }
  price: number | string
  compare_price?: number | string | null
  stock: number
  is_published: boolean
  sku?: string
  images?: ProductImage[] | string[] | null
}

function getImageUrl(images: Product['images']): string | null {
  if (!images || images.length === 0) return null
  const first = images[0]
  const rawUrl = typeof first === 'string' ? first : (first as ProductImage).url ?? null
  return storageUrl(rawUrl)
}

const PUBLISHED_OPTIONS = [
  { label: 'Tous',       value: '' },
  { label: 'Publiés',    value: '1' },
  { label: 'Brouillons', value: '0' },
]

export default function ProductsPage() {
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()
  const confirm = useConfirm()

  const [search, setSearch]             = useState('')
  const [publishedFilter, setPublished] = useState('')
  const [page, setPage]                 = useState(1)

  const debouncedSearch = useDebounce(search, 400)

  const params: Record<string, unknown> = { page, per_page: 15 }
  if (debouncedSearch) params.q = debouncedSearch
  if (publishedFilter !== '') params.is_published = publishedFilter

  const { data, isLoading, isFetching, refetch } = useQuery({
    queryKey: ['products', params],
    queryFn: () => productsApi.list(params).then(r => r.data),
  })

  const products: Product[] = data?.data ?? []
  const lastPage = data?.meta?.last_page ?? data?.last_page ?? 1
  const total    = data?.meta?.total    ?? data?.total    ?? 0

  const toggleMutation = useMutation({
    mutationFn: ({ id, val }: { id: number; val: boolean }) =>
      productsApi.toggle(id, val),
    onSuccess: (_, { val }) => {
      queryClient.invalidateQueries({ queryKey: ['products'] })
      success(val ? 'Produit publié avec succès.' : 'Produit dépublié.')
    },
    onError: () => error('Impossible de modifier la visibilité.'),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => productsApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['products'] })
      success('Produit supprimé.')
    },
    onError: () => error('Impossible de supprimer ce produit.'),
  })

  async function handleDelete(id: number, name: string) {
    const ok = await confirm({
      title: 'Supprimer le produit',
      description: `Voulez-vous vraiment supprimer "${name}" ? Cette action est irréversible.`,
      confirmLabel: 'Supprimer',
      cancelLabel: 'Annuler',
      variant: 'danger',
    })
    if (ok) deleteMutation.mutate(id)
  }

  const columns: ColumnDef<Product>[] = [
    {
      accessorKey: 'name',
      header: 'Produit',
      cell: ({ row }) => {
        const imgUrl = getImageUrl(row.original.images)
        return (
          <div className="flex items-center gap-3">
            {imgUrl ? (
              <img
                src={imgUrl}
                alt={row.original.name}
                className="w-11 h-11 rounded-[10px] object-cover shrink-0 border border-[#E5E7EB]"
                loading="lazy"
              />
            ) : (
              <div className="w-11 h-11 rounded-[10px] bg-gray-100 flex items-center justify-center shrink-0">
                <Package className="w-5 h-5 text-gray-300" />
              </div>
            )}
            <div className="min-w-0">
              <p className="font-semibold text-[#0D0D0D] truncate max-w-[200px]">{row.original.name}</p>
              <div className="flex items-center gap-2 mt-0.5">
                {row.original.category && (
                  <span className="text-xs text-gray-400">{row.original.category.name}</span>
                )}
                {row.original.sku && (
                  <span className="text-xs text-gray-300 font-mono">#{row.original.sku}</span>
                )}
              </div>
            </div>
          </div>
        )
      },
    },
    {
      accessorKey: 'price',
      header: 'Prix',
      size: 140,
      cell: ({ row }) => (
        <div>
          <p className="font-semibold text-[#F4620A]">
            {formatPrice(row.getValue<number | string>('price'))}
          </p>
          {row.original.compare_price && Number(row.original.compare_price) > 0 && (
            <p className="text-xs text-gray-400 line-through">
              {formatPrice(row.original.compare_price)}
            </p>
          )}
        </div>
      ),
    },
    {
      accessorKey: 'stock',
      header: 'Stock',
      size: 120,
      cell: ({ row }) => {
        const stock = row.getValue<number>('stock')
        if (stock === -1) return <Badge variant={'blue' as BadgeVariant}>Illimité</Badge>
        if (stock === 0)  return <Badge variant={'red' as BadgeVariant}>Épuisé</Badge>
        if (stock <= 5)   return <Badge variant={'amber' as BadgeVariant}>{stock} restants</Badge>
        return <Badge variant={'green' as BadgeVariant}>{stock} en stock</Badge>
      },
    },
    {
      accessorKey: 'is_published',
      header: 'Publié',
      size: 80,
      cell: ({ row }) => (
        <Switch
          checked={row.original.is_published}
          onCheckedChange={val => toggleMutation.mutate({ id: row.original.id, val })}
          disabled={toggleMutation.isPending}
          aria-label={row.original.is_published
            ? `Dépublier ${row.original.name}`
            : `Publier ${row.original.name}`}
        />
      ),
    },
    {
      id: 'actions',
      header: '',
      size: 120,
      cell: ({ row }) => {
        const isDeleting = deleteMutation.isPending && deleteMutation.variables === row.original.id
        return (
          <div
            className="flex items-center justify-end gap-2"
            onClick={e => e.stopPropagation()}
          >
            <Button
              size="sm"
              variant="outline"
              onClick={e => { e.stopPropagation(); navigate(`/produits/${row.original.id}`) }}
              aria-label={`Modifier ${row.original.name}`}
            >
              <Pencil className="w-3.5 h-3.5" />
              Modifier
            </Button>
            <Button
              size="sm"
              variant="destructive"
              loading={isDeleting}
              onClick={e => { e.stopPropagation(); handleDelete(row.original.id, row.original.name) }}
              aria-label={`Supprimer ${row.original.name}`}
            >
              <Trash2 className="w-3.5 h-3.5" />
            </Button>
          </div>
        )
      },
    },
  ]

  return (
    <div className="space-y-6">

      {/* En-tête */}
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D]">Produits</h1>
          {total > 0 && (
            <p className="text-sm text-gray-500 mt-0.5">
              {total} produit{total > 1 ? 's' : ''} au total
            </p>
          )}
        </div>
        <div className="flex gap-2">
          <Button
            variant="outline"
            size="sm"
            onClick={() => refetch()}
            aria-label="Actualiser la liste"
          >
            <RefreshCw className={cn('w-4 h-4', isFetching && 'animate-spin')} />
          </Button>
          <Button onClick={() => navigate('/produits/creer')} aria-label="Créer un nouveau produit">
            <Plus className="w-4 h-4" />
            Nouveau produit
          </Button>
        </div>
      </div>

      {/* Filtres */}
      <div className="flex flex-wrap items-center gap-3">
        <div className="relative flex-1 min-w-[220px]">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
          <Input
            type="search"
            value={search}
            onChange={e => { setSearch(e.target.value); setPage(1) }}
            placeholder="Rechercher par nom, SKU…"
            aria-label="Rechercher un produit"
            className="pl-9"
          />
        </div>
        <div className="flex gap-1" role="group" aria-label="Filtrer par statut de publication">
          {PUBLISHED_OPTIONS.map(opt => (
            <button
              key={opt.value}
              onClick={() => { setPublished(opt.value); setPage(1) }}
              aria-pressed={publishedFilter === opt.value}
              className={cn(
                'px-3.5 py-2 text-xs font-semibold rounded-full transition-all duration-150 cursor-pointer',
                publishedFilter === opt.value
                  ? 'bg-[#F4620A] text-white shadow-sm'
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
              )}
            >
              {opt.label}
            </button>
          ))}
        </div>
      </div>

      {/* DataTable */}
      <DataTable
        columns={columns}
        data={products}
        isLoading={isLoading}
        page={page}
        pageCount={lastPage}
        total={total}
        onPageChange={setPage}
        emptyMessage="Aucun produit trouvé."
        emptyIcon={<Package className="w-10 h-10" />}
      />
    </div>
  )
}
