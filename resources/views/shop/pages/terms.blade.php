@extends('layouts.shop')

@section('title', 'Kushtet e Përdorimit — ProMobile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shop/static.css') }}">
@endpush

@section('content')
<div class="legal-wrap">

    <div class="legal-header">
        <h1>Kushtet e Përdorimit</h1>
        <p>
            @if(setting('terms_updated'))
                Përditësuar: {{ setting('terms_updated') }} &nbsp;·&nbsp; ProMobile
            @else
                ProMobile
            @endif
        </p>
    </div>

    @if(setting('terms_content'))
        <div class="legal-content">
            {!! setting('terms_content') !!}
        </div>
    @else
        <div class="legal-empty">
            <span class="material-symbols-outlined" style="font-size:48px;color:var(--text-muted);">article</span>
            Kushtet e përdorimit nuk janë vendosur ende.<br>
            <a href="/admin/settings" style="color:var(--primary);font-size:13px;margin-top:8px;display:inline-block;">
                Shto nga paneli admin →
            </a>
        </div>
    @endif

</div>
@endsection