interface LogoProps {
  variant?: 'mark' | 'full'
  dark?: boolean
  className?: string
}

export function Logo({ variant = 'full', dark = false, className = '' }: LogoProps) {
  const textColor = dark ? '#ffffff' : '#0D0D0D'

  const mark = (
    <svg width="44" height="44" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      {/* Hexagon open on the right (C-shape) */}
      <path
        d="M56 20 L32 6 L8 20 L8 44 L32 58 L56 44"
        stroke={dark ? '#ffffff' : '#0D0D0D'}
        strokeWidth="4"
        strokeLinecap="round"
        strokeLinejoin="round"
        fill="none"
      />
      {/* Handshake — two hands meeting in the center */}
      <g stroke="#F4620A" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" fill="none">
        {/* Left arm */}
        <path d="M14 34 L22 28" />
        {/* Right arm */}
        <path d="M50 34 L42 28" />
        {/* Left hand fingers */}
        <path d="M22 28 L26 24 M22 28 L24 32" />
        {/* Right hand fingers */}
        <path d="M42 28 L38 24 M42 28 L40 32" />
        {/* Clasped hands center */}
        <path d="M26 24 C28 22 30 22 32 23 C34 22 36 22 38 24 L40 28 C40 30 38 32 36 32 L28 32 C26 32 24 30 24 28 Z" fill="#F4620A" opacity="0.15" />
        <path d="M26 24 C28 22 30 22 32 23 C34 22 36 22 38 24 L40 28 C40 30 38 32 36 32 L28 32 C26 32 24 30 24 28 Z" />
        {/* Wrist lines */}
        <path d="M24 32 L18 36 M40 32 L46 36" />
      </g>
    </svg>
  )

  if (variant === 'mark') return <div className={className}>{mark}</div>

  return (
    <div className={`flex items-center gap-3 ${className}`}>
      {mark}
      <span
        style={{
          fontFamily: "'Space Grotesk', sans-serif",
          fontWeight: 700,
          fontSize: '1.25rem',
          letterSpacing: '0.05em',
          color: textColor,
          lineHeight: 1,
        }}
      >
        NETSPRING
      </span>
    </div>
  )
}
