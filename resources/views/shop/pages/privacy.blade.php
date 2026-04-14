@extends('layouts.shop')

@section('title', 'Politika e Privatësisë — ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/static.css') }}">
@endpush

@section('content')
<div class="legal-wrap">
    <div class="legal-header">
        <h1>Politika e Privatësisë</h1>
        <p>
            @if(setting('privacy_updated'))
                Përditësuar: {{ setting('privacy_updated') }} &nbsp;·&nbsp; ProMobile
            @else
                ProMobile
            @endif
        </p>
    </div>

    @if(setting('privacy_content'))
        <div class="legal-content">
            {!! setting('privacy_content') !!}
        </div>
    @else
        <div class="legal-empty">
            <span class="material-symbols-outlined" style="font-size:48px;color:var(--text-muted);">shield</span>
            <p>Politika e privatësisë nuk është vendosur ende.</p>
            <a href="/admin/settings" style="color:var(--primary);font-size:13px;margin-top:8px;display:inline-block;">
                Shto nga paneli admin →
            </a>
        </div>
    @endif
</div>
@endsection