@extends('layouts.shop')

@section('title', 'Produktet — ShopZone')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/products.css') }}">
@endpush

@section('content')

<div class="shop-wrap">

    {{-- HEADER --}}
   <div class="shop-header">
    <h1>
        @if(request('search') && trim(request('search')) !== '')
            Rezultatet për "<span>{{ request('search') }}</span>"
        @elseif(request('featured'))
            <span>Favoritet</span>
        @elseif(request('category') && $activeCategory)
            <span>{{ $activeCategory->name }}</span>
        @else
            Të gjitha <span>Produktet</span>
        @endif
    </h1>
    <p>{{ $products->total() }} produkte gjithsej</p>
</div>
    {{-- FILTERS --}}
    <div class="filters-bar">
        <div class="filters-left">
            <a href="{{ url('/shop') }}"
               class="filter-chip {{ !request('category') && !request('featured') ? 'active' : '' }}">
                Të gjitha
            </a>
            <a href="{{ url('/shop?featured=1') }}"
               class="filter-chip {{ request('featured') ? 'active' : '' }}">
                <i class="fa-solid fa-star" style="font-size:11px;"></i> Favoritet
            </a>
            <div class="filter-sep"></div>
            @foreach($categories as $cat)
                <a href="{{ url('/shop?category='.$cat->id) }}"
                   class="filter-chip {{ request('category') == $cat->id && !request('brand') ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
        <div class="sort-wrapper">
            <form action="{{ url('/shop') }}" method="GET" style="display:flex;">
                @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                @if(request('brand'))    <input type="hidden" name="brand"    value="{{ request('brand') }}"> @endif
                @if(request('featured')) <input type="hidden" name="featured" value="1"> @endif
                <select name="sort" class="sort-select" onchange="this.form.submit()">
                    <option value="">Rendit sipas...</option>
                    <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Çmimi: i ulët - i lartë</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Çmimi: i lartë - i ulët</option>
                    <option value="newest"     {{ request('sort') == 'newest'     ? 'selected' : '' }}>Më të rinjtë</option>
                    <option value="name_asc"   {{ request('sort') == 'name_asc'   ? 'selected' : '' }}>Emri: A-Z</option>
                </select>
            </form>
        </div>
    </div>

    {{-- BRENDET --}}
    @if(isset($categoryBrands) && $categoryBrands->count() > 0)
    <div class="brands-border">
        <div class="brands-bar">
            <a href="{{ url('/shop?category='.request('category')) }}"
               class="brand-chip {{ !request('brand') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group" style="font-size:10px;"></i>
                Të gjitha
            </a>

            @foreach($categoryBrands as $brand)
            <div class="brand-dd-wrap" data-brand-id="{{ $brand->id }}">
                <button type="button"
                    class="brand-chip {{ request('brand') == $brand->id ? 'active' : '' }}"
                    @if(isset($brandSubcategories[$brand->id]) && count($brandSubcategories[$brand->id]) > 0)
                        onclick="event.stopPropagation(); toggleDrop(this)"
                    @else
                        onclick="window.location='{{ url('/shop?category='.request('category').'&brand='.$brand->id) }}'"
                    @endif>
                    {{ $brand->name }}
                    @if(isset($brandSubcategories[$brand->id]) && count($brandSubcategories[$brand->id]) > 0)
                        <i class="fa-solid fa-chevron-down" style="font-size:9px;margin-left:5px;"></i>
                    @endif
                </button>
                <div class="brand-dd-menu">
                    <a href="{{ url('/shop?category='.request('category').'&brand='.$brand->id) }}"
                       class="brand-dd-item {{ request('brand') == $brand->id && !request('subcategory') ? 'active' : '' }}">
                        Të gjitha
                    </a>
                    @if(isset($brandSubcategories[$brand->id]))
                        @foreach($brandSubcategories[$brand->id] as $sub)
                            <a href="{{ url('/shop?category='.request('category').'&brand='.$brand->id.'&subcategory='.$sub) }}"
                               class="brand-dd-item {{ request('subcategory') == $sub ? 'active' : '' }}">
                                {{ $sub }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- GRID --}}
    <div class="products-grid" id="mainProductsGrid">
        @forelse($products as $i => $product)
        @php
            $firstVariant = $product->variants?->first();
            $primaryImg   = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first();
            $prodSrc = null;
            if ($primaryImg) {
                $prodSrc = asset('storage/' . $primaryImg->image_path);
            } elseif ($firstVariant && !empty($firstVariant->image_path)) {
                $prodSrc = asset('storage/' . $firstVariant->image_path);
            }
            if ($firstVariant) {
                $hasSale    = $firstVariant->sale_price && $firstVariant->sale_price < $firstVariant->price;
                $finalPrice = $hasSale ? $firstVariant->sale_price : $firstVariant->price;
                $oldPrice   = $hasSale ? $firstVariant->price : null;
            } else {
                $hasSale    = $product->sale_price && $product->sale_price < $product->price;
                $finalPrice = $hasSale ? $product->sale_price : $product->price;
                $oldPrice   = $hasSale ? $product->price : null;
            }
            $colors = $product->variants
                ? $product->variants->filter(fn($v) => $v->color_name)->unique('color_name')->values()
                : collect();
        @endphp

        <a href="{{ route('shop.product', $product->uuid) }}" class="product-card" style="animation-delay:{{ $i * 0.04 }}s">
            <div class="product-img">
                @if($prodSrc)
                    <img src="{{ $prodSrc }}" alt="{{ $product->name }}" loading="lazy">
                @else
                    <div class="product-img-placeholder"><i class="fa-solid fa-box"></i></div>
                @endif

                <button class="pm-wish-btn"
                        data-id="{{ $product->id }}"
                        data-name="{{ addslashes($product->name) }}"
                        data-price="{{ number_format($finalPrice, 2) }}"
                        data-img="{{ $prodSrc ?? '' }}"
                        data-url="{{ route('shop.product', $product->uuid) }}"
                        data-cat="{{ $product->category->name ?? '' }}"
                        onclick="event.preventDefault(); toggleWish(this)">
                    <i class="fa-regular fa-heart"></i>
                </button>

                <div class="product-badges">
                    @if($product->featured)
                        <span class="badge badge-featured"><i class="fa-solid fa-star" style="font-size:9px;"></i> Top</span>
                    @endif
                    @if($hasSale)
                        <span class="badge badge-sale">SALE</span>
                    @endif
                </div>
            </div>

      <div class="product-info">
    <div>
        <span class="product-cat">{{ $product->category->name ?? '—' }}</span>
        <span class="product-brand">{{ $product->brand->name ?? '' }}</span>
    </div>

    <div class="product-name">{{ $product->name }}</div>

    @if($colors->count() > 0)
    <div class="product-colors">
        @foreach($colors as $color)
            <span class="product-color-dot" title="{{ $color->color_name }}"
                  style="background:{{ $color->color_hex ?? '#ccc' }};"></span>
        @endforeach
    </div>
    @endif

    {{-- ✅ Çmimi dhe butoni në footer --}}
<div class="product-footer">
    <div class="product-price">
        @if($oldPrice)
            <span class="old">{{ number_format($oldPrice, 2) }} €</span>
        @endif
        @if($product->variants && $product->variants->count() > 0)
            <span style="font-size:11px;font-weight:500;color:#6e6e73;margin-right:2px;">nga</span>
        @endif
        {{ number_format($finalPrice, 2) }} €
    </div>

    @if($product->variants && $product->variants->count() > 0)
        {{-- Ka variante — shko te detajet --}}
        <button
            class="add-btn"
            onclick="event.preventDefault(); window.location='{{ route('shop.product', $product->uuid) }}'"
            type="button">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    @else
        {{-- Nuk ka variante — shto direkt në shportë --}}
        <button
            class="add-btn"
            id="btn-{{ $product->id }}"
            onclick="event.preventDefault(); addToCart({{ $product->id }}, this)"
            type="button">
            <i class="fa-solid fa-plus"></i>
        </button>
    @endif
</div>
</div>
        </a>

        @empty
            @if(!request('featured'))
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                <h3>Nuk u gjetën produkte</h3>
                <p>Provo të ndryshosh filtrat ose kërkon diçka tjetër.</p>
                <a href="{{ url('/shop') }}" class="btn btn-outline" style="margin-top:20px;display:inline-flex;">Pastro filtrat</a>
            </div>
            @endif
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($products->hasPages())
    <div class="pagination-wrap">
        @if($products->onFirstPage())
            <span class="page-btn" style="opacity:0.3;cursor:default;"><i class="fa-solid fa-chevron-left"></i></span>
        @else
            <a href="{{ $products->previousPageUrl() }}" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
        @endif
        @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
            <a href="{{ $url }}" class="page-btn {{ $page == $products->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        @if($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
        @else
            <span class="page-btn" style="opacity:0.3;cursor:default;"><i class="fa-solid fa-chevron-right"></i></span>
        @endif
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
    window.brandSubcategories = @json($brandSubcategories ?? []);
    window.currentCategory    = "{{ request('category') }}";
    window.currentBrand       = "{{ request('brand') }}";
    window.currentSubcat      = "{{ request('subcategory') }}";
</script>
<script src="{{ asset('js/shop/product/product-main.js') }}"></script>
@endpush