<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porosia u konfirmua</title>
</head>
<body style="margin:0;padding:32px 16px;background:#f3f4f8;font-family:Arial,Helvetica,sans-serif;color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center">
<table width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 20px 60px rgba(15,23,42,0.10);">

    <!-- HEADER -->
    <tr>
        <td style="background:#1f1f23;padding:28px 24px;text-align:center;">
            <span style="font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">
                Pro<span style="color:#1780f2;">Mobile</span>
            </span>
        </td>
    </tr>

    <!-- HERO -->
    <tr>
        <td style="background:linear-gradient(180deg,#1676db 0%,#1568c2 100%);padding:38px 24px 42px;text-align:center;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr><td align="center">
                    <div style="width:58px;height:58px;background:rgba(255,255,255,0.18);border-radius:50%;margin:0 auto 18px;font-size:30px;line-height:58px;text-align:center;">&#10003;</div>
                    <h1 style="color:#ffffff;margin:0 0 12px;font-size:28px;font-weight:800;line-height:1.2;">Porosia u konfirmua!</h1>
                    <p style="color:rgba(255,255,255,0.92);margin:0 auto;font-size:16px;line-height:1.6;max-width:460px;">
                        Faleminderit për blerjen tuaj. Porosia juaj është pranuar dhe po procesohet.
                    </p>
                </td></tr>
            </table>
        </td>
    </tr>

    <!-- BODY -->
    <tr>
        <td style="padding:40px 36px 32px;">

            <!-- GREETING -->
            <p style="margin:0 0 28px;font-size:16px;line-height:1.8;color:#1f2937;">
                Përshëndetje <strong>{{ $order->customer_name }}</strong>,<br><br>
                Porosia juaj me numër <strong>#{{ $order->order_number }}</strong> u konfirmua me sukses.
                Do t'ju kontaktojmë së shpejti për detajet e dorëzimit.
            </p>

            <!-- ORDER DETAILS -->
            <p style="font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#6b7280;margin:0 0 12px;">Detajet e porosisë</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7fa;border-radius:16px;overflow:hidden;margin-bottom:28px;">
                <tr>
                    <td style="padding:14px 20px;border-bottom:1px solid #e8eaf0;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:15px;color:#6b7280;">Numri i porosisë</td>
                            <td style="font-size:15px;font-weight:700;color:#111827;text-align:right;">#{{ $order->order_number }}</td>
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;border-bottom:1px solid #e8eaf0;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:15px;color:#6b7280;">Data</td>
                            <td style="font-size:15px;font-weight:700;color:#111827;text-align:right;">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;border-bottom:1px solid #e8eaf0;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:15px;color:#6b7280;">Adresa</td>
                            <td style="font-size:15px;font-weight:700;color:#111827;text-align:right;">{{ $order->shipping_address }}, {{ $order->city }}</td>
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td style="font-size:15px;color:#6b7280;">Telefoni</td>
                            <td style="font-size:15px;font-weight:700;color:#111827;text-align:right;">{{ $order->customer_phone ?? '—' }}</td>
                        </tr></table>
                    </td>
                </tr>
            </table>

            <!-- PRODUCTS -->
            <p style="font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#6b7280;margin:0 0 12px;">Produktet e porositura</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:0;">
                @foreach($order->items as $item)
                <tr>
                    <td style="padding:13px 0;border-bottom:1px solid #eceef3;font-size:15px;color:#111827;">
                        {{ optional($item->product)->name ?? 'Produkt' }} &times; {{ $item->quantity }}
                    </td>
                    <td style="padding:13px 0;border-bottom:1px solid #eceef3;font-size:15px;font-weight:800;color:#1780f2;text-align:right;white-space:nowrap;">
                        {{ number_format($item->subtotal, 2) }} &euro;
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td style="padding:18px 0 4px;font-size:20px;font-weight:800;color:#111827;">Totali</td>
                    <td style="padding:18px 0 4px;font-size:20px;font-weight:900;color:#1780f2;text-align:right;white-space:nowrap;">
                        {{ number_format($order->total_amount, 2) }} &euro;
                    </td>
                </tr>
            </table>

            <!-- PAYMENT BADGE -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:22px;">
                <tr><td align="center">
                    @if($order->payment_method === 'bank')
                        <span style="display:inline-block;padding:10px 20px;border-radius:999px;font-size:14px;font-weight:700;background:#dbeafe;color:#1568c2;">
                            &#10003; Paguar me kartë (Stripe)
                        </span>
                    @else
                        <span style="display:inline-block;padding:10px 20px;border-radius:999px;font-size:14px;font-weight:700;background:#fef3c7;color:#92400e;">
                            &#128181; Cash në dorëzim
                        </span>
                    @endif
                </td></tr>
            </table>

            @if($order->payment_method !== 'bank')
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:14px;">
                <tr>
                    <td style="background:#fff8e8;border:1px solid #f4df9d;border-radius:14px;padding:14px 18px;font-size:14px;color:#7c5a10;line-height:1.7;">
                        Pagesa bëhet në dorëzim. Ju lutemi të keni shumën e saktë gati:
                        <strong>{{ number_format($order->total_amount, 2) }} &euro;</strong>
                    </td>
                </tr>
            </table>
            @endif

            <!-- CTA -->
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">
                <tr><td align="center">
                    <a href="{{ url('/profili-im') }}"
                       style="display:inline-block;padding:14px 32px;background:#1f1f23;color:#ffffff;text-decoration:none;border-radius:999px;font-size:16px;font-weight:800;">
                        Shiko porositë e mia
                    </a>
                </td></tr>
            </table>

        </td>
    </tr>

    <!-- FOOTER -->
    <tr>
        <td style="background:#f6f7fb;border-top:1px solid #eceff5;padding:32px 24px;text-align:center;">
            <p style="margin:0 0 10px;font-size:22px;font-weight:800;color:#111827;">ProMobile</p>
            <p style="margin:0;color:#6b7280;font-size:14px;line-height:1.8;">
                Faleminderit që zgjodhët ProMobile!<br>
                Nëse keni pyetje, na kontaktoni: info@promobile.com<br>
                &copy; {{ date('Y') }} ProMobile. Të gjitha të drejtat e rezervuara.
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>