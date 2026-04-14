@extends('layouts.shop')

@section('title', $product->name . ' — ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/product-detail.css') }}">
@endpush

@section('content')

@php
$variants      = $product->variants ?? collect();
$variantImages = $product->variantImages ?? collect();
$hasVariants   = $variants->count() > 0;
$images        = $product->images ?? collect();
$specs         = $product->specs ?? collect();

$garancia = $specs->firstWhere('spec_key', 'Garancia');
$dergesa  = $specs->firstWhere('spec_key', 'Dërgesa');

$variantGroups = $variants->groupBy(function ($v) {
    return strtolower(trim(($v->color_hex ?? ''))) . '||' . strtolower(trim(($v->color_name ?? '')));
});

$variantImageGroups = $variantImages->groupBy(function ($img) {
    return strtolower(trim(($img->color_hex ?? ''))) . '||' . strtolower(trim(($img->color_name ?? '')));
});

$allColorKeys = $variantGroups->keys()->merge($variantImageGroups->keys())->unique();

$colorGroups = $allColorKeys->map(function ($key) use ($variantGroups, $variantImageGroups) {
    [$hex, $name] = array_pad(explode('||', $key, 2), 2, '');
    $groupVariants = $variantGroups->get($key, collect());
    $groupImages   = $variantImageGroups->get($key, collect());

    $imgs = $groupImages
        ->sortBy([['is_primary','desc'],['sort_order','asc'],['id','asc']])
        ->pluck('image_path')->filter()->unique()->values()
        ->map(fn($p) => asset('storage/' . $p))->toArray();

    $storages = $groupVariants->map(fn($v) => [
        'id'          => $v->id,
        'storage'     => $v->storage ?? '',
        'price'       => (float)($v->price ?? 0),
        'base_price'  => (float)($v->base_price ?? 0),
        'extra_price' => (float)($v->extra_price ?? 0),
        'sale_price'  => $v->sale_price !== null ? (float)$v->sale_price : null,
        'stock'       => (int)($v->stock ?? 0),
    ])->values()->toArray();

    return ['color_hex' => $hex, 'color_name' => $name, 'images' => $imgs, 'storages' => $storages];
})->values();

$productImages = $images->sortByDesc('is_primary')->values();
$mainSrc = null;
$primary = $productImages->firstWhere('is_primary', true) ?? $productImages->first();
if ($primary) {
    $mainSrc = asset('storage/' . $primary->image_path);
} elseif (!empty($product->image)) {
    $mainSrc = asset('storage/' . $product->image);
}

$firstVariant = $variants->first();
$basePrice    = $product->price ?? 0;
$baseOldPrice = ($product->sale_price && $product->sale_price < $product->price) ? $product->price : null;
$basePrice    = $baseOldPrice ? $product->sale_price : $basePrice;
$initStock    = (int)($product->stock ?? 0);
$hasVariants  = $variants->count() > 0 && $colorGroups->count() > 0;
@endphp

