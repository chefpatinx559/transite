import {
  type ColumnDef,
  flexRender,
  getCoreRowModel,
  useReactTable,
} from '@tanstack/react-table'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { ChevronLeft, ChevronRight } from 'lucide-react'
import { cn } from '@/lib/utils'

interface DataTableProps<TData, TValue> {
  columns: ColumnDef<TData, TValue>[]
  data: TData[]
  isLoading?: boolean
  page?: number
  pageCount?: number
  total?: number
  onPageChange?: (page: number) => void
  onRowClick?: (row: TData) => void
  emptyMessage?: string
  emptyIcon?: React.ReactNode
}

export function DataTable<TData, TValue>({
  columns,
  data,
  isLoading = false,
  page = 1,
  pageCount = 1,
  total,
  onPageChange,
  onRowClick,
  emptyMessage = 'Aucun résultat.',
  emptyIcon,
}: DataTableProps<TData, TValue>) {
  const table = useReactTable({
    data,
    columns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    pageCount,
  })

  return (
    <div className="rounded-[16px] border border-[#E5E7EB] dark:border-white/10 bg-white dark:bg-[#1e1e1e] shadow-[0_2px_8px_rgba(0,0,0,0.04)] dark:shadow-none overflow-hidden transition-colors">
      <Table>
        <TableHeader>
          {table.getHeaderGroups().map(hg => (
            <TableRow key={hg.id} className="bg-[#F8F8F8] dark:bg-[#171717] hover:bg-[#F8F8F8] dark:hover:bg-[#171717]">
              {hg.headers.map(header => (
                <TableHead
                  key={header.id}
                  style={{ width: header.getSize() !== 150 ? header.getSize() : undefined }}
                >
                  {header.isPlaceholder
                    ? null
                    : flexRender(header.column.columnDef.header, header.getContext())}
                </TableHead>
              ))}
            </TableRow>
          ))}
        </TableHeader>

        <TableBody>
          {/* Loading state */}
          {isLoading && Array.from({ length: 8 }).map((_, i) => (
            <TableRow key={`skel-${i}`} className="hover:bg-transparent dark:hover:bg-transparent">
              {columns.map((_, j) => (
                <TableCell key={j}>
                  <Skeleton className="h-4 w-full rounded dark:bg-white/10" />
                </TableCell>
              ))}
            </TableRow>
          ))}

          {/* Empty state */}
          {!isLoading && table.getRowModel().rows.length === 0 && (
            <TableRow className="hover:bg-transparent dark:hover:bg-transparent">
              <TableCell colSpan={columns.length} className="py-16 text-center">
                <div className="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-600">
                  {emptyIcon && <div className="opacity-30">{emptyIcon}</div>}
                  <p className="text-sm font-medium">{emptyMessage}</p>
                </div>
              </TableCell>
            </TableRow>
          )}

          {/* Data rows */}
          {!isLoading && table.getRowModel().rows.map(row => (
            <TableRow
              key={row.id}
              data-state={row.getIsSelected() ? 'selected' : undefined}
              onClick={() => onRowClick?.(row.original)}
              className={cn(onRowClick && 'cursor-pointer')}
            >
              {row.getVisibleCells().map(cell => (
                <TableCell key={cell.id}>
                  {flexRender(cell.column.columnDef.cell, cell.getContext())}
                </TableCell>
              ))}
            </TableRow>
          ))}
        </TableBody>
      </Table>

      {/* Pagination */}
      {pageCount > 1 && onPageChange && (
        <div className="flex items-center justify-between px-5 py-4 border-t border-[#E5E7EB] dark:border-white/10">
          <p className="text-sm text-gray-500 dark:text-gray-400">
            {total != null
              ? <><span className="font-semibold text-[#0D0D0D] dark:text-white">{total.toLocaleString('fr-FR')}</span> résultat{Number(total) > 1 ? 's' : ''} — </>
              : null
            }
            Page <span className="font-semibold text-[#0D0D0D] dark:text-white">{page}</span> sur <span className="font-semibold text-[#0D0D0D] dark:text-white">{pageCount}</span>
          </p>
          <div className="flex items-center gap-2">
            <Button
              variant="outline"
              size="sm"
              onClick={() => onPageChange(page - 1)}
              disabled={page <= 1}
              aria-label="Page précédente"
            >
              <ChevronLeft className="w-4 h-4" aria-hidden="true" />
              Précédent
            </Button>
            <Button
              variant="outline"
              size="sm"
              onClick={() => onPageChange(page + 1)}
              disabled={page >= pageCount}
              aria-label="Page suivante"
            >
              Suivant
              <ChevronRight className="w-4 h-4" aria-hidden="true" />
            </Button>
          </div>
        </div>
      )}
    </div>
  )
}
