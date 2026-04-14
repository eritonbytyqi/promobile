@extends('layouts.shop')

@section('title', 'Porosia u bë — ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/checkout.css') }}">
@endpush

@section('content')
<div class="success-wrap">

    @if(isset($order) && $order->payment_method === 'bank')
        {{-- Stripe/Bank payment --}}
        <div class="success-icon pending">
            <i class="fa-solid fa-clock"></i>
        </div>
        <h1 class="success-title">Pagesa u krye!</h1>
        <div class="status-badge status-awaiting">
            <i class="fa-solid fa-hourglass-half"></i>
            Në pritje të konfirmimit
        </div>
        <p class="success-sub">
            Pagesa juaj u pranua nga Stripe.<br>
            Porosia do të konfirmohet nga ekipi ynë brenda 24 orëve.<br>
            Do të njoftoheni me email sapo të konfirmohet.
        </p>
    @else
        {{-- Cash on delivery --}}
        <div class="success-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        <h1 class="success-title">Faleminderit!</h1>
        <div class="status-badge status-pending">
            <i class="fa-solid fa-truck"></i>
            Cash në dorëzim
        </div>
        <p class="success-sub">
            Porosia juaj u regjistrua me sukses.<br>
            Do t'ju kontaktojmë së shpejti për konfirmim dhe dorëzim.
        </p>
    @endif

    {{-- Order details --}}
    @isset($order)
    <div class="success-card">
        <div class="success-card-title">Detajet e porosisë</div>
        <div class="success-card-row">
            <span>Numri i porosisë</span>
            <span>#{{ $order->order_number }}</span>
        </div>
        <div class="success-card-row">
            <span>Emri</span>
            <span>{{ $order->customer_name }}</span>
        </div>
        <div class="success-card-row">
            <span>Email</span>
            <span>{{ $order->customer_email }}</span>
        </div>
        <div class="success-card-row">
            <span>Metoda e pagesës</span>
            <span>{{ $order->payment_method === 'bank' ? 'Kartë krediti (Stripe)' : 'Cash në dorëzim' }}</span>
        </div>
        @foreach($order->items as $item)
        <div class="success-card-row">
            <span>{{ $item->product->name ?? 'Produkt' }} × {{ $item->quantity }}</span>
            <span>{{ number_format($item->subtotal, 2) }} €</span>
        </div>
        @endforeach
        <div class="success-card-row" style="font-size:15px;">
            <span><strong>Totali</strong></span>
            <span style="color:var(--primary);font-size:16px;"><strong>{{ number_format($order->total_amount, 2) }} €</strong></span>
        </div>
    </div>
    @endisset

    <div>
        <a href="{{ url('/shop') }}" class="success-btn">
            <i class="fa-solid fa-bag-shopping"></i>
            Vazhdo blerjen
        </a>
        @auth
        <a href="{{ route('profile.index') }}" class="success-secondary">
            <i class="fa-solid fa-receipt"></i>
            Porositë e mia
        </a>
        @endauth
    </div>

</div>
@endsection
