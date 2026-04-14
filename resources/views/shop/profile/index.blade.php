@extends('layouts.shop')

@section('title', 'Profili im – ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/profile.css') }}">
@endpush

@section('content')
<div class="profile-wrap">

    {{-- ── HERO HEADER ── --}}
    <div class="profile-hero">
        <div class="profile-hero-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="profile-hero-info">
            <div class="profile-hero-name">{{ $user->name }} {{ $user->surname ?? '' }}</div>
            <div class="profile-hero-email">{{ $user->email }}</div>
        </div>
        <a href="{{ route('password.request') }}" class="profile-hero-reset">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Harrove fjalëkalimin?
        </a>
    </div>

    @if(session('success'))
        <div class="pf-alert pf-alert--success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="pf-alert pf-alert--error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="profile-body">

        {{-- ── LEFT ── --}}
        <div class="profile-left">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                {{-- Personale --}}
                <div class="pf-card">
                    <div class="pf-card-head">
                        <div class="pf-card-icon" style="background:#e6f6f8;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#006980" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <span>Të dhënat personale</span>
                    </div>
                    <div class="pf-grid">
                        <div class="pf-field">
                            <label>Emri</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Emri" required />
                            @error('name')<span class="pf-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="pf-field">
                            <label>Mbiemri</label>
                            <input type="text" name="surname" value="{{ old('surname', $user->surname ?? '') }}" placeholder="Mbiemri" />
                        </div>
                        <div class="pf-field pf-full">
                            <label>Telefoni</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+383 4X XXX XXX" />
                        </div>
                    </div>
                </div>

                {{-- Adresa --}}
                <div class="pf-card">
                    <div class="pf-card-head">
                        <div class="pf-card-icon" style="background:#e6f6f8;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#006980" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <span>Adresa e dorëzimit</span>
                    </div>
                    <div class="pf-grid">
                        <div class="pf-field pf-full">
                            <label>Adresa</label>
                            <input type="text" name="address_line_1" value="{{ old('address_line_1', $address->address_line_1 ?? '') }}" placeholder="Rr. Nënë Tereza, Nr. 10" />
                        </div>
                        <div class="pf-field">
                            <label>Qyteti</label>
                            <input type="text" name="city" value="{{ old('city', $address->city ?? '') }}" placeholder="Prishtinë" />
                        </div>
                        <div class="pf-field">
                            <label>Kodi Postar</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" placeholder="10000" />
                        </div>
                        <div class="pf-field pf-full">
                            <label>Shteti</label>
                            <input type="text" name="country" value="{{ old('country', $address->country ?? 'Kosovë') }}" placeholder="Kosovë" />
                        </div>
                    </div>
                </div>

                {{-- Password --}}
                <div class="pf-card">
                    <div class="pf-card-head">
                        <div class="pf-card-icon" style="background:#e6f6f8;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#006980" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </div>
                        <span>Ndrysho fjalëkalimin</span>
                    </div>
                    <div class="pf-grid">
                        <div class="pf-field pf-full">
                            <label>Fjalëkalimi aktual</label>
                            <input type="password" name="current_password" placeholder="••••••••" />
                            @error('current_password')<span class="pf-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="pf-field">
                            <label>Fjalëkalimi i ri</label>
                            <input type="password" name="password" placeholder="••••••••" />
                            @error('password')<span class="pf-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="pf-field">
                            <label>Përsërit fjalëkalimin</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" />
                        </div>
                    </div>
                    <p class="pf-hint">Lëre bosh nëse nuk dëshiron të ndryshosh fjalëkalimin.</p>
                </div>

                <button type="submit" class="pf-save-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Ruaj ndryshimet
                </button>
            </form>
        </div>

        {{-- ── RIGHT: Orders ── --}}
        <div class="profile-right">
            <div class="orders-box">
                <div class="orders-box-head">
                    <div class="orders-box-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#006980" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Porositë e mia
                    </div>
                    @if(isset($orders) && $orders->count() > 0)
                        <span class="orders-count">{{ $orders->count() }}</span>
                    @endif
                </div>

                <div class="orders-list">
                    @if(isset($orders) && $orders->count() > 0)
                        @foreach($orders as $order)
                        <details class="order-item">
                            <summary class="order-row">
                                <div class="order-row-main">
                                    <div class="order-row-top">
                                        <span class="order-num">#{{ $order->order_number ?? $order->id }}</span>
                                        <span class="order-amt">{{ number_format($order->total_amount, 2) }} €</span>
                                    </div>
                                    <div class="order-row-bot">
                                        <span class="order-dt">{{ $order->created_at->format('d M Y') }}</span>
                                        <span class="order-badge badge-{{ $order->status ?? 'pending' }}">
                                            {{ match($order->status ?? 'pending') {
                                                'pending'          => 'Në pritje',
                                                'awaiting_payment' => 'Pret pagesën',
                                                'confirmed'        => 'Konfirmuar',
                                                'shipped'          => 'Dërguar',
                                                'delivered'        => 'Dorëzuar',
                                                'cancelled'        => 'Anuluar',
                                                default            => ucfirst($order->status)
                                            } }}
                                        </span>
                                    </div>
                                </div>
                                <svg class="order-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </summary>

                            <div class="order-detail">
                                @foreach($order->items as $item)
                                <div class="order-detail-row">
                                    <span>{{ optional($item->product)->name ?? 'Produkt' }} × {{ $item->quantity }}</span>
                                    <strong>{{ number_format($item->subtotal, 2) }} €</strong>
                                </div>
                                @endforeach
                                <div class="order-meta">
                                    <div><span>Pagesa</span><strong>{{ $order->payment_method === 'bank' ? 'Kartë' : 'Cash' }}</strong></div>
                                    <div><span>Adresa</span><strong>{{ $order->shipping_address ?? '—' }}</strong></div>
                                    <div><span>Qyteti</span><strong>{{ $order->city ?? '—' }}</strong></div>
                                </div>
                            </div>
                        </details>
                        @endforeach
                    @else
                        <div class="orders-empty">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#006980" stroke-width="1.5" style="opacity:0.4;display:block;margin:0 auto 10px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            Nuk keni asnjë porosi ende.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection