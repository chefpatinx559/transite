import * as React from "react"
import { Slot } from "@radix-ui/react-slot"
import { cva, type VariantProps } from "class-variance-authority"
import { cn } from "@/lib/utils"

const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-[10px] text-sm font-semibold ring-offset-background transition-all duration-[220ms] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 cursor-pointer [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0",
  {
    variants: {
      variant: {
        default:     "bg-[#F4620A] text-white hover:bg-[#d45208] shadow-[0_2px_8px_rgba(244,98,10,0.3)] hover:shadow-[0_4px_16px_rgba(244,98,10,0.4)]",
        primary:     "bg-[#F4620A] text-white hover:bg-[#d45208] shadow-[0_2px_8px_rgba(244,98,10,0.3)] hover:shadow-[0_4px_16px_rgba(244,98,10,0.4)]",
        destructive: "bg-red-500 text-white hover:bg-red-600",
        danger:      "bg-red-500 text-white hover:bg-red-600",
        outline:     "border border-[#E5E7EB] dark:border-white/15 bg-white dark:bg-white/5 hover:bg-gray-50 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 hover:text-[#F4620A] dark:hover:text-[#F4620A] hover:border-[#F4620A]/40 dark:hover:border-[#F4620A]/40",
        secondary:   "bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-white/15",
        ghost:       "text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/8 hover:text-gray-900 dark:hover:text-white",
        link:        "text-[#F4620A] underline-offset-4 hover:underline",
      },
      size: {
        default: "h-10 px-5 py-2.5",
        sm:      "h-8 rounded-[8px] px-3 text-xs",
        lg:      "h-12 rounded-[12px] px-8 text-base",
        icon:    "h-9 w-9 rounded-[10px]",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  }
)

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {
  asChild?: boolean
  loading?: boolean
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant, size, asChild = false, loading, disabled, children, ...props }, ref) => {
    const Comp = asChild ? Slot : "button"
    return (
      <Comp
        className={cn(buttonVariants({ variant, size, className }))}
        ref={ref}
        disabled={asChild ? undefined : (disabled || loading)}
        {...props}
      >
        {!asChild && loading && (
          <span className="w-3.5 h-3.5 border-2 border-current border-t-transparent rounded-full animate-spin" aria-hidden="true" />
        )}
        {children}
      </Comp>
    )
  }
)
Button.displayName = "Button"

export { Button, buttonVariants }
