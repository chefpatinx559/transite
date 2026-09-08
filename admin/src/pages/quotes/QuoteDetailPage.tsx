import { useState, useEffect } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { ArrowLeft } from 'lucide-react'
import { quotesApi } from '@/api/endpoints'
import { formatDate, formatDateTime, formatPrice } from '@/lib/utils'
import { Badge, quoteStatusBadge, quoteStatusLabel } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardHeader, CardContent } from '@/components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { useToast } from '@/hooks/useToast'

interface Quote {
  id: number
  first_name?: string
  last_name?: string
  name?: string
  email: string
  whatsapp?: string
  phone?: string
  city?: string
  country_source?: string
  experience?: string
  company?: string
  product?: string
  service?: string
  description?: string
  services?: string[]
  quantity?: number | string | null
  budget?: string | number | null
  status: string
  admin_notes?: string
  created_at: string
  updated_at?: string
}

interface ApiResponse {
  data?: Quote
}

const QUOTE_STATUSES = [
  { value: 'new',       label: 'Nouveau' },
  { value: 'in_review', label: 'En révision' },
  { value: 'quoted',    label: 'Devis envoyé' },
  { value: 'won',       label: 'Accepté' },
  { value: 'lost',      label: 'Perdu' },
]

function getQuoteName(quote: Quote): string {
  if (quote.name) return quote.name
  return [quote.first_name, quote.last_name].filter(Boolean).join(' ') || '—'
}

