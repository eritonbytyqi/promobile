@extends('layouts.shop')

@section('title', 'ShopZone — Kryefaqja')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/home.css') }}">
@endpush

@section('content')
@php
    $keywords = ['accessor', 'accessories', 'accesor', 'aksesor', 'aksesorë'];

    $accessoriesCategory = \App\Models\Category::where(function ($query) use ($keywords) {
        foreach ($keywords as $word) {
            $query->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($word) . '%']);
        }
    })->first();

    $bentoProduct = $featured->first();
    $bentoImg = $bentoProduct?->images?->firstWhere('is_primary', true) ?? $bentoProduct?->images?->first();
    $bentoSrc = $bentoImg ? asset('storage/' . $bentoImg->image_path) : null;
    $bentoBanner = $banners->first();
@endphp

{{-- CAROUSEL --}}
@include('shop.partials.hero-carousel', ['banners' => $banners])

{{-- KATEGORITË --}}
@if($categories->count() > 0)
<div class="pm-cats-section">
    <div class="pm-section-label">Shfleto sipas kategorisë</div>
    <div class="pm-section-header">
        <h2 class="pm-section-title" style="margin-bottom:0;">Kategoritë</h2>
        <a href="{{ url('/shop') }}" class="pm-see-all">
            Shiko të gjitha <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i>
        </a>
    </div>

    <div class="pm-cats-grid">
        @foreach($categories as $cat)
            <a href="{{ url('/shop?category=' . $cat->id) }}" class="pm-cat-card">
                <div class="pm-cat-icon">
                    <i class="fa-solid {{ $cat->icon ?? 'fa-tag' }}"></i>
                </div>
                <span class="pm-cat-name">{{ $cat->name }}</span>
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- FEATURED --}}
@if($featured->count() > 0)
<div class="pm-products-section">
    <div class="pm-section-header">
        <div>
            <div class="pm-section-label"><i class="fa-solid fa-star"></i>      
        </div>
            <h2 class="pm-section-title" style="margin-bottom:0;">Top Produktet</h2>
        </div>
        <a href="{{ url('/shop?featured=1') }}" class="pm-see-all">
            Shiko të gjitha <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>


    <div class="pm-products-grid">
        @foreach($featured as $product)
            @php
                $pImg = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first();
                $pSrc = $pImg ? asset('storage/' . $pImg->image_path) : null;
                $pHasSale = $product->sale_price && $product->sale_price < $product->price;
                $pPrice = $pHasSale ? $product->sale_price : $product->price;
                $colors = $product->variants
                    ? $product->variants->filter(fn($v) => $v->color_name)->unique('color_name')->values()
                    : collect();
            @endphp

            <a href="{{ route('shop.product', $product->uuid) }}" class="pm-product-card">
                <div class="pm-product-img">
                    @if($pSrc)
                        <img src="{{ $pSrc }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div class="pm-product-img-ph"><i class="fa-solid fa-box"></i></div>
                    @endif

                    <button
                        class="pm-wish-btn"
                        data-id="{{ $product->id }}"
                        data-name="{{ e($product->name) }}"
                        data-price="{{ number_format($pPrice, 2, '.', '') }}"
                        data-img="{{ $pSrc ?? '' }}"
                        data-url="{{ route('shop.product', $product->uuid) }}"
                        data-cat="{{ e($product->category->name ?? '') }}"
                        onclick="event.preventDefault(); toggleWish(this)"
                        type="button"
                    >
                        <i class="fa-solid fa-heart"></i>
                    </button>

                    @if($product->featured)
                        <span class="pm-product-badge pm-badge-featured">
                            <i class="fa-solid fa-star" style="font-size:9px;"></i> Top
                        </span>
                    @endif

                    @if($pHasSale)
                        <span class="pm-product-badge pm-badge-sale" style="left:auto;right:12px;">SALE</span>
                    @endif
                </div>

                <div class="pm-product-info">
                    <div class="pm-product-cat">{{ $product->category->name ?? '—' }}</div>
                    <div class="pm-product-name">{{ $product->name }}</div>
                    <div class="pm-product-brand">{{ $product->brand->name ?? '' }}</div>

                    @if($colors->count() > 0)
                        <div class="pm-card-colors">
                            @foreach($colors as $color)
                                <span
                                    class="pm-card-color"
                                    title="{{ $color->color_name }}"
                                    style="background:{{ $color->color_hex ?? '#ccc' }};"
                                ></span>
                            @endforeach
                        </div>
                    @endif

                    <div class="pm-product-footer">
                        <div>
                            @if($pHasSale)
                                <span class="pm-product-old">{{ number_format($product->price, 2) }} €</span>
                            @endif

                            <span class="pm-product-price">
                                @if($product->variants && $product->variants->count() > 0)
                                    <span style="font-size:11px;font-weight:500;color:#6e6e73;">nga</span>
                                @endif
                                {{ number_format($pPrice, 2) }} €
                            </span>
                        </div>

                  <button
    class="pm-add-btn"
    onclick="event.preventDefault(); 
    @if($product->variants && $product->variants->count() > 0)
        window.location='{{ route('shop.product', $product->uuid) }}'
    @else
        addToCart({{ $product->id }}, this)
    @endif"
    type="button"
