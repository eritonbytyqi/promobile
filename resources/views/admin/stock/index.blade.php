@extends('layouts.admin')

@section('title', 'Stoku')
@section('page-title', 'Stoku')

@section('breadcrumb')
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>Stoku</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/stock.css') }}">
@endpush

@section('content')

{{-- ── STATS ── --}}
<div class="st-stats">
    <div class="st-stat">
        <div class="st-stat-icon" style="background:rgba(124,111,255,0.1);color:var(--accent);">
            <i class="fa-solid fa-box"></i>
        </div>
        <div>
            <div class="st-stat-num">{{ $stats['total_products'] }}</div>
            <div class="st-stat-lbl">Produkte</div>
        </div>
    </div>
    <div class="st-stat">
        <div class="st-stat-icon" style="background:rgba(0,229,160,0.1);color:var(--accent3);">
            <i class="fa-solid fa-swatchbook"></i>
        </div>
        <div>
            <div class="st-stat-num">{{ $stats['total_variants'] }}</div>
            <div class="st-stat-lbl">Variante</div>
        </div>
    </div>
    <div class="st-stat">
        <div class="st-stat-icon" style="background:rgba(255,149,0,0.1);color:#ff9500;">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <div class="st-stat-num" style="color:#ff9500;">{{ $stats['low_stock'] }}</div>
            <div class="st-stat-lbl">Stok i ulët (≤3)</div>
        </div>
    </div>
    <div class="st-stat">
        <div class="st-stat-icon" style="background:rgba(248,113,113,0.1);color:var(--danger);">
            <i class="fa-solid fa-ban"></i>
        </div>
        <div>
            <div class="st-stat-num" style="color:var(--danger);">{{ $stats['out_of_stock'] }}</div>
            <div class="st-stat-lbl">Jashtë stoku</div>
        </div>
    </div>
</div>

{{-- ── TOOLBAR ── --}}
<div class="st-toolbar">
    <div class="st-filters">
        <a href="{{ route('admin.stock.index') }}"
           class="st-filter {{ !$filter ? 'active' : '' }}">
            Të gjitha
        </a>
        <a href="{{ route('admin.stock.index', ['filter'=>'low']) }}"
           class="st-filter warn {{ $filter==='low' ? 'active' : '' }}">
            <i class="fa-solid fa-triangle-exclamation"></i> Stok i ulët
        </a>
        <a href="{{ route('admin.stock.index', ['filter'=>'out']) }}"
           class="st-filter danger {{ $filter==='out' ? 'active' : '' }}">
            <i class="fa-solid fa-ban"></i> Jashtë stoku
        </a>
    </div>
    <form method="GET" action="{{ route('admin.stock.index') }}" class="st-search">
        @if($filter)<input type="hidden" name="filter" value="{{ $filter }}">@endif
        <input type="text" name="search" value="{{ $search }}" placeholder="Kërko produkt...">
        <button type="submit" class="btn btn-primary" style="padding:6px 12px;font-size:13px;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>
</div>

{{-- ── ACCORDION CARDS ── --}}
<div class="st-cards">

@forelse($products as $product)
@php
    $productImg  = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first();
    $imgSrc      = $productImg ? asset('storage/'.$productImg->image_path) : null;
    $hasVariants = $product->variants->count() > 0;

    // Total stok
    $totalStock = $hasVariants
        ? $product->variants->sum('stock')
        : ($product->stock ?? 0);

    // Badge ngjyra per total
    $totalColor = $totalStock <= 0
        ? 'var(--danger)'
        : ($totalStock <= 3 ? '#ff9500' : 'var(--accent3)');

    // Mini badges për header
    $hasOut = $hasVariants
        ? $product->variants->contains(fn($v) => $v->stock <= 0)
        : ($product->stock ?? 0) <= 0;
    $hasLow = $hasVariants
        ? $product->variants->contains(fn($v) => $v->stock > 0 && $v->stock <= 3)
        : (($product->stock ?? 0) > 0 && ($product->stock ?? 0) <= 3);

    // Auto-hap nëse ka problem stoku
    $autoOpen = $hasOut || $hasLow;
@endphp

<div class="st-card" id="stcard_{{ $product->id }}">

    {{-- ── HEADER (klikueshëm) ── --}}
    <div class="st-card-row" onclick="toggleCard({{ $product->id }})">

        {{-- Foto --}}
        <div class="st-card-img">
            @if($imgSrc)
                <img src="{{ $imgSrc }}" alt="">
            @else
                <i class="fa-solid fa-box"></i>
            @endif
        </div>

        {{-- Emri + meta --}}
        <div>
            <div class="st-card-name">{{ $product->name }}</div>
            <div class="st-card-sub">
                {{ $product->category?->name }}
                @if($product->brand) · {{ $product->brand->name }} @endif
                @if($hasVariants)
                    · {{ $product->variants->count() }} variante
                @endif
            </div>
        </div>

        {{-- Mini badges --}}
        <div class="st-mini-badges">
            @if($hasOut)
                <span class="st-mini-badge out">Jashtë stoku</span>
            @endif
            @if($hasLow)
                <span class="st-mini-badge low">Stok i ulët</span>
            @endif
            @if(!$hasOut && !$hasLow)
                <span class="st-mini-badge ok">Në stok</span>
            @endif
        </div>

        {{-- Total stoku --}}
        <div class="st-card-total" style="color:{{ $totalColor }};">
            {{ $totalStock }}
            <span style="font-size:11px;font-weight:400;color:var(--text-muted);">copë</span>
        </div>

        {{-- Chevron --}}
        <a href="{{ route('admin.stock.history', $product->id) }}"
   style="color:var(--text-muted);font-size:13px;padding:6px 10px;
          border-radius:8px;border:1px solid var(--border);
          text-decoration:none;display:flex;align-items:center;gap:5px;
          background:var(--surface2);transition:all 0.15s;white-space:nowrap;"
   onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
   onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'"
   onclick="event.stopPropagation()">
    <i class="fa-solid fa-clock-rotate-left"></i>
    Historia
