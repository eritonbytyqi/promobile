{{-- CART SIDEBAR --}}
<div class="cs-overlay" id="cartOverlay" onclick="closeCart()"></div>

<div class="cs-sidebar" id="cartSidebar">

    <div class="cs-header">
        <div class="cs-title">
            <i class="fa-solid fa-bag-shopping"></i>
            Shporta ime
            <span class="cs-count-badge" id="csCountBadge"
                  style="{{ count(session('cart', [])) === 0 ? 'display:none' : '' }}">
                {{ count(session('cart', [])) }}
            </span>
        </div>
        <button class="cs-close" onclick="closeCart()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="cs-body" id="csBody">
        @php $cart = session('cart', []); @endphp
        @include('shop.partials.cart-sidebar-items', ['cart' => $cart])
    </div>

    <div class="cs-footer" id="csFooter" style="{{ count($cart) === 0 ? 'display:none' : '' }}">
        @php
            $shipping  = \App\Helpers\ShippingHelper::getShipping();
            $freeMin   = $shipping['free_min'];
            $shipCost  = $shipping['cost'];
            $freeText  = $shipping['free_text'];
            $country   = $shipping['country'];
            $cartTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

            $flags = [
                'kosovo'    => '🇽🇰',
                'albania'   => '🇦🇱',
                'macedonia' => '🇲🇰',
                'serbia'    => '🇷🇸',
            ];

            $names = [
                'kosovo'    => 'Kosovë',
                'albania'   => 'Shqipëri',
                'macedonia' => 'Maqedoni',
                'serbia'    => 'Serbi',
            ];

            $flag = $flags[$country] ?? '🇽🇰';
            $name = $names[$country] ?? 'Kosovë';
        @endphp

        <div class="cs-subtotal">
            <span>Nëntotali</span>
            <span class="cs-subtotal-val" id="csTotal">
                {{ number_format($cartTotal, 2) }} €
            </span>
        </div>

        <div class="cs-shipping">
            <span style="font-size:14px;">{{ $flag }}</span>
            @if($cartTotal >= $freeMin)
                {{ $freeText }} — {{ $name }}
            @else
                Dërgesa {{ $name }} {{ number_format($shipCost, 2) }} €
                @if($cartTotal > 0)
                    — shto {{ number_format($freeMin - $cartTotal, 2) }} € për falas
                @endif
            @endif
        </div>

        <a href="{{ route('cart.checkout') }}" class="cs-checkout-btn">
            Përfundo porosinë
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

</div>
