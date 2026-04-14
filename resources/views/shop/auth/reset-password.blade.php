@extends('layouts.shop')

@section('content')
<div style="max-width:420px;margin:60px auto;padding:0 16px;">
    <h2 style="font-weight:700;margin-bottom:6px;">Vendos fjalëkalimin e ri</h2>
    <p style="color:#6e6e73;font-size:13px;margin-bottom:24px;">
        Vendos fjalëkalimin e ri për llogarinë tënde.
    </p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div style="margin-bottom:16px;">
            <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Email</label>
            <input type="email" name="email" value="{{ old('email', $request->email) }}"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;box-sizing:border-box;"
                   required autofocus autocomplete="username">
            @error('email')
                <span style="color:#ef4444;font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:16px;">
            <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Fjalëkalimi i ri</label>
            <input type="password" name="password"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;box-sizing:border-box;"
                   required autocomplete="new-password">
            @error('password')
                <span style="color:#ef4444;font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom:24px;">
            <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Përsërit fjalëkalimin</label>
            <input type="password" name="password_confirmation"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;box-sizing:border-box;"
                   required autocomplete="new-password">
        </div>

        <button type="submit"
                style="width:100%;padding:11px;background:#111;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
            Ndrysho Fjalëkalimin
        </button>
    </form>
</div>
@endsection