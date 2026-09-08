import * as React from "react"
import { cn } from "@/lib/utils"

export interface TextareaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {}

const Textarea = React.forwardRef<HTMLTextAreaElement, TextareaProps>(
  ({ className, ...props }, ref) => {
    return (
      <textarea
        className={cn(
          "flex min-h-[80px] w-full rounded-[10px] border border-[#E5E7EB] dark:border-white/10",
          "bg-white dark:bg-[#1e1e1e] px-4 py-3 text-sm text-[#0D0D0D] dark:text-white",
          "placeholder:text-gray-400 dark:placeholder:text-gray-500",
          "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F4620A] focus-visible:border-transparent",
          "disabled:cursor-not-allowed disabled:opacity-50 resize-y transition-all duration-[150ms]",
          className
        )}
        ref={ref}
        {...props}
      />
    )
  }
)
Textarea.displayName = "Textarea"

export { Textarea }
