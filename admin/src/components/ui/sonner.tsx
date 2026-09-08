import * as React from "react"
import { Toaster as Sonner } from "sonner"

type ToasterProps = React.ComponentProps<typeof Sonner>

const Toaster = ({ ...props }: ToasterProps) => {
  return (
    <Sonner
      theme="light"
      className="toaster group"
      position="bottom-right"
      richColors
      toastOptions={{
        classNames: {
          toast:
            "group toast group-[.toaster]:bg-white group-[.toaster]:text-[#0D0D0D] group-[.toaster]:border-[#E5E7EB] group-[.toaster]:shadow-lg group-[.toaster]:rounded-[12px]",
          description: "group-[.toast]:text-gray-500",
          actionButton: "group-[.toast]:bg-[#F4620A] group-[.toast]:text-white",
          cancelButton: "group-[.toast]:bg-gray-100 group-[.toast]:text-gray-700",
          success: "group-[.toaster]:!border-l-4 group-[.toaster]:!border-l-emerald-500",
          error:   "group-[.toaster]:!border-l-4 group-[.toaster]:!border-l-red-500",
          info:    "group-[.toaster]:!border-l-4 group-[.toaster]:!border-l-[#F4620A]",
        },
      }}
      {...props}
    />
  )
}

export { Toaster }
