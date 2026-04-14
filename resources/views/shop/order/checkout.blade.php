@extends('layouts.shop')

@section('title', 'Checkout — ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/checkout.css') }}">
@endpush

@section('content')
<div class="checkout-wrap">

    @if(count($cart) === 0)
        <div class="checkout-empty">
            <i class="fa-solid fa-bag-shopping"></i>
            <h2>Shporta është bosh</h2>
            <p>Shto produkte para se të vazhdosh me checkout.</p>
            <a href="{{ url('/shop') }}">
                <i class="fa-solid fa-arrow-left"></i>
                Shko te produktet
            </a>
        </div>

    @else

        <h1 class="checkout-title">Checkout</h1>

        <form method="POST" action="{{ route('checkout.place') }}">
        @csrf

        <div class="checkout-grid">

            {{-- ── LEFT ── --}}
            <div class="checkout-left">

                {{-- Guest prompt ose auth banner --}}
                @guest
                    <div class="guest-prompt">
                        <div class="guest-prompt-text">
                            <strong>Keni llogari?</strong>
                            Kyçuni për të plotësuar automatikisht të dhënat tuaja dhe për të ruajtur porositë.
                        </div>
                        <a href="{{ route('login') }}" class="guest-prompt-btn">
                            <span class="material-symbols-outlined" style="font-size:16px;">login</span>
                            Kyçu
                        </a>
                    </div>
                @else
                    <div class="auth-info-banner">
                        <span class="material-symbols-outlined">verified_user</span>
                        Të dhënat u plotësuan automatikisht nga profili juaj.
                        <a href="{{ route('profile.index') }}" style="color:var(--primary);font-weight:700;margin-left:auto;text-decoration:none;font-size:12px;">Ndrysho</a>
                    </div>
                @endguest

                {{-- Informacioni i dërgimit --}}
                <div class="checkout-section">
                    <div class="checkout-section-title">
                        <i class="fa-solid fa-truck"></i>
                        Informacioni i dërgimit
                    </div>

                    <div class="form-grid-2">
                        @auth
                        @php $address = \App\Models\UserAddress::where('user_id', auth()->id())->where('is_default',1)->first(); @endphp

                        <div class="form-group">
                            <label class="form-label">Emri i plotë</label>
                            <input type="text" name="customer_name" class="form-input"
                                   value="{{ auth()->user()->name . ' ' . auth()->user()->surname }}"
                                   placeholder="Emri Mbiemri" required autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" class="form-input"
                                   value="{{ auth()->user()->email }}"
                                   placeholder="email@example.com" required autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefoni</label>
                            <input type="tel" name="customer_phone" class="form-input"
                                   value="{{ auth()->user()->phone ?? '' }}"
                                   placeholder="+383 44 000 000" autocomplete="tel" inputmode="tel">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Qyteti</label>
                            <input type="text" name="city" class="form-input"
                                   value="{{ $address->city ?? '' }}"
                                   placeholder="Prishtinë" autocomplete="address-level2">
                        </div>
                        <div class="form-group full">
                            <label class="form-label">Adresa</label>
                            <input type="text" name="shipping_address" class="form-input"
                                   value="{{ $address->address_line_1 ?? '' }}"
                                   placeholder="Rruga, numri..." autocomplete="street-address">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kodi Postar</label>
                            <input type="text" name="postal_code" class="form-input"
                                   value="{{ $address->postal_code ?? '' }}"
                                   placeholder="10000" autocomplete="postal-code">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Shteti</label>
                            <select name="country" class="form-input" id="countrySelect" onchange="updateShipping(this.value)">
                                <option value="">— Zgjedh shtetin —</option>
                                <option value="Kosovë"   {{ ($address->country ?? '') == 'Kosovë'   ? 'selected' : '' }}>🇽🇰 Kosovë</option>
                                <option value="Shqipëri" {{ ($address->country ?? '') == 'Shqipëri' ? 'selected' : '' }}>🇦🇱 Shqipëri</option>
                                <option value="Maqedoni" {{ ($address->country ?? '') == 'Maqedoni' ? 'selected' : '' }}>🇲🇰 Maqedoni e Veriut</option>
                                <option value="Serbi"    {{ ($address->country ?? '') == 'Serbi'    ? 'selected' : '' }}>🇷🇸 Serbi</option>
                            </select>
                        </div>

                        @else

                        <div class="form-group">
                            <label class="form-label">Emri i plotë</label>
                            <input type="text" name="customer_name" class="form-input" placeholder="Emri Mbiemri" required autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" class="form-input" placeholder="email@example.com" required autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefoni</label>
                            <input type="tel" name="customer_phone" class="form-input" placeholder="+383 44 000 000" autocomplete="tel" inputmode="tel">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Qyteti</label>
                            <input type="text" name="city" class="form-input" placeholder="Prishtinë" autocomplete="address-level2">
                        </div>
                        <div class="form-group full">
                            <label class="form-label">Adresa</label>
                            <input type="text" name="shipping_address" class="form-input" placeholder="Rruga, numri..." autocomplete="street-address">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kodi Postar</label>
                            <input type="text" name="postal_code" class="form-input" placeholder="10000" autocomplete="postal-code">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Shteti</label>
                            <select name="country" class="form-input" id="countrySelect" onchange="updateShipping(this.value)">
                                <option value="">— Zgjedh shtetin —</option>
                                <option value="Kosovë">🇽🇰 Kosovë</option>
                                <option value="Shqipëri">🇦🇱 Shqipëri</option>
                                <option value="Maqedoni">🇲🇰 Maqedoni e Veriut</option>
                            </select>
                        </div>

                        @endauth
                    </div>
                </div>

                {{-- Mënyra e pagesës --}}
                <div class="checkout-section">
                    <div class="checkout-section-title">
                        <i class="fa-solid fa-wallet"></i>
                        Mënyra e pagesës
                    </div>
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="cash" value="cash" checked>
                            <label for="cash">
                                <i class="fa-solid fa-money-bill-wave" style="color:#34c759;"></i>
                                Para në dorë (Cash on delivery)
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" id="bank" value="bank">
                            <label for="bank">
                                <i class="fa-solid fa-building-columns" style="color:var(--primary);"></i>
                                Transfertë bankare
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT: Order Summary ── --}}
            <div class="checkout-right">
                <div class="order-summary">

                    <div class="order-summary-title">Përmbledhja e porosisë</div>

                    <div class="order-items">
                        @foreach($cart as $key => $item)
                        <div class="order-item">
                            <div class="order-item-img">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                                @else
                                    <div class="order-item-img-ph"><i class="fa-solid fa-box"></i></div>
                                @endif
                            </div>
                            <div class="order-item-info">
                                <div class="order-item-name">{{ $item['name'] }}</div>
                                <div class="order-item-qty">Sasia: {{ $item['quantity'] }}</div>
                            </div>
                            <div class="order-item-price">
                                {{ number_format($item['price'] * $item['quantity'], 2) }} €
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="order-divider"></div>

                    <div class="order-totals">
                        <div class="order-row">
                            <span>Nëntotali</span>
                            <span>{{ number_format($total, 2) }} €</span>
                        </div>

                        <div class="order-row shipping">
                            <span>Dërgesa</span>
                            <span id="shippingDisplay" style="color:#006980;font-weight:600;">
                                <i class="fa-solid fa-truck" style="font-size:11px;"></i> Zgjedh shtetin
                            </span>
                        </div>

                        <div id="shippingNote" style="display:none;font-size:12px;color:#6b7280;margin-top:4px;padding:8px 12px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;">
                            <i class="fa-solid fa-circle-info" style="color:#006980;margin-right:4px;"></i>
                            <span id="shippingNoteText"></span>
                        </div>

                        <div class="order-divider"></div>

                        <div class="order-row total">
                            <span>Totali</span>
                            <span>{{ number_format($total, 2) }} €</span>
                        </div>
                    </div>

                    <button type="submit" class="checkout-btn">
                        <i class="fa-solid fa-check" style="font-size:13px;"></i>
                        Përfundo porosinë
                    </button>

                    <div class="secure-badge">
                        <i class="fa-solid fa-shield-halved" style="font-size:12px;"></i>
                        Transaksion i sigurt
                    </div>

                </div>
            </div>

        </div>

        <input type="hidden" name="shipping_cost" id="shippingCost" value="0">

        </form>

    @endif
