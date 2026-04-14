@extends('layouts.app')

@section('title', 'Cilësimet — ProMobile Admin')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --primary:        #534AB7;
    --primary-light:  #EEEDFE;
    --primary-mid:    #AFA9EC;
    --teal:           #0F6E56;
    --teal-light:     #E1F5EE;
    --amber:          #854F0B;
    --amber-light:    #FAEEDA;
    --pink:           #993556;
    --pink-light:     #FBEAF0;
    --red:            #E24B4A;
    --red-light:      #FCEBEB;
    --surface:        #FFFFFF;
    --bg:             #F7F7F8;
    --border:         rgba(0,0,0,.09);
    --border-mid:     rgba(0,0,0,.15);
    --text:           #111111;
    --muted:          #6B7280;
    --radius-sm:      6px;
    --radius-md:      8px;
    --radius-lg:      12px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }

/* ── Layout ── */
.sw { max-width: 880px; margin: 0 auto; padding: 32px 20px 64px; }

/* ── Page header ── */
.sw-head { margin-bottom: 24px; }
.sw-head h1 { font-size: 20px; font-weight: 600; margin-bottom: 3px; }
.sw-head p  { font-size: 13px; color: var(--muted); }

/* ── Alert ── */
.sw-alert {
    display: flex; align-items: center; gap: 9px;
    background: #ECFDF5; border: 0.5px solid #6EE7B7;
    color: #065F46; padding: 11px 15px;
    border-radius: var(--radius-md); font-size: 13px; font-weight: 500;
    margin-bottom: 20px;
}

/* ── Tabs ── */
.sw-tabs {
    display: flex; gap: 0;
    border-bottom: 0.5px solid var(--border);
    margin-bottom: 22px;
}
.sw-tab {
    padding: 9px 18px;
    border: 0.5px solid transparent;
    border-bottom: none;
    background: transparent;
    color: var(--muted);
    font-size: 13px; font-weight: 500;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex; align-items: center; gap: 7px;
    border-radius: var(--radius-sm) var(--radius-sm) 0 0;
    transition: color .15s, background .15s;
    margin-bottom: -1px;
    position: relative;
}
.sw-tab:hover { color: var(--text); background: #F1F0F5; }
.sw-tab.active {
    color: var(--text);
    background: var(--surface);
    border-color: var(--border);
    border-bottom-color: var(--surface);
}

/* ── Panel ── */
.sw-panel { display: none; }
.sw-panel.active { display: block; }

/* ── Section card ── */
.sw-section {
    background: var(--surface);
    border: 0.5px solid var(--border);
    border-radius: var(--radius-lg);
    margin-bottom: 14px;
    overflow: hidden;
}

.sw-section-head {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 18px;
    border-bottom: 0.5px solid var(--border);
    background: #FAFAFA;
}
.sw-section-icon {
    width: 26px; height: 26px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.si-purple { background: var(--primary-light); color: var(--primary); }
.si-teal   { background: var(--teal-light);    color: var(--teal);    }
.si-amber  { background: var(--amber-light);   color: var(--amber);   }
.si-pink   { background: var(--pink-light);    color: var(--pink);    }

.sw-section-head span.label {
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .7px;
    color: var(--muted);
}

.sw-section-body { padding: 18px; }

/* ── Fields ── */
.sw-field { display: flex; flex-direction: column; gap: 5px; }
.sw-field label {
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--muted);
}
.sw-field input,
.sw-field textarea {
    padding: 8px 12px;
    border: 0.5px solid var(--border-mid);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    background: var(--surface);
    color: var(--text);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    width: 100%;
}
.sw-field input:focus,
.sw-field textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(83,74,183,.12);
}
.sw-field input::placeholder,
.sw-field textarea::placeholder { color: #C4C4C8; }

.sw-grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; }
.sw-grid3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 13px; }

/* ── Location cards ── */
.sw-hint {
    font-size: 12px; color: var(--muted);
    line-height: 1.6; margin-bottom: 14px;
}

