<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">
    <title>@yield('title', 'ProMobile')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<meta name="stripe-key" content="{{ config('services.stripe.key') }}">

        <link rel="stylesheet" href="{{ asset('css/shop/layout.css') }}">
    @stack('styles')
</head>
<body>

@php $cartCount = count(session('cart', [])); @endphp
@include('shop.partials.cart-sidebar')

<!-- NAVBAR -->
<nav class="navbar">
<div class="navbar-inner">
    <a href="{{ url('/') }}" class="nav-logo">
        <img src="{{ asset('images/Logo.svg') }}" alt="ProMobile Logo">
    </a>
</li>
     <ul class="nav-links">
    <li>
        <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
            Kryefaqja
        </a>
    </li>

    <li>
        <a href="{{ url('/shop') }}" class="{{ request()->is('shop') && !request('featured') ? 'active' : '' }}">
            Produktet
        </a>
    </li>

    <li>
        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">
            Kontakt
        </a>
    </li>

    <li>
        <a href="{{ url('/shop?featured=1') }}"
           class="{{ request()->is('shop') && request('featured') ? 'active' : '' }}"
           style="display:flex;align-items:center;gap:6px;">
            Favoritet
            <span id="navWishCount" style="display:none;background:#ff3b30;color:white;font-size:10px;font-weight:700;min-width:18px;height:18px;border-radius:999px;padding:0 5px;align-items:center;justify-content:center;">0</span>
        </a>
    </li>
</ul>

        <div class="nav-right">
         <form action="{{ url('/shop') }}" method="GET" class="nav-search" id="searchForm" autocomplete="off">
    @if(request('category'))
        <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
    @if(request('featured'))
        <input type="hidden" name="featured" value="1">
    @endif
    <span class="material-symbols-outlined search-icon" style="font-size:18px;">search</span>
    <input type="text" name="search" id="searchInput"
           placeholder="Kërko..." value="{{ request('search') }}"
           autocomplete="off">
    <div id="searchDropdown" style="
        display:none;
        position:absolute;
        top:calc(100% + 6px);
        left:0; right:0;
        background:white;
        border:1px solid #e2e2e4;
        border-radius:14px;
        box-shadow:0 8px 32px rgba(0,0,0,0.12);
        z-index:999;
        overflow:hidden;
        max-height:320px;
        overflow-y:auto;
    "></div>
</form>
{{-- PROFILI / LOGIN --}}
@auth
<div style="position:relative;" id="profileDropdownWrap">
    <button onclick="toggleProfileMenu()" style="
        width:38px; height:38px; border-radius:50%;
        background:var(--primary-fixed); border:1.5px solid var(--primary-dim);
        color:var(--primary); font-size:14px; font-weight:800;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        font-family:'Inter',sans-serif; transition: all 0.2s;
    ">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </button>

    <div id="profileMenu" style="
        display:none; position:absolute; top:calc(100% + 10px); right:0;
        background:var(--surface); border:1px solid var(--border-solid);
        border-radius:16px; min-width:200px;
        box-shadow:0 8px 32px rgba(0,0,0,0.1); z-index:999; overflow:hidden;
    ">
        <div style="padding:14px 16px; border-bottom:1px solid var(--border-solid);">
            <div style="font-size:13px; font-weight:700; color:var(--text);">
                {{ auth()->user()->name }} {{ auth()->user()->surname }}
            </div>
            <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                {{ auth()->user()->email }}
            </div>
        </div>
        <a href="{{ route('profile.index') }}" style="
            display:flex; align-items:center; gap:10px;
            padding:12px 16px; text-decoration:none;
            color:var(--text-soft); font-size:13px; font-weight:500;
            transition:background 0.15s;
        " onmouseover="this.style.background='var(--surface-low)'"
           onmouseout="this.style.background='transparent'">
            <span class="material-symbols-outlined" style="font-size:17px;">person</span>
            Profili im
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="
                width:100%; display:flex; align-items:center; gap:10px;
                padding:12px 16px; background:transparent; border:none;
                color:var(--error); font-size:13px; font-weight:500;
                cursor:pointer; font-family:'Inter',sans-serif;
                border-top:1px solid var(--border-solid);
                transition:background 0.15s; text-align:left;
            " onmouseover="this.style.background='var(--error-bg)'"
               onmouseout="this.style.background='transparent'">
                <span class="material-symbols-outlined" style="font-size:17px;">logout</span>
                Dil
            </button>
        </form>
    </div>
</div>
@else
<a href="{{ route('login') }}" style="
    display:flex; align-items:center; gap:6px;
    padding:8px 16px; border-radius:var(--radius-full);
    background:var(--primary); color:white;
    font-size:13px; font-weight:700; text-decoration:none;
    transition:opacity 0.2s;
" onmouseover="this.style.opacity='0.85'"
   onmouseout="this.style.opacity='1'">
    <span class="material-symbols-outlined" style="font-size:16px;">login</span>
    Hyr
