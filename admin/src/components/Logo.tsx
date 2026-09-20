import imgLogo from '@/assets/netspring.png'

interface LogoProps {
  variant?: 'mark' | 'full'
  dark?: boolean
  className?: string
  size?: number
}

export function Logo({ dark = false, className = '', size = 48 }: LogoProps) {
  const img = (
    <img
      src={imgLogo}
      alt="NETSPRING"
      style={{ height: size, width: 'auto', display: 'block' }}
    />
  )

  if (dark) {
    return (
      <div
        className={className}
        style={{ background: 'white', borderRadius: 10, padding: '6px 10px', display: 'inline-flex', alignItems: 'center' }}
      >
        {img}
      </div>
    )
  }

  return <div className={`inline-flex items-center ${className}`}>{img}</div>
}
