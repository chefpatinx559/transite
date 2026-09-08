import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { type ColumnDef } from '@tanstack/react-table'
import { FileText, Plus, Pencil, Trash2, ExternalLink } from 'lucide-react'
import { postsApi } from '@/api/endpoints'
import { cn, formatDate, truncate } from '@/lib/utils'
import { buttonVariants } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { DataTable } from '@/components/ui/data-table'
import { useDebounce } from '@/hooks/useDebounce'
import { useToast } from '@/hooks/useToast'
import { useConfirm } from '@/components/ui/confirm-dialog'

type BadgeVariant = 'default' | 'secondary' | 'destructive' | 'outline' | 'success' | 'warning' | 'info' | 'purple' | 'gray' | 'green' | 'red' | 'blue' | 'amber' | 'emerald' | 'orange'

interface PostCategory {
  id: number
  name: string
  color?: string | null
}

interface Post {
  id: number
  title: string
  slug?: string
  category?: PostCategory
  status: string
  views_count?: number
  reading_time?: number | null
  published_at?: string | null
  created_at: string
}

const STATUS_FILTERS = [
  { label: 'Tous',      value: '' },
  { label: 'Brouillon', value: 'draft' },
  { label: 'Publié',    value: 'published' },
  { label: 'Archivé',   value: 'archived' },
]

export default function PostsPage() {
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()
  const confirm = useConfirm()

  const [search, setSearch]             = useState('')
  const [statusFilter, setStatusFilter] = useState('')
  const [page, setPage]                 = useState(1)

  const debouncedSearch = useDebounce(search, 400)

  const params: Record<string, unknown> = { page, per_page: 15 }
  if (debouncedSearch) params.q = debouncedSearch
  if (statusFilter) params.status = statusFilter

  const { data, isLoading } = useQuery({
    queryKey: ['posts', params],
    queryFn: () => postsApi.list(params).then(r => r.data),
  })

  const posts: Post[] = data?.data ?? []
  const lastPage = data?.meta?.last_page ?? data?.last_page ?? 1
  const total    = data?.meta?.total    ?? data?.total    ?? 0

  const deleteMutation = useMutation({
    mutationFn: (id: number) => postsApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['posts'] })
      success('Article supprimé.')
    },
    onError: () => error("Impossible de supprimer l'article."),
  })

  async function handleDelete(id: number, title: string) {
    const ok = await confirm({
      title: 'Supprimer l\'article',
      description: `Voulez-vous vraiment supprimer "${title}" ? Cette action est irréversible.`,
      confirmLabel: 'Supprimer',
      variant: 'danger',
    })
    if (ok) {
      deleteMutation.mutate(id)
    }
  }

  const columns: ColumnDef<Post>[] = [
    {
      accessorKey: 'title',
      header: 'Titre',
      cell: ({ row }) => (
        <div>
          <p className="font-medium text-[#0D0D0D] text-sm truncate max-w-[280px]">
            {row.getValue<string>('title')}
          </p>
          <p className="text-xs text-gray-400">{row.original.reading_time} min de lecture</p>
        </div>
      ),
    },
    {
      id: 'category',
      header: 'Catégorie',
      size: 130,
      cell: ({ row }) => row.original.category
        ? (
          <Badge
            style={{ backgroundColor: row.original.category.color || '#F4620A' }}
            className="text-white border-0"
          >
            {row.original.category.name}
          </Badge>
        )
        : <span className="text-gray-400 text-xs">—</span>,
    },
    {
      accessorKey: 'status',
      header: 'Statut',
      size: 110,
      cell: ({ row }) => {
        const v = row.getValue<string>('status')
        const map: Record<string, { variant: BadgeVariant; label: string }> = {
          published: { variant: 'green', label: 'Publié' },
          draft:     { variant: 'gray',  label: 'Brouillon' },
          archived:  { variant: 'amber', label: 'Archivé' },
        }
        const { variant, label } = map[v] ?? { variant: 'gray' as BadgeVariant, label: v }
        return <Badge variant={variant}>{label}</Badge>
      },
    },
    {
      accessorKey: 'views_count',
      header: 'Vues',
      size: 80,
      cell: ({ row }) => (
        <span className="text-sm text-gray-600">
          {(row.getValue<number>('views_count') ?? 0).toLocaleString('fr-FR')}
        </span>
      ),
    },
    {
      accessorKey: 'published_at',
      header: 'Date',
      size: 120,
      cell: ({ row }) => (
        <span className="text-xs text-gray-500">
          {formatDate(row.getValue<string | null>('published_at') ?? '')}
        </span>
      ),
    },
    {
      id: 'actions',
      size: 100,
      cell: ({ row }) => (
        <div
          className="flex items-center justify-end gap-1"
          onClick={e => e.stopPropagation()}
        >
          {row.original.status === 'published' && (
            <a
              href={`/blog/${row.original.slug}`}
              target="_blank"
              rel="noopener"
              aria-label="Voir l'article publié"
              className={cn(buttonVariants({ size: 'sm', variant: 'ghost' }), 'h-8 w-8 p-0')}
            >
              <ExternalLink className="w-3.5 h-3.5" aria-hidden="true" />
            </a>
          )}
          <Button
            size="sm"
            variant="ghost"
            onClick={() => navigate(`/articles/${row.original.id}`)}
            aria-label="Modifier"
          >
            <Pencil className="w-3.5 h-3.5" />
          </Button>
          <Button
            size="sm"
            variant="ghost"
            className="text-red-400 hover:text-red-600 hover:bg-red-50"
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

      {/* En-tête */}
      <div className="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D]">Articles</h1>
          {total > 0 && (
            <p className="text-sm text-gray-500 mt-0.5">
              {total} article{total > 1 ? 's' : ''}
            </p>
          )}
        </div>
        <Button onClick={() => navigate('/articles/creer')} aria-label="Créer un nouvel article">
          <Plus className="w-4 h-4" />
          Nouvel article
        </Button>
      </div>

      {/* Filtres */}
      <div className="flex flex-wrap items-center gap-3">
        <div className="flex-1 min-w-[220px]">
          <Input
            placeholder="Rechercher un article…"
            value={search}
            onChange={e => { setSearch(e.target.value); setPage(1) }}
            aria-label="Rechercher un article"
          />
        </div>
        <div className="flex gap-1" role="group" aria-label="Filtrer par statut">
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
        data={posts}
        isLoading={isLoading}
        page={page}
        pageCount={lastPage}
        total={total}
        onPageChange={setPage}
        emptyMessage="Aucun article trouvé."
        emptyIcon={<FileText className="w-10 h-10" />}
      />
    </div>
  )
}
