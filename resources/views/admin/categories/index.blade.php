{{-- ============================================================ --}}
{{-- FILE: resources/views/categories/index.blade.php           --}}
{{-- ============================================================ --}}

@extends('layouts.admin')

@section('title', 'Kategoritë')
@section('page-title', 'Kategoritë')

@section('breadcrumb')
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>Kategoritë</span>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/categories.css') }}">
@endpush
@section('content')

<div id="cat-layout" style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">

    {{-- TABLE --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Të gjitha Kategoritë</span>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Kërko..." oninput="filterTable()">
            </div>
        </div>
        <div class="table-wrap">
            <table id="catTable">
             <thead>
    <tr>
        <th>#</th>
        <th>Emri</th>
        <th>Slug</th>
        <th>Produktet</th>
        <th>Brendet</th>
        <th>Veprimet</th>
    </tr>
</thead>
              <tbody>
    @forelse($categories as $cat)
        <tr>
            <td style="color:var(--text-muted);font-size:12px;">{{ $cat->id }}</td>
            <td>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:8px;background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.2);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:14px;">
<i class="fa-solid {{ $cat->icon ?? 'fa-tag' }}"></i>                    </div>
                    <span style="font-weight:600;">{{ $cat->name }}</span>
                </div>
            </td>
            <td style="font-size:12px;color:var(--text-muted);font-family:monospace;">
                {{ $cat->slug ?? \Str::slug($cat->name) }}
            </td>
            <td>
                <span class="badge badge-muted">
                    {{ $cat->products_count ?? ($cat->products ? $cat->products->count() : 0) }}
                </span>
            </td>
            <td>
                <span class="badge badge-muted">{{ $cat->brands_count ?? 0 }} brende</span>
            </td>
            <td>
                <div style="display:flex;gap:6px;">
                    <a href="{{ url('/admin/categories/'.$cat->id.'/edit') }}" class="btn btn-secondary btn-sm btn-icon">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form action="{{ url('/admin/categories/'.$cat->id) }}" method="POST" onsubmit="return confirm('Fshi kategorinë?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm btn-icon">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="text-align:center;padding:48px;color:var(--text-muted);">
                <i class="fa-solid fa-tags" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                Nuk ka kategori.
            </td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>

    {{-- QUICK ADD FORM --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ isset($editCategory) ? 'Edito Kategorinë' : 'Kategori e Re' }}</span>
            @if(isset($editCategory))
            <a href="{{ url('/admin/categories') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-xmark"></i> Anulo
            </a>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ isset($editCategory) ? url('/admin/categories/'.$editCategory->id) : url('/admin/categories') }}" method="POST">
                @csrf
                @if(isset($editCategory)) @method('PUT') @endif

                <div class="form-group">
                    <label class="form-label">Emri i Kategorisë *</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $editCategory->name ?? '') }}"
                        placeholder="p.sh. Elektronikë" required
                        oninput="generateSlug(this.value)">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
              <div class="form-group">

    <label class="form-label">Ikona</label>

    <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px;">
        Merr ikonë nga 
        <a href="https://fontawesome.com/icons?s=solid" target="_blank" style="color:#00bcd4;">FontAwesome</a>
        dhe shkruaj vetëm emrin pas <b>fa-</b>
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
        
        {{-- Preview --}}
        <div style="width:40px;height:40px;border-radius:10px;background:rgba(0,188,212,0.1);display:flex;align-items:center;justify-content:center;">
            <i id="iconPreviewI" class="fa-solid fa-tag"></i>
        </div>

        {{-- Input --}}
        <div style="position:relative;flex:1;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;color:var(--text-muted);font-family:monospace;">fa-</span>
            
            <input type="text"
                   id="iconInput"
                   class="form-control"
                   value="{{ old('icon', isset($editCategory) ? ltrim($editCategory->icon ?? 'fa-tag','fa-') : 'tag') }}"
                   placeholder="mobile-screen, laptop, headphones..."
                   oninput="previewIcon(this.value)"
                   style="padding-left:32px;font-family:monospace;">
        </div>
    </div>

    {{-- Hidden input që ruhet në DB --}}
    <input type="hidden" name="icon" id="iconHidden"
           value="{{ old('icon', $editCategory->icon ?? 'fa-tag') }}">

