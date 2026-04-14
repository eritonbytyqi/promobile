@extends('layouts.admin')

@section('title', isset($product) ? 'Edito Produktin' : 'Shto Produkt')
@section('page-title', isset($product) ? 'Edito Produktin' : 'Produkt i Ri')

@section('breadcrumb')
    <a href="{{ url('/') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <a href="{{ url('/admin/products') }}">Produktet</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>{{ isset($product) ? $product->name : 'I Ri' }}</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/products.css') }}">
@endpush

@section('content')
<div class="pf-wrap">

{{-- PAGE HEADER --}}
<div class="pf-header">
    <div>
        <div class="pf-breadcrumb">
            <a href="{{ url('/') }}">Dashboard</a>
            <i class="fa-solid fa-chevron-right" style="font-size:8px;"></i>
            <a href="{{ url('/admin/products') }}">Produktet</a>
            <i class="fa-solid fa-chevron-right" style="font-size:8px;"></i>
            <span>{{ isset($product) ? $product->name : 'I Ri' }}</span>
        </div>
        <h1 class="pf-title">
            {{ isset($product) ? 'Edito ' : 'Produkt ' }}<span>{{ isset($product) ? 'Produktin' : 'i Ri' }}</span>
        </h1>
    </div>
    <a href="{{ url('/admin/products') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kthehu
    </a>
</div>

