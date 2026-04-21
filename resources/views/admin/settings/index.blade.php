@extends('layouts.admin')

@section('title', 'Cilësimet — ProMobile Admin')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/settings.css') }}">
@endpush

@section('content')
<div class="sw">

    {{-- Page header --}}
    <div class="sw-head">
        <h1>Cilësimet</h1>
        <p>Menaxho të gjitha informacionet e faqes nga një vend.</p>
    </div>

    @if(session('success'))

    @endif

    {{-- Tabs --}}
    <div class="sw-tabs">
        <button class="sw-tab active" onclick="swTab('contact', this)">
             Kontakti & Kompania
        </button>
        <button class="sw-tab" onclick="swTab('terms', this)">
             Kushtet
        </button>
        <button class="sw-tab" onclick="swTab('privacy', this)">
             Privatësia
        </button>
        <button class="sw-tab" onclick="swTab('delivery', this)">
            Dërgesa
        </button>
    </div>

    {{-- ═══════════════ PANEL: CONTACT ═══════════════ --}}
    <div class="sw-panel active" id="panel-contact">
        <form method="POST" action="/admin/settings">
            @csrf
            <input type="hidden" name="section" value="contact">

            {{-- Email --}}
            <div class="sw-section">
                <div class="sw-section-head">
                    
                    <span class="label">Email</span>
                </div>
                <div class="sw-section-body">
                    <div class="sw-grid2">
                        <div class="sw-field">
                            <label>Email Kryesor</label>
                            <input type="email" name="company_email"
                                   value="{{ $settings['company_email'] ?? '' }}"
                                   placeholder="info@promobile.com">
                        </div>
                        <div class="sw-field">
                            <label>Email Support</label>
                            <input type="email" name="company_email2"
                                   value="{{ $settings['company_email2'] ?? '' }}"
                                   placeholder="support@promobile.com">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Telefoni --}}
            <div class="sw-section">
                <div class="sw-section-head">
                    
                    <span class="label">Telefoni</span>
                </div>
                <div class="sw-section-body">
                    <div class="sw-grid2">
                        <div class="sw-field">
                            <label>Telefoni 1</label>
                            <input type="text" name="company_phone"
                                   value="{{ $settings['company_phone'] ?? '' }}"
                                   placeholder="+383 44 000 000">
                        </div>
                        <div class="sw-field">
                            <label>Telefoni 2</label>
                            <input type="text" name="company_phone2"
                                   value="{{ $settings['company_phone2'] ?? '' }}"
                                   placeholder="+383 44 000 001">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lokacionet --}}
            <div class="sw-section">
                <div class="sw-section-head">
                   
                    <span class="label">Lokacionet</span>
                </div>
                <div class="sw-section-body">
                    <p class="sw-hint">Shto një ose më shumë lokacione për faqen Kontakt.</p>

                    @php
                        $locations = old('locations');
                        if (!$locations) {
                            $locations = !empty($settings['company_locations'])
                                ? $settings['company_locations']
                                : [['name' => '', 'phone' => '', 'address_full' => '', 'address_short' => '']];
                        }
                    @endphp

                    <div id="locationsWrap">
                        @foreach($locations as $i => $loc)
                        <div class="loc-card" data-index="{{ $i }}">
                            <div class="loc-head">
                                <div class="loc-label">
                                    <span>Lokacioni</span>
                                    <span class="loc-badge loc-number">{{ $i + 1 }}</span>
                                </div>
                                <button type="button" class="btn-remove" onclick="removeLoc(this)">
                                    <span class="material-symbols-outlined"></span> Largo
                                </button>
                            </div>

                            <div class="sw-grid2 mb12">
                                <div class="sw-field">
                                    <label>Emri / Qyteti</label>
                                    <input type="text"
                                           name="locations[{{ $i }}][name]"
                                           value="{{ $loc['name'] ?? '' }}"
                                           placeholder="Prishtinë">
                                </div>
                                <div class="sw-field">
                                    <label>Telefoni</label>
                                    <input type="text"
                                           name="locations[{{ $i }}][phone]"
                                           value="{{ $loc['phone'] ?? '' }}"
                                           placeholder="+383 44 000 000">
                                </div>
                            </div>

                            <div class="sw-field mb10">
                                <label>Adresa e Plotë</label>
                                <input type="text"
                                       class="loc-addr-full"
                                       name="locations[{{ $i }}][address_full]"
                                       value="{{ $loc['address_full'] ?? '' }}"
                                       placeholder="Rr. Nënë Tereza, Nr.15, Prishtinë"
                                       oninput="updateLocMap(this)">
                            </div>

                            <div class="sw-field">
                                <label>Adresa e Shkurtër</label>
                                <input type="text"
                                       name="locations[{{ $i }}][address_short]"
                                       value="{{ $loc['address_short'] ?? '' }}"
                                       placeholder="Prishtinë, Kosovë">
                            </div>

                            <div class="map-preview loc-map-wrap"
                                 style="{{ !empty($loc['address_full']) ? '' : 'display:none' }}">
                                <iframe class="loc-map-iframe"
                                        src="{{ !empty($loc['address_full']) ? 'https://maps.google.com/maps?q='.urlencode($loc['address_full']).'&output=embed' : '' }}"
                                        loading="lazy"></iframe>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn-add-loc" onclick="addLoc()">
                        Shto Lokacion
                    </button>
                </div>
            </div>

            {{-- Orët e punës --}}
            <div class="sw-section">
                <div class="sw-section-head">
                    <div class="sw-section-icon si-teal">
                    </div>
                    <span class="label">Orët e Punës</span>
                </div>
                <div class="sw-section-body">
                    <div class="sw-grid3">
                        <div class="sw-field">
                            <label>E Hënë — E Premte</label>
                            <input type="text" name="hours_weekdays"
                                   value="{{ $settings['hours_weekdays'] ?? '' }}"
                                   placeholder="09:00 — 18:00">
                        </div>
                        <div class="sw-field">
                            <label>E Shtunë</label>
                            <input type="text" name="hours_saturday"
                                   value="{{ $settings['hours_saturday'] ?? '' }}"
                                   placeholder="10:00 — 15:00">
                        </div>
                        <div class="sw-field">
                            <label>E Diel</label>
                            <input type="text" name="hours_sunday"
                                   value="{{ $settings['hours_sunday'] ?? '' }}"
                                   placeholder="Mbyllur">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social --}}
            <div class="sw-section">
                <div class="sw-section-head">
                    <div class="sw-section-icon si-pink">
                    </div>
                    <span class="label">Rrjetet Sociale</span>
                </div>
                <div class="sw-section-body">
                    <div class="sw-grid3">
                        <div class="sw-field">
                            <label>Instagram URL</label>
                            <input type="text" name="social_instagram"
                                   value="{{ $settings['social_instagram'] ?? '' }}"
                                   placeholder="https://instagram.com/...">
                        </div>
                        <div class="sw-field">
                            <label>Facebook URL</label>
                            <input type="text" name="social_facebook"
                                   value="{{ $settings['social_facebook'] ?? '' }}"
                                   placeholder="https://facebook.com/...">
                        </div>
                        <div class="sw-field">
                            <label>WhatsApp Nr.</label>
                            <input type="text" name="social_whatsapp"
                                   value="{{ $settings['social_whatsapp'] ?? '' }}"
                                   placeholder="+383 4X XXX XXX">
                        </div>
                    </div>
                </div>
                <div class="sw-save-row">
                    <button type="submit" class="btn-save">
                         Ruaj Ndryshimet
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ═══════════════ PANEL: TERMS ═══════════════ --}}
    <div class="sw-panel" id="panel-terms">
        <form method="POST" action="/admin/settings">
            @csrf
            <input type="hidden" name="section" value="terms">

            <div class="sw-section">
                <div class="sw-section-head">
                    <div class="sw-section-icon si-teal">
                    </div>
                    <span class="label">Data e Përditësimit</span>
                </div>
                <div class="sw-section-body">
                    <div class="date-narrow">
                        <div class="sw-field">
                            <label>Data (shfaqet te header i faqes)</label>
                            <input type="date" name="terms_updated"
                                   value="{{ $settings['terms_updated'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="sw-section">
                <div class="sw-section-head">
                    <div class="sw-section-icon si-purple">
                    </div>
                    <span class="label">Përmbajtja e Kushteve</span>
                </div>
                <div class="sw-section-body">
                    <div id="terms_editor"></div>
                </div>
                <div class="sw-save-row">
                    <button type="submit" class="btn-save" id="terms_submit">
                       Ruaj Kushtet
                    </button>
                </div>
            </div>
            <textarea name="terms_content" style="display:none"></textarea>
        </form>
    </div>

    {{-- ═══════════════ PANEL: PRIVACY ═══════════════ --}}
    <div class="sw-panel" id="panel-privacy">
        <form method="POST" action="/admin/settings">
            @csrf
            <input type="hidden" name="section" value="privacy">

            <div class="sw-section">
                <div class="sw-section-head">
                    <div class="sw-section-icon si-amber">
                    </div>
                    <span class="label">Data e Përditësimit</span>
                </div>
                <div class="sw-section-body">
                    <div class="date-narrow">
                        <div class="sw-field">
                            <label>Data (shfaqet te header i faqes)</label>
                            <input type="date" name="privacy_updated"
                                   value="{{ $settings['privacy_updated'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="sw-section">
                <div class="sw-section-head">
                    <div class="sw-section-icon si-pink">
                    </div>
                    <span class="label">Përmbajtja e Privatësisë</span>
                </div>
                <div class="sw-section-body">
                    <div id="privacy_editor"></div>
                </div>
                <div class="sw-save-row">
                    <button type="submit" class="btn-save" id="privacy_submit">
                        Ruaj Privatësinë
                    </button>
                </div>
            </div>
            <textarea name="privacy_content" style="display:none"></textarea>
        </form>
    </div>
{{-- ═══════════════ PANEL: DELIVERY ═══════════════ --}}
{{-- ═══════════════ PANEL: DELIVERY ═══════════════ --}}
<div class="sw-panel" id="panel-delivery">
    <form method="POST" action="{{ route('admin.settings.delivery') }}">
        @csrf

        @if(session('delivery_success'))
            <div style="margin-bottom:16px;padding:12px 16px;background:#d1fae5;border-radius:8px;color:#065f46;">
                Cilësimet e dërgesës u ruajtën!
            </div>
        @endif

        {{-- KOSOVË --}}
        <div class="sw-section">
            <div class="sw-section-head">
                <span class="label">🇽🇰 Kosovë</span>
            </div>
            <div class="sw-section-body">
                <div class="sw-grid3">
                    <div class="sw-field">
                        <label>Çmimi Minimal për Falas (€)</label>
                        <input type="number" name="shipping_kosovo_free_min"
                               value="{{ $settings['shipping_kosovo_free_min'] ?? '100' }}"
                               min="0" step="1" placeholder="100">
                    </div>
                    <div class="sw-field">
                        <label>Kostoja e Dërgesës (€)</label>
                        <input type="number" name="shipping_kosovo_cost"
                               value="{{ $settings['shipping_kosovo_cost'] ?? '2' }}"
                               min="0" step="0.50" placeholder="2">
                    </div>
                    <div class="sw-field">
                        <label>Teksti Dërgesa Falas</label>
                        <input type="text" name="shipping_kosovo_free_text"
                               value="{{ $settings['shipping_kosovo_free_text'] ?? 'Dërgesa Falas' }}"
                               placeholder="Dërgesa Falas">
                    </div>
                </div>
            </div>
        </div>

        {{-- SHQIPËRI --}}
        <div class="sw-section">
            <div class="sw-section-head">
                <span class="label">🇦🇱 Shqipëri</span>
            </div>
            <div class="sw-section-body">
                <div class="sw-grid3">
                    <div class="sw-field">
                        <label>Çmimi Minimal për Falas (€)</label>
                        <input type="number" name="shipping_albania_free_min"
                               value="{{ $settings['shipping_albania_free_min'] ?? '150' }}"
                               min="0" step="1" placeholder="150">
                    </div>
                    <div class="sw-field">
                        <label>Kostoja e Dërgesës (€)</label>
                        <input type="number" name="shipping_albania_cost"
                               value="{{ $settings['shipping_albania_cost'] ?? '5' }}"
                               min="0" step="0.50" placeholder="5">
                    </div>
                    <div class="sw-field">
                        <label>Teksti Dërgesa Falas</label>
                        <input type="text" name="shipping_albania_free_text"
                               value="{{ $settings['shipping_albania_free_text'] ?? 'Dërgesa Falas' }}"
                               placeholder="Dërgesa Falas">
                    </div>
                </div>
            </div>
        </div>

        {{-- MAQEDONI --}}
        <div class="sw-section">
            <div class="sw-section-head">
                <span class="label">🇲🇰 Maqedoni</span>
            </div>
            <div class="sw-section-body">
                <div class="sw-grid3">
                    <div class="sw-field">
                        <label>Çmimi Minimal për Falas (€)</label>
                        <input type="number" name="shipping_macedonia_free_min"
                               value="{{ $settings['shipping_macedonia_free_min'] ?? '200' }}"
                               min="0" step="1" placeholder="200">
                    </div>
                    <div class="sw-field">
                        <label>Kostoja e Dërgesës (€)</label>
                        <input type="number" name="shipping_macedonia_cost"
                               value="{{ $settings['shipping_macedonia_cost'] ?? '8' }}"
                               min="0" step="0.50" placeholder="8">
                    </div>
                    <div class="sw-field">
                        <label>Teksti Dërgesa Falas</label>
                        <input type="text" name="shipping_macedonia_free_text"
                               value="{{ $settings['shipping_macedonia_free_text'] ?? 'Dërgesa Falas' }}"
                               placeholder="Dërgesa Falas">
                    </div>
                </div>
            </div>
        </div>

      

        <div class="sw-save-row">
            <button type="submit" class="btn-save">
                Ruaj Cilësimet e Dërgesës
            </button>
        </div>

    </form>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
