import * as React from "react"
import { cn } from "@/lib/utils"

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string
  error?: string
}

const Input = React.forwardRef<HTMLInputElement, InputProps>(
  ({ className, type, label, error, id, ...props }, ref) => {
    const inputEl = (
      <input
        type={type}
        id={id}
        className={cn(
          "flex h-10 w-full rounded-[10px] border border-[#E5E7EB] dark:border-white/10",
          "bg-white dark:bg-[#1e1e1e] px-4 py-2.5 text-sm text-[#0D0D0D] dark:text-white",
          "file:border-0 file:bg-transparent file:text-sm file:font-medium",
          "placeholder:text-gray-400 dark:placeholder:text-gray-500",
          "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F4620A] focus-visible:border-transparent",
          "disabled:cursor-not-allowed disabled:opacity-50",
          "transition-all duration-150",
          error && "border-red-400",
          className
        )}
        ref={ref}
        {...props}
      />
    )

    if (label || error) {
      return (
        <div className="space-y-1.5">
          {label && (
            <label htmlFor={id} className="block text-sm font-medium text-gray-700 dark:text-gray-300">
              {label}
            </label>
          )}
          {inputEl}
          {error && <p className="text-xs text-red-600" role="alert">{error}</p>}
        </div>
      )
    }

    return inputEl
  }
)
Input.displayName = "Input"

export { Input }