.loc-card {
    border: 0.5px solid var(--border);
    border-radius: var(--radius-md);
    background: #FAFAFA;
    padding: 14px;
    margin-bottom: 10px;
}
.loc-head {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 0.5px solid var(--border);
}
.loc-label {
    display: flex; align-items: center; gap: 7px;
    font-size: 12px; color: var(--muted); font-weight: 500;
}
.loc-badge {
    background: var(--primary-light);
    color: var(--primary);
    font-size: 11px; font-weight: 600;
    padding: 1px 8px; border-radius: 999px;
}

.btn-remove {
    border: 0.5px solid var(--border-mid);
    background: var(--surface);
    color: var(--muted);
    border-radius: var(--radius-sm);
    padding: 5px 11px;
    font-size: 12px; font-weight: 500;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex; align-items: center; gap: 5px;
    transition: .15s;
}
.btn-remove:hover {
    border-color: var(--red);
    color: var(--red);
    background: var(--red-light);
}

.btn-add-loc {
    width: 100%;
    margin-top: 4px;
    border: 0.5px dashed var(--border-mid);
    background: transparent;
    color: var(--muted);
    border-radius: var(--radius-md);
    padding: 10px 14px;
    font-size: 13px; font-weight: 500;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    transition: .15s;
}
.btn-add-loc .material-symbols-outlined { font-size: 15px; }
.btn-add-loc:hover {
    color: var(--text);
    border-color: var(--border-mid);
    background: #F1F0F5;
}

.map-preview {
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 0.5px solid var(--border);
    margin-top: 11px;
}
.map-preview iframe { width: 100%; height: 170px; display: block; border: none; }

/* ── Spacing helpers ── */
.mb12 { margin-bottom: 12px; }
.mb10 { margin-bottom: 10px; }

/* ── Save row ── */
.sw-save-row {
    padding: 13px 18px;
    border-top: 0.5px solid var(--border);
    background: #FAFAFA;
    display: flex; justify-content: flex-end;
}
.btn-save {
    padding: 8px 20px;
    background: var(--primary);
    color: #EEEDFE;
    border: none;
    border-radius: var(--radius-md);
    font-size: 13px; font-weight: 500;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    display: flex; align-items: center; gap: 7px;
    transition: background .15s, transform .1s;
}
.btn-save .material-symbols-outlined { font-size: 15px; }
.btn-save:hover { background: #3C3489; }
.btn-save:active { transform: scale(.98); }

/* ── Quill overrides ── */
.ql-toolbar.ql-snow {
    border: 0.5px solid var(--border-mid) !important;
    border-bottom: none !important;
    border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
    background: #FAFAFA !important;
    font-family: 'Inter', sans-serif !important;
}
.ql-container.ql-snow {
    border: 0.5px solid var(--border-mid) !important;
    border-radius: 0 0 var(--radius-md) var(--radius-md) !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    min-height: 280px;
}
.ql-editor { min-height: 280px; color: var(--text); }

/* ── Date field ── */
.date-narrow { max-width: 220px; }

/* ── Responsive ── */
@media (max-width: 600px) {
    .sw-grid2, .sw-grid3 { grid-template-columns: 1fr; }
    .sw-tabs { flex-wrap: wrap; border-bottom: none; gap: 4px; }
    .sw-tab { border: 0.5px solid var(--border); border-radius: var(--radius-sm); margin-bottom: 0; }
    .sw-tab.active { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .loc-head { flex-direction: column; align-items: flex-start; gap: 8px; }
    .btn-remove { width: 100%; justify-content: center; }
}
</style>
@endpush

@push('scripts')
    <script src="{{ asset('js/admin/settings.js') }}"></script>
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
            <span >contacts</span> Kontakti & Kompania
        </button>
        <button class="sw-tab" onclick="swTab('terms', this)">
            <span >gavel</span> Kushtet
        </button>
        <button class="sw-tab" onclick="swTab('privacy', this)">
            <span >shield</span> Privatësia
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

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/settings.js') }}"></script>

@endpush