export default function QuoteDetailPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()

  const [newStatus, setNewStatus] = useState('')
  const [adminNotes, setAdminNotes] = useState('')

  const { data: response, isLoading } = useQuery({
    queryKey: ['quote', id],
    queryFn: () => quotesApi.get(Number(id)).then(r => r.data as ApiResponse | Quote),
    enabled: !!id,
  })

  const quote: Quote | undefined =
    (response as ApiResponse)?.data ?? (response as Quote | undefined)

  useEffect(() => {
    if (!quote) return
    setNewStatus(quote.status)
    setAdminNotes(quote.admin_notes ?? '')
  }, [quote])

  const statusMutation = useMutation({
    mutationFn: () => quotesApi.updateStatus(Number(id), newStatus, adminNotes || undefined),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['quote', id] })
      queryClient.invalidateQueries({ queryKey: ['quotes'] })
      success('Statut et notes enregistrés.')
    },
    onError: () => error('Impossible d\'enregistrer les modifications.'),
  })

  if (isLoading) {
    return <div className="flex items-center justify-center py-20 text-gray-400">Chargement…</div>
  }

  if (!quote) {
    return (
      <div className="text-center py-20 text-gray-400">
        <p>Devis introuvable.</p>
        <Button variant="outline" size="sm" className="mt-4" onClick={() => navigate('/devis')}>
          Retour aux devis
        </Button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-3">
        <Button variant="outline" size="sm" onClick={() => navigate('/devis')} aria-label="Retour aux devis">
          <ArrowLeft size={15} />
          Retour
        </Button>
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D]">
            Devis #{quote.id}
          </h1>
          <p className="text-sm text-gray-500 mt-0.5">{formatDateTime(quote.created_at)}</p>
        </div>
        <Badge variant={quoteStatusBadge[quote.status] ?? 'gray'} className="ml-auto">
          {quoteStatusLabel[quote.status] ?? quote.status}
        </Badge>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main column */}
        <div className="lg:col-span-2 space-y-6">

          {/* Client info */}
          <Card>
            <CardHeader title="Informations du client" />
            <CardContent className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
              <div>
                <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nom</p>
                <p className="font-semibold text-[#0D0D0D]">{getQuoteName(quote)}</p>
              </div>
              <div>
                <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</p>
                <a href={`mailto:${quote.email}`} className="text-[#F4620A] hover:underline">
                  {quote.email}
                </a>
              </div>
              {(quote.whatsapp ?? quote.phone) && (
                <div>
                  <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">WhatsApp</p>
                  <a
                    href={`https://wa.me/${(quote.whatsapp ?? quote.phone ?? '').replace(/\D/g, '')}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-emerald-600 hover:underline"
                  >
                    {quote.whatsapp ?? quote.phone}
                  </a>
                </div>
              )}
              {quote.city && (
                <div>
                  <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Ville</p>
                  <p className="text-gray-700">{quote.city}</p>
                </div>
              )}
              {quote.experience && (
                <div>
                  <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Expérience</p>
                  <p className="text-gray-700">{quote.experience}</p>
                </div>
              )}
              {quote.company && (
                <div>
                  <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Entreprise</p>
                  <p className="text-gray-700">{quote.company}</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Project details */}
          <Card>
            <CardHeader title="Détails du projet" />
            <CardContent className="space-y-4 text-sm">
              {(quote.product ?? quote.service) && (
                <div>
                  <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                    Produit / Service
                  </p>
                  <p className="text-gray-700">{quote.product ?? quote.service}</p>
                </div>
              )}
              {quote.description && (
                <div>
                  <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Description</p>
                  <div className="bg-gray-50 rounded-[10px] px-4 py-3 text-gray-700 whitespace-pre-wrap leading-relaxed">
                    {quote.description}
                  </div>
                </div>
              )}
              <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
                {quote.quantity != null && (
                  <div>
                    <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Quantité</p>
                    <p className="text-gray-700">{quote.quantity}</p>
                  </div>
                )}
                {quote.budget != null && (
                  <div>
                    <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Budget</p>
                    <p className="text-gray-700 font-semibold">{formatPrice(quote.budget)}</p>
                  </div>
                )}
                {quote.country_source && (
                  <div>
                    <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Pays source</p>
                    <p className="text-gray-700">{quote.country_source}</p>
                  </div>
                )}
              </div>
              {quote.services && quote.services.length > 0 && (
                <div>
                  <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Services demandés</p>
                  <div className="flex flex-wrap gap-2">
                    {quote.services.map((s, i) => (
                      <Badge key={i} variant="blue">{s}</Badge>
                    ))}
                  </div>
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Sidebar */}
        <div className="space-y-6">

          {/* CRM status card */}
          <Card>
            <CardHeader title="Statut CRM" />
            <CardContent className="space-y-4">
              <div className="flex items-center gap-2">
                <span className="text-sm text-gray-500">Statut actuel :</span>
                <Badge variant={quoteStatusBadge[quote.status] ?? 'gray'}>
                  {quoteStatusLabel[quote.status] ?? quote.status}
                </Badge>
              </div>
              <div className="space-y-1.5">
                <Label htmlFor="quote-status">Changer le statut</Label>
                <Select value={newStatus} onValueChange={val => setNewStatus(val)}>
                  <SelectTrigger id="quote-status" aria-label="Sélectionner le statut du devis">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {QUOTE_STATUSES.map(s => (
                      <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div className="space-y-1.5">
                <Label htmlFor="admin-notes">Notes internes (non visibles par le client)</Label>
                <Textarea
                  id="admin-notes"
                  rows={4}
                  value={adminNotes}
                  onChange={e => setAdminNotes(e.target.value)}
                  placeholder="Commentaires réservés à l'équipe…"
                />
              </div>
              {quote.admin_notes && (
                <div className="bg-amber-50 border border-amber-200 rounded-[10px] p-3">
                  <p className="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-1">Notes existantes</p>
                  <p className="text-sm text-amber-800 whitespace-pre-wrap">{quote.admin_notes}</p>
                </div>
              )}
              <Button
                className="w-full"
                loading={statusMutation.isPending}
                disabled={newStatus === quote.status && adminNotes === (quote.admin_notes ?? '')}
                onClick={() => statusMutation.mutate()}
                aria-label="Enregistrer le statut et les notes"
              >
                Enregistrer
              </Button>
            </CardContent>
          </Card>

          {/* Dates card */}
          <Card>
            <CardHeader title="Dates" />
            <CardContent className="text-sm space-y-3">
              <div className="flex justify-between">
                <span className="text-gray-500">Créée le</span>
                <span className="font-medium">{formatDate(quote.created_at)}</span>
              </div>
              {quote.updated_at && (
                <div className="flex justify-between">
                  <span className="text-gray-500">Mise à jour</span>
                  <span className="font-medium">{formatDate(quote.updated_at)}</span>
                </div>
              )}
            </CardContent>
          </Card>

          <Button
            variant="outline"
            className="w-full"
            onClick={() => navigate('/devis')}
            aria-label="Retour à la liste des devis"
          >
            <ArrowLeft size={14} />
            Retour aux devis
          </Button>
        </div>
      </div>
    </div>
  )
}