</a>

<i class="fa-solid fa-chevron-down st-chevron"></i>
    </div>

    {{-- ── DROPDOWN — variantet ── --}}
    <div class="st-dropdown">

        @if($hasVariants)

        {{-- Header kolonave --}}
        <div class="st-drop-head">
            <span></span>
            <span>Ngjyra / Storage</span>
            <span>Çmimi</span>
            <span>Statusi</span>
            <span>Stoku</span>
            <span></span>
        </div>

        {{-- Rreshtat e varianteve --}}
        @foreach($product->variants->sortBy(['color_name','storage']) as $variant)
        @php
            $badge    = $variant->stock <= 0 ? 'out' : ($variant->stock <= 3 ? 'low' : 'ok');
            $badgeTxt = $variant->stock <= 0 ? 'Jashtë stoku' : ($variant->stock <= 3 ? 'Stok i ulët' : 'Në stok');
            $price    = ($variant->sale_price && $variant->sale_price < $variant->price)
                        ? $variant->sale_price : $variant->price;
        @endphp
        <div class="st-var-row">

            {{-- Ngjyra dot --}}
            @if($variant->color_hex)
                <div class="st-dot" style="background:{{ $variant->color_hex }};"></div>
            @else
                <div></div>
            @endif

            {{-- Emri variantit --}}
            <div style="font-size:13px;font-weight:500;">
                {{ $variant->color_name ?? '' }}
                @if($variant->storage)
                    <span style="color:var(--text-muted);margin-left:3px;font-weight:400;">
                        {{ $variant->storage }}
                    </span>
                @endif
            </div>

            {{-- Çmimi --}}
            <div style="font-weight:600;font-size:13px;">
                {{ number_format($price, 2) }} €
            </div>

            {{-- Statusi --}}
            <span class="st-badge {{ $badge }}">{{ $badgeTxt }}</span>

            {{-- Controls --}}
            <div class="st-ctrl">
                <button class="st-ctrl-btn" onclick="ch('v{{ $variant->id }}',-1)">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <input class="st-ctrl-inp" id="input_v{{ $variant->id }}"
                       type="number" value="{{ $variant->stock }}" min="0">
                <button class="st-ctrl-btn" onclick="ch('v{{ $variant->id }}',1)">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>

            {{-- Ruaj --}}
            <button class="st-save" id="btn_v{{ $variant->id }}"
                    onclick="saveVariant({{ $variant->id }})">
                Ruaj
            </button>

        </div>
        @endforeach

        @else

        {{-- Produkt pa variante --}}
        @php
            $st       = $product->stock ?? 0;
            $badge    = $st <= 0 ? 'out' : ($st <= 3 ? 'low' : 'ok');
            $badgeTxt = $st <= 0 ? 'Jashtë stoku' : ($st <= 3 ? 'Stok i ulët' : 'Në stok');
            $price    = ($product->sale_price && $product->sale_price < $product->price)
                        ? $product->sale_price : $product->price;
        @endphp
        <div class="st-simple-row">
            <div style="font-size:13px;color:var(--text-muted);">Pa variante</div>
            <div style="font-weight:600;">{{ number_format($price, 2) }} €</div>
            <span class="st-badge {{ $badge }}">{{ $badgeTxt }}</span>
            <div class="st-ctrl">
                <button class="st-ctrl-btn" onclick="ch('p{{ $product->id }}',-1)">
                    <i class="fa-solid fa-minus"></i>
                </button>
                <input class="st-ctrl-inp" id="input_p{{ $product->id }}"
                       type="number" value="{{ $st }}" min="0">
                <button class="st-ctrl-btn" onclick="ch('p{{ $product->id }}',1)">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
            <button class="st-save" id="btn_p{{ $product->id }}"
                    onclick="saveProduct({{ $product->id }})">
                Ruaj
            </button>
        </div>

        @endif
    </div>{{-- /dropdown --}}

</div>{{-- /card --}}

@empty
<div style="text-align:center;padding:60px;color:var(--text-muted);
            background:var(--surface);border:1px solid var(--border);border-radius:14px;">
    <i class="fa-solid fa-box" style="font-size:32px;opacity:0.3;display:block;margin-bottom:14px;"></i>
    Nuk u gjet asnjë produkt.
</div>
@endforelse

</div>{{-- /cards --}}

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/stock.js') }}"></script>
@endpush
