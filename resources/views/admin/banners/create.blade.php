@extends('layouts.admin')

@section('title', 'Bannerat')
@section('page-title', 'Bannerat')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/banners.css') }}">
@endpush

@section('content')
<div class="pf-wrap">

    <div class="pf-header">
        <div>
            <div class="pf-breadcrumb">
                <a href="{{ url('/admin') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Bannerat</span>
            </div>
            <h1 class="pf-title">Menaxho <span>Bannerat</span></h1>
        </div>
    </div>


    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="card-title-icon" style="background:rgba(245,197,24,0.12);color:var(--accent2);">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    Oferta / Banner
                </div>
            </div>

            <div class="card-body">

                <div class="form-group">
                    <label class="form-label">Pamja paraprake</label>
                    <div class="banner-stage" id="bannerPreview"
                         style="background-color:{{ old('banner_bg_color', '#0a0a1a') }};">
                        <div class="banner-bg-img"
                             id="previewBg"
                             style="background-position: {{ old('banner_image_position', 'center center') }};"></div>
                           <video id="previewVideo" 
                              autoplay muted loop playsinline
                            style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:none;z-index:1;">
                         </video>                    
                        <div class="banner-overlay"></div>
                        <div class="no-img-hint" id="noImgHint"><span>Ngarko foto për sfond</span></div>

                        <div class="banner-content-preview">
                            <div class="bp-badge-new" id="previewBadge">{{ old('banner_badge', '⚡ Oferta javore') }}</div>
                            <div class="bp-title-new" id="previewTitle">{{ old('banner_title', 'Titulli i bannerit') }}</div>
                            <div class="bp-sub-new" id="previewSub">{{ old('banner_subtitle', 'Përshkrimi i shkurtër...') }}</div>

                            <div class="bp-btns">
                                <div class="bp-btn-primary" id="previewBtn1">{{ old('banner_btn_primary_text', 'Buy Now') }}</div>
                                <div class="bp-btn-secondary" id="previewBtn2">{{ old('banner_btn_secondary_text', 'Shiko detajet') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns:1fr 1fr;">
                    <div class="form-group">
                        <label class="form-label">Titulli</label>
                        <input type="text"
                               name="banner_title"
                               class="form-control"
                               value="{{ old('banner_title') }}"
                               placeholder="p.sh. iPhone 15 Pro"
                               oninput="document.getElementById('previewTitle').textContent=this.value||'Titulli i bannerit'">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nëntitulli</label>
                        <input type="text"
                               name="banner_subtitle"
                               class="form-control"
                               value="{{ old('banner_subtitle') }}"
                               placeholder="Performancë premium"
                               oninput="document.getElementById('previewSub').textContent=this.value||'Përshkrimi i shkurtër...'">
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns:1fr 1fr 220px 220px;">
                    <div class="form-group">
                        <label class="form-label">Badge Teksti</label>
                        <input type="text"
                               name="banner_badge"
                               class="form-control"
                               value="{{ old('banner_badge') }}"
                               placeholder="⚡ Oferta javore"
                               oninput="document.getElementById('previewBadge').textContent=this.value||'⚡ Oferta javore'">

                        <div class="badge-presets">
                            <span class="badge-preset" onclick="setBadge('⚡ Oferta javore')">⚡ Oferta javore</span>
                            <span class="badge-preset" onclick="setBadge('🔥 Hot deal')">🔥 Hot deal</span>
                            <span class="badge-preset" onclick="setBadge('✨ I ri')">✨ I ri</span>
                            <span class="badge-preset" onclick="setBadge('🎯 Limituar')">🎯 Limituar</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto e Bannerit</label>
                        <input type="file"
                               name="banner_image"
                               class="form-control"
                               accept="image/*"
                               onchange="previewBannerImage(this)">
                        <small style="display:block;margin-top:6px;color:var(--text-muted);font-size:12px;">
                            Rekomandohet: 1920x800px ose minimumi 1600x700px.
                        </small>
                    </div>
                    {{-- pas form-group të fotos --}}
                    <div class="form-group">
                        <label class="form-label">Video e Sfondit (MP4)</label>
                        <input type="file"
                               name="banner_video"
                               class="form-control"
                               accept="video/mp4,video/webm"
                               onchange="previewBannerVideo(this)">
                                           <small style="display:block;margin-top:6px;color:var(--text-muted);font-size:12px;">
                            Rekomandohet: 1920x800px, MP4, max 10MB. Video ka prioritet mbi foton.
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pozicioni i Fotos</label>
                        <select name="banner_image_position"
                                class="form-select"
                                onchange="document.getElementById('previewBg').style.backgroundPosition=this.value">
                            <option value="left center" {{ old('banner_image_position') == 'left center' ? 'selected' : '' }}>Majtas</option>
                            <option value="center center" {{ old('banner_image_position', 'center center') == 'center center' ? 'selected' : '' }}>Qendër</option>
                            <option value="right center" {{ old('banner_image_position') == 'right center' ? 'selected' : '' }}>Djathtas</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ngjyra e Sfondit</label>
                        <div class="color-picker-row">
                            <input type="color"
                                   name="banner_bg_color"
                                   id="bgColorPicker"
                                   value="{{ old('banner_bg_color', '#0a0a1a') }}"
                                   oninput="document.getElementById('bannerPreview').style.backgroundColor=this.value;document.getElementById('bgColorText').value=this.value">

                            <input type="text"
                                   id="bgColorText"
                                   class="form-control"
                                   value="{{ old('banner_bg_color', '#0a0a1a') }}"
                                   placeholder="#0a0a1a"
                                   oninput="document.getElementById('bgColorPicker').value=this.value;document.getElementById('bannerPreview').style.backgroundColor=this.value">
                        </div>

                        <div class="swatch-row">
                            <div class="color-swatch active" style="background:#0a0a1a;" onclick="setSwatchColor('#0a0a1a',this)"></div>
                            <div class="color-swatch" style="background:#1a0a2e;" onclick="setSwatchColor('#1a0a2e',this)"></div>
                            <div class="color-swatch" style="background:#0d2137;" onclick="setSwatchColor('#0d2137',this)"></div>
                            <div class="color-swatch" style="background:#1a1200;" onclick="setSwatchColor('#1a1200',this)"></div>
                            <div class="color-swatch" style="background:#1a0010;" onclick="setSwatchColor('#1a0010',this)"></div>
                            <div class="color-swatch" style="background:#0a1a0a;" onclick="setSwatchColor('#0a1a0a',this)"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Lidhe me Produkt</label>
                    <select name="product_id" class="form-select">
                        <option value="">-- Pa produkt specifik --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted);font-size:12px;">
                        Nëse zgjedh produkt, linkat gjenerohen automatikisht.
                    </small>
                </div>

                <div class="form-row" style="grid-template-columns:1fr 1fr;">
                    <div class="form-group">
                        <label class="form-label">Butoni Kryesor — Teksti</label>
                        <input type="text"
                               name="banner_btn_primary_text"
                               class="form-control"
                               value="{{ old('banner_btn_primary_text', 'Buy Now') }}"
                               oninput="document.getElementById('previewBtn1').textContent=this.value||'Buy Now'">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Butoni Dytësor — Teksti</label>
                        <input type="text"
                               name="banner_btn_secondary_text"
                               class="form-control"
                               value="{{ old('banner_btn_secondary_text', 'Shiko detajet') }}"
                               oninput="document.getElementById('previewBtn2').textContent=this.value||'Shiko detajet'">
                    </div>
                </div>

                <div class="form-group" style="max-width:160px;">
                    <label class="form-label">Rendi (sort)</label>
                    <input type="number"
                           name="banner_sort"
                           class="form-control"
                           value="{{ old('banner_sort', 0) }}"
                           placeholder="0">
                </div>

                <button type="submit" class="btn btn-warning btn-full">
                    <i class="fa-solid fa-fire"></i> Krijo Bannerin
                </button>
            </div>
        </div>
    </form>

    <div class="card" style="margin-top:24px;">
        <div class="card-header">
            <div class="card-title">Bannerat ekzistues</div>
        </div>
        <div class="card-body">
           {{-- Bannerat ekzistues --}}
@forelse($banners as $banner)
<div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--border);gap:16px;">
    <div>
        <div style="font-weight:700;display:flex;align-items:center;gap:8px;">
            {{ $banner->title ?: 'Pa titull' }}
            @if(!($banner->is_active ?? true))
                <span style="font-size:11px;padding:2px 8px;border-radius:999px;background:rgba(255,59,48,0.1);color:#ff3b30;">Joaktiv</span>
            @endif
        </div>
        <div style="font-size:12px;color:var(--text-muted);">
            {{ $banner->btn_primary_text }} → {{ $banner->btn_primary_url }}
        </div>
    </div>

    <div style="display:flex;gap:8px;">
        {{-- Toggle active --}}
        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST">
            @csrf
            @if($banner->is_active ?? true)
                @method('PATCH')
                <button type="submit" class="btn btn-sm"
                        style="background:rgba(255,149,0,0.1);color:#ff9500;border:1px solid rgba(255,149,0,0.3);">
                    <i class="fa-solid fa-eye-slash"></i> Çaktivizo
                </button>
            @else
                @method('PATCH')
                <button type="submit" class="btn btn-sm"
                        style="background:rgba(52,199,89,0.1);color:#34c759;border:1px solid rgba(52,199,89,0.3);">
                    <i class="fa-solid fa-eye"></i> Aktivizo
                </button>
            @endif
        </form>

        {{-- Fshi --}}
        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
              onsubmit="return confirm('Me e fshi këtë banner?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger-soft btn-sm">
                <i class="fa-solid fa-trash"></i> Fshi
            </button>
        </form>
    </div>