</div>
<br>
<div class="form-group">
    <label class="form-label">Brendet e Lidhura</label>
    <div style="display:flex;flex-direction:column;gap:6px;">
        @foreach($brands as $brand)
        @php
            $checked = in_array($brand->id, old('brand_ids', isset($editCategory) ? $editCategory->brands->pluck('id')->toArray() : []));
        @endphp
        <div class="brand-item {{ $checked ? 'checked' : '' }}"
             onclick="toggleBrand(this)"
             style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;border:1px solid {{ $checked ? '#00bcd4' : 'var(--border)' }};border-radius:10px;background:{{ $checked ? 'rgba(0,188,212,0.06)' : 'var(--surface2)' }};transition:all 0.15s;">

            <input type="checkbox"
                   name="brand_ids[]"
                   value="{{ $brand->id }}"
                   {{ $checked ? 'checked' : '' }}
                   style="display:none;">

            <div class="brand-checkbox-box" style="width:20px;height:20px;border-radius:6px;border:2px solid {{ $checked ? '#00bcd4' : 'var(--border)' }};background:{{ $checked ? '#00bcd4' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.15s;">
                <i class="fa-solid fa-check brand-checkbox-check" style="font-size:10px;color:white;{{ $checked ? '' : 'display:none;' }}"></i>
            </div>

            <div style="display:flex;align-items:center;gap:8px;flex:1;">
                <div style="width:28px;height:28px;border-radius:6px;background:rgba(0,188,212,0.1);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#00bcd4;flex-shrink:0;">
                    {{ strtoupper(substr($brand->name, 0, 2)) }}
                </div>
                <span style="font-size:13px;font-weight:500;color:var(--text);">{{ $brand->name }}</span>
            </div>
        </div>
        @endforeach
    </div>

    
<div class="form-group">
    <label class="form-label">Nënkategoritë</label>
    <div style="font-size:11px;color:var(--text-muted);margin-bottom:8px;">
        <i class="fa-solid fa-circle-info"></i>
        Shto nënkategoritë që do shfaqen si filtra në shop (p.sh. Kalikë, Kufje, Mbrojtëse).
    </div>
 
    {{-- Lista e nënkategorive --}}
    <div id="subcatList" style="display:flex;flex-direction:column;gap:6px;margin-bottom:8px;">
 
        {{-- Nënkategoritë ekzistuese (edit mode) --}}
        @if(isset($editCategory) && $editCategory->subcategories->count() > 0)
            @foreach($editCategory->subcategories as $sub)
                <div class="subcat-row" style="display:flex;gap:8px;align-items:center;">
                    <input type="text"
                           name="subcategories[]"
                           class="form-control"
                           value="{{ $sub->name }}"
                           placeholder="p.sh. Kalikë">
                    <button type="button"
                            onclick="this.closest('.subcat-row').remove()"
                            style="width:34px;height:34px;border-radius:8px;background:rgba(230,57,70,0.1);border:1px solid rgba(230,57,70,0.2);color:var(--danger);cursor:pointer;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
                    </button>
                </div>
            @endforeach
        @endif
 
    </div>
    <button type="button"
            onclick="addSubcatRow()"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;background:var(--surface2);border:1px solid var(--border);color:var(--text-soft);font-size:13px;cursor:pointer;width:100%;justify-content:center;">
        <i class="fa-solid fa-plus" style="font-size:11px;"></i>
        Shto Nënkategori
    </button>
</div>
</div>
<br>
                <div class="form-group">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" id="slugField" class="form-control"
                        value="{{ old('slug', $editCategory->slug ?? '') }}"
                        placeholder="elektronike">
                  
                </div>
<br>
                <div class="form-group">
                    <label class="form-label">Përshkrimi</label>
                    <textarea name="description" class="form-control" rows="3"
                        placeholder="Përshkrim i shkurtër...">{{ old('description', $editCategory->description ?? '') }}</textarea>
                </div>
                <br>

                <div class="form-group" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
                        <i class="fa-solid fa-{{ isset($editCategory) ? 'floppy-disk' : 'plus' }}"></i>
                        {{ isset($editCategory) ? 'Ruaj Ndryshimet' : 'Shto Kategorinë' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/categories.js') }}"></script>
@endpush