{{-- MAIN FORM --}}
<form action="{{ isset($product) ? route('admin.products.update', $product->uuid) : route('admin.products.store') }}"
      method="POST" enctype="multipart/form-data" id="productForm">
    @csrf
    @if(isset($product)) @method('PUT') @endif

    <div class="pf-grid">

        {{-- ═══════════════ LEFT COLUMN ═══════════════ --}}
        <div class="pf-left">

            {{-- ── Informacioni Bazë ── --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(230,57,70,0.10);color:var(--accent);">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        Informacioni Bazë
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Emri i Produktit *</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $product->name ?? '') }}"
                               placeholder="p.sh. iPhone 15 Pro Max" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Përshkrimi</label>
                        <textarea name="description" class="form-control" rows="5"
                                  placeholder="Përshkruani produktin...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── Çmimi & Stoku ── --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(5,150,105,0.10);color:var(--accent3);">
                            <i class="fa-solid fa-tag"></i>
                        </div>
                        Çmimi & Stoku
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Çmimi Bazë (€) *</label>
                            <input type="number" name="price" step="0.01" class="form-control"
                                   value="{{ old('price', $product->price ?? '') }}"
                                   placeholder="0.00">
                            @error('price')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Çmimi me Zbritje (€)</label>
                            <input type="number" name="sale_price" step="0.01" class="form-control"
                                   value="{{ old('sale_price', $product->sale_price ?? '') }}" placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stoku Bazë</label>
                            <input type="number" name="stock" class="form-control"
                                   value="{{ old('stock', $product->stock ?? 0) }}" placeholder="0">
                        </div>
                    </div>
                    <div class="hint-box">
                        <i class="fa-solid fa-circle-info"></i>
                        Nëse shton variante më poshtë, çmimi i variantit do zëvendësojë këtë çmim bazë.
                    </div>
                </div>
            </div>

            {{-- ── Galeria e Fotove ── --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(5,150,105,0.10);color:var(--accent3);">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        Fotot e Produktit
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div id="dropzone" class="dropzone"
                             onclick='document.getElementById("imagesInput").click()'
                             ondragover="event.preventDefault();dzHover(true)"
                             ondragleave="dzHover(false)"
                             ondrop="handleDrop(event)">
                            <i class="fa-solid fa-cloud-arrow-up dropzone-icon"></i>
                            <div class="dropzone-text">
                                Tërhiq fotot këtu ose <span>kliko për upload</span>
                            </div>
                            <div class="dropzone-sub">JPG, PNG, WEBP — max 4MB secila</div>
                        </div>
                        <input type="file" id="imagesInput" name="images[]" multiple accept="image/*" hidden>
                        @error('images')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    <div id="previewSection" style="display:none;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                            <div class="card-title" style="font-size:13px;">Preview <span id="previewCount"></span></div>
                            <button type="button" class="btn btn-danger-soft btn-sm" onclick='clearPreviews()'>
                                <i class="fa-solid fa-trash"></i> Pastro
                            </button>
                        </div>
                        <div id="previewList"></div>
                        <div class="primary-selector">
                            <label class="form-label" style="margin-bottom:0;">Foto Kryesore</label>
                            <select name="primary_index" id="primarySelect" class="form-select"></select>
                        </div>
                    </div>

                    @if(isset($product) && $product->images && $product->images->count())
                        <div class="section-sep"></div>
                        <div class="form-group">
                            <label class="form-label">Fotot Ekzistuese</label>
                            <div class="existing-grid">
                                @foreach($product->images as $img)
                                    <div class="existing-img-card {{ $img->is_primary ? 'is-primary' : '' }}" id="imgWrap{{ $img->id }}">
                                        <img src="{{ asset('storage/'.$img->image_path) }}" alt="">
                                        @if($img->is_primary)
                                            <span class="existing-primary-badge">
                                                <i class="fa-solid fa-star" style="font-size:8px;"></i> KRYESORE
                                            </span>
                                        @endif
                                        <div class="existing-img-actions">
                                            @if(!$img->is_primary)
                                                <button type="button" class="img-action-btn img-action-primary"
                                                        onclick='deleteImage(@json($img->id), @json($product->id))'>
                                                    <i class="fa-solid fa-star"></i> Kryesore
                                                </button>
                                            @endif
                                            <button type="button" class="img-action-btn img-action-delete"
                                                    onclick='deleteImage(@json($img->id), @json($product->id))'>
                                                <i class="fa-solid fa-trash"></i> Fshi
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Variantet ── --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(124,58,237,0.10);color:#7c3aed;">
                            <i class="fa-solid fa-palette"></i>
                        </div>
                        Variantet — Ngjyra, Foto & Storage
                    </div>
                    <button type="button" onclick='addVariant()' class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> Shto Ngjyrë
                    </button>
                </div>
                <div class="card-body">
                    <div id="variantsList" style="display:flex;flex-direction:column;gap:16px;">

                    @php
                        $groupedVariants = isset($product) && $product->variants
                            ? $product->variants->groupBy(function ($v) {
                                return trim(($v->color_hex ?? '')) . '||' . trim(($v->color_name ?? ''));
                            })
                            : collect();

                        $groupedVarImages = isset($product) && $product->variantImages
                            ? $product->variantImages->groupBy(function ($img) {
                                return trim(($img->color_hex ?? '')) . '||' . trim(($img->color_name ?? ''));
                            })
                            : collect();
                    @endphp

                        @if($groupedVariants->count() > 0)
                        @foreach($groupedVariants as $colorKey => $colorVariants)
                        @php
                            $vi = $loop->index;
                            $firstVariant = $colorVariants->first();
                            $colorImages  = $groupedVarImages->get($colorKey, collect());
                        @endphp

                            <div class="variant-color-group" id="variant_{{ $vi }}">
                                {{-- HEAD --}}
                                <div class="variant-color-head">
                                    <div class="color-swatch-preview" id="swatchPreview_{{ $vi }}"
                                         style="background:{{ $firstVariant->color_hex ?? '#cccccc' }};"></div>

                                    <div style="flex:1;">
                                        <label class="form-label" style="margin-bottom:6px;">Ngjyra</label>

                                        {{-- Ngjyrat e shpejta --}}
                                        <!-- <div class="quick-colors">
                                            @foreach(['#000000','#ffffff','#c0c0c0','#ffd700','#ff6b35','#e74c3c','#2ecc71','#3498db','#9b59b6','#1abc9c'] as $qc)
                                                <span class="quick-color" style="background:{{ $qc }};"
                                                      onclick="document.getElementById('colorHex_{{ $vi }}').value='{{ $qc }}'; updateSwatch({{ $vi }}, '{{ $qc }}');"
                                                      title="{{ $qc }}"></span>
                                            @endforeach
                                        </div> -->

                                   <div class="color-picker-row">
                                       <input type="color"
                                              id="colorHex_{{ $vi }}"
                                              name="variants[{{ $vi }}][color_hex]"
                                              value="{{ $firstVariant->color_hex ?? '#ff0000' }}"
                                              oninput="updateSwatch({{ $vi }}, this.value)">
                                   
                                       <input type="hidden"
                                              name="variants[{{ $vi }}][color_name]"
                                              value="">
                                   </div>
                                    </div>

                                    <button type="button" class="rm-btn" onclick='removeVariant(@json($vi))'>
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>

                                <div class="variant-body">
                                    {{-- FOTO MULTIPLE --}}
                                    <div class="color-photos-section">
                                        <div class="color-photos-header">
                                            <div class="label">
                                                <i class="fa-solid fa-images" style="color:var(--accent3);"></i>
                                                Fotot e Ngjyrës
                                                @if($colorImages->count())
                                                    <span style="background:rgba(5,150,105,0.1);color:var(--accent3);padding:1px 8px;border-radius:10px;font-size:10px;">
                                                        {{ $colorImages->count() }} foto
                                                    </span>
                                                @endif
                                            </div>
                                            <button type="button"
                                                    onclick='document.getElementById("colorPhotoInput_{{ $vi }}").click()'
                                                    class="btn btn-success-soft btn-sm">
                                                <i class="fa-solid fa-plus"></i> Shto Foto
                                            </button>
                                            <input type="file"
                                                   id="colorPhotoInput_{{ $vi }}"
                                                   name="variants[{{ $vi }}][images][]"
                                                   accept="image/*" multiple hidden
                                                   oninput='updateSwatch(@json($vi), this.value)'>
                                        </div>
                                        <div class="color-photos-grid" id="colorPhotosGrid_{{ $vi }}">
                                            @foreach($colorImages as $imgIdx => $varImg)
                                                <div class="color-photo-item {{ $varImg->is_primary ? 'primary-photo' : '' }}"
                                                     data-vi="{{ $vi }}"
                                                     data-existing-id="{{ $varImg->id }}">
                                                    <img src="{{ asset('storage/'.$varImg->image_path) }}" alt="">
                                                    <input type="hidden"
                                                           name="variants[{{ $vi }}][existing_images][]"
                                                           value="{{ $varImg->image_path }}">
                                                    @if($varImg->is_primary)
                                                        <span class="color-photo-primary-badge">★ Kryesore</span>
                                                    @endif
                                                    <button type="button" class="color-photo-rm"
                                                            onclick="removeColorPhoto(this)">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </div>
                                            @endforeach

                                            @if($colorImages->isEmpty())
                                                <div style="padding:12px;font-size:11px;color:var(--text-muted);width:100%;">
                                                    <i class="fa-solid fa-circle-info"></i>
                                                    Nuk ka foto për këtë ngjyrë.
                                                </div>
                                            @endif

                                            <div class="color-photo-add-btn"
                                                 onclick="document.getElementById('colorPhotoInput_{{ $vi }}').click()">
                                                <i class="fa-solid fa-plus"></i>
                                                <span>Shto</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- STORAGE --}}
                                    <div class="storage-section">
                                        <div class="storage-section-header">
                                            <div class="label">
                                                <i class="fa-solid fa-hard-drive" style="color:#7c3aed;"></i>
                                                Storage / Çmimi / Stoku
                                            </div>
                                            <button type="button"
                                                    onclick='addStorageRow(@json($vi))'
                                                    class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-plus"></i> Shto Storage
                                            </button>
                                        </div>
                                        <div class="storage-col-headers">
                                            <span>Storage</span>
                                            <!-- <span>Bazë (€)</span> -->
                                            <span>Shtesë (€)</span>
                                            <span>Totali</span>
                                            <span>Stoku</span>
                                            <span></span>
                                        </div>
                                        @foreach($colorVariants as $si => $sv)
                                        <div class="storage-row" id="storageRow_{{ $vi }}_{{ $si }}">
                                            <select name="variants[{{ $vi }}][storages][{{ $si }}][storage]" class="form-select">
                                                <option value="">— Zgjedh —</option>
                                                @foreach(['64GB','128GB','256GB','512GB','1TB','2TB'] as $opt)
                                                    <option value="{{ $opt }}" {{ ($sv->storage ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                                @endforeach
                                                @if(!in_array($sv->storage ?? '', ['','64GB','128GB','256GB','512GB','1TB','2TB']))
                                                    <option value="{{ $sv->storage }}" selected>{{ $sv->storage }}</option>
                                                @endif
                                            </select>

                                            @if($si === 0)
                                                <input type="number" step="0.01"
                                                       name="variants[{{ $vi }}][storages][{{ $si }}][base_price]"
                                                       class="form-control storage-base"
                                                       id="basePrice_{{ $vi }}"
                                                       value="{{ $sv->base_price ?? '' }}"
                                                       placeholder="999.00"
                                                       oninput='syncBasePrice(@json($vi)); calcTotal(@json($vi), @json($si))'>
                                            @else
                                                <div style="font-size:12px;color:var(--text-muted);align-self:center;padding:0 4px;">
                                                    <i class="fa-solid fa-arrow-turn-down-right" style="font-size:10px;"></i>
                                                    <span id="baseDisplay_{{ $vi }}_{{ $si }}" style="font-weight:600;">
                                                        {{ $sv->base_price > 0 ? number_format($sv->base_price, 2) . ' €' : '—' }}
                                                    </span>
                                                </div>
                                                <input type="hidden"
                                                       name="variants[{{ $vi }}][storages][{{ $si }}][base_price]"
                                                       id="basePriceHidden_{{ $vi }}_{{ $si }}"
                                                       value="{{ $sv->base_price ?? 0 }}">
                                            @endif

                                            <input type="number" step="0.01"
                                                   name="variants[{{ $vi }}][storages][{{ $si }}][extra_price]"
                                                   class="form-control"
                                                   value="{{ $sv->extra_price ?? 0 }}"
                                                   placeholder="+0.00"
                                                 oninput='calcTotal(@json($vi), @json($si))'>

                                            <div style="font-size:13px;color:#059669;font-weight:700;align-self:center;"
                                                 id="total_{{ $vi }}_{{ $si }}">
                                                @php $t = ($sv->base_price ?? 0) + ($sv->extra_price ?? 0); @endphp
                                                {{ $t > 0 ? number_format($t, 2) . ' €' : '—' }}
                                            </div>

                                            <input type="number"
                                                   name="variants[{{ $vi }}][storages][{{ $si }}][stock]"
                                                   class="form-control"
                                                   value="{{ $sv->stock ?? 0 }}"
                                                   placeholder="0">

                                            <button type="button" class="rm-btn-sm" onclick="removeStorageRow(this)">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @else
                            <div id="variantsEmpty" class="variant-empty">
                                <i class="fa-solid fa-palette" style="font-size:28px;opacity:0.2;display:block;margin-bottom:10px;"></i>
                                Nuk ka variante. Kliko <strong>"Shto Ngjyrë"</strong> për të shtuar ngjyrë me foto dhe storage.
                            </div>
                        @endif
                    </div>

                    <div class="hint-box" style="margin-top:4px;">
                        <i class="fa-solid fa-circle-info"></i>
                        Për çdo ngjyrë mund të shtosh <strong style="color:#92400e;">shumë foto</strong> dhe <strong style="color:#92400e;">shumë storage</strong>.
                    </div>
                </div>
            </div>

            {{-- ── Karakteristikat Teknike ── --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(217,119,6,0.10);color:var(--accent2);">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        Karakteristikat Teknike
                    </div>
                    <button type="button" onclick="addSpec()" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-plus"></i> Shto
                    </button>
                </div>
                <div class="card-body">
                    <div class="spec-header">
                        <span>Karakteristika</span>
                        <span>Vlera</span>
                        <span></span>
                    </div>
                    <div id="specsList" style="display:flex;flex-direction:column;gap:8px;">
                        <div class="spec-suggestions" id="specSuggestions">
                            @foreach(['Ekrani','Procesori','RAM','Bateria','Kamera','OS','Pesha','Dimensionet','USB','5G','Garancia','Dërgesa'] as $sug)
                                <button type="button" onclick="addSpec('{{ $sug }}')" class="spec-chip">
                                    + {{ $sug }}
                                </button>
                            @endforeach
                        </div>

                        @if(isset($product) && $product->specs && $product->specs->count() > 0)
                            @foreach($product->specs as $si => $spec)
                                <div class="spec-row" id="specRow_{{ $si }}">
                                    <input type="text" name="specs[{{ $si }}][key]"
                                           class="form-control" value="{{ $spec->spec_key }}"
                                           placeholder="p.sh. Ekrani">
                                    <input type="text" name="specs[{{ $si }}][value]"
                                           class="form-control" value="{{ $spec->spec_value }}"
                                           placeholder="p.sh. 6.7&quot; OLED">
                                    <button type="button" onclick="removeSpec(this)" class="rm-btn">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div id="specsEmpty" class="spec-empty">
                                <i class="fa-solid fa-list-check" style="font-size:22px;opacity:0.2;display:block;margin-bottom:8px;"></i>
                                Nuk ka karakteristika. Kliko "Shto" ose zgjidh nga sugjerimet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Linked Products --}}
            <div class="card" id="linkedProductsCard" style="display:none;">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(59,130,246,0.10);color:#3b82f6;">
                            <i class="fa-solid fa-link"></i>
                        </div>
                        Lidhe me Produkte
                    </div>
                    <span id="linkedBadge" style="background:rgba(59,130,246,0.1);color:#3b82f6;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;display:none;">
                        <span id="linkedCount">0</span> të zgjedhur
                    </span>
                </div>
                <div class="card-body">
                    <div class="hint-box" style="margin-bottom:14px;">
                        <i class="fa-solid fa-circle-info"></i>
                        Zgjedh produktet me të cilat lidhet ky aksesor.
                    </div>
                    <input type="text" id="linkedSearch" class="form-control"
                           placeholder="Kërko produkt..." oninput="linkedFilter()"
                           autocomplete="off" style="margin-bottom:10px;">
                    <div id="linkedList" style="max-height:320px;overflow-y:auto;border:1px solid var(--border);border-radius:10px;padding:6px;display:flex;flex-direction:column;gap:3px;">
                        @forelse($mainProducts as $mp)
                            @php
                                $isLinked = isset($product) && $product->accessoryOf->contains('id', $mp->id);
                                $mpImg    = $mp->images?->firstWhere('is_primary', true) ?? $mp->images?->first();
                                $mpSrc    = $mpImg ? asset('storage/'.$mpImg->image_path) : null;
                            @endphp
                            <label class="acc-row {{ $isLinked ? 'acc-row--on' : '' }}"
                                   data-name="{{ strtolower($mp->name) }}">
                                <input type="checkbox" name="linked_product_ids[]"
                                       value="{{ $mp->id }}" {{ $isLinked ? 'checked' : '' }}>
                                @if($mpSrc)
                                    <img src="{{ $mpSrc }}" alt="" class="acc-row-img">
                                @else
                                    <div class="acc-row-img acc-row-img--ph">
                                        <i class="fa-solid fa-box" style="font-size:11px;opacity:0.3;"></i>
                                    </div>
                                @endif
                                <div class="acc-row-info">
                                    <div class="acc-row-name">{{ $mp->name }}</div>
                                    @if($mp->category)
                                        <div class="acc-row-cat">{{ $mp->category->name }}</div>
                                    @endif
                                </div>
                                <i class="fa-solid fa-check acc-row-check"></i>
                            </label>
                        @empty
                            <div style="padding:20px;text-align:center;font-size:13px;color:var(--text-muted);">
                                Nuk ka produkte aktive.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>{{-- /LEFT --}}

        {{-- ═══════════════ RIGHT COLUMN ═══════════════ --}}
        <div class="pf-right">

            {{-- Publikimi --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(230,57,70,0.10);color:var(--accent);">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        Publikimi
                    </div>
                </div>
                <div class="card-body" style="gap:0;padding:14px 22px;">
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label-title">
                                <i class="fa-solid fa-star" style="color:var(--accent2);"></i>
                                Produkt i Featuar
                            </div>
                            <div class="toggle-label-sub">Shfaqet në seksionin kryesor</div>
                        </div>
                        <label class="toggle">
                            <input type="hidden" name="featured" value="0">
                            <input type="checkbox" name="featured" value="1"
                                   {{ old('featured', $product->featured ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-row">
                        <div>
                            <div class="toggle-label-title">
                                <i class="fa-solid fa-eye" style="color:var(--accent3);"></i>
                                Aktiv / i Dukshëm
                            </div>
                            <div class="toggle-label-sub">Shfaqet në dyqan</div>
                        </div>
                        <label class="toggle">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Organizimi --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(124,58,237,0.10);color:#7c3aed;">
                            <i class="fa-solid fa-folder-tree"></i>
                        </div>
                        Organizimi
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Kategoria *</label>
                        <select name="category_id" id="categorySelect" class="form-select" required>
                            <option value="">— Zgjedh kategorinë —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Brendi</label>
                        <select name="brand_id" id="brandSelect" class="form-select">
                            <option value="">— Zgjedh brendin —</option>
                            @if(isset($product) && $product->brand)
                                <option value="{{ $product->brand->id }}" selected>
                                    {{ $product->brand->name }}
                                </option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group" id="subcategoryGroup"
                         style="{{ isset($categorySubcategories) && count($categorySubcategories) > 0 ? '' : 'display:none;' }}">
                        <label class="form-label">Nënkategoria</label>
                        <select name="subcategory" id="subcategorySelect" class="form-select">
                            <option value="">— Zgjedh nënkategorinë —</option>
                            @if(isset($categorySubcategories))
                                @foreach($categorySubcategories as $sub)
                                    <option value="{{ $sub }}"
                                        {{ old('subcategory', $product->subcategory ?? '') == $sub ? 'selected' : '' }}>
                                        {{ $sub }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            {{-- Fotot --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-title-icon" style="background:rgba(5,150,105,0.10);color:var(--accent3);">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        Fotot
                    </div>
                </div>
                <div class="card-body" style="gap:0;padding:14px 22px;">
                    <div class="photo-stat">
                        <span style="font-size:13px;color:var(--text-muted);">Ekzistuese</span>
                        <span class="photo-stat-val" id="statExisting">
                            {{ isset($product) ? ($product->images->count() ?? 0) : 0 }}
                        </span>
                    </div>
                    <div class="photo-stat">
                        <span style="font-size:13px;color:var(--text-muted);">Të reja</span>
                        <span class="photo-stat-val" id="statNew">0</span>
                    </div>
                    <div class="photo-stat" style="border-top:1px solid var(--border);margin-top:4px;padding-top:12px;">
                        <span style="font-size:13px;font-weight:600;">Gjithsej</span>
                        <span class="photo-stat-val" style="color:var(--accent);" id="statTotal">
                            {{ isset($product) ? ($product->images->count() ?? 0) : 0 }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Butonat --}}
            <div class="pf-actions" style="display:flex;flex-direction:column;gap:10px;">
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-{{ isset($product) ? 'floppy-disk' : 'box' }}"></i>
                    {{ isset($product) ? 'Ruaj Ndryshimet' : 'Krijo Produktin' }}
                </button>
                <a href="{{ url('/admin/products') }}" class="btn btn-secondary btn-full">
                    <i class="fa-solid fa-arrow-left"></i> Kthehu
                </a>
            </div>

        </div>{{-- /RIGHT --}}
    </div>
</form>

</div>{{-- /pf-wrap --}}
@endsection
@push('scripts')
<script>
    window.ACCESSORY_CAT_IDS = @json($accessoryCategoryIds);
    window.ALL_CATEGORIES = @json($categories->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
    ]));

 @php
    $productData = [
        'existingImagesCount' => isset($product) ? ($product->images->count() ?? 0) : 0,
        'variantCount' => isset($product) && $product->variants
            ? $product->variants->groupBy(fn($v) => trim($v->color_hex ?? '') . '||' . trim($v->color_name ?? ''))->count()
            : 0,
        'specCount' => isset($product) && $product->specs ? $product->specs->count() : 0,
        'brandId' => isset($product) ? $product->brand_id : null,
        'categoryId' => isset($product) ? $product->category_id : null,
        'subcategory' => $product->subcategory ?? '',
    ];
@endphp
window.PRODUCT_DATA = @json($productData);
</script>
<script src="{{ asset('js/admin/products-create.js') }}"></script>
@endpush