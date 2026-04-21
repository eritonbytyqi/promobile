@extends('layouts.admin')

@section('title', 'Produktet')
@section('page-title', 'Produktet')

@section('breadcrumb')
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>Produktet</span>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products.css') }}">
@endpush

@section('content')
<div class="page-wrap">


{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card red">
        <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
        <div class="stat-value">{{ $products->total() ?? $products->count() }}</div>
        <div class="stat-label">Totali i Produkteve</div>
        <div class="stat-sub">Produktë aktivë në sistem</div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon"><i class="fa-solid fa-tag"></i></div>
        <div class="stat-value">{{ $categories->count() ?? '—' }}</div>
        <div class="stat-label">Kategoritë</div>
        <div class="stat-sub">Kategori aktive</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fa-solid fa-certificate"></i></div>
        <div class="stat-value">{{ $brands->count() ?? '—' }}</div>
        <div class="stat-label">Brendet</div>
        <div class="stat-sub">Brende të regjistruara</div>
    </div>
</div>

{{-- ALL PRODUCTS TABLE --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-left">
            <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);">
                <i class="fa-solid fa-list"></i>
            </div>
            <span class="card-title">Të Gjithë Produktet</span>
        </div>
     <form method="GET" action="{{ route('admin.products.index') }}" id="filterForm">
<div class="filters-row">
    <div class="search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" placeholder="Kërko produkt..."
               value="{{ request('q') }}"
               onchange="this.form.submit()">
    </div>
    <select name="category" onchange="this.form.submit()" class="filter-select">
        <option value="">Të gjitha kategoritë</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <select name="brand" onchange="this.form.submit()" class="filter-select">
        <option value="">Të gjitha brendet</option>
        @foreach($brands as $brand)
            <option value="{{ $brand->name }}" {{ request('brand') == $brand->name ? 'selected' : '' }}>
                {{ $brand->name }}
            </option>
        @endforeach
    </select>
    <select name="status" onchange="this.form.submit()" class="filter-select">
        <option value="">Të gjitha</option>
        <option value="aktiv"   {{ request('status') == 'aktiv'   ? 'selected' : '' }}>✅ Aktiv</option>
        <option value="joaktiv" {{ request('status') == 'joaktiv' ? 'selected' : '' }}>❌ Joaktiv</option>
    </select>
    <div style="display:flex;gap:8px;margin-left:auto;">
        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" onclick="submitBulkDelete()" style="display:none;">
            <i class="fa-solid fa-trash"></i> Fshi të zgjedhurat
        </button>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Shto
        </a>
    </div>
</div>
</form>
    </div>

    {{-- DESKTOP TABLE --}}
    <div class="table-wrap">
        <table id="productsTable">
            <thead>
                <tr>
                    <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Emri</th>
                    <th>Kategoria</th>
                    <th>Brendi</th>
                    <th>Çmimi</th>
                    <th>Stock</th>
                    <th>Statusi</th>
                    <th>Veprimet</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
            <tr data-status="{{ ($product->is_active ?? true) ? 'aktiv' : 'joaktiv' }}">
                <td><input type="checkbox" class="row-check" value="{{ $product->id }}"></td>
                <td style="color:var(--text-muted);font-size:12px;font-family:monospace;">#{{ $product->id }}</td>
                <td>
                    <div class="thumb">
                        @php $tImg = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first(); @endphp
                        @if($tImg)
                            <img src="{{ asset('storage/'.$tImg->image_path) }}">
                        @else
                            <i class="fa-solid fa-box"></i>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="prod-name">{{ $product->name }}</div>
                    @if($product->variants && $product->variants->count() > 0)
                        <div style="font-size:10px;color:var(--text-muted);margin-top:3px;">
                            <i class="fa-solid fa-palette" style="font-size:9px;"></i>
                            {{ $product->variants->unique('color_name')->count() }} ngjyra ·
                            {{ $product->variants->count() }} variante
                        </div>
                    @endif
                </td>
                <td>{{ $product->category->name ?? '—' }}</td>
                <td>{{ $product->brand->name ?? '—' }}</td>
                <td><span class="price">{{ number_format($product->price, 2) }} €</span></td>
                <td>
                    <a href="{{ url('/admin/stock?product='.$product->id) }}" style="text-decoration:none;">
                        @if(($product->stock ?? 0) > 10)
                            <span class="badge badge-success">{{ $product->stock }}</span>
                        @elseif(($product->stock ?? 0) > 0)
                            <span class="badge badge-warning">{{ $product->stock }}</span>
                        @else
                            <span class="badge badge-danger">Skadon</span>
                        @endif
                    </a>
                </td>
                <td>
                    @if($product->is_active ?? true)
                        <span class="badge badge-success">Aktiv</span>
                    @else
                        <span class="badge badge-muted">Joaktiv</span>
                    @endif
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.products.show', $product->uuid) }}" class="btn btn-ghost btn-sm btn-icon">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.products.edit', $product->uuid) }}" class="btn btn-ghost btn-sm btn-icon">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        {{-- Toggle aktiv/joaktiv --}}
                        <form action="{{ route('admin.products.toggle', $product->uuid) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-ghost btn-sm btn-icon"
                                    title="{{ ($product->is_active ?? true) ? 'Çaktivizo' : 'Aktivizo' }}">
                                @if($product->is_active ?? true)
                                    <i class="fa-solid fa-toggle-on" style="color:#10b981;"></i>
                                @else
                                    <i class="fa-solid fa-toggle-off" style="color:#8e8e93;"></i>
                                @endif
                            </button>
                        </form>
                        <form action="{{ route('admin.products.destroy', $product->uuid) }}" method="POST" onsubmit="return confirm('Fshi produktin?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-icon">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
                        <h3>Nuk ka produkte ende</h3>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