</div>
@endsection

@push('scripts')
<script>
const baseTotal = {{ $total }};
const shippingRates = {
    'Kosovë':   {{ $settings['shipping_kosovo_cost']    ?? 0 }},
    'Shqipëri': {{ $settings['shipping_albania_cost']   ?? 5 }},
    'Maqedoni': {{ $settings['shipping_macedonia_cost'] ?? 3 }},
};
const freeMin = {
    'Kosovë':   {{ $settings['shipping_kosovo_free_min']    ?? 100 }},
    'Shqipëri': {{ $settings['shipping_albania_free_min']   ?? 150 }},
    'Maqedoni': {{ $settings['shipping_macedonia_free_min'] ?? 200 }},
};

function updateShipping(country) {
    const min  = freeMin[country] ?? 0;
    const cost = (min > 0 && baseTotal >= min) ? 0 : (shippingRates[country] ?? 0);

    document.getElementById('shippingCost').value = cost;

    const display = document.getElementById('shippingDisplay');
    const note    = document.getElementById('shippingNote');
    const noteTxt = document.getElementById('shippingNoteText');

    if (!country) {
        display.innerHTML  = '<i class="fa-solid fa-truck" style="font-size:11px;"></i> Zgjedh shtetin';
        note.style.display = 'none';
        return;
    }

    if (cost === 0) {
        display.innerHTML  = '<i class="fa-solid fa-truck" style="font-size:11px;"></i> Falas';
        note.style.display = 'none';
    } else {
        display.innerHTML  = cost.toFixed(2) + ' €';
        noteTxt.textContent = 'Kostoja e dërgesës për ' + country + ' është ' + cost.toFixed(2) + ' €.';
        note.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('countrySelect');
    if (sel && sel.value) updateShipping(sel.value);
});
</script>
@endpush