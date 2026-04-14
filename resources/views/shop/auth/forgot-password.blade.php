{{-- resources/views/shop/auth/forgot-password.blade.php --}}
@extends('layouts.shop')

@section('content')
<div style="max-width:420px;margin:60px auto;padding:0 16px;">
    <h2 style="font-weight:700;margin-bottom:6px;">Keni harruar fjalëkalimin?</h2>
    <p style="color:#6e6e73;font-size:13px;margin-bottom:24px;">
        Shkruaj emailin tënd dhe do të dërgojmë një link për të rivendosur fjalëkalimin.
    </p>

    @if(session('status'))
        <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div style="margin-bottom:16px;">
            <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="emailijot@gmail.com"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;"
                   required autofocus>
            @error('email')
                <span style="color:#ef4444;font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit"
                style="width:100%;padding:11px;background:#111;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
            Dërgo Link-un
        </button>
    </form>

    <div style="text-align:center;margin-top:16px;">
        <a href="{{ route('login') }}" style="font-size:13px;color:#6e6e73;">← Kthehu te hyrja</a>
    </div>
</div>
@endsection