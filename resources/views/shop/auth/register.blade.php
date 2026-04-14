@extends('layouts.shop')

@section('title', 'Regjistrohu – ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/auth.css') }}">
@endpush

@section('content')
<div class="auth-page-wrap">
    <div class="auth-card">

        <div class="auth-logo">Pro<span>Mobile</span></div>

        {{-- Tab bar --}}
        <div class="auth-tab-bar">
            <a href="{{ route('login') }}" class="auth-tab">Hyr në llogari</a>
            <a href="{{ route('register') }}" class="auth-tab active">Regjistrohu</a>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Emri & Mbiemri --}}
            <div class="auth-two-col">
                <div class="auth-field">
                    <label for="name">Emri</label>
                    <input id="name" type="text" name="name"
                           value="{{ old('name') }}"
                           placeholder="Emri"
                           required autofocus autocomplete="given-name" />
                    @error('name')
                        <p class="error-msg">
                            <span class="material-symbols-outlined" style="font-size:14px;">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="surname">Mbiemri</label>
                    <input id="surname" type="text" name="surname"
                           value="{{ old('surname') }}"
                           placeholder="Mbiemri"
                           required autocomplete="family-name" />
                    @error('surname')
                        <p class="error-msg">
                            <span class="material-symbols-outlined" style="font-size:14px;">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Telefoni --}}
            <div class="auth-field">
                <label for="phone">Telefoni</label>
                <input id="phone" type="text" name="phone"
                       value="{{ old('phone') }}"
                       placeholder="+383 4X XXX XXX"
                       required autocomplete="tel" inputmode="tel" />
                @error('phone')
                    <p class="error-msg">
                        <span class="material-symbols-outlined" style="font-size:14px;">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="auth-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="shembull@email.com"
                       required autocomplete="email" />
                @error('email')
                    <p class="error-msg">
                        <span class="material-symbols-outlined" style="font-size:14px;">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password & Konfirmo --}}
            <div class="auth-two-col">
                <div class="auth-field">
                    <label for="password">Fjalëkalimi</label>
                    <input id="password" type="password" name="password"
                           placeholder="••••••••"
                           required autocomplete="new-password" />
                    @error('password')
                        <p class="error-msg">
                            <span class="material-symbols-outlined" style="font-size:14px;">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="auth-field">
                    <label for="password_confirmation">Përsërit</label>
                    <input id="password_confirmation" type="password"
                           name="password_confirmation"
                           placeholder="••••••••"
                           required autocomplete="new-password" />
                </div>
            </div>

            <button type="submit" class="auth-btn-submit">Regjistrohu</button>
        </form>

        <div class="auth-switch">
            Keni llogari?
            <a href="{{ route('login') }}">Hyrni këtu</a>
        </div>

    </div>
</div>
@endsection
