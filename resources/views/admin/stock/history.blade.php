@extends('layouts.admin')

@section('title', 'Historia e Stokut — ' . $product->name)
@section('page-title', 'Historia e Stokut')

@section('breadcrumb')
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <a href="{{ route('admin.stock.index') }}">Stoku</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>{{ $product->name }}</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/stock.css') }}">
@endpush

@section('content')

{{-- Header produktit --}}
<div class="hist-header">
    @php
        $img = $product->images?->firstWhere('is_primary', true) ?? $product->images?->first();
    @endphp
    <div class="hist-img">
        @if($img)<img src="{{ asset('storage/'.$img->image_path) }}" alt="">
        @else<i class="fa-solid fa-box"></i>@endif
    </div>
    <div>
        <div class="hist-name">{{ $product->name }}</div>
        <div class="hist-sub">
            {{ $logs->total() }} regjistrime gjithsej
        </div>
    </div>
    <a href="{{ route('admin.stock.index') }}"
       class="btn btn-secondary" style="margin-left:auto;">
        <i class="fa-solid fa-arrow-left"></i> Kthehu
    </a>
</div>

{{-- Tabela --}}
<table class="hist-table">
    <thead>
        <tr>
            <th>Data</th>
            <th>Lloji</th>
            <th>Varianti</th>
            <th>Sasia</th>
            <th>Para</th>
            <th>Pas</th>
            <th>Shënim</th>
            <th>Nga</th>
        </tr>
    </thead>
    <tbody>
        @forelse($logs as $log)
        <tr>
            <td style="color:var(--text-muted);white-space:nowrap;">
                {{ $log->created_at->format('d/m/Y H:i') }}
            </td>
            <td>
                <span class="type-badge {{ $log->type_badge }}">
                    {{ $log->type_label }}
                </span>
            </td>
            <td>
                @if($log->variant)
                    @if($log->variant->color_hex)
                        <span class="c-dot" style="background:{{ $log->variant->color_hex }};"></span>
                    @endif
                    {{ $log->variant->color_name ?? '' }}
                    <span style="color:var(--text-muted);">{{ $log->variant->storage ?? '' }}</span>
                @else
                    <span style="color:var(--text-muted);">Pa variant</span>
                @endif
            </td>
            <td>
                <span class="{{ $log->quantity > 0 ? 'qty-plus' : 'qty-minus' }}">
                    {{ $log->quantity_display }}
                </span>
            </td>
            <td style="font-weight:600;">{{ $log->stock_before }}</td>
            <td style="font-weight:600;">{{ $log->stock_after }}</td>
            <td style="color:var(--text-muted);max-width:200px;">
                {{ $log->note ?? '—' }}
            </td>
            <td style="color:var(--text-muted);">
                {{ $log->creator?->name ?? 'Sistem' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted);">
                <i class="fa-solid fa-clock-rotate-left"
                   style="font-size:28px;opacity:0.3;display:block;margin-bottom:12px;"></i>
                Nuk ka historik ende.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Pagination --}}
@if($logs->hasPages())
<div style="margin-top:20px;">
    {{ $logs->links() }}
</div>
@endif

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/stock.js') }}"></script>
@endpush