</div>
@empty
    <div style="color:var(--text-muted);">Nuk ka bannera ende.</div>
@endforelse
        </div>
    </div>

</div>

<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <div class="card-title">
            <div class="card-title-icon" style="background:rgba(0,188,212,0.12);color:#00bcd4;">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            Upgrade Banner
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.banners.upgrade') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-row" style="grid-template-columns:1fr 1fr;">
                <div class="form-group">
                    <label class="form-label">Badge</label>
                    <input type="text"
                           name="upgrade_badge"
                           class="form-control"
                           value="{{ old('upgrade_badge', cache('upgrade_badge')) }}"
                           placeholder="p.sh. ♻️ Upgrade Program">
                </div>

                <div class="form-group">
                    <label class="form-label">Titulli</label>
                    <input type="text"
                           name="upgrade_title"
                           class="form-control"
                           value="{{ old('upgrade_title', cache('upgrade_title')) }}"
                           placeholder="p.sh. Upgrade the Old">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nëntitulli</label>
                <textarea name="upgrade_subtitle"
                          class="form-control"
                          rows="3"
                          placeholder="p.sh. Ndërro telefonin tënd të vjetër me një model më të ri.">{{ old('upgrade_subtitle', cache('upgrade_subtitle')) }}</textarea>
            </div>

            <div class="form-row" style="grid-template-columns:1fr 1fr;">
                <div class="form-group">
                    <label class="form-label">Teksti i butonit</label>
                    <input type="text"
                           name="upgrade_button_text"
                           class="form-control"
                           value="{{ old('upgrade_button_text', cache('upgrade_button_text')) }}"
                           placeholder="p.sh. Shiko ofertat">
                </div>

                <div class="form-group">
                    <label class="form-label">URL</label>
                    <input type="text"
                           name="upgrade_url"
                           class="form-control"
                           value="{{ old('upgrade_url', cache('upgrade_url', '/shop')) }}"
                           placeholder="/shop">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Foto e background-it</label>
                <input type="file"
                       name="upgrade_image"
                       class="form-control"
                       accept="image/*">

                @if(cache('upgrade_image'))
                    <div style="margin-top:10px;">
                        <img src="{{ asset('storage/' . cache('upgrade_image')) }}"
                             alt="Upgrade preview"
                             style="width:220px;max-width:100%;border-radius:14px;object-fit:cover;">
                    </div>
                @endif
            </div>

            <div class="form-group" style="max-width:220px;">
                <label style="display:flex;gap:10px;align-items:center;">
                    <input type="checkbox"
                           name="upgrade_active"
                           value="1"
                           {{ cache('upgrade_active', true) ? 'checked' : '' }}>
                    Aktivizo seksionin
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Ruaj upgrade banner
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/banners.js') }}"></script>
@endpush
