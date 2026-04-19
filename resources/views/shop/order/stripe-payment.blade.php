@extends('layouts.shop')

@section('title', 'Pagesa — ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/payment.css') }}">
@endpush

@section('content')
<div class="payment-wrap">

    <div class="payment-header">
        <h1>Pagesa e sigurt</h1>
        <p>{{ $pending['customer_name'] ?? '' }} — {{ number_format($total, 2) }} €</p>
    </div>

    {{-- Test cards info --}}
    <div class="test-cards">
        <div class="test-cards-title">
            <span class="material-symbols-outlined" style="font-size:15px;">info</span>
            Kartela testimi (Sandbox mode)
        </div>
        <div class="test-card-row">
            <span>✅ Sukses:</span>
            <span>4242 4242 4242 4242</span>
        </div>
        <div class="test-card-row">
            <span>❌ Refuzuar:</span>
            <span>4000 0000 0000 0002</span>
        </div>
        <div class="test-card-row">
            <span>📅 Skadimi:</span>
            <span>Çdo datë e ardhshme</span>
        </div>
        <div class="test-card-row">
            <span>🔐 CVV:</span>
            <span>Çdo 3 shifra</span>
        </div>
    </div>

    {{-- Order summary --}}
    <div class="payment-card">
        <div class="payment-card-title">
            <span class="material-symbols-outlined">receipt</span>
            Përmbledhja
        </div>
        <div class="order-mini">
            @foreach($cart as $item)
            <div class="order-mini-row">
                <span>{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                <span>{{ number_format($item['price'] * $item['quantity'], 2) }} €</span>
            </div>
            @endforeach
            <div class="order-mini-row total">
                <span>Totali</span>
                <span>{{ number_format($total, 2) }} €</span>
            </div>
        </div>
    </div>

    {{-- Stripe payment form --}}
    <div class="payment-card">
        <div class="payment-card-title">
            <span class="material-symbols-outlined">credit_card</span>
            Të dhënat e kartës
        </div>

        <label class="stripe-label">Numri i kartës, skadimi dhe CVV</label>
        <div id="card-element"></div>
        <div id="card-errors"></div>

        <button id="payBtn" class="pay-btn" onclick="handlePayment()">
            <span class="material-symbols-outlined" style="font-size:18px;">lock</span>
            <span id="payBtnText">Paguaj {{ number_format($total, 2) }} €</span>
            <div class="spinner" id="paySpinner"></div>
        </button>

        <div class="secure-note">
            <span class="material-symbols-outlined" style="font-size:14px;">shield</span>
            Pagesa e sigurt përmes Stripe — SSL 256-bit
        </div>
    </div>

</div>
@endsection

<script src="https://js.stripe.com/v3/"></script>
<script>
    window.stripeKey       = "{{ config('services.stripe.key') }}";
    window.orderTotal      = {{ (int) round($total * 100) }}; // në cents për Stripe
    window.bankConfirmUrl  = "{{ route('bank.confirm') }}";
    window.csrfToken       = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/shop/stripe-payment.js') }}"></script>