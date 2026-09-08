import * as React from "react"
import { cn } from "@/lib/utils"

const Card = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div
      ref={ref}
      className={cn(
        "rounded-[16px] border border-[#E5E7EB] dark:border-white/10 bg-white dark:bg-[#1e1e1e] shadow-[0_2px_8px_rgba(0,0,0,0.04)] dark:shadow-none transition-colors",
        className
      )}
      {...props}
    />
  )
)
Card.displayName = "Card"

interface CardHeaderProps extends React.HTMLAttributes<HTMLDivElement> {
  title?: string
  action?: React.ReactNode
}

const CardHeader = React.forwardRef<HTMLDivElement, CardHeaderProps>(
  ({ className, title, action, children, ...props }, ref) => {
    if (title != null) {
      return (
        <div
          ref={ref}
          className={cn(
            "flex items-center justify-between px-6 py-4 border-b border-[#E5E7EB] dark:border-white/10",
            className
          )}
          {...props}
        >
          <h2 className="font-sans font-semibold text-[#0D0D0D] dark:text-white text-sm">{title}</h2>
          {action}
        </div>
      )
    }
    return (
      <div
        ref={ref}
        className={cn("flex flex-col space-y-1.5 p-6 pb-4", className)}
        {...props}
      >
        {children}
      </div>
    )
  }
)
CardHeader.displayName = "CardHeader"

const CardTitle = React.forwardRef<
  HTMLHeadingElement,
  React.HTMLAttributes<HTMLHeadingElement>
>(({ className, ...props }, ref) => (
  <h3
    ref={ref}
    className={cn(
      "font-sans font-semibold text-[#0D0D0D] dark:text-white text-base leading-none tracking-tight",
      className
    )}
    {...props}
  />
))
CardTitle.displayName = "CardTitle"

const CardDescription = React.forwardRef<
  HTMLParagraphElement,
  React.HTMLAttributes<HTMLParagraphElement>
>(({ className, ...props }, ref) => (
  <p ref={ref} className={cn("text-sm text-gray-500 dark:text-gray-400", className)} {...props} />
))
CardDescription.displayName = "CardDescription"

const CardContent = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div ref={ref} className={cn("p-6 pt-0", className)} {...props} />
  )
)
CardContent.displayName = "CardContent"

const CardFooter = React.forwardRef<HTMLDivElement, React.HTMLAttributes<HTMLDivElement>>(
  ({ className, ...props }, ref) => (
    <div ref={ref} className={cn("flex items-center p-6 pt-0", className)} {...props} />
  )
)
CardFooter.displayName = "CardFooter"

interface StatCardProps {
  label: string
  value: string | number
  icon: React.ReactNode
  iconBg?: string
}

function StatCard({ label, value, icon, iconBg = "bg-orange-50 text-[#F4620A]" }: StatCardProps) {
  return (
    <Card>
      <div className="flex items-center gap-4 p-6">
        <div className={cn("w-11 h-11 rounded-[12px] flex items-center justify-center shrink-0", iconBg)}>
          {icon}
        </div>
        <div className="min-w-0">
          <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider truncate">{label}</p>
          <p className="font-sans font-bold text-xl text-[#0D0D0D] dark:text-white mt-0.5 truncate">{value}</p>
        </div>
      </div>
    </Card>
  )
}

export {
  Card,
  CardHeader,
  CardFooter,
  CardTitle,
  CardDescription,
  CardContent,
  StatCard,
}