>
    <i class="fa-solid fa-plus"></i>
</button>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- BENTO GRID --}}
<div class="pm-bento">
    <div class="pm-bento-grid">
    @if(cache('upgrade_active', true))
    <a href="{{ cache('upgrade_url', '/shop') }}" class="pm-bento-hero pm-upgrade-hero">
        <div
            class="pm-bento-hero-img pm-upgrade-hero-bg"
            style="
                {{ cache('upgrade_image')
                    ? "background-image:url('" . asset('storage/' . cache('upgrade_image')) . "');"
                    : "background:linear-gradient(135deg,#0b1220 0%,#13213f 55%,#1e293b 100%);" }}
            "
        ></div>

        <div class="pm-upgrade-overlay"></div>

        <div class="pm-bento-hero-content">
            @if(cache('upgrade_badge'))
                <div class="pm-bento-hero-badge">{{ cache('upgrade_badge') }}</div>
            @endif

            @if(cache('upgrade_title'))
                <div class="pm-bento-hero-title">{{ cache('upgrade_title') }}</div>
            @endif

            @if(cache('upgrade_subtitle'))
                <div class="pm-upgrade-hero-sub">{{ cache('upgrade_subtitle') }}</div>
            @endif

            @if(cache('upgrade_button_text'))
                <span class="pm-bento-hero-btn">
                    {{ cache('upgrade_button_text') }}
                    <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                </span>
            @endif
        </div>
    </a>
@endif
<a href="{{ route('contact') }}" class="pm-bento-card support">
    <div>
        <div class="pm-bento-card-title">
            🛡️ Bli me besim
        </div>

        <div class="pm-bento-card-sub">
            ✔️ Dërgesë e shpejtë në Kosovë<br>
            ✔️ Pagesë e sigurt (Kartelë / Cash)<br>
            ✔️ Produkte origjinale
        </div>
    </div>

    <span class="pm-bento-card-link">
        Na kontaktoni →
    </span>
</a>
         
    </div>
</div>

{{-- AKSESORËT --}}
@if($accessories->count() > 0)
<div class="pm-more-cats-section">
    <div class="pm-section-header">
        <div>
            <div class="pm-section-label">Eksploro më shumë</div>
            <h2 class="pm-section-title" style="margin-bottom:0;">Aksesorë të rekomanduar</h2>
        </div>

        @if($accessoriesCategory)
            <a href="{{ url('/shop?category=' . $accessoriesCategory->id) }}" class="pm-see-all">
                Shiko të gjitha <i class="fa-solid fa-arrow-right" style="font-size:11px;"></i>
            </a>
        @endif
    </div>

    <div class="pm-more-cats-grid pm-more-products-grid">
        @foreach($accessories->take(6) as $product)
            @php
                $pImg = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first();
                $pSrc = $pImg ? asset('storage/' . $pImg->image_path) : null;
                $pHasSale = $product->sale_price && $product->sale_price < $product->price;
                $pPrice = $pHasSale ? $product->sale_price : $product->price;
            @endphp

            <a href="{{ route('shop.product', $product->uuid) }}" class="pm-more-product-card">
                <div class="pm-more-product-thumb">
                    @if($pSrc)
                        <img src="{{ $pSrc }}" alt="{{ $product->name }}" loading="lazy">
                    @else
                        <div class="pm-more-product-ph"><i class="fa-solid fa-plug"></i></div>
                    @endif
                </div>

                <div class="pm-more-product-name">{{ $product->name }}</div>
                <div class="pm-more-product-price">{{ number_format($pPrice, 2) }} €</div>
            </a>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
    <script src="{{ asset('js/shop/home.js') }}"></script>
@endpush