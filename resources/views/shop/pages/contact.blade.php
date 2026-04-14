@extends('layouts.shop')

@section('title', 'Kontakto — ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/contact.css') }}">
@endpush

@section('content')
@php
    $locations = setting('company_locations');
    $locations = $locations ? json_decode($locations, true) : [];
@endphp

<div class="contact-page">

    {{-- HERO --}}
    <div class="ct-hero">
        <div class="ct-hero-badge">
            <i class="fa-solid fa-headset"></i>
            Kontakt & Mbështetje
        </div>
        <h1>Na kontaktoni</h1>
        <p>Jemi këtu për çdo pyetje, porosi ose mbështetje.</p>
    </div>

    @if(session('contact_success'))
        <div class="ct-alert">
            <i class="fa-solid fa-circle-check"></i>
            Mesazhi u dërgua me sukses. Do t'ju kontaktojmë së shpejti!
        </div>
    @endif

    {{-- QUICK INFO BAR --}}
    <div class="ct-info-bar">
        @if(setting('company_phone'))
        <a href="tel:{{ preg_replace('/\s+/', '', setting('company_phone')) }}" class="ct-info-item">
            <div class="ct-info-icon"><i class="fa-solid fa-phone"></i></div>
            <div>
                <div class="ct-info-label">Telefoni</div>
                <div class="ct-info-value">{{ setting('company_phone') }}</div>
            </div>
        </a>
        @endif

        @if(setting('company_email'))
        <a href="mailto:{{ setting('company_email') }}" class="ct-info-item">
            <div class="ct-info-icon"><i class="fa-solid fa-envelope"></i></div>
            <div>
                <div class="ct-info-label">Email</div>
                <div class="ct-info-value">{{ setting('company_email') }}</div>
            </div>
        </a>
        @endif

        @if(setting('social_whatsapp'))
        <a href="https://wa.me/{{ preg_replace('/\s+/', '', setting('social_whatsapp')) }}" target="_blank" class="ct-info-item">
            <div class="ct-info-icon"><i class="fa-brands fa-whatsapp"></i></div>
            <div>
                <div class="ct-info-label">WhatsApp</div>
                <div class="ct-info-value">{{ setting('social_whatsapp') }}</div>
            </div>
        </a>
        @endif

        @if(setting('hours_weekdays'))
        <div class="ct-info-item">
            <div class="ct-info-icon"><i class="fa-solid fa-clock"></i></div>
            <div>
                <div class="ct-info-label">Orari</div>
                <div class="ct-info-value">{{ setting('hours_weekdays') }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- MAIN GRID --}}
    <div class="ct-main">

        {{-- LOKACIONET --}}
        @if(count($locations))
        <div class="ct-card">
            <div class="ct-card-head">
                <i class="fa-solid fa-location-dot"></i>
                Lokacionet tona
            </div>
            <div class="ct-card-body">
                @foreach($locations as $loc)
                <div class="ct-location">
                    <div class="ct-location-info">
                        <div class="ct-location-name">{{ $loc['name'] ?? 'Lokacioni' }}</div>
                        @if(!empty($loc['address_full']))
                            <div class="ct-location-addr">{{ $loc['address_full'] }}</div>
                        @endif
                        @if(!empty($loc['phone']))
                            <a href="tel:{{ preg_replace('/\s+/', '', $loc['phone']) }}" class="ct-location-phone">
                                <i class="fa-solid fa-phone" style="font-size:11px;"></i>
                                {{ $loc['phone'] }}
                            </a>
                        @endif
                    </div>
                    @if(!empty($loc['address_full']))
                    <div class="ct-map">
                        <iframe
                            src="https://maps.google.com/maps?q={{ urlencode($loc['address_full']) }}&output=embed"
                            loading="lazy" allowfullscreen>
                        </iframe>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ANASH --}}
        <div class="ct-side">

            {{-- ORARI --}}
            @if(setting('hours_weekdays') || setting('hours_saturday') || setting('hours_sunday'))
            <div class="ct-card">
                <div class="ct-card-head">
                    <i class="fa-solid fa-clock"></i>
                    Orët e punës
                </div>
                <div class="ct-card-body" style="gap:8px;">
                    @if(setting('hours_weekdays'))
                    <div class="ct-hours-row">
                        <span>E Hënë — E Premte</span>
                        <span>{{ setting('hours_weekdays') }}</span>
                    </div>
                    @endif
                    @if(setting('hours_saturday'))
                    <div class="ct-hours-row">
                        <span>E Shtunë</span>
                        <span>{{ setting('hours_saturday') }}</span>
                    </div>
                    @endif
                    @if(setting('hours_sunday'))
                    <div class="ct-hours-row">
                        <span>E Diel</span>
                        <span>{{ setting('hours_sunday') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- SOCIAL --}}
            @if(setting('social_instagram') || setting('social_facebook'))
            <div class="ct-card">
                <div class="ct-card-head">
                    <i class="fa-solid fa-share-nodes"></i>
                    Na ndiqni
                </div>
                <div class="ct-card-body">
                    <div class="ct-social">
                        @if(setting('social_instagram'))
                        <a href="{{ setting('social_instagram') }}" target="_blank" class="ct-social-btn">
                            <i class="fa-brands fa-instagram"></i> Instagram
                        </a>
                        @endif
                        @if(setting('social_facebook'))
                        <a href="{{ setting('social_facebook') }}" target="_blank" class="ct-social-btn">
                            <i class="fa-brands fa-facebook-f"></i> Facebook
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- KONTAKTE SHTESË --}}
            @if(setting('company_email2') || setting('company_phone2'))
            <div class="ct-card">
                <div class="ct-card-head">
                    <i class="fa-solid fa-address-book"></i>
                    Kontakte shtesë
                </div>
                <div class="ct-card-body" style="gap:10px;">
                    @if(setting('company_phone2'))
                    <a href="tel:{{ preg_replace('/\s+/', '', setting('company_phone2')) }}" class="ct-extra-link">
                        <i class="fa-solid fa-phone"></i>
                        {{ setting('company_phone2') }}
                    </a>
                    @endif
                    @if(setting('company_email2'))
                    <a href="mailto:{{ setting('company_email2') }}" class="ct-extra-link">
                        <i class="fa-solid fa-envelope"></i>
                        {{ setting('company_email2') }}
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection