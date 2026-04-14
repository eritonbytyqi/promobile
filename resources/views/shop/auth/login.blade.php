@extends('layouts.shop')

@section('title', 'Hyr në llogari – ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/auth.css') }}">
@endpush

@section('content')
<div class="auth-page-wrap">
    <div class="auth-card">

        <div class="auth-logo">Pro<span>Mobile</span></div>

        {{-- Tab bar --}}
        <div class="auth-tab-bar">
            <a href="{{ route('login') }}" class="auth-tab active">Hyr në llogari</a>
            <a href="{{ route('register') }}" class="auth-tab">Regjistrohu</a>
        </div>

        {{-- Session status --}}
        @if (session('status'))
            <div class="auth-session-status">
                <span class="material-symbols-outlined" style="font-size:16px;">check_circle</span>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="auth-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="shembull@email.com"
                       required autofocus autocomplete="username" />
                @error('email')
                    <p class="error-msg">
                        <span class="material-symbols-outlined" style="font-size:14px;">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="auth-field">
                <label for="password">Fjalëkalimi</label>
                <input id="password" type="password" name="password"
                       placeholder="••••••••"
                       required autocomplete="current-password" />
                @error('password')
                    <p class="error-msg">
                        <span class="material-symbols-outlined" style="font-size:14px;">error</span>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Forgot password --}}
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-forgot">
                    Harruat fjalëkalimin?
                </a>
            @endif

            {{-- Remember me --}}
            <div class="auth-check-row">
                <input id="remember_me" type="checkbox" name="remember" />
                <label for="remember_me">Mbaje mend</label>
            </div>

            <button type="submit" class="auth-btn-submit">Hyr</button>
        </form>

        <div class="auth-switch">
            Nuk keni llogari?
<a href="{{ route('register') }}">Regjistrohu</a>
        </div>

    </div>
</div>
@endsection