<div class="pm-wrap">

    <a href="{{ url('/shop') }}" class="pm-back">
        <i class="fa-solid fa-chevron-left" style="font-size:12px;"></i> Produktet
    </a>

    <div class="pm-grid">

        {{-- GALERIA --}}
        <div class="pm-gallery">
            <div class="pm-img-main" id="mainImgWrap">
                @if($mainSrc)
                    <img id="mainImg" src="{{ $mainSrc }}" alt="{{ $product->name }}">
                @else
                    <div class="pm-img-placeholder"><i class="fa-solid fa-box"></i></div>
                @endif
            </div>

            @if($productImages->count() > 1)
            <div class="pm-thumbs" id="productThumbs">
                @foreach($productImages as $i => $img)
                <button type="button" class="pm-thumb {{ $i === 0 ? 'active' : '' }}"
                        onclick="switchImage(this, '{{ asset('storage/'.$img->image_path) }}')">
                    <img src="{{ asset('storage/'.$img->image_path) }}" alt="foto {{ $i+1 }}" loading="lazy">
                </button>
                @endforeach
            </div>
            @endif

            <div id="colorGallerySection" style="display:none;">
                <div class="pm-thumbs" id="colorGallery"></div>
            </div>
        </div>

        {{-- INFO --}}
        <div class="pm-info">

            <h1 class="pm-title">{{ $product->name }}</h1>

            @if($product->description)
                <p class="pm-desc">{{ $product->description }}</p>
            @endif

            <div class="pm-price-wrap">
                <div class="pm-price" id="priceMain">{{ number_format($basePrice, 2) }} €</div>
                <div class="pm-price-old" id="priceOld" style="{{ $baseOldPrice ? '' : 'display:none;' }}">
                    @if($baseOldPrice){{ number_format($baseOldPrice, 2) }} €@endif
                </div>
                <span class="pm-price-badge" id="priceBadge" style="{{ $baseOldPrice ? '' : 'display:none;' }}">
                    @if($baseOldPrice)-{{ round((($baseOldPrice - $basePrice) / $baseOldPrice) * 100) }}%@endif
                </span>
            </div>

            <div class="pm-stock">
                <span class="pm-stock-dot {{ $initStock > 10 ? 'in' : ($initStock > 0 ? 'low' : 'out') }}" id="stockDot"></span>
                <span id="stockText">
                    @if($initStock > 10) Në stok — {{ $initStock }} të mbetur
                    @elseif($initStock > 0) Vetëm {{ $initStock }} të mbetur!
                    @else Jashtë stoku
                    @endif
                </span>
            </div>

            @if($hasVariants)
            <div class="pm-toast" id="variantToast">
                <i class="fa-solid fa-hand-pointer" style="color:var(--pm-blue);flex-shrink:0;"></i>
                <span id="variantToastText">Zgjedh ngjyrën dhe storage-in për çmimin e saktë</span>
            </div>

            @if($colorGroups->count() > 0)
            <div class="pm-section">
                <div class="pm-section-label">Finish. <span id="colorLabel">Zgjidh ngjyrën tënde.</span></div>
                <div class="pm-colors">
                    @foreach($colorGroups as $cg)
                    <div class="pm-color" data-color="{{ $cg['color_name'] }}"
                         onclick="selectColor(this, '{{ $cg['color_name'] }}')">
                        <div class="pm-color-dot" style="background:{{ $cg['color_hex'] ?? '#ccc' }};">
                            <i class="fa-solid fa-check pm-color-check"></i>
                        </div>
                        <span class="pm-color-label">{{ $cg['color_name'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="pm-section" id="storageSection" style="display:none;">
                <div class="pm-section-label">Storage. <span id="storageLabel">Sa hapësirë të nevojitet?</span></div>
                <div class="pm-storages" id="storageGrid"></div>
            </div>
            @endif

            {{-- BUTONI --}}
            <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
                <button id="orderBtn" type="button"
                        class="pm-btn-primary {{ ($initStock <= 0 || $hasVariants) ? 'disabled' : '' }}"
{{ ($initStock <= 0 || $hasVariants) ? 'disabled' : '' }}
                        style="margin-bottom:0;flex:1;"
                        onclick="addCurrentProductToCart(this)">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span id="orderBtnText">Shto në shportë</span>
                </button>

                <button id="detailWishBtn" type="button"
                        data-id="{{ $product->id }}"
                        data-name="{{ addslashes($product->name) }}"
                        data-price="{{ number_format($basePrice, 2) }}"
                        data-img="{{ $mainSrc ?? '' }}"
                        data-url="{{ route('shop.product', $product->uuid) }}"
                        data-cat="{{ $product->category->name ?? '' }}"
                        onclick="toggleWish(this)"
                        style="width:54px;height:54px;flex-shrink:0;border-radius:50%;background:#f5f5f7;border:1px solid rgba(0,0,0,0.1);display:flex;align-items:center;justify-content:center;font-size:20px;color:#ccc;cursor:pointer;transition:all 0.22s cubic-bezier(.4,0,.2,1);">
                    <i class="fa-regular fa-heart"></i>
                </button>
            </div>

            <div class="pm-delivery">
                <div class="pm-delivery-item">
                    <i class="fa-solid fa-truck" style="color:var(--pm-blue);"></i>
                    {{ $dergesa ? $dergesa->spec_value : 'Dërgesa Falas' }}
                </div>
                @if($garancia)
                <div class="pm-delivery-item">
                    <i class="fa-solid fa-shield-halved" style="color:var(--pm-blue);"></i>
                    Garanci {{ $garancia->spec_value }}
                </div>
                @endif
            </div>

            @if($specs->count() > 0)
            <div class="pm-specs">
                <div class="pm-specs-head"><i class="fa-solid fa-list-check"></i> Karakteristikat Teknike</div>
                @foreach($specs as $spec)
                <div class="pm-spec-row">
                    <span class="sk">{{ $spec->spec_key }}</span>
                    <span class="sv">{{ $spec->spec_value }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="pm-details">
                <div class="pm-details-head">Detajet e Produktit</div>
                @if($product->category)
                <div class="pm-detail-row">
                    <span class="dk">Kategoria</span><span class="dv">{{ $product->category->name }}</span>
                </div>
                @endif
                @if($product->brand)
                <div class="pm-detail-row">
                    <span class="dk">Brendi</span><span class="dv">{{ $product->brand->name }}</span>
                </div>
                @endif
                @if(!$hasVariants)
                <div class="pm-detail-row">
                    <span class="dk">Stoku</span>
                    <span class="dv">
                        @if(($product->stock ?? 0) > 0) {{ $product->stock }} copë
                        @else <span style="color:#ff3b30;">Jashtë stoku</span>
                        @endif
                    </span>
                </div>
                @endif
                <div class="pm-detail-row">
                    <span class="dk">Statusi</span>
                    <span class="dv">
                        @if($product->is_active)
                            <span style="color:#34c759;">● Aktiv</span>
                        @else
                            <span style="color:#8e8e93;">Joaktiv</span>
                        @endif
                    </span>
                </div>
            </div>

        </div>
    </div>

    {{-- RELATED --}}
    @if(isset($related) && $related->count() > 0)
    <div class="pm-related">
        <h2 class="pm-related-title">Aksesore të rekomanduar</h2>
        <div class="pm-related-grid">
            @foreach($related as $rel)
            @php
                $relImg   = $rel->images?->firstWhere('is_primary', true) ?? $rel->images?->first();
                $relSrc   = $relImg ? asset('storage/'.$relImg->image_path) : ($rel->image ? asset('storage/'.$rel->image) : null);
                $relGroup = $rel->variants?->first();
                $relPrice = $relGroup
                    ? (($relGroup->sale_price && $relGroup->sale_price < $relGroup->price) ? $relGroup->sale_price : $relGroup->price)
                    : (($rel->sale_price && $rel->sale_price < $rel->price) ? $rel->sale_price : $rel->price);
            @endphp
            <a href="{{ route('shop.product', $rel->uuid) }}" class="pm-related-card">
                <div class="pm-related-img">
                    @if($relSrc)
                        <img src="{{ $relSrc }}" alt="{{ $rel->name }}" loading="lazy">
                    @else
                        <div class="pm-related-img-ph"><i class="fa-solid fa-box"></i></div>
                    @endif
                </div>
                <div class="pm-related-info">
                    <div class="pm-related-name">{{ $rel->name }}</div>
                    <div class="pm-related-price">{{ number_format($relPrice, 2) }} €</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- LIGHTBOX --}}
<div class="pm-lightbox" id="lightbox">
    <button class="pm-lb-close" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button>
    <button class="pm-lb-nav pm-lb-prev" onclick="lbNav(-1)" id="lbPrev" style="display:none;"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="pm-lb-nav pm-lb-next" onclick="lbNav(1)"  id="lbNext" style="display:none;"><i class="fa-solid fa-chevron-right"></i></button>
    <img id="lbImg" src="" alt="" style="animation:lbIn 0.25s ease;">
</div>

{{-- POPUP --}}
<div class="pm-popup-overlay" id="cartPopup">
    <div class="pm-popup-box">
        <button class="pm-popup-close" onclick="closeCartPopup()"><i class="fa-solid fa-xmark"></i></button>
        <div class="pm-popup-icon">✓</div>
        <div class="pm-popup-title">U shtua në shportë!</div>
        <div class="pm-popup-sub">Produkti u shtua me sukses. Çfarë dëshiron të bësh tani?</div>
        <div class="pm-popup-btns">
            <a href="/cart/checkout" class="pm-popup-btn-primary">
                <i class="fa-solid fa-credit-card"></i> Vazhdo me pagesën
            </a>
            <button class="pm-popup-btn-secondary" onclick="closeCartPopup()">
                <i class="fa-solid fa-arrow-left"></i> Vazhdo blerjen
            </button>
            <div style="border-top:1px solid #f0f0f0;margin-top:8px;padding-top:12px;text-align:center;">
                @auth
                    <a href="{{ route('profile.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:var(--primary);text-decoration:none;">
                        <span class="material-symbols-outlined" style="font-size:16px;">person</span>
                        Shiko porositë & profilin tim
                    </a>
                @else
                    <p style="font-size:12px;color:#6e6e73;margin-bottom:8px;">Kyçu për të ruajtur porositë dhe të dhënat tuaja</p>
                    <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:var(--primary);color:white;border-radius:999px;font-size:13px;font-weight:700;text-decoration:none;">
                        <span class="material-symbols-outlined" style="font-size:15px;">login</span> Kyçu tani
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    window.productDetailData = {
        colorGroups: @json($colorGroups),
        productId: {{ $product->id }},
        basePrice: {{ (float) $basePrice }},
        baseOld: {!! $baseOldPrice ? (float)$baseOldPrice : 'null' !!},
        baseStock: {{ (int)($product->stock ?? 0) }},
        productImages: [
            @foreach($productImages as $img)
                '{{ asset('storage/'.$img->image_path) }}',
            @endforeach
        ],
        colorImages: [
            @foreach($colorGroups as $cg)
                @foreach($cg['images'] as $imgSrc)
                    '{{ $imgSrc }}',
                @endforeach
            @endforeach
        ]
    };
</script>
<script src="{{ asset('js/shop/product-detail.js') }}"></script>
