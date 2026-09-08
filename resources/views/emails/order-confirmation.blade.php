<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande confirmée</title>
</head>
<body style="margin:0;padding:0;background:#F4F4F4;font-family:'Century Gothic','Trebuchet MS',sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F4F4;padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                {{-- Header --}}
                <tr>
                    <td style="background:#0D0D0D;border-radius:16px 16px 0 0;padding:32px 40px;text-align:center;">
                        <span style="font-size:28px;font-weight:900;color:#ffffff;letter-spacing:2px;">NET<span style="color:#F4620A;">SPRING</span></span>
                        <p style="color:#9CA3AF;font-size:13px;margin:8px 0 0 0;">Chine – Côte d'Ivoire</p>
                    </td>
                </tr>

                {{-- Hero --}}
                <tr>
                    <td style="background:#F4620A;padding:32px 40px;text-align:center;">
                        <div style="font-size:48px;margin-bottom:12px;">🛒</div>
                        <h1 style="color:#ffffff;font-size:22px;font-weight:700;margin:0 0 8px 0;font-family:'Century Gothic','Trebuchet MS',sans-serif;">
                            Commande enregistrée !
                        </h1>
                        <p style="color:rgba(255,255,255,0.85);font-size:15px;margin:0;">
                            Merci pour votre commande. Nous la traitons dans les plus brefs délais.
                        </p>
                    </td>
                </tr>

                {{-- Contenu --}}
                <tr>
                    <td style="background:#ffffff;padding:40px;">

                        <p style="color:#374151;font-size:15px;margin:0 0 24px 0;">
                            Bonjour <strong style="color:#0D0D0D;">{{ $order->customer_name }}</strong>,
                        </p>

                        {{-- Numéro commande --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:12px;margin-bottom:28px;">
                            <tr>
                                <td style="padding:20px 24px;text-align:center;">
                                    <p style="color:#9A3412;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 6px 0;">Numéro de commande</p>
                                    <p style="color:#F4620A;font-size:22px;font-weight:900;margin:0;font-family:'Century Gothic','Trebuchet MS',sans-serif;">
                                        #{{ $order->order_number }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- Articles --}}
                        <h2 style="color:#0D0D0D;font-size:15px;font-weight:700;margin:0 0 12px 0;font-family:'Century Gothic','Trebuchet MS',sans-serif;">
                            Articles commandés
                        </h2>
                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E5E7EB;border-radius:12px;margin-bottom:28px;overflow:hidden;">
                            @foreach($order->items as $item)
                            <tr style="border-bottom:{{ !$loop->last ? '1px solid #E5E7EB' : 'none' }};">
                                <td style="padding:14px 20px;">
                                    <p style="color:#0D0D0D;font-size:14px;font-weight:600;margin:0;">{{ $item->name }}</p>
                                    <p style="color:#6B7280;font-size:13px;margin:2px 0 0 0;">Quantité : {{ $item->quantity }}</p>
                                </td>
                                <td style="padding:14px 20px;text-align:right;">
                                    <p style="color:#F4620A;font-size:14px;font-weight:700;margin:0;white-space:nowrap;">
                                        {{ number_format($item->subtotal, 0, ',', ' ') }} FCFA
                                    </p>
                                </td>
                            </tr>
                            @endforeach
                            {{-- Total --}}
                            <tr style="background:#F9FAFB;border-top:2px solid #E5E7EB;">
                                <td style="padding:16px 20px;">
                                    <p style="color:#0D0D0D;font-size:15px;font-weight:700;margin:0;">Total</p>
                                </td>
                                <td style="padding:16px 20px;text-align:right;">
                                    <p style="color:#F4620A;font-size:18px;font-weight:900;margin:0;white-space:nowrap;">
                                        {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- Livraison --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E5E7EB;border-radius:12px;margin-bottom:32px;">
                            <tr>
                                <td style="padding:20px 24px;border-bottom:1px solid #E5E7EB;">
                                    <p style="color:#6B7280;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 4px 0;">Adresse de livraison</p>
                                    <p style="color:#0D0D0D;font-size:14px;margin:0;">
                                        {{ $order->shipping_address['address'] ?? '' }},
                                        {{ $order->shipping_address['city'] ?? '' }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="color:#6B7280;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 4px 0;">Paiement</p>
                                    <p style="color:#0D0D0D;font-size:14px;margin:0;">{{ strtoupper($order->payment_method ?? '—') }}</p>
                                </td>
                            </tr>
                        </table>

                        {{-- CTA --}}
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center">
                                    <a href="https://wa.me/2250594429552?text=Bonjour+NETSPRING,+j%27ai+une+question+sur+ma+commande+%23{{ $order->order_number }}."
                                       style="display:inline-block;background:#25D366;color:#ffffff;font-size:14px;font-weight:700;padding:14px 32px;border-radius:10px;text-decoration:none;">
                                        💬 Suivre ma commande sur WhatsApp
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