/* ── Quill editors ── */
const toolbarOpts = [
    ['bold','italic','underline'],
    [{'header':2},{'header':3}],
    [{'list':'ordered'},{'list':'bullet'}],
    ['link'],['clean']
];

const termsQuill = new Quill('#terms_editor', {
    theme: 'snow',
    placeholder: 'Shkruaj kushtet e përdorimit këtu...',
    modules: { toolbar: toolbarOpts }
});

const privacyQuill = new Quill('#privacy_editor', {
    theme: 'snow',
    placeholder: 'Shkruaj politikën e privatësisë këtu...',
    modules: { toolbar: toolbarOpts }
});

const termsContent   = @json($settings['terms_content']   ?? '');
const privacyContent = @json($settings['privacy_content'] ?? '');
if (termsContent)   termsQuill.root.innerHTML   = termsContent;
if (privacyContent) privacyQuill.root.innerHTML = privacyContent;

document.querySelector('#panel-terms form').addEventListener('submit', function () {
    this.querySelector('textarea[name="terms_content"]').value = termsQuill.root.innerHTML;
});
document.querySelector('#panel-privacy form').addEventListener('submit', function () {
    this.querySelector('textarea[name="privacy_content"]').value = privacyQuill.root.innerHTML;
});

/* ── Tab switching ── */
function swTab(id, el) {
    document.querySelectorAll('.sw-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.sw-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + id).classList.add('active');
    el.classList.add('active');
}

/* ── Locations ── */
let locIdx = document.querySelectorAll('.loc-card').length;

function addLoc() {
    const wrap = document.getElementById('locationsWrap');
    const num  = wrap.querySelectorAll('.loc-card').length + 1;
    const i    = locIdx++;

    const html = `
    <div class="loc-card" data-index="${i}">
        <div class="loc-head">
            <div class="loc-label">
                <span>Lokacioni</span>
                <span class="loc-badge loc-number">${num}</span>
            </div>
            <button type="button" class="btn-remove" onclick="removeLoc(this)">
                 Largo
            </button>
        </div>
        <div class="sw-grid2 mb12">
            <div class="sw-field">
                <label>Emri / Qyteti</label>
                <input type="text" name="locations[${i}][name]" placeholder="Prishtinë">
            </div>
            <div class="sw-field">
                <label>Telefoni</label>
                <input type="text" name="locations[${i}][phone]" placeholder="+383 44 000 000">
            </div>
        </div>
        <div class="sw-field mb10">
            <label>Adresa e Plotë</label>
            <input type="text" class="loc-addr-full"
                   name="locations[${i}][address_full]"
                   placeholder="Rr. Nënë Tereza, Nr.15, Prishtinë"
                   oninput="updateLocMap(this)">
        </div>
        <div class="sw-field">
            <label>Adresa e Shkurtër</label>
            <input type="text" name="locations[${i}][address_short]" placeholder="Prishtinë, Kosovë">
        </div>
        <div class="map-preview loc-map-wrap" style="display:none">
            <iframe class="loc-map-iframe" loading="lazy"></iframe>
        </div>
    </div>`;

    wrap.insertAdjacentHTML('beforeend', html);
    refreshLocNums();
}

function removeLoc(btn) {
    const wrap  = document.getElementById('locationsWrap');
    const cards = wrap.querySelectorAll('.loc-card');
    if (cards.length <= 1) { return; }
    btn.closest('.loc-card').remove();
    reindexLocs();
    refreshLocNums();
}

function refreshLocNums() {
    document.querySelectorAll('#locationsWrap .loc-card').forEach((c, i) => {
        const el = c.querySelector('.loc-number');
        if (el) el.textContent = i + 1;
    });
}

function reindexLocs() {
    document.querySelectorAll('#locationsWrap .loc-card').forEach((card, i) => {
        card.setAttribute('data-index', i);
        card.querySelectorAll('input').forEach(inp => {
            const n = inp.getAttribute('name');
            if (n) inp.setAttribute('name', n.replace(/locations\[\d+\]/, `locations[${i}]`));
        });
    });
}

function updateLocMap(input) {
    const card   = input.closest('.loc-card');
    const wrap   = card.querySelector('.loc-map-wrap');
    const iframe = card.querySelector('.loc-map-iframe');
    const val    = input.value.trim();

    clearTimeout(input._mapTimer);

    if (!val) { wrap.style.display = 'none'; iframe.src = ''; return; }

    input._mapTimer = setTimeout(() => {
        iframe.src = 'https://maps.google.com/maps?q=' + encodeURIComponent(val) + '&output=embed';
        wrap.style.display = 'block';
    }, 700);
}
</script>
@endpush
