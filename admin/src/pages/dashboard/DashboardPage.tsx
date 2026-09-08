import { useQuery } from '@tanstack/react-query'
import { dashboardApi } from '@/api/endpoints'
import { useAuth } from '@/context/AuthContext'
import { useTheme } from '@/context/ThemeContext'
import { StatCard } from '@/components/ui/card'
import { Badge, orderStatusBadge, orderStatusLabel } from '@/components/ui/badge'
import { formatPrice, formatDate } from '@/lib/utils'
import {
  Users, ShoppingCart, TrendingUp, MessageSquare,
  Package, FileText,
} from 'lucide-react'
import {
  AreaChart, Area, XAxis, YAxis, CartesianGrid,
  Tooltip, ResponsiveContainer,
} from 'recharts'

export default function DashboardPage() {
  const { user } = useAuth()
  const { theme } = useTheme()
  const isDark = theme === 'dark'

  const { data, isLoading } = useQuery({
    queryKey: ['dashboard'],
    queryFn: () => dashboardApi.stats().then(r => r.data),
  })

  const statCards = [
    { label: 'Utilisateurs',       value: data?.stats?.users_count    ?? '—', icon: <Users className="w-5 h-5" />,         iconBg: 'bg-blue-50 dark:bg-blue-900/20 text-blue-600' },
    { label: 'Commandes auj.',     value: data?.stats?.orders_today   ?? '—', icon: <ShoppingCart className="w-5 h-5" />,   iconBg: 'bg-orange-50 dark:bg-orange-900/20 text-[#F4620A]' },
    { label: 'Revenus du mois',    value: data?.stats?.revenue_month != null ? formatPrice(data.stats.revenue_month) : '—', icon: <TrendingUp className="w-5 h-5" />, iconBg: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' },
    { label: 'Devis nouveaux',     value: data?.stats?.quotes_new     ?? '—', icon: <MessageSquare className="w-5 h-5" />, iconBg: 'bg-purple-50 dark:bg-purple-900/20 text-purple-600' },
    { label: 'Produits publiés',   value: data?.stats?.products_count ?? '—', icon: <Package className="w-5 h-5" />,        iconBg: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600' },
    { label: 'Articles publiés',   value: data?.stats?.posts_count    ?? '—', icon: <FileText className="w-5 h-5" />,       iconBg: 'bg-pink-50 dark:bg-pink-900/20 text-pink-600' },
  ]

  const gridStroke   = isDark ? 'rgba(255,255,255,0.06)' : '#F3F4F6'
  const tickFill     = isDark ? 'rgba(255,255,255,0.35)' : '#9CA3AF'
  const tooltipStyle = isDark
    ? { borderRadius: 10, border: '1px solid rgba(255,255,255,0.1)', fontSize: 12, background: '#1e1e1e', color: '#fff' }
    : { borderRadius: 10, border: '1px solid #E5E7EB', fontSize: 12 }

  const panel = isDark
    ? 'bg-[#1e1e1e] rounded-[16px] border border-white/10'
    : 'bg-white rounded-[16px] border border-[#E5E7EB]'

  const divider = isDark ? 'divide-white/8' : 'divide-[#E5E7EB]'
  const rowHover = isDark ? 'hover:bg-white/4' : 'hover:bg-[#FAFAFA]'
  const theadBg  = isDark ? 'bg-[#171717] border-b border-white/10' : 'bg-[#FAFAFA] border-b border-[#E5E7EB]'

  if (isLoading) return (
    <div className="flex items-center justify-center h-64">
      <div className="w-8 h-8 border-2 border-[#F4620A] border-t-transparent rounded-full animate-spin" aria-label="Chargement" />
    </div>
  )

  return (
    <div className="space-y-8">
      {/* En-tête */}
      <div>
        <h1 className="font-sans font-bold text-[#0D0D0D] dark:text-white text-2xl">
          Bonjour, {user?.first_name} 👋
        </h1>
        <p className="text-gray-500 dark:text-gray-400 text-sm mt-1">Voici un aperçu de l'activité NETSPRING</p>
      </div>

      {/* Stat cards */}
      <section aria-labelledby="stats-title">
        <h2 id="stats-title" className="sr-only">Statistiques globales</h2>
        <div className="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
          {statCards.map(card => (
            <StatCard key={card.label} {...card} />
          ))}
        </div>
      </section>

      {/* Graphique + tableaux */}
      <div className="grid lg:grid-cols-3 gap-6">

        {/* Graphique revenus 7 jours */}
        <section className={`lg:col-span-2 ${panel} p-6`} aria-labelledby="chart-title">
          <h2 id="chart-title" className="font-sans font-semibold text-[#0D0D0D] dark:text-white text-sm mb-6">
            Revenus des 7 derniers jours (FCFA)
          </h2>
          {data?.orders_chart?.length ? (
            <ResponsiveContainer width="100%" height={220}>
              <AreaChart data={data.orders_chart} margin={{ top: 0, right: 0, left: 0, bottom: 0 }}>
                <defs>
                  <linearGradient id="colorRevenue" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%"  stopColor="#F4620A" stopOpacity={0.15} />
                    <stop offset="95%" stopColor="#F4620A" stopOpacity={0} />
                  </linearGradient>
                </defs>
                <CartesianGrid strokeDasharray="3 3" stroke={gridStroke} vertical={false} />
                <XAxis dataKey="date" tick={{ fontSize: 11, fill: tickFill }} axisLine={false} tickLine={false} />
                <YAxis tick={{ fontSize: 11, fill: tickFill }} axisLine={false} tickLine={false}
                  tickFormatter={v => v >= 1000 ? `${(v/1000).toFixed(0)}k` : v} />
                <Tooltip
                  formatter={(value: unknown) => [formatPrice(value as number), 'Revenus']}
                  contentStyle={tooltipStyle}
                />
                <Area type="monotone" dataKey="total" stroke="#F4620A" strokeWidth={2}
                  fill="url(#colorRevenue)" dot={false} activeDot={{ r: 4, fill: '#F4620A' }} />
              </AreaChart>
            </ResponsiveContainer>
          ) : (
            <div className="flex items-center justify-center h-[220px] text-gray-300 dark:text-gray-600 text-sm">
              Aucune donnée de commande
            </div>
          )}
        </section>

        {/* Derniers devis */}
        <section className={`${panel} overflow-hidden`} aria-labelledby="quotes-title">
          <div className={`px-5 py-4 border-b ${isDark ? 'border-white/10' : 'border-[#E5E7EB]'}`}>
            <h2 id="quotes-title" className="font-sans font-semibold text-[#0D0D0D] dark:text-white text-sm">Derniers devis</h2>
          </div>
          <ul className={`divide-y ${divider}`}>
            {data?.recent_quotes?.length ? data.recent_quotes.map((q: Record<string, unknown>) => (
              <li key={q.id as number} className="px-5 py-3.5">
                <div className="flex items-start justify-between gap-2">
                  <div className="min-w-0">
                    <p className="font-medium text-[#0D0D0D] dark:text-white text-xs truncate">{q.name as string}</p>
                    <p className="text-gray-400 dark:text-gray-500 text-[11px] mt-0.5 truncate">{q.product_description as string}</p>
                  </div>
                  <Badge variant={(q.status_badge as 'blue') ?? 'gray'} className="shrink-0">
                    {q.status as string}
                  </Badge>
                </div>
              </li>
            )) : (
              <li className="px-5 py-8 text-center text-gray-400 dark:text-gray-600 text-sm">Aucun devis</li>
            )}
          </ul>
        </section>
      </div>

      {/* Dernières commandes */}
      <section className={`${panel} overflow-hidden`} aria-labelledby="orders-title">
        <div className={`px-6 py-4 border-b ${isDark ? 'border-white/10' : 'border-[#E5E7EB]'}`}>
          <h2 id="orders-title" className="font-sans font-semibold text-[#0D0D0D] dark:text-white text-sm">Dernières commandes</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm" aria-label="Dernières commandes">
            <thead className={theadBg}>
              <tr>
                {['N° Commande','Client','Montant','Statut','Date'].map(h => (
                  <th key={h} className="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">{h}</th>
                ))}
              </tr>
            </thead>
            <tbody className={`divide-y ${divider}`}>
              {data?.recent_orders?.length ? data.recent_orders.map((o: Record<string, unknown>) => (
                <tr key={o.id as number} className={`${rowHover} transition-colors`}>
                  <td className="px-5 py-3.5 font-medium text-[#F4620A] text-xs">{o.order_number as string}</td>
                  <td className="px-5 py-3.5 text-gray-700 dark:text-gray-300">{o.customer_name as string}</td>
                  <td className="px-5 py-3.5 font-medium text-[#0D0D0D] dark:text-white">{formatPrice(o.total as number)}</td>
                  <td className="px-5 py-3.5">
                    <Badge variant={orderStatusBadge[o.status as string] ?? 'gray'}>
                      {orderStatusLabel[o.status as string] ?? o.status as string}
                    </Badge>
                  </td>
                  <td className="px-5 py-3.5 text-gray-400 dark:text-gray-500 text-xs">{formatDate(o.created_at as string)}</td>
                </tr>
              )) : (
                <tr><td colSpan={5} className="px-5 py-10 text-center text-gray-400 dark:text-gray-600 text-sm">Aucune commande</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  )
}
