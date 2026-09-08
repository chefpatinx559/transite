import { createContext, useCallback, useContext, useRef, useState } from 'react'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { AlertTriangle } from 'lucide-react'

// ── Types ────────────────────────────────────────────────────
interface ConfirmOptions {
  title?: string
  description: string
  confirmLabel?: string
  cancelLabel?: string
  variant?: 'danger' | 'warning' | 'default'
}

interface ConfirmContextType {
  confirm: (options: ConfirmOptions) => Promise<boolean>
}

// ── Context ──────────────────────────────────────────────────
const ConfirmContext = createContext<ConfirmContextType | null>(null)

// ── Provider ─────────────────────────────────────────────────
export function ConfirmProvider({ children }: { children: React.ReactNode }) {
  const [open, setOpen] = useState(false)
  const [options, setOptions] = useState<ConfirmOptions>({
    description: '',
  })
  const resolveRef = useRef<((value: boolean) => void) | null>(null)

  const confirm = useCallback((opts: ConfirmOptions): Promise<boolean> => {
    setOptions(opts)
    setOpen(true)
    return new Promise<boolean>(resolve => {
      resolveRef.current = resolve
    })
  }, [])

  function handleConfirm() {
    setOpen(false)
    resolveRef.current?.(true)
  }

  function handleCancel() {
    setOpen(false)
    resolveRef.current?.(false)
  }

  const iconColor = {
    danger:  'text-red-500',
    warning: 'text-amber-500',
    default: 'text-[#F4620A]',
  }[options.variant ?? 'danger']

  const confirmVariant = options.variant === 'danger'
    ? 'destructive'
    : options.variant === 'warning'
      ? 'default'
      : 'default'

  return (
    <ConfirmContext.Provider value={{ confirm }}>
      {children}

      <Dialog open={open} onOpenChange={v => { if (!v) handleCancel() }}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <div className={`w-12 h-12 rounded-[14px] flex items-center justify-center mb-2 ${
              options.variant === 'danger' ? 'bg-red-50' :
              options.variant === 'warning' ? 'bg-amber-50' : 'bg-orange-50'
            }`}>
              <AlertTriangle className={`w-6 h-6 ${iconColor}`} aria-hidden="true" />
            </div>
            <DialogTitle className="text-[#0D0D0D]">
              {options.title ?? 'Confirmer l\'action'}
            </DialogTitle>
            <DialogDescription className="text-gray-600 leading-relaxed">
              {options.description}
            </DialogDescription>
          </DialogHeader>

          <DialogFooter className="gap-2 sm:gap-2">
            <Button
              variant="outline"
              onClick={handleCancel}
              className="flex-1 sm:flex-none"
            >
              {options.cancelLabel ?? 'Annuler'}
            </Button>
            <Button
              variant={confirmVariant as 'destructive' | 'default'}
              onClick={handleConfirm}
              className="flex-1 sm:flex-none"
            >
              {options.confirmLabel ?? 'Confirmer'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </ConfirmContext.Provider>
  )
}

// ── Hook ─────────────────────────────────────────────────────
export function useConfirm() {
  const ctx = useContext(ConfirmContext)
  if (!ctx) throw new Error('useConfirm must be used inside ConfirmProvider')
  return ctx.confirm
}
