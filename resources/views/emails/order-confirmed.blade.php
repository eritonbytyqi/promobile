<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porosia u konfirmua</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
            background: #f3f4f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
        }

        .wrapper {
            max-width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.10);
        }

        .header {
            background: #1f1f23;
            padding: 34px 24px;
            text-align: center;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: #1780f2;
        }

        .hero {
            background: linear-gradient(180deg, #1676db 0%, #1568c2 100%);
            text-align: center;
            padding: 38px 24px 42px;
            color: #ffffff;
        }

        .hero-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: rgba(255,255,255,0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            font-weight: 700;
        }

        .hero h1 {
            margin: 0 0 12px;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 800;
        }

        .hero p {
            margin: 0 auto;
            max-width: 520px;
            font-size: 18px;
            line-height: 1.6;
            color: rgba(255,255,255,0.92);
        }

        .body {
            padding: 42px 36px 34px;
        }

        .greeting {
            margin: 0 0 28px;
            font-size: 16px;
            line-height: 1.8;
            color: #1f2937;
        }

        .order-box {
            background: #f7f7fa;
            border-radius: 18px;
            padding: 22px 22px 14px;
            margin-bottom: 28px;
        }

        .order-box-title,
        .items-title {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 16px;
        }

        .order-row,
        .item-row,
        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 0;
        }

        .order-row {
            border-bottom: 1px solid #e8eaf0;
        }

        .order-row:last-child {
            border-bottom: none;
        }

        .label {
            color: #6b7280;
            font-size: 15px;
        }

        .value {
            color: #111827;
            font-size: 15px;
            font-weight: 700;
            text-align: right;
        }

        .item-row {
            border-bottom: 1px solid #eceef3;
            font-size: 16px;
        }

        .item-name {
            color: #111827;
        }

        .item-price {
            color: #1780f2;
            font-weight: 800;
            white-space: nowrap;
        }

        .total-row {
            margin-top: 6px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .total-label {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .total-value {
            font-size: 20px;
            font-weight: 900;
            color: #1780f2;
        }

        .payment-badge {
            display: inline-block;
            margin-top: 18px;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
        }

        .payment-stripe {
            background: #dbeafe;
            color: #1568c2;
        }

        .payment-cash {
            background: #fef3c7;
            color: #92400e;
        }

        .info-box {
            margin-top: 16px;
            background: #fff8e8;
            border: 1px solid #f4df9d;
            color: #7c5a10;
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 14px;
            line-height: 1.7;
        }

        .info-box p {
            margin: 0;
        }

        .cta {
            text-align: center;
            margin-top: 30px;
        }

        .cta a {
            display: inline-block;
            padding: 14px 28px;
            background: #1f1f23;
            color: #ffffff;
            text-decoration: none;
            border-radius: 999px;
            font-size: 17px;
            font-weight: 800;
        }

        .footer {
            background: #f6f7fb;
            border-top: 1px solid #eceff5;
            text-align: center;
            padding: 34px 24px 28px;
        }

        .brand {
            font-size: 26px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 14px;
        }

        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 15px;
            line-height: 1.8;
        }

        @media (max-width: 640px) {
            body {
                padding: 12px;
            }

            .wrapper {
                border-radius: 18px;
            }

            .hero {
                padding: 32px 18px 36px;
            }

            .hero h1 {
                font-size: 24px;
            }

            .hero p {
                font-size: 15px;
            }

            .body {
                padding: 28px 18px 24px;
            }

            .order-row,
            .item-row,
            .total-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .value,
            .item-price,
            .total-value {
                text-align: left;
            }

            .total-label,
            .total-value {
                font-size: 18px;
            }

            .brand {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="logo">Pro<span>Mobile</span></div>
    </div>

    <div class="hero">
        <div class="hero-icon">✓</div>
        <h1>Porosia u konfirmua!</h1>
        <p>Faleminderit për blerjen tuaj. Porosia juaj është pranuar dhe po procesohet.</p>
    </div>

    <div class="body">
        <p class="greeting">
            Përshëndetje <strong>{{ $order->customer_name }}</strong>,<br><br>
            Porosia juaj me numër <strong>#{{ $order->order_number }}</strong> u konfirmua me sukses.
            Do t'ju kontaktojmë së shpejti për detajet e dorëzimit.
        </p>

        <div class="order-box">
            <div class="order-box-title">Detajet e porosisë</div>

            <div class="order-row">
                <span class="label">Numri i porosisë</span>
                <span class="value">#{{ $order->order_number }}</span>
            </div>

            <div class="order-row">
                <span class="label">Data</span>
                <span class="value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>

            <div class="order-row">
                <span class="label">Adresa</span>
                <span class="value">{{ $order->shipping_address }}, {{ $order->city }}</span>
            </div>

            <div class="order-row">
                <span class="label">Telefoni</span>
                <span class="value">{{ $order->customer_phone ?? '—' }}</span>
            </div>
        </div>

        <div class="items-title">Produktet e porositura</div>

        @foreach($order->items as $item)
        <div class="item-row">
            <span class="item-name">{{ optional($item->product)->name ?? 'Produkt' }} × {{ $item->quantity }}</span>
            <span class="item-price">{{ number_format($item->subtotal, 2) }} €</span>
        </div>
        @endforeach

        <div class="total-row">
            <span class="total-label">Totali</span>
            <span class="total-value">{{ number_format($order->total_amount, 2) }} €</span>
        </div>

        @if($order->payment_method === 'bank')
            <div style="text-align:center;">
                <span class="payment-badge payment-stripe">✓ Paguar me kartë (Stripe)</span>
            </div>
        @else
            <div style="text-align:center;">
                <span class="payment-badge payment-cash">💵 Cash në dorëzim</span>
            </div>

            <div class="info-box">
                <p>Pagesa bëhet në dorëzim. Ju lutemi të keni shumën e saktë gati: <strong>{{ number_format($order->total_amount, 2) }} €</strong></p>
            </div>
        @endif

        <div class="cta">
            <a href="{{ url('/profili-im') }}">Shiko porositë e mia</a>
        </div>
    </div>

    <div class="footer">
        <div class="brand">ProMobile</div>
        <p>
            Faleminderit që zgjodhët ProMobile!<br>
            Nëse keni pyetje, na kontaktoni: info@promobile.com<br>
            © {{ date('Y') }} ProMobile. Të gjitha të drejtat e rezervuara.
        </p>
    </div>

</div>
</body>
</html>
