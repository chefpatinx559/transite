import * as React from "react"
import { cva, type VariantProps } from "class-variance-authority"
import { cn } from "@/lib/utils"

const badgeVariants = cva(
  "inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2",
  {
    variants: {
      variant: {
        default:     "border-transparent bg-[#F4620A] text-white",
        secondary:   "border-transparent bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-gray-200",
        destructive: "border-transparent bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400",
        outline:     "border-[#E5E7EB] dark:border-white/20 text-gray-700 dark:text-gray-300",
        success:     "border-transparent bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400",
        warning:     "border-transparent bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400",
        info:        "border-transparent bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400",
        purple:      "border-transparent bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400",
        gray:        "border-transparent bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300",
        green:       "border-transparent bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400",
        red:         "border-transparent bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400",
        blue:        "border-transparent bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400",
        amber:       "border-transparent bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400",
        emerald:     "border-transparent bg-emerald-200 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400",
        orange:      "border-transparent bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  }
)

export interface BadgeProps
  extends React.HTMLAttributes<HTMLDivElement>,
    VariantProps<typeof badgeVariants> {}

function Badge({ className, variant, ...props }: BadgeProps) {
  return (
    <div className={cn(badgeVariants({ variant }), className)} {...props} />
  )
}

export { Badge, badgeVariants }
export default Badge

type BadgeVariant = NonNullable<BadgeProps["variant"]>

export const orderStatusBadge: Record<string, BadgeVariant> = {
  pending:    "gray",
  paid:       "green",
  processing: "blue",
  shipped:    "purple",
  delivered:  "emerald",
  cancelled:  "red",
  refunded:   "orange",
}
export const orderStatusLabel: Record<string, string> = {
  pending:    "En attente",
  paid:       "Payé",
  processing: "En cours",
  shipped:    "Expédié",
  delivered:  "Livré",
  cancelled:  "Annulé",
  refunded:   "Remboursé",
}
export const quoteStatusBadge: Record<string, BadgeVariant> = {
  new:       "blue",
  in_review: "amber",
  quoted:    "purple",
  won:       "green",
  lost:      "red",
}
export const quoteStatusLabel: Record<string, string> = {
  new:       "Nouveau",
  in_review: "En révision",
  quoted:    "Devis envoyé",
  won:       "Accepté",
  lost:      "Perdu",
}
