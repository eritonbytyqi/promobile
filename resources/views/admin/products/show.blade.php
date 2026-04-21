@extends('layouts.admin')

@section('title', $product->name)
@section('page-title', 'Detajet e Produktit')

@section('breadcrumb')
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <a href="{{ route('admin.products.index') }}">Produktet</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>{{ $product->name }}</span>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products.css') }}">
<style>
.show-grid { display:grid; grid-template-columns:420px 1fr; gap:24px; align-items:start; }
.gallery-card { position:relative; }
.main-image-wrap { width:100%; aspect-ratio:1/1; background:var(--bg,#f8f9fa); border-radius:14px; overflow:hidden; display:flex; align-items:center; justify-content:center; margin-bottom:12px; border:1px solid var(--border,#eee); }
.main-image-wrap img { width:100%; height:100%; object-fit:cover; transition:transform .35s ease; }
.main-image-wrap img:hover { transform:scale(1.04); }
.main-image-wrap .no-img { font-size:64px; color:var(--text-muted,#aaa); }
.thumbs-row { display:flex; gap:8px; flex-wrap:wrap; }
.thumb-btn { width:62px; height:62px; border-radius:10px; overflow:hidden; border:2px solid transparent; cursor:pointer; background:var(--bg,#f8f9fa); transition:border-color .2s,transform .2s; padding:0; }
.thumb-btn img { width:100%; height:100%; object-fit:cover; }
.thumb-btn.active,.thumb-btn:hover { border-color:var(--accent,#e63946); transform:scale(1.05); }
.info-card .card-body { padding:28px; }
.product-badge-row { display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-bottom:12px; }
.prod-title { font-size:26px; font-weight:700; color:var(--text,#1a1a2e); line-height:1.25; margin-bottom:8px; }
.prod-meta-row { display:flex; gap:20px; align-items:center; flex-wrap:wrap; margin-bottom:20px; color:var(--text-muted,#888); font-size:13px; }
.prod-meta-row span { display:flex; align-items:center; gap:5px; }
.price-block { display:flex; align-items:center; gap:14px; margin-bottom:24px; padding:18px 20px; background:var(--bg,#f8f9fa); border-radius:12px; border:1px solid var(--border,#eee); }
.price-main { font-size:32px; font-weight:800; color:var(--accent,#e63946); letter-spacing:-1px; }
.price-old { font-size:18px; color:var(--text-muted,#aaa); text-decoration:line-through; }
.sale-badge { background:var(--accent,#e63946); color:#fff; font-size:11px; font-weight:700; padding:3px 9px; border-radius:20px; }
.stock-row { display:flex; align-items:center; gap:10px; margin-bottom:22px; }
.stock-bar-wrap { flex:1; height:6px; background:var(--border,#eee); border-radius:99px; overflow:hidden; }
.stock-bar-fill { height:100%; border-radius:99px; background:var(--accent,#e63946); }
.stock-bar-fill.green { background:#2ecc71; }
.stock-bar-fill.yellow { background:#f39c12; }
.desc-text { font-size:14px; color:var(--text-muted,#666); line-height:1.7; margin-bottom:0; }
.section-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-top:24px; }
.specs-table { width:100%; border-collapse:collapse; }
.specs-table tr { border-bottom:1px solid var(--border,#eee); }
.specs-table tr:last-child { border-bottom:none; }
.specs-table td { padding:10px 4px; font-size:13.5px; }
.specs-table td:first-child { color:var(--text-muted,#888); font-weight:500; width:42%; }
.specs-table td:last-child { color:var(--text,#1a1a2e); font-weight:600; }
.variants-wrap { display:flex; flex-direction:column; gap:14px; }
.variant-row { background:var(--bg,#f8f9fa); border-radius:10px; border:1px solid var(--border,#eee); padding:14px 16px; }
.variant-color-row { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.color-dot { width:16px; height:16px; border-radius:50%; border:2px solid rgba(0,0,0,.12); flex-shrink:0; }
.variant-color-name { font-weight:600; font-size:13.5px; }
.storages-row { display:flex; gap:8px; flex-wrap:wrap; }
.storage-chip { font-size:12px; font-weight:600; padding:5px 11px; border-radius:8px; background:#fff; border:1.5px solid var(--border,#ddd); color:var(--text,#333); display:flex; flex-direction:column; align-items:center; gap:1px; line-height:1.3; }
.storage-chip .s-price { color:var(--accent,#e63946); font-size:11px; }
.storage-chip .s-stock { color:var(--text-muted,#999); font-size:10px; }
.acc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:12px; }
.acc-card { border-radius:10px; border:1px solid var(--border,#eee); background:var(--bg,#f8f9fa); overflow:hidden; text-decoration:none; transition:box-shadow .2s,transform .2s; display:block; }
.acc-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.08); transform:translateY(-2px); }
.acc-card-img { width:100%; aspect-ratio:1/1; object-fit:cover; background:#fff; }
.acc-card-img-placeholder { width:100%; aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; font-size:28px; color:var(--text-muted,#ccc); background:#fff; }
.acc-card-body { padding:8px 10px 10px; }
.acc-card-name { font-size:12px; font-weight:600; color:var(--text,#222); line-height:1.3; margin-bottom:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.acc-card-price { font-size:11px; font-weight:700; color:var(--accent,#e63946); }
.action-bar { display:flex; gap:10px; align-items:center; margin-top:24px; padding-top:20px; border-top:1px solid var(--border,#eee); flex-wrap:wrap; }
.empty-small { text-align:center; padding:24px 0; color:var(--text-muted,#aaa); font-size:13px; }
.empty-small i { font-size:28px; margin-bottom:8px; display:block; }
.variant-images-row { display:flex; gap:6px; flex-wrap:wrap; margin-top:10px; }
.variant-img-thumb { width:48px; height:48px; border-radius:8px; object-fit:cover; border:1px solid var(--border,#eee); cursor:pointer; transition:transform .2s; }
.variant-img-thumb:hover { transform:scale(1.08); }
.linked-products-wrap { display:flex; flex-direction:column; gap:10px; }
.linked-prod-row { display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:10px; background:var(--bg,#f8f9fa); border:1px solid var(--border,#eee); text-decoration:none; transition:box-shadow .2s; }
.linked-prod-row:hover { box-shadow:0 4px 14px rgba(0,0,0,.07); }
.linked-prod-thumb { width:42px; height:42px; border-radius:8px; object-fit:cover; background:#fff; border:1px solid var(--border,#eee); flex-shrink:0; }
.linked-prod-info { flex:1; min-width:0; }
.linked-prod-name { font-size:13px; font-weight:600; color:var(--text,#222); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.linked-prod-cat { font-size:11px; color:var(--text-muted,#999); margin-top:2px; }
@media (max-width:900px) { .show-grid { grid-template-columns:1fr; } .section-grid { grid-template-columns:1fr; } }
@media (max-width:600px) { .prod-title { font-size:20px; } .price-main { font-size:24px; } .acc-grid { grid-template-columns:repeat(2,1fr); } }
</style>
@endpush

@section('content')
<div class="page-wrap">

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="breadcrumb-row">
            <a href="{{ url('/') }}">Dashboard</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('admin.products.index') }}">Produktet</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $product->name }}</span>
        </div>
        <h1 class="page-title">{{ $product->name }}</h1>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('admin.products.edit', $product->uuid) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i> Edito
        </a>
        <form action="{{ route('admin.products.destroy', $product->uuid) }}" method="POST"
              onsubmit="return confirm('A jeni i sigurt?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fa-solid fa-trash"></i> Fshi
            </button>
        </form>
        <a href="{{ route('admin.products.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left"></i> Kthehu
        </a>
    </div>
</div>

<div class="show-grid">

    {{-- GALLERY --}}
    <div class="card gallery-card">
        <div style="padding:20px;">
            @php
                $primaryImg = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first();
                $allImgs    = $product->images ?? collect();
            @endphp
            <div class="main-image-wrap" id="mainImgWrap">
                @if($primaryImg)
                    <img src="{{ asset('storage/'.$primaryImg->image_path) }}" id="mainImg" alt="{{ $product->name }}">
                @else
                    <div class="no-img"><i class="fa-solid fa-box"></i></div>
                @endif
            </div>
            @if($allImgs->count() > 1)
            <div class="thumbs-row">
                @foreach($allImgs as $img)
                <button type="button" class="thumb-btn {{ $img->is_primary ? 'active' : '' }}"
                        onclick="switchImage(this, '{{ asset('storage/'.$img->image_path) }}')">
                    <img src="{{ asset('storage/'.$img->image_path) }}" alt="">
                </button>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- INFO --}}
    <div class="card info-card">
        <div class="card-body">
            <div class="product-badge-row">
                <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-muted' }}">
                    {{ $product->is_active ? 'Aktiv' : 'Joaktiv' }}
                </span>
                @if($product->featured ?? false)
                    <span class="badge badge-warning"><i class="fa-solid fa-star" style="font-size:9px;"></i> Featured</span>
                @endif
                @if($product->sale_price)
                    @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                    <span class="sale-badge">-{{ $discount }}% ZBRITJE</span>
                @endif
                <span style="font-size:11px;color:var(--text-muted);font-family:monospace;">#{{ $product->id }}</span>
            </div>

            <h2 class="prod-title">{{ $product->name }}</h2>

            <div class="prod-meta-row">
                @if($product->category)
                <span><i class="fa-solid fa-tag"></i> {{ $product->category->name }}
                    @if($product->subcategory) &rsaquo; {{ $product->subcategory }} @endif
                </span>
                @endif
                @if($product->brand)
                <span><i class="fa-solid fa-certificate"></i> {{ $product->brand->name }}</span>
                @endif
                <span><i class="fa-solid fa-clock"></i> {{ $product->created_at->format('d M Y') }}</span>
            </div>

            <div class="price-block">
                @if($product->sale_price)
                    <span class="price-main">{{ number_format($product->sale_price, 2) }} €</span>
                    <span class="price-old">{{ number_format($product->price, 2) }} €</span>
                @else
                    <span class="price-main">{{ number_format($product->price ?? 0, 2) }} €</span>
                @endif
            </div>

            @php
                $stock = $product->stock ?? 0;
                $barClass = $stock > 10 ? 'green' : ($stock > 0 ? 'yellow' : '');
                $barWidth = min(100, $stock * 2);
            @endphp
            <div class="stock-row">
                <span style="font-size:13px;font-weight:600;color:var(--text);">
                    <i class="fa-solid fa-warehouse" style="margin-right:5px;color:var(--text-muted);"></i> Stock:
                </span>
                @if($stock > 10)
                    <span class="badge badge-success">{{ $stock }} copë</span>
                @elseif($stock > 0)
                    <span class="badge badge-warning">{{ $stock }} copë</span>
                @else
                    <span class="badge badge-danger">Skadon</span>
                @endif
                <div class="stock-bar-wrap">
                    <div class="stock-bar-fill {{ $barClass }}" style="width:{{ $barWidth }}%;"></div>
                </div>
            </div>

            @if($product->description)
            <div>
                <div style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;">Përshkrimi</div>
                <p class="desc-text">{{ $product->description }}</p>
            </div>
            @endif

            <div class="action-bar">
                <a href="{{ route('admin.products.edit', $product->uuid) }}" class="btn btn-primary">
                    <i class="fa-solid fa-pen"></i> Edito Produktin
                </a>
                <a href="{{ url('/admin/stock?product='.$product->id) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-warehouse"></i> Menaxho Stokun
                </a>
                <form action="{{ route('admin.products.destroy', $product->uuid) }}" method="POST"
                      onsubmit="return confirm('A jeni i sigurt?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Fshi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="section-grid">

    {{-- SPECS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);"><i class="fa-solid fa-list-check"></i></div>
                <span class="card-title">Specifikimet</span>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            @if($product->specs && $product->specs->count() > 0)
            <table class="specs-table">
                @foreach($product->specs as $spec)
                <tr><td>{{ $spec->spec_key }}</td><td>{{ $spec->spec_value }}</td></tr>
                @endforeach
            </table>
            @else
            <div class="empty-small"><i class="fa-solid fa-list-check"></i> Nuk ka specifikimet.</div>
            @endif
        </div>
    </div>

    {{-- VARIANTS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);"><i class="fa-solid fa-palette"></i></div>
                <span class="card-title">Variantet</span>
                @if($product->variants && $product->variants->count() > 0)
                    <span class="badge badge-success" style="margin-left:8px;">{{ $product->variants->unique('color_name')->count() }} ngjyra</span>
                @endif
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            @if($product->variants && $product->variants->count() > 0)
            <div class="variants-wrap">
                @foreach($product->variants->groupBy('color_name') as $colorName => $colorVariants)
                @php $firstV = $colorVariants->first(); @endphp
                <div class="variant-row">
                    <div class="variant-color-row">
                        <div class="color-dot" style="background:{{ $firstV->color_hex ?? '#ccc' }};"></div>
                        <span class="variant-color-name">{{ $colorName ?: 'Pa ngjyrë' }}</span>
                        <span style="font-size:11px;color:var(--text-muted);margin-left:auto;">{{ $colorVariants->count() }} variante</span>
                    </div>
                    @php $vImages = $product->variantImages?->where('color_name', $colorName) ?? collect(); @endphp
                    @if($vImages->count() > 0)
                    <div class="variant-images-row">
                        @foreach($vImages as $vImg)
                        <img src="{{ asset('storage/'.$vImg->image_path) }}" class="variant-img-thumb"
                             onclick="switchImage(null, '{{ asset('storage/'.$vImg->image_path) }}')" alt="">
                        @endforeach
                    </div>
                    @endif
                    <div class="storages-row" style="margin-top:10px;">
                        @foreach($colorVariants as $sv)
                        <div class="storage-chip">
                            <span>{{ $sv->storage ?: '—' }}</span>
                            <span class="s-price">{{ $sv->sale_price ? number_format($sv->sale_price,0) : number_format($sv->price??0,0) }} €</span>
                            <span class="s-stock">{{ $sv->stock ?? 0 }} copë</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-small"><i class="fa-solid fa-palette"></i> Ky produkt nuk ka variante.</div>
            @endif
        </div>
    </div>

    {{-- ACCESSORIES --}}
    @if($product->accessories && $product->accessories->count() > 0)
    <div class="card" style="grid-column:1/-1;">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);"><i class="fa-solid fa-puzzle-piece"></i></div>
                <span class="card-title">Aksesorët</span>
                <span class="badge badge-success" style="margin-left:8px;">{{ $product->accessories->count() }}</span>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div class="acc-grid">
                @foreach($product->accessories as $acc)
                @php $accImg = $acc->images?->firstWhere('is_primary', true) ?? $acc->images?->first(); @endphp
                <a href="{{ route('admin.products.show', $acc->uuid) }}" class="acc-card">
                    @if($accImg)
                        <img src="{{ asset('storage/'.$accImg->image_path) }}" class="acc-card-img" alt="{{ $acc->name }}">
                    @else
                        <div class="acc-card-img-placeholder"><i class="fa-solid fa-box"></i></div>
                    @endif
                    <div class="acc-card-body">
                        <div class="acc-card-name">{{ $acc->name }}</div>
                        <div class="acc-card-price">{{ number_format($acc->price ?? 0, 2) }} €</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- LINKED MAIN PRODUCTS --}}
    @if($product->accessoryOf && $product->accessoryOf->count() > 0)
    <div class="card" style="grid-column:1/-1;">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);"><i class="fa-solid fa-link"></i></div>
                <span class="card-title">I lidhur me Produktet</span>
                <span class="badge badge-success" style="margin-left:8px;">{{ $product->accessoryOf->count() }}</span>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div class="linked-products-wrap">
                @foreach($product->accessoryOf as $mainProd)
                @php $mpImg = $mainProd->images?->firstWhere('is_primary', true) ?? $mainProd->images?->first(); @endphp
                <a href="{{ route('admin.products.show', $mainProd->uuid) }}" class="linked-prod-row">
                    @if($mpImg)
                        <img src="{{ asset('storage/'.$mpImg->image_path) }}" class="linked-prod-thumb" alt="">
                    @else
                        <div style="width:42px;height:42px;border-radius:8px;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid fa-box" style="color:var(--text-muted);font-size:16px;"></i>
                        </div>
                    @endif
                    <div class="linked-prod-info">
                        <div class="linked-prod-name">{{ $mainProd->name }}</div>
                        <div class="linked-prod-cat">{{ $mainProd->category->name ?? '—' }}</div>
                    </div>
                    <span style="font-size:13px;font-weight:700;color:var(--accent);">{{ number_format($mainProd->price ?? 0, 2) }} €</span>
                    <i class="fa-solid fa-chevron-right" style="color:var(--text-muted);font-size:11px;"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

</div>
@endsection

@push('scripts')
<script>
function switchImage(thumbBtn, src) {
    const mainImg = document.getElementById('mainImg');
    if (mainImg) {
        mainImg.style.opacity = '0';
        setTimeout(() => { mainImg.src = src; mainImg.style.opacity = '1'; }, 150);
    }
    document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
    if (thumbBtn) thumbBtn.classList.add('active');
}
document.addEventListener('DOMContentLoaded', () => {
    const img = document.getElementById('mainImg');
    if (img) img.style.transition = 'opacity .15s ease';
});
</script>
@endpush