<div id="mobileSelectBar" style="display:none;padding:10px 16px;border-bottom:1px solid var(--border);align-items:center;gap:10px;background:var(--surface2,#fafafa);">
    <input type="checkbox" id="selectAllMobile" style="width:18px;height:18px;accent-color:#e63946;cursor:pointer;">
    <label for="selectAllMobile" style="font-size:13px;font-weight:600;color:var(--text);cursor:pointer;">
        Zgjedh të gjitha
    </label>
</div>
    {{-- MOBILE CARD LIST --}}
    <div class="mobile-list">
        @forelse($products as $product)
        <div class="mobile-product-card" data-status="{{ ($product->is_active ?? true) ? 'aktiv' : 'joaktiv' }}">
            <div style="display:flex;align-items:center;gap:12px;">

                <input type="checkbox" class="row-check"
                       value="{{ $product->id }}"
                       style="width:18px;height:18px;flex-shrink:0;accent-color:#e63946;cursor:pointer;">

                <div class="mobile-thumb" style="flex-shrink:0;">
                    @php $mImg = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first(); @endphp
                    @if($mImg)
                        <img src="{{ asset('storage/'.$mImg->image_path) }}" alt="">
                    @else
                        <i class="fa-solid fa-box"></i>
                    @endif
                </div>

                <div class="mobile-info" style="flex:1;min-width:0;">
                    <div class="mobile-name">{{ $product->name }}</div>
                    <div class="mobile-meta">
                        <span>{{ $product->category->name ?? '—' }}</span>
                        <span style="color:#ccc;">·</span>
                        <span>{{ $product->brand->name ?? '—' }}</span>
                    </div>
                    <div class="mobile-row">
                        <span class="mobile-price">{{ number_format($product->price, 2) }} €</span>
                        @if(($product->stock ?? 0) > 10)
                            <span class="badge badge-success">{{ $product->stock }}</span>
                        @elseif(($product->stock ?? 0) > 0)
                            <span class="badge badge-warning">{{ $product->stock }}</span>
                        @else
                            <span class="badge badge-danger">Skadon</span>
                        @endif
                        @if($product->is_active ?? true)
                            <span class="badge badge-success">Aktiv</span>
                        @else
                            <span class="badge badge-muted">Joaktiv</span>
                        @endif
                    </div>
                </div>

                <div class="mobile-actions" style="display:flex;flex-direction:column;gap:6px;flex-shrink:0;">
                    <a href="{{ route('admin.products.show', $product->uuid) }}" class="btn btn-ghost btn-sm btn-icon">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.products.edit', $product->uuid) }}" class="btn btn-ghost btn-sm btn-icon">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.products.toggle', $product->uuid) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-ghost btn-sm btn-icon"
                                title="{{ ($product->is_active ?? true) ? 'Çaktivizo' : 'Aktivizo' }}">
                            @if($product->is_active ?? true)
                                <i class="fa-solid fa-toggle-on" style="color:#10b981;"></i>
                            @else
                                <i class="fa-solid fa-toggle-off" style="color:#8e8e93;"></i>
                            @endif
                        </button>
                    </form>
                    <form action="{{ route('admin.products.destroy', $product->uuid) }}" method="POST"
                          onsubmit="return confirm('Fshi produktin?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm btn-icon">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
            <h3>Nuk ka produkte ende</h3>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Shto Produkt
            </a>
        </div>
        @endforelse
    </div>

    @if(method_exists($products, 'hasPages') && $products->hasPages())
    <div class="pagination-wrap">
        <div class="pag-inner">
            @if($products->onFirstPage())
                <span class="pag-btn pag-disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="pag-btn"><i class="fa-solid fa-chevron-left"></i></a>
            @endif
            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if($page == $products->currentPage())
                    <span class="pag-btn pag-active">{{ $page }}</span>
                @elseif($page == 1 || $page == $products->lastPage() || abs($page - $products->currentPage()) <= 2)
                    <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                @elseif(abs($page - $products->currentPage()) == 3)
                    <span class="pag-btn pag-dots">&#8230;</span>
                @endif
            @endforeach
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="pag-btn"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="pag-btn pag-disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
        <div class="pag-info">
            Shfaqen {{ $products->firstItem() }}&ndash;{{ $products->lastItem() }} nga {{ $products->total() }}
        </div>
    </div>
    @endif
</div>

</div>
{{-- BULK DELETE FORM --}}
<form id="bulkDeleteForm" action="{{ route('admin.products.bulk-delete') }}" method="POST" style="display:none;">
    @csrf @method('DELETE')
    <div id="selectedProductsContainer"></div>
</form>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/products-index.js') }}"></script>
@endpush