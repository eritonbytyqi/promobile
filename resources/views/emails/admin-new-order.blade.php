<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:20px; }
        .card { background:#fff; border-radius:10px; padding:30px; max-width:600px; margin:0 auto; }
        .header { background:#e63946; color:#fff; padding:20px 30px; border-radius:10px 10px 0 0; margin:-30px -30px 20px; }
        .row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #eee; }
        .label { color:#888; font-size:13px; }
        .value { font-weight:600; }
        .badge { background:#e63946; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; }
        .btn { display:inline-block; background:#e63946; color:#fff; padding:12px 24px; border-radius:8px; text-decoration:none; margin-top:20px; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <h2 style="margin:0;">🛍️ Porosi e Re!</h2>
        <p style="margin:5px 0 0;opacity:0.9;">{{ config('app.name') }}</p>
    </div>

    <div class="row">
        <span class="label">Porosia #</span>
        <span class="value">{{ $order->id }}</span>
    </div>
    <div class="row">
        <span class="label">Klienti</span>
        <span class="value">{{ $order->customer_name }}</span>
    </div>
    <div class="row">
        <span class="label">Telefoni</span>
        <span class="value">{{ $order->customer_phone }}</span>
    </div>
    @if($order->customer_email)
    <div class="row">
        <span class="label">Email</span>
        <span class="value">{{ $order->customer_email }}</span>
    </div>
    @endif
    <div class="row">
        <span class="label">Adresa</span>
        <span class="value">{{ $order->shipping_address }}, {{ $order->city }}</span>
    </div>
    <div class="row">
        <span class="label">Totali</span>
        <span class="value" style="color:#e63946;font-size:18px;">{{ number_format($order->total, 2) }} €</span>
    </div>
    <div class="row">
        <span class="label">Statusi</span>
        <span class="badge">{{ $order->status }}</span>
    </div>

    @if($order->items && $order->items->count())
    <h3 style="margin:20px 0 10px;">Produktet:</h3>
    @foreach($order->items as $item)
    <div class="row">
        <span class="label">{{ $item->product->name ?? 'Produkt' }}</span>
        <span class="value">{{ $item->quantity }}x — {{ number_format($item->price, 2) }} €</span>
    </div>
    @endforeach
    @endif

    <a href="{{ config('app.url') }}/admin/orders/{{ $order->uuid }}" class="btn">
        Shiko Porosinë →
    </a>
</div>
</body>
</html>