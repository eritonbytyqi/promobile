@extends('layouts.admin')

@section('title', 'Profili im — Admin')
@section('page-title', 'Profili')

@push('styles')
<style>
.ap-wrap { max-width: 640px; }

.ap-hero {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    margin-bottom: 20px;
}

.ap-avatar {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: var(--accent);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; font-weight: 800; flex-shrink: 0;
}

.ap-name { font-size: 18px; font-weight: 800; color: var(--text); }
.ap-role { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
.ap-email { font-size: 13px; color: var(--accent); margin-top: 2px; }

.ap-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
}

.ap-card-head {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 15px 20px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    background: var(--surface2);
}

.ap-card-head i { color: var(--accent); }

.ap-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.ap-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.ap-field { display: flex; flex-direction: column; gap: 6px; }

.ap-field label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
    color: var(--text-muted);
}

.ap-field input {
    height: 42px; padding: 0 13px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: var(--surface2);
    color: var(--text);
    font-size: 13.5px; font-family: inherit;
    outline: none;
    transition: border-color 0.18s, box-shadow 0.18s;
}

.ap-field input:focus {
    border-color: var(--accent);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(0,105,128,0.10);
}

.ap-hint {
    font-size: 12px;
    color: var(--text-muted);
    padding: 10px 12px;
    background: var(--surface2);
    border-radius: 8px;
}

.ap-actions { display: flex; gap: 10px; margin-top: 4px; }

@media (max-width: 600px) {
    .ap-row { grid-template-columns: 1fr; }
    .ap-wrap { max-width: 100%; }
}
</style>
@endpush

@section('content')
<div class="ap-wrap">

    @if(session('success'))
        <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
  
    {{-- HERO --}}
    <div class="ap-hero">
        <div class="ap-avatar">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>
        <div>
            <div class="ap-name">{{ auth()->user()->name }} {{ auth()->user()->surname ?? '' }}</div>
            <div class="ap-role">🛡️ Administrator</div>
            <div class="ap-email">{{ auth()->user()->email }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf

        {{-- Info personale --}}
        <div class="ap-card">
            <div class="ap-card-head">
                <i class="fa-solid fa-user"></i> Informacioni Personal
            </div>
            <div class="ap-card-body">
                <div class="ap-row">
                    <div class="ap-field">
                        <label>Emri *</label>
                        <input type="text" name="name"
                               value="{{ old('name', auth()->user()->name) }}" required autocomplete="given-name">
                        @error('name')<span style="font-size:11px;color:var(--danger);">{{ $message }}</span>@enderror
                    </div>
                    <div class="ap-field">
                        <label>Mbiemri</label>
                        <input type="text" name="surname"
                               value="{{ old('surname', auth()->user()->surname ?? '') }}" autocomplete="family-name">
                    </div>
                </div>
                <div class="ap-field">
                    <label>Email *</label>
                    <input type="email" name="email"
                           value="{{ old('email', auth()->user()->email) }}" required autocomplete="email">
                    @error('email')<span style="font-size:11px;color:var(--danger);">{{ $message }}</span>@enderror
                </div>
                <div class="ap-field">
                    <label>Telefoni</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', auth()->user()->phone ?? '') }}"
                           placeholder="+383 44 000 000" autocomplete="tel" inputmode="tel">
                </div>
            </div>
        </div>

        {{-- Fjalëkalimi --}}
        <div class="ap-card">
            <div class="ap-card-head">
                <i class="fa-solid fa-lock"></i> Ndrysho Fjalëkalimin
            </div>
            <div class="ap-card-body">
                <div class="ap-field">
                    <label>Fjalëkalimi Aktual</label>
                    <input type="password" name="current_password" placeholder="••••••••" autocomplete="current-password">
                    @error('current_password')<span style="font-size:11px;color:var(--danger);">{{ $message }}</span>@enderror
                </div>
                <div class="ap-row">
                    <div class="ap-field">
                        <label>Fjalëkalimi i Ri</label>
                        <input type="password" name="password" placeholder="Min. 8 karaktere" autocomplete="new-password">
                        @error('password')<span style="font-size:11px;color:var(--danger);">{{ $message }}</span>@enderror
                    </div>
                    <div class="ap-field">
                        <label>Përsërit</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••" autocomplete="new-password">
                    </div>
                </div>
                <div class="ap-hint">
                    <i class="fa-solid fa-circle-info" style="color:var(--accent);"></i>
                    Lëre bosh nëse nuk dëshiron të ndryshosh fjalëkalimin.
                </div>
            </div>
        </div>

        <div class="ap-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Ruaj Ndryshimet
            </button>
            <a href="{{ url('/admin') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kthehu
            </a>
        </div>

    </form>
</div>
@endsection