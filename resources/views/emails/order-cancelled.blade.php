<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porosia u anulua</title>
</head>
<body>
<div class="wrapper">

    <!-- Header -->
    <div class="header">
        <div class="logo">Pro<span>Mobile</span></div>
    </div>

    <!-- Hero -->
    <div class="hero">
        <div class="hero-icon">✕</div>
        <h1>Porosia u anulua</h1>
        <p>Porosia juaj është anuluar. Nëse keni paguar, paratë do t'ju kthehen automatikisht.</p>
    </div>

    <!-- Body -->
    <div class="body">
        <p class="greeting">
            Përshëndetje <strong>{{ $order->customer_name }}</strong>,<br><br>
            Na vjen keq të njoftojmë se porosia juaj <strong>#{{ $order->order_number }}</strong> është anuluar.
        </p>

        <!-- Order details -->
        <div class="order-box">
            <div class="order-box-title">Detajet e porosisë</div>
            <div class="order-row">
                <span class="label">Numri i porosisë</span>
                <span class="value">#{{ $order->order_number }}</span>
            </div>
            <div class="order-row">
                <span class="label">Data e porosisë</span>
                <span class="value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="order-row">
                <span class="label">Totali</span>
                <span class="value">{{ number_format($order->total_amount, 2) }} €</span>
            </div>
        </div>

        <!-- Refund info -->
        @if($order->payment_method === 'bank' && $order->payment_intent_id)
        <div class="refund-box">
            <div class="refund-box-title">✓ Kthimi i pagesës</div>
            <div class="refund-amount">{{ number_format($order->total_amount, 2) }} €</div>
            <p>
                Pagesa juaj do të kthehet automatikisht te karta juaj brenda <strong>5-10 ditëve pune</strong>,
                varësisht nga banka juaj.
            </p>
        </div>
        @endif

        <div class="cta">
            <a href="{{ url('/shop') }}">Vazhdo blerjen</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="brand">ProMobile</div>
        <p>
            Nëse keni pyetje rreth anulimit, na kontaktoni: info@promobile.com<br>
            © {{ date('Y') }} ProMobile. Të gjitha të drejtat e rezervuara.
        </p>
    </div>

</div>
</body>
</html>
