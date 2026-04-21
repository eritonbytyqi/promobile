@extends('layouts.admin')

@section('title', 'Brendet')
@section('page-title', 'Brendet')

@section('breadcrumb')
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>Brendet</span>
@endsection

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/brands.css') }}">
@endpush
<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">

    {{-- TABELA --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Të gjitha Brendet</span>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Kërko brendin..." oninput="filterTable()">
            </div>
        </div>
    <div class="table-wrap">
    <table id="brandsTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Emri</th>
                <th>Produktet</th>
                <th>Kategoritë</th>
                <th>Veprimet</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $brand->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:8px;background:rgba(201,180,88,0.1);border:1px solid rgba(201,180,88,0.2);display:flex;align-items:center;justify-content:center;color:var(--accent2);font-size:14px;flex-shrink:0;">
                                <i class="fa-solid fa-copyright"></i>
                            </div>
                            <span style="font-weight:600;">{{ $brand->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-muted">{{ $brand->products_count ?? 0 }} produkte</span>
                    </td>
               <td>
    @if($brand->categories->count())
        <div style="display:flex;flex-wrap:wrap;gap:6px;">
            @foreach($brand->categories as $cat)
                <span class="badge badge-muted">{{ $cat->name }}</span>
            @endforeach
        </div>
    @else
        <span style="color:var(--text-muted);font-size:12px;">—</span>
    @endif
</td>

<td>
    <div style="display:flex;gap:6px;">
        <a href="{{ url('/admin/brands/'.$brand->id.'/edit') }}" class="btn btn-secondary btn-sm btn-icon" title="Edito">
            <i class="fa-solid fa-pen"></i>
        </a>
        <form action="{{ url('/admin/brands/'.$brand->id) }}" method="POST" onsubmit="return confirm('Fshi brendin {{ addslashes($brand->name) }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Fshi">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</td>
                       
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <i class="fa-solid fa-copyright" style="font-size:32px;display:block;margin-bottom:12px;opacity:0.3;"></i>
                        Nuk ka brende ende.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{-- MOBILE LIST --}}
<div class="mobile-brands" style="display:none;">
    @forelse($brands as $brand)
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid var(--border);">
        
        {{-- Ikona --}}
        <div style="width:40px;height:40px;border-radius:10px;background:rgba(201,180,88,0.1);border:1px solid rgba(201,180,88,0.2);display:flex;align-items:center;justify-content:center;color:var(--accent2);font-size:16px;flex-shrink:0;">
            <i class="fa-solid fa-copyright"></i>
        </div>

        {{-- Info --}}
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;font-size:14px;color:var(--text);margin-bottom:4px;">
                {{ $brand->name }}
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <span class="badge badge-muted" style="font-size:10px;">{{ $brand->products_count ?? 0 }} produkte</span>
                @foreach($brand->categories->take(2) as $cat)
                    <span class="badge badge-muted" style="font-size:10px;">{{ $cat->name }}</span>
                @endforeach
            </div>
        </div>

        {{-- Veprimet --}}
        <div style="display:flex;flex-direction:column;gap:5px;flex-shrink:0;">
            <a href="{{ url('/admin/brands/'.$brand->id.'/edit') }}" 
               style="width:34px;height:34px;border-radius:8px;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-soft);font-size:13px;">
                <i class="fa-solid fa-pen"></i>
            </a>
            <form action="{{ url('/admin/brands/'.$brand->id) }}" method="POST" 
                  onsubmit="return confirm('Fshi {{ addslashes($brand->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" 
                        style="width:34px;height:34px;border-radius:8px;background:rgba(230,57,70,0.1);border:1px solid rgba(230,57,70,0.2);display:flex;align-items:center;justify-content:center;color:var(--danger);font-size:13px;cursor:pointer;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:var(--text-muted);">
        <i class="fa-solid fa-copyright" style="font-size:28px;display:block;margin-bottom:10px;opacity:0.3;"></i>
        Nuk ka brende ende.
    </div>
    @endforelse
</div>
    </div>

    {{-- FORMA ANASH --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                {{ isset($editBrand) ? 'Edito Brendin' : 'Brend i Ri' }}
            </span>
            @if(isset($editBrand))
            <a href="{{ url('/admin/brands') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-xmark"></i> Anulo
            </a>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ isset($editBrand) ? url('/admin/brands/'.$editBrand->id) : url('/admin/brands') }}" method="POST">
                @csrf
                @if(isset($editBrand)) @method('PUT') @endif

                <div class="form-group">
                    <label class="form-label">Emri i Brendit *</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $editBrand->name ?? '') }}"
                        placeholder="p.sh. Apple, Samsung, Nike..."
                        required autofocus>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Slug (opsional)</label>
                    <input type="text" name="slug" class="form-control"
                        value="{{ old('slug', $editBrand->slug ?? '') }}"
                        placeholder="apple">
                    <div style="font-size:11px;color:var(--text-muted);margin-top:5px;">
                        <i class="fa-solid fa-link"></i> Lëre bosh për ta gjeneruar automatikisht
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Përshkrimi (opsional)</label>
                    <textarea name="description" class="form-control" rows="3"
                        placeholder="Përshkrim i shkurtër i brendit...">{{ old('description', $editBrand->description ?? '') }}</textarea>
                </div>
       <div class="form-group">
    <label class="form-label">Kategoritë e Lidhura</label>
    <div style="display:flex;flex-direction:column;gap:6px;">
        @foreach($categories as $category)
        @php
            $checked = in_array($category->id, old('category_ids', isset($editBrand) ? $editBrand->categories->pluck('id')->toArray() : []));
        @endphp
        <div class="brand-item {{ $checked ? 'checked' : '' }}"
             onclick="toggleBrand(this)"
             style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;border:1px solid {{ $checked ? '#00bcd4' : 'var(--border)' }};border-radius:10px;background:{{ $checked ? 'rgba(0,188,212,0.06)' : 'var(--surface2)' }};transition:all 0.15s;">

            <input type="checkbox"
                   name="category_ids[]"
                   value="{{ $category->id }}"
                   {{ $checked ? 'checked' : '' }}
                   style="display:none;">

            <div class="brand-checkbox-box" style="width:20px;height:20px;border-radius:6px;border:2px solid {{ $checked ? '#00bcd4' : 'var(--border)' }};background:{{ $checked ? '#00bcd4' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all 0.15s;">
                <i class="fa-solid fa-check brand-checkbox-check" style="font-size:10px;color:white;{{ $checked ? '' : 'display:none;' }}"></i>
            </div>

            <div style="display:flex;align-items:center;gap:8px;flex:1;">
                <div style="width:28px;height:28px;border-radius:6px;background:rgba(0,188,212,0.1);display:flex;align-items:center;justify-content:center;font-size:13px;color:#00bcd4;flex-shrink:0;">
                    <i class="fa-solid {{ $category->icon ?? 'fa-tag' }}"></i>
                </div>
                <span style="font-size:13px;font-weight:500;color:var(--text);">{{ $category->name }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
                <div class="form-group" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;">
                        <i class="fa-solid fa-{{ isset($editBrand) ? 'floppy-disk' : 'plus' }}"></i>
                        {{ isset($editBrand) ? 'Ruaj Ndryshimet' : 'Shto Brendin' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

   </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/brands.js') }}"></script>
@endpush
