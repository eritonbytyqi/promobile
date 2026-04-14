<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:32px 16px;background:#f3f4f8;font-family:Arial,Helvetica,sans-serif;color:#111827;">

@php
$heroConfig = [
    'confirmed' => ['icon'=>'&#10003;', 'bg'=>'linear-gradient(180deg,#1676db,#1568c2)', 'title'=>'Porosia u konfirmua!'],
    'shipped'   => ['icon'=>'&#8594;',  'bg'=>'linear-gradient(180deg,#7c3aed,#6d28d9)', 'title'=>'Porosia është rrugës!'],
    'delivered' => ['icon'=>'&#10003;', 'bg'=>'linear-gradient(180deg,#059669,#047857)', 'title'=>'Porosia u dorëzua!'],
    'cancelled' => ['icon'=>'&#10007;', 'bg'=>'linear-gradient(180deg,#dc2626,#b91c1c)', 'title'=>'Porosia u anulua'],
    'pending'   => ['icon'=>'&#8987;',  'bg'=>'linear-gradient(180deg,#d97706,#b45309)', 'title'=>'Porosia në pritje'],
];
$hero = $heroConfig[$order->status] ?? ['icon'=>'&#9993;', 'bg'=>'linear-gradient(180deg,#1676db,#1568c2)', 'title'=>$subject];
@endphp

<div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;">

    {{-- HEADER me Logo --}}
  <div style="background:#1f1f23;padding:24px;text-align:center;">
    <div style="font-size:24px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">
        Pro<span style="color:#1780f2;">Mobile</span>
    </div>
</div>

    {{-- HERO --}}
    <div style="background:{{ $hero['bg'] }};text-align:center;padding:36px 24px;color:#ffffff;">
     <div style="width:64px;height:64px;margin:0 auto 16px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:36px;color:#ffffff;font-weight:700;line-height:1;">
    {!! $hero['icon'] !!}
</div>
        <h1 style="margin:0 0 10px;font-size:26px;font-weight:800;color:#ffffff;">{{ $hero['title'] }}</h1>
        <p style="margin:0;font-size:15px;color:rgba(255,255,255,0.88);">Faleminderit për blerjen tuaj.</p>
    </div>

    {{-- BODY --}}
    <div style="padding:32px 28px;">

        {{-- Teksti i editueshëm --}}
        <div style="font-size:15px;line-height:1.8;color:#1f2937;margin-bottom:28px;">
            {!! $body !!}
        </div>

        {{-- Detajet e porosisë --}}
        <div style="background:#f7f7fa;border-radius:12px;padding:20px;margin-bottom:24px;">
            <div style="font-size:11px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#6b7280;margin-bottom:14px;">Detajet e porosisë</div>
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:8px 0;color:#6b7280;font-size:14px;border-bottom:1px solid #e8eaf0;">Numri i porosisë</td>
                    <td style="padding:8px 0;font-weight:700;color:#111827;font-size:14px;text-align:right;border-bottom:1px solid #e8eaf0;">#{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#6b7280;font-size:14px;border-bottom:1px solid #e8eaf0;">Data</td>
                    <td style="padding:8px 0;font-weight:700;color:#111827;font-size:14px;text-align:right;border-bottom:1px solid #e8eaf0;">{{ $order->created_at->format('d M Y, H:i') }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#6b7280;font-size:14px;border-bottom:1px solid #e8eaf0;">Adresa</td>
                    <td style="padding:8px 0;font-weight:700;color:#111827;font-size:14px;text-align:right;border-bottom:1px solid #e8eaf0;">{{ $order->shipping_address }}, {{ $order->city }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#6b7280;font-size:14px;">Telefoni</td>
                    <td style="padding:8px 0;font-weight:700;color:#111827;font-size:14px;text-align:right;">{{ $order->customer_phone ?? '—' }}</td>
                </tr>
            </table>
        </div>

        {{-- Produktet --}}
        <div style="font-size:11px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#6b7280;margin-bottom:12px;">Produktet e porositura</div>

        @foreach($order->items as $item)
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
            <tr>
                <td style="padding:10px 0;color:#111827;font-size:15px;border-bottom:1px solid #eceef3;">
                    {{ optional($item->product)->name ?? 'Produkt' }} × {{ $item->quantity }}
                </td>
                <td style="padding:10px 0;font-weight:800;color:#1780f2;font-size:15px;text-align:right;border-bottom:1px solid #eceef3;white-space:nowrap;">
                    {{ number_format($item->subtotal, 2) }} €
                </td>
            </tr>
        </table>
        @endforeach

        {{-- Totali --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;border-top:2px solid #e5e7eb;">
            <tr>
                <td style="padding:14px 0;font-size:18px;font-weight:800;color:#111827;">Totali</td>
                <td style="padding:14px 0;font-size:20px;font-weight:900;color:#1780f2;text-align:right;">
                    {{ number_format($order->total_amount, 2) }} €
                </td>
            </tr>
        </table>

        {{-- Payment Badge --}}
        @if($order->payment_method === 'bank' || $order->payment_method === 'stripe')
        <div style="text-align:center;margin-top:16px;">
            <span style="display:inline-block;padding:10px 18px;border-radius:999px;background:#dbeafe;color:#1568c2;font-size:14px;font-weight:700;">
                ✓ Paguar me kartë (Stripe)
            </span>
        </div>
        @else
        <div style="text-align:center;margin-top:16px;">
            <span style="display:inline-block;padding:10px 18px;border-radius:999px;background:#fef3c7;color:#92400e;font-size:14px;font-weight:700;">
                💵 Cash në dorëzim
            </span>
        </div>
        @if($order->status !== 'cancelled')
        <div style="background:#fff8e8;border:1px solid #f4df9d;color:#7c5a10;border-radius:12px;padding:12px 16px;font-size:14px;line-height:1.7;margin-top:12px;">
            Pagesa bëhet në dorëzim. Ju lutemi të keni shumën e saktë gati: <strong>{{ number_format($order->total_amount, 2) }} €</strong>
        </div>
        @endif
        @endif

        {{-- CTA --}}
        @if($order->status !== 'cancelled')
        <div style="text-align:center;margin-top:28px;">
            <a href="{{ url('/profili-im') }}" style="display:inline-block;padding:14px 28px;background:#1f1f23;color:#ffffff;text-decoration:none;border-radius:999px;font-size:16px;font-weight:800;">
                Shiko porositë e mia
            </a>
        </div>
        @endif

    </div>

    {{-- FOOTER --}}
    <div style="background:#f6f7fb;border-top:1px solid #eceff5;text-align:center;padding:28px 24px;">
        <div style="font-size:22px;font-weight:800;color:#111827;margin-bottom:10px;">ProMobile</div>
        <p style="margin:0;color:#6b7280;font-size:14px;line-height:1.8;">
            Faleminderit që zgjodhët ProMobile!<br>
            Nëse keni pyetje, na kontaktoni: info@promobile.com<br>
            © {{ date('Y') }} ProMobile. Të gjitha të drejtat e rezervuara.
        </p>
    </div>

</div>
</body>
</html>