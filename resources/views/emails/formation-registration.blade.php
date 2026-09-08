<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription</title>
</head>
<body style="margin:0;padding:0;background:#F4F4F4;font-family:'Century Gothic','Trebuchet MS',sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F4F4;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                {{-- Header --}}
                <tr>
                    <td style="background:#0D0D0D;border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
                        <div style="display:inline-block;">
                            <span style="font-size:28px;font-weight:900;color:#ffffff;letter-spacing:2px;">NET<span style="color:#F4620A;">SPRING</span></span>
                        </div>
                        <p style="color:#9CA3AF;font-size:13px;margin:8px 0 0 0;">Chine – Côte d'Ivoire</p>
                    </td>
                </tr>

                {{-- Hero --}}
                <tr>
                    <td style="background:#F4620A;padding:32px 40px;text-align:center;">
                        <div style="font-size:48px;margin-bottom:12px;">🎓</div>
                        <h1 style="color:#ffffff;font-size:22px;font-weight:700;margin:0 0 8px 0;font-family:'Century Gothic','Trebuchet MS',sans-serif;">
                            @if($formation->is_free || $registration->payment_status === 'paid')
                                Inscription confirmée !
                            @else
                                Inscription enregistrée !
                            @endif
                        </h1>
                        <p style="color:rgba(255,255,255,0.85);font-size:15px;margin:0;">
                            @if($registration->payment_status === 'paid')
                                Votre paiement a été accepté. À bientôt !
                            @elseif($formation->is_free)
                                Votre inscription gratuite est confirmée. À bientôt !
                            @else
                                Votre inscription a bien été enregistrée.
                            @endif
                        </p>
                    </td>
                </tr>

                {{-- Contenu --}}
                <tr>
                    <td style="background:#ffffff;padding:40px;">

                        <p style="color:#374151;font-size:15px;margin:0 0 24px 0;">
                            Bonjour <strong style="color:#0D0D0D;">{{ $registration->name }}</strong>,
                        </p>

                        {{-- Détails formation --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#F9FAFB;border-radius:12px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:24px;">
                                    <h2 style="color:#0D0D0D;font-size:17px;font-weight:700;margin:0 0 16px 0;font-family:'Century Gothic','Trebuchet MS',sans-serif;">
                                        {{ $formation->title }}
                                    </h2>

                                    @if($formation->date_start)
                                    <p style="color:#6B7280;font-size:14px;margin:0 0 8px 0;">
                                        📅 <strong>Date :</strong>
                                        {{ \Carbon\Carbon::parse($formation->date_start)->translatedFormat('d F Y à H\hi') }}
                                    </p>
                                    @endif

                                    @if($formation->duration)
                                    <p style="color:#6B7280;font-size:14px;margin:0 0 8px 0;">
                                        ⏱️ <strong>Durée :</strong> {{ $formation->duration }}
                                    </p>
                                    @endif

                                    @if($formation->type === 'presentielle' && $formation->location)
                                    <p style="color:#6B7280;font-size:14px;margin:0 0 8px 0;">
                                        📍 <strong>Lieu :</strong> {{ $formation->location }}
                                    </p>
                                    @else
                                    <p style="color:#6B7280;font-size:14px;margin:0 0 8px 0;">
                                        🌐 <strong>Format :</strong> Formation en ligne
                                    </p>
                                    @endif

                                    <p style="color:#6B7280;font-size:14px;margin:0;">
                                        💰 <strong>Tarif :</strong>
                                        @if($formation->is_free)
                                            <span style="color:#059669;font-weight:700;">Gratuite</span>
                                        @else
                                            <span style="color:#F4620A;font-weight:700;">{{ number_format($formation->price, 0, ',', ' ') }} FCFA</span>
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- Récap participant --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E5E7EB;border-radius:12px;margin-bottom:32px;">
                            <tr>
                                <td style="padding:20px 24px;border-bottom:1px solid #E5E7EB;">
                                    <p style="color:#6B7280;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 4px 0;">Participant</p>
                                    <p style="color:#0D0D0D;font-size:15px;font-weight:600;margin:0;">{{ $registration->name }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:20px 24px;border-bottom:1px solid #E5E7EB;">
                                    <p style="color:#6B7280;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 4px 0;">Email</p>
                                    <p style="color:#0D0D0D;font-size:15px;margin:0;">{{ $registration->email }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="color:#6B7280;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 4px 0;">Téléphone</p>
                                    <p style="color:#0D0D0D;font-size:15px;margin:0;">{{ $registration->phone ?? '—' }}</p>
                                </td>
                            </tr>
                        </table>

                        {{-- CTA WhatsApp --}}
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="padding-bottom:24px;">
                                    <a href="https://wa.me/2250594429552?text=Bonjour+NETSPRING,+j%27ai+une+question+sur+la+formation+{{ urlencode($formation->title) }}."
                                       style="display:inline-block;background:#25D366;color:#ffffff;font-size:14px;font-weight:700;padding:14px 32px;border-radius:10px;text-decoration:none;">
                                        💬 Nous contacter sur WhatsApp
                                    </a>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#0D0D0D;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
                        <p style="color:#6B7280;font-size:12px;margin:0 0 6px 0;">
                            © {{ date('Y') }} NETSPRING — Chine – Côte d'Ivoire
                        </p>
                        <p style="color:#4B5563;font-size:11px;margin:0;">
                            netspringbusiness@gmail.com · +225 05 94 42 95 52
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