</a>
@endauth
            <button onclick="openCart()" class="cart-btn" title="Shporta">
                <span class="material-symbols-outlined" style="font-size:20px;">shopping_cart</span>
                <span class="cart-count" style="{{ $cartCount > 0 ? '' : 'display:none;' }}">
                    {{ $cartCount }}
                </span>
            </button>
        </div>
    </div>
</nav>

<!-- FLASH MESSAGES -->
@if(session('success'))
    <div class="flash flash-success">
        <span class="material-symbols-outlined" style="font-size:18px;">check_circle</span>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flash flash-error">
        <span class="material-symbols-outlined" style="font-size:18px;">error</span>
        {{ session('error') }}
    </div>
@endif

<!-- CONTENT -->
<div class="main-wrap">
    @yield('content')
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
       <div class="footer-brand">
    <img src="{{ asset('images/logo.svg') }}" alt="ProMobile" 
         style="height:24px;width:auto;object-fit:contain;margin-bottom:12px;">
    <p>Dyqani juaj online me produktet më të mira. Cilësi e lartë, çmime të arsyeshme, dorëzim i shpejtë.</p>
</div>
        <div class="footer-col">
            <h4>Navigim</h4>
            <ul>
                <li><a href="{{ url('/') }}">Kryefaqja</a></li>
                <li><a href="{{ url('/shop') }}">Produktet</a></li>
                <li><a href="{{ route('contact') }}">Kontakt</a></li>
                <li><a href="{{ url('/shop?featured=1') }}">Favoritet</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Informata</h4>
            <ul>
                <li><a href="{{ route('terms') }}">Kushtet e Përdorimit</a></li>
                <li><a href="{{ route('privacy') }}">Politika e Privatësisë</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Kontakt</h4>
            <ul>
                <li><a href="mailto:info@promobile.com">info@promobile.com</a></li>
                <li><a href="tel:+38344000000">+383 44 000 000</a></li>
                <li><span>Prishtinë, Kosovë</span></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <span>© {{ date('Y') }} ProMobile. Të gjitha të drejtat e rezervuara.</span>
    </div>
</footer>

<!-- CART + WISHLIST JS -->

<script src="{{ asset('js/shop/layout.js') }}"></script>
<script src="{{ asset('js/shop/pages.js') }}"></script>

    <script>
        (function () {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!tokenMeta) return;
            const token = tokenMeta.getAttribute('content');

            const originalFetch = window.fetch;
            if (originalFetch) {
                window.fetch = function(resource, options) {
                    options = options || {};
                    const method = ((options.method || 'GET') + '').toUpperCase();
                    const headers = new Headers(options.headers || {});
                    if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
                        if (!headers.has('X-CSRF-TOKEN')) headers.set('X-CSRF-TOKEN', token);
                        if (!headers.has('X-Requested-With')) headers.set('X-Requested-With', 'XMLHttpRequest');
                        if (!headers.has('Accept')) headers.set('Accept', 'application/json, text/plain, */*');
                    }
                    options.headers = headers;
                    return originalFetch(resource, options);
                };
            }

            const open = XMLHttpRequest.prototype.open;
            const send = XMLHttpRequest.prototype.send;
            XMLHttpRequest.prototype.open = function(method) {
                this._method = method;
                return open.apply(this, arguments);
            };
            XMLHttpRequest.prototype.send = function() {
                const method = ((this._method || 'GET') + '').toUpperCase();
                if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
                    this.setRequestHeader('X-CSRF-TOKEN', token);
                    this.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    this.setRequestHeader('Accept', 'application/json, text/plain, */*');
                }
                return send.apply(this, arguments);
            };

            if (window.top !== window.self) {
                try { window.top.location = window.self.location; } catch (e) {}
            }
        })();
    </script>
    @stack('scripts')
<!-- BOTTOM NAV MOBILE -->
<div class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ url('/') }}" class="bottom-nav-item {{ request()->is('/') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Kryefaqja</span>
        </a>

        <a href="{{ url('/shop?featured=1') }}" class="bottom-nav-item {{ request()->is('shop') && request('featured') ? 'active' : '' }}">
            <i class="fa-regular fa-heart"></i>
            <span>Favoritet</span>
            <span class="bottom-nav-badge" id="bottomNavWishCount" style="display:none;">0</span>
        </a>

     <a href="{{ url('/profili-im') }}" class="bottom-nav-item {{ request()->is('profili-im') ? 'active' : '' }}">
    <i class="fa-solid fa-bag-shopping"></i>
    <span>Blerjet</span>
</a>

        <button onclick="openCart()" class="bottom-nav-item">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Shporta</span>
            <span class="bottom-nav-badge" id="bottomNavCartCount" style="{{ $cartCount > 0 ? '' : 'display:none;' }}">
                {{ $cartCount }}
            </span>
        </button>
    </div>
</div>
</body>
</html>
