import { useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { ArrowLeft, Package, Clock, Truck, CheckCircle2 } from 'lucide-react'
import { ordersApi } from '@/api/endpoints'
import { formatPrice, formatDate, formatDateTime } from '@/lib/utils'
import { Badge, orderStatusBadge, orderStatusLabel } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardHeader, CardContent } from '@/components/ui/card'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useToast } from '@/hooks/useToast'

interface OrderItem {
  id: number
  product?: { name: string; photo_url?: string | null; type?: string }
  product_name?: string
  product_type?: string
  quantity: number
  unit_price: number | string
  subtotal?: number | string
}

interface OrderAddress {
  line1?: string
  line2?: string
  city?: string
  country?: string
  postal_code?: string
  notes?: string
}

interface OrderUser {
  id?: number
  name: string
  email: string
  phone?: string
}

interface Order {
  id: number
  reference?: string
  status: string
  payment_status?: string
  transaction_reference?: string
  subtotal?: number | string
  shipping_cost?: number | string
  discount?: number | string
  total: number | string
  items: OrderItem[]
  user?: OrderUser
  shipping_address?: OrderAddress
  notes?: string
  created_at: string
  shipped_at?: string | null
  delivered_at?: string | null
}

interface ApiResponse {
  data?: Order
}

const ORDER_STATUSES = [
  { value: 'pending',    label: 'En attente' },
  { value: 'paid',       label: 'Payé' },
  { value: 'processing', label: 'En cours' },
  { value: 'shipped',    label: 'Expédié' },
  { value: 'delivered',  label: 'Livré' },
  { value: 'cancelled',  label: 'Annulé' },
  { value: 'refunded',   label: 'Remboursé' },
]

export default function OrderDetailPage() {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const queryClient = useQueryClient()
  const { success, error } = useToast()

  const [newStatus, setNewStatus] = useState('')

  const { data: response, isLoading } = useQuery({
    queryKey: ['order', id],
    queryFn: () => ordersApi.get(Number(id)).then(r => r.data as ApiResponse | Order),
    enabled: !!id,
  })

  const order: Order | undefined =
    (response as ApiResponse)?.data ?? (response as Order | undefined)

  const statusMutation = useMutation({
    mutationFn: (status: string) => ordersApi.updateStatus(Number(id), status),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['order', id] })
      queryClient.invalidateQueries({ queryKey: ['orders'] })
      success('Statut de la commande mis à jour.')
    },
    onError: () => error('Impossible de mettre à jour le statut.'),
  })

  if (isLoading) {
    return (
      <div className="flex items-center justify-center py-20 text-gray-400">
        Chargement…
      </div>
    )
  }

  if (!order) {
    return (
      <div className="text-center py-20 text-gray-400">
        <p>Commande introuvable.</p>
        <Button variant="outline" size="sm" className="mt-4" onClick={() => navigate('/commandes')}>
          Retour aux commandes
        </Button>
      </div>
    )
  }

  const effectiveStatus = newStatus || order.status

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-3">
        <Button variant="outline" size="sm" onClick={() => navigate('/commandes')} aria-label="Retour aux commandes">
          <ArrowLeft size={15} />
          Retour
        </Button>
        <div>
          <h1 className="font-sans font-bold text-2xl text-[#0D0D0D]">
            Commande #{order.reference ?? order.id}
          </h1>
          <p className="text-sm text-gray-500 mt-0.5">{formatDateTime(order.created_at)}</p>
        </div>
        <Badge variant={orderStatusBadge[order.status] ?? 'gray'} className="ml-auto">
          {orderStatusLabel[order.status] ?? order.status}
        </Badge>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Main column */}
        <div className="lg:col-span-2 space-y-6">

          {/* Articles */}
          <Card>
            <CardHeader title="Articles commandés" />
            <div className="divide-y divide-[#E5E7EB]">
              {order.items.map(item => {
                const name = item.product?.name ?? item.product_name ?? '—'
                const itemType = item.product?.type ?? item.product_type
                const subtotal = item.subtotal ?? (Number(item.unit_price) * item.quantity)
                return (
                  <div key={item.id} className="flex items-center gap-4 px-6 py-4">
                    {item.product?.photo_url ? (
                      <img
                        src={item.product.photo_url}
                        alt={name}
                        className="w-12 h-12 rounded-[8px] object-cover border border-[#E5E7EB] flex-shrink-0"
                      />
                    ) : (
                      <div className="w-12 h-12 rounded-[8px] bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <Package size={18} className="text-gray-400" />
                      </div>
                    )}
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center gap-2 flex-wrap">
                        <p className="font-semibold text-[#0D0D0D] truncate">{name}</p>
                        {itemType && (
                          <Badge variant="blue" className="text-[10px]">{itemType}</Badge>
                        )}
                      </div>
                      <p className="text-sm text-gray-500">
                        {formatPrice(item.unit_price)} × {item.quantity}
                      </p>
                    </div>
                    <p className="font-bold text-[#0D0D0D] flex-shrink-0">
                      {formatPrice(subtotal)}
                    </p>
                  </div>
                )
              })}
            </div>
            <div className="border-t border-[#E5E7EB] px-6 py-4 space-y-2">
              {order.subtotal != null && (
                <div className="flex justify-between text-sm text-gray-600">
                  <span>Sous-total</span>
                  <span>{formatPrice(order.subtotal)}</span>
                </div>
              )}
              {order.shipping_cost != null && (
                <div className="flex justify-between text-sm text-gray-600">
                  <span>Frais de livraison</span>
                  <span>{formatPrice(order.shipping_cost)}</span>
                </div>
              )}
              {order.discount != null && Number(order.discount) > 0 && (
                <div className="flex justify-between text-sm text-gray-600">
                  <span>Remise</span>
                  <span className="text-emerald-600">-{formatPrice(order.discount)}</span>
                </div>
              )}
              <div className="flex justify-between font-bold text-[#0D0D0D] text-base pt-2 border-t border-[#E5E7EB]">
                <span>Total</span>
                <span className="text-[#F4620A]">{formatPrice(order.total)}</span>
              </div>
            </div>
          </Card>

          {/* Client info */}
          <Card>
            <CardHeader title="Informations client" />
            <CardContent className="space-y-3 text-sm">
              <p className="font-semibold text-[#0D0D0D] text-base">{order.user?.name ?? '—'}</p>
              {order.user?.email && (
                <a
                  href={`mailto:${order.user.email}`}
                  className="block text-[#F4620A] hover:underline"
                >
                  {order.user.email}
                </a>
              )}
              {order.user?.phone && (
                <a
                  href={`tel:${order.user.phone}`}
                  className="block text-gray-600 hover:underline"
                >
                  {order.user.phone}
                </a>
              )}
              {order.user?.id && (
                <p className="text-xs text-gray-400">Compte client #{order.user.id}</p>
              )}
            </CardContent>
          </Card>

          {/* Shipping address */}
          {order.shipping_address && (
            <Card>
              <CardHeader title="Adresse de livraison" />
              <CardContent className="text-sm text-gray-600 space-y-1">
                {order.shipping_address.line1 && <p>{order.shipping_address.line1}</p>}
                {order.shipping_address.line2 && <p>{order.shipping_address.line2}</p>}
                {(order.shipping_address.postal_code || order.shipping_address.city) && (
                  <p>
                    {[order.shipping_address.postal_code, order.shipping_address.city].filter(Boolean).join(' ')}
                  </p>
                )}
                {order.shipping_address.country && <p>{order.shipping_address.country}</p>}
                {order.shipping_address.notes && (
                  <p className="mt-2 pt-2 border-t border-[#E5E7EB] text-gray-500 italic">
                    {order.shipping_address.notes}
                  </p>
                )}
              </CardContent>
            </Card>
          )}
        </div>

        {/* Sidebar */}
        <div className="space-y-6">

          {/* Status card */}
          <Card>
            <CardHeader title="Statut commande" />
            <CardContent className="space-y-4">
              <div className="flex items-center gap-2">
                <span className="text-sm text-gray-500">Statut actuel :</span>
                <Badge variant={orderStatusBadge[order.status] ?? 'gray'}>
                  {orderStatusLabel[order.status] ?? order.status}
                </Badge>
              </div>
              <div className="space-y-1.5">
                <label className="block text-sm font-medium text-gray-700">
                  Changer le statut
                </label>
                <Select value={effectiveStatus} onValueChange={val => setNewStatus(val)}>
                  <SelectTrigger aria-label="Sélectionner le statut de la commande">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {ORDER_STATUSES.map(s => (
                      <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <Button
                className="w-full"
                loading={statusMutation.isPending}
                disabled={effectiveStatus === order.status}
                onClick={() => statusMutation.mutate(effectiveStatus)}
                aria-label="Mettre à jour le statut"
              >
                Mettre à jour
              </Button>

              {order.payment_status && (
                <div className="pt-3 border-t border-[#E5E7EB] space-y-2">
                  <div className="flex items-center gap-2">
                    <span className="text-sm text-gray-500">Paiement :</span>
                    <Badge variant={order.payment_status === 'paid' ? 'green' : 'gray'}>
                      {order.payment_status === 'paid' ? 'Payé' : order.payment_status}
                    </Badge>
                  </div>
                  {order.transaction_reference && (
                    <p className="text-xs text-gray-400 font-mono break-all">
                      Réf. : {order.transaction_reference}
                    </p>
                  )}
                </div>
              )}
            </CardContent>
          </Card>

          {/* Important dates */}
          <Card>
            <CardHeader title="Dates importantes" />
            <CardContent className="text-sm space-y-3">
              <div className="flex items-center gap-2">
                <Clock size={14} className="text-gray-400 flex-shrink-0" />
                <div className="flex-1 flex justify-between">
                  <span className="text-gray-500">Créée le</span>
                  <span className="font-medium">{formatDate(order.created_at)}</span>
                </div>
              </div>
              <div className="flex items-center gap-2">
                <Truck size={14} className="text-gray-400 flex-shrink-0" />
                <div className="flex-1 flex justify-between">
                  <span className="text-gray-500">Expédiée le</span>
                  <span className="font-medium">{formatDate(order.shipped_at)}</span>
                </div>
              </div>
              <div className="flex items-center gap-2">
                <CheckCircle2 size={14} className="text-gray-400 flex-shrink-0" />
                <div className="flex-1 flex justify-between">
                  <span className="text-gray-500">Livrée le</span>
                  <span className="font-medium">{formatDate(order.delivered_at)}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Button
            variant="outline"
            className="w-full"
            onClick={() => navigate('/commandes')}
            aria-label="Retour à la liste des commandes"
          >
            <ArrowLeft size={14} />
            Retour aux commandes
          </Button>
        </div>
      </div>
    </div>
  )
}
