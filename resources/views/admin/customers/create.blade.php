@extends('layouts.admin')
@section('title', 'Krijo Përdorues')

@section('content')
<div class="cu-wrap">

    <div class="cu-header">
        <div>
            <div class="cu-breadcrumb">
                <a href="{{ route('admin.customers.index') }}">Klientët</a>
                <i class="fa-solid fa-chevron-right" style="font-size:8px;"></i>
                <span>Krijo të Ri</span>
            </div>
            <h1 class="cu-title">Krijo <span>Përdorues</span></h1>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kthehu
        </a>
    </div>

    @if($errors->any())
        <div class="cu-alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.customers.store') }}">
        @csrf

        <div class="cu-grid">

            {{-- LEFT --}}
            <div class="cu-left">
                <div class="cu-card">
                    <div class="cu-card-head">
                        <div class="cu-card-icon" style="background:rgba(0,105,128,0.10);">
                            <i class="fa-solid fa-user" style="color:#006980;"></i>
                        </div>
                        <span>Informacioni Personal</span>
                    </div>
                    <div class="cu-card-body">
                        <div class="cu-row">
                            <div class="cu-field">
                                <label>Emri <span class="req">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       placeholder="p.sh. Eridon" required>
                                @error('name')<span class="cu-err">{{ $message }}</span>@enderror
                            </div>
                            <div class="cu-field">
                                <label>Mbiemri</label>
                                <input type="text" name="surname" value="{{ old('surname') }}"
                                       placeholder="p.sh. Bytyqi">
                            </div>
                        </div>

                        <div class="cu-field">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="email@example.com" required>
                            @error('email')<span class="cu-err">{{ $message }}</span>@enderror
                        </div>

                        <div class="cu-field">
                            <label>Telefoni</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="+383 44 000 000">
                        </div>
                    </div>
                </div>

                <div class="cu-card">
                    <div class="cu-card-head">
                        <div class="cu-card-icon" style="background:rgba(124,58,237,0.10);">
                            <i class="fa-solid fa-lock" style="color:#7c3aed;"></i>
                        </div>
                        <span>Fjalëkalimi</span>
                    </div>
                    <div class="cu-card-body">
                        <div class="cu-row">
                            <div class="cu-field">
                                <label>Fjalëkalimi <span class="req">*</span></label>
                                <input type="password" name="password"
                                       placeholder="Min. 8 karaktere" required>
                                @error('password')<span class="cu-err">{{ $message }}</span>@enderror
                            </div>
                            <div class="cu-field">
                                <label>Përsërit Fjalëkalimin <span class="req">*</span></label>
                                <input type="password" name="password_confirmation"
                                       placeholder="Përsërit fjalëkalimin" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="cu-right">
                <div class="cu-card">
                    <div class="cu-card-head">
                        <div class="cu-card-icon" style="background:rgba(5,150,105,0.10);">
                            <i class="fa-solid fa-shield-halved" style="color:#059669;"></i>
                        </div>
                        <span>Roli & Qasja</span>
                    </div>
                    <div class="cu-card-body">
                        <div class="cu-field">
                            <label>Roli <span class="req">*</span></label>
                            <select name="role">
                                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>
                                    👤 Customer
                                </option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    🛡️ Admin
                                </option>
                            </select>
                        </div>
                        <div class="cu-hint">
                            <i class="fa-solid fa-circle-info"></i>
                            Admini ka qasje të plotë në panelin e administrimit.
                        </div>
                    </div>
                </div>

                <button type="submit" class="cu-save-btn">
                    <i class="fa-solid fa-user-plus"></i>
                    Krijo Përdoruesin
                </button>
            </div>

        </div>
    </form>

</div>

<style>
.cu-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 32px 24px 60px;
}

.cu-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
}

.cu-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.cu-breadcrumb a { color: var(--text-muted); text-decoration: none; }
.cu-breadcrumb a:hover { color: #006980; }

.cu-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.5px;
}
.cu-title span { color: #006980; }

.cu-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    background: #fee2e2;
    border: 1px solid #fca5a5;
    border-radius: 12px;
    color: #991b1b;
    font-size: 13px;
    margin-bottom: 20px;
}

.cu-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 20px;
    align-items: start;
}

.cu-card {
    background: var(--surface, #fff);
    border: 1px solid var(--border, #e8eaed);
    border-radius: 18px;
    overflow: hidden;
    margin-bottom: 16px;
}

.cu-card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border, #e8eaed);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
}

.cu-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.cu-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.cu-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.cu-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.cu-field label {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.req { color: #ef4444; }

.cu-field input,
.cu-field select {
    height: 44px;
    padding: 0 14px;
    font-size: 14px;
    border: 1.5px solid var(--border, #e8eaed);
    border-radius: 10px;
    background: var(--surface2, #f8f9fa);
    color: var(--text);
    outline: none;
    transition: border-color 0.18s, box-shadow 0.18s;
    font-family: inherit;
    width: 100%;
    box-sizing: border-box;
}

.cu-field input:focus,
.cu-field select:focus {
    border-color: #006980;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(0,105,128,0.10);
}

.cu-err {
    font-size: 11px;
    color: #dc2626;
}

.cu-hint {
    font-size: 12px;
    color: var(--text-muted);
    background: var(--surface2, #f8f9fa);
    border-radius: 8px;
    padding: 10px 12px;
    display: flex;
    align-items: flex-start;
    gap: 7px;
    line-height: 1.5;
}

.cu-save-btn {
    width: 100%;
    height: 50px;
    border: none;
    border-radius: 12px;
    background: #006980;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity 0.18s, transform 0.18s;
    font-family: inherit;
}
.cu-save-btn:hover { opacity: 0.88; transform: translateY(-1px); }

@media (max-width: 768px) {
    .cu-wrap { padding: 20px 14px 40px; }
    .cu-grid { grid-template-columns: 1fr; }
    .cu-row  { grid-template-columns: 1fr; }
    .cu-right { order: -1; }
    .cu-title { font-size: 20px; }
}
</style>

@endsection