@extends('layouts.admin')

@section('title', 'Porositë e ' . $customer->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/customers.css') }}">
@endpush

@section('content')
<div class="page-wrap">

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="breadcrumb-row">
            <a href="{{ url('/') }}">Dashboard</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('admin.customers.index') }}">Klientët</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $customer->name }}</span>
        </div>
        <h1 class="page-title">Porositë e {{ $customer->name }}</h1>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-ghost">
        <i class="fa-solid fa-arrow-left"></i> Kthehu
    </a>
</div>

{{-- CUSTOMER BANNER --}}
<div class="customer-info-banner">
    <div class="cib-avatar">
        {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
    </div>
    <div class="cib-info">
        <div class="cib-name">{{ $customer->name }} {{ $customer->surname ?? '' }}</div>
        <div class="cib-email">
            <i class="fa-solid fa-envelope" style="font-size:11px;margin-right:4px;"></i>
            {{ $customer->email }}
            @if(!empty($customer->phone))
                &nbsp;·&nbsp;
                <i class="fa-solid fa-phone" style="font-size:11px;margin-right:4px;"></i>
                {{ $customer->phone }}
            @endif
        </div>
    </div>
    <div class="cib-stats">
        <div class="cib-stat">
            <div class="cib-stat-val">{{ $orders->count() }}</div>
            <div class="cib-stat-lbl">Porosi</div>
        </div>
        <div class="cib-stat">
            <div class="cib-stat-val">{{ number_format($orders->sum('total_amount'), 0) }} €</div>
            <div class="cib-stat-lbl">Totali</div>
        </div>
        <div class="cib-stat">
            <div class="cib-stat-val">{{ $orders->where('status','delivered')->count() }}</div>
            <div class="cib-stat-lbl">Dorëzuara</div>
        </div>
    </div>
</div>

{{-- ORDERS CARD --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-left">
            <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <span class="card-title">Porositë</span>
            <span class="badge badge-success" style="margin-left:8px;">{{ $orders->count() }}</span>
        </div>
    </div>

    @if($orders->count())

    {{-- DESKTOP TABLE --}}
    <div class="table-wrap">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nr. Porosisë</th>
                    <th>Data</th>
                    <th>Totali</th>
                    <th>Statusi</th>
                    <th>Pagesa</th>
                    <th>Veprimi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
            @php
                $st = strtolower($order->status ?? '');
                $pillClass = match($st) {
                    'delivered'        => 'sp-delivered',
                    'pending'          => 'sp-pending',
                    'processing'       => 'sp-processing',
                    'confirmed'        => 'sp-confirmed',
                    'shipped'          => 'sp-shipped',
                    'cancelled'        => 'sp-cancelled',
                    'payment_failed'   => 'sp-payment_failed',
                    'awaiting_payment' => 'sp-awaiting_payment',
                    default            => 'sp-default',
                };
            @endphp
            <tr>
                <td style="color:var(--text-muted);font-size:12px;font-family:monospace;">#{{ $order->id }}</td>
                <td>
                    <span style="font-family:monospace;font-size:12px;color:var(--text-muted);">
                        {{ $order->order_number }}
                    </span>
                </td>
                <td>
                    <div style="font-size:13px;font-weight:600;">{{ $order->created_at?->format('d.m.Y') }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $order->created_at?->format('H:i') }}</div>
                </td>
                <td style="font-weight:700;font-size:14px;color:var(--accent);">
                    {{ number_format($order->total_amount, 2) }} €
                </td>
                <td>
                    <span class="status-pill {{ $pillClass }}">
                        <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                        {{ $order->status }}
                    </span>
                </td>
                <td>
                    <span class="pay-badge">
                        <i class="fa-solid fa-credit-card" style="font-size:9px;"></i>
                        {{ $order->payment_method ?? '—' }}
                    </span>
                </td>
                <td>
                    <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn btn-ghost btn-sm btn-icon">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARD LIST --}}
    <div class="mobile-list" style="display:none;">
        @foreach($orders as $order)
        @php
            $st = strtolower($order->status ?? '');
            $pillClass = match($st) {
                'delivered'        => 'sp-delivered',
                'pending'          => 'sp-pending',
                'processing'       => 'sp-processing',
                'confirmed'        => 'sp-confirmed',
                'shipped'          => 'sp-shipped',
                'cancelled'        => 'sp-cancelled',
                'payment_failed'   => 'sp-payment_failed',
                'awaiting_payment' => 'sp-awaiting_payment',
                default            => 'sp-default',
            };
        @endphp
        <div class="mobile-order-card">
            <div class="mob-order-top">
                <div>
                    <div class="mob-order-num">{{ $order->order_number }}</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                        {{ $order->created_at?->format('d.m.Y H:i') }}
                    </div>
                </div>
                <div class="mob-order-amount">{{ number_format($order->total_amount, 2) }} €</div>
            </div>
            <div class="mob-order-bottom">
                <span class="status-pill {{ $pillClass }}">
                    <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                    {{ $order->status }}
                </span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="pay-badge">
                        <i class="fa-solid fa-credit-card" style="font-size:9px;"></i>
                        {{ $order->payment_method ?? '—' }}
                    </span>
                    <a href="{{ url('/admin/orders/'.$order->id) }}" class="btn btn-ghost btn-sm btn-icon">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    <div class="empty-state" style="padding:48px 0;">
        <div class="empty-icon"><i class="fa-solid fa-bag-shopping"></i></div>
        <h3>Ky klient nuk ka porosi ende</h3>
    </div>
    @endif

</div>

</div>{{-- /page-wrap --}}
@endsection