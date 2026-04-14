@extends('layouts.admin')

@section('title', 'Klientët')
@section('page-title', 'Klientët')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/customers.css') }}">
@endpush

@section('content')
@php
    $totalCustomers = $customers->total();
    $newThisMonth   = $customers->getCollection()->filter(fn($c) => $c->created_at?->isCurrentMonth())->count();
    $withPhone      = $customers->getCollection()->filter(fn($c) => !empty($c->phone))->count();
@endphp

<div class="page-wrap">

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="breadcrumb-row">
            <a href="{{ url('/') }}">Dashboard</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Klientët</span>
        </div>
        <h1 class="page-title">Klientët</h1>
    </div>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Shto Përdorues
    </a>
</div>

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card teal">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-value">{{ $totalCustomers }}</div>
        <div class="stat-label">Gjithsej Klientë</div>
        <div class="stat-sub">Klientë të regjistruar</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="fa-solid fa-user-plus"></i></div>
        <div class="stat-value">{{ $newThisMonth }}</div>
        <div class="stat-label">Të Rinj Këtë Muaj</div>
        <div class="stat-sub">Regjistruar muajin aktual</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon"><i class="fa-solid fa-phone"></i></div>
        <div class="stat-value">{{ $withPhone }}</div>
        <div class="stat-label">Me Numër Telefoni</div>
        <div class="stat-sub">Klientë me kontakt</div>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-left">
            <div class="section-icon" style="background:rgba(46,196,182,.12);color:#2ec4b6;">
                <i class="fa-solid fa-list"></i>
            </div>
            <span class="card-title">Lista e Klientëve</span>
        </div>
    </div>
<div class="role-filters">
    <a href="{{ route('admin.customers.index') }}"
       class="role-chip {{ !request('role') ? 'active' : '' }}">
        Të gjithë
    </a>
    <a href="{{ route('admin.customers.index', ['role' => 'admin']) }}"
       class="role-chip {{ request('role') == 'admin' ? 'active-admin' : '' }}">
        🛡️ Adminët
    </a>
    <a href="{{ route('admin.customers.index', ['role' => 'customer']) }}"
       class="role-chip {{ request('role') == 'customer' ? 'active' : '' }}">
        👤 Klientët
    </a>
</div>
    @if($customers->count())

    {{-- DESKTOP TABLE --}}
    <div class="table-wrap">
        <table class="customers-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Klienti</th>
                    <th>Email</th>
                    <th>Telefoni</th>
                    <th>Regjistruar</th>
                    <th>Porositë</th>
                    <th>Roli</th>
                    <th>Veprimet</th>
                </tr>
            </thead>
            <tbody>
            @foreach($customers as $c)
            <tr>
                <td style="color:var(--text-muted);font-size:12px;font-family:monospace;">
                    #{{ $c->id }}
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="customer-avatar">
                            {{ strtoupper(substr($c->name ?? 'C', 0, 1)) }}
                        </div>
                        <div>
                            <div class="customer-name-cell">{{ $c->name }} {{ $c->surname ?? '' }}</div>
                            <div class="customer-sub-cell">
                                {{ $c->role === 'admin' ? 'Administrator' : 'Klient i regjistruar' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td style="color:var(--text-muted);font-size:13px;">{{ $c->email }}</td>
                <td>
                    @if(!empty($c->phone))
                        <span style="font-size:13px;">{{ $c->phone }}</span>
                    @else
                        <span style="color:var(--text-muted);font-size:12px;font-style:italic;">Pa numër</span>
                    @endif
                </td>
                <td>
                    <div style="font-size:13px;font-weight:600;">{{ $c->created_at?->format('d.m.Y') }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ $c->created_at?->format('H:i') }}</div>
                </td>
                <td>
                    <a href="{{ route('admin.customers.orders', $c->uuid) }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-box"></i>
                        <span style="margin-left:4px;">{{ $c->orders_count_custom ?? 0 }}</span>
                    </a>
                </td>
                <td>
                    @if($c->role === 'admin')
                        <span class="customer-badge" style="background:rgba(124,58,237,0.12);color:#7c3aed;border:1px solid rgba(124,58,237,0.2);">
                            🛡️ Admin
                        </span>
                    @else
                        <span class="customer-badge" style="background:rgba(0,105,128,0.10);color:#006980;border:1px solid rgba(0,105,128,0.2);">
                            👤 Customer
                        </span>
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.customers.destroy', $c->uuid) }}" method="POST"
                          onsubmit="return confirm('Fshi klientin?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm btn-icon">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARD LIST --}}
    <div class="mobile-list" style="display:none;">
        @foreach($customers as $c)
        <div class="mobile-customer-card">
            <div class="mobile-customer-avatar">
                {{ strtoupper(substr($c->name ?? 'C', 0, 1)) }}
            </div>
            <div class="mobile-customer-info">
                <div class="mobile-customer-name">{{ $c->name }} {{ $c->surname ?? '' }}</div>
                <div style="font-size:11px;margin-top:2px;">
                    @if($c->role === 'admin')
                        <span style="color:#7c3aed;font-weight:700;">🛡️ Admin</span>
                    @else
                        <span style="color:#006980;font-weight:600;">👤 Customer</span>
                    @endif
                </div>
                <div class="mobile-customer-email">{{ $c->email }}</div>
                <div class="mobile-customer-meta">
                    @if(!empty($c->phone))
                        <span class="badge badge-success" style="font-size:10px;padding:2px 7px;">
                            <i class="fa-solid fa-phone" style="font-size:8px;"></i> {{ $c->phone }}
                        </span>
                    @endif
                    <span style="font-size:11px;color:var(--text-muted);">
                        {{ $c->created_at?->format('d.m.Y') }}
                    </span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;">
             <a href="{{ route('admin.customers.orders', $c->uuid) }}" class="btn btn-ghost btn-sm btn-icon" title="Porositë">
                    <i class="fa-solid fa-box"></i>
                </a>
                <form action="{{ route('admin.customers.destroy', $c->uuid) }}" method="POST"
                      onsubmit="return confirm('Fshi klientin?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm btn-icon">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
                <span style="font-size:10px;color:var(--text-muted);text-align:center;">
                    {{ $c->orders_count_custom ?? 0 }} porosi
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- PAGINATION --}}
    @if(method_exists($customers, 'hasPages') && $customers->hasPages())
    <div class="pagination-wrap">
        <div class="pag-inner">
            @if($customers->onFirstPage())
                <span class="pag-btn pag-disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $customers->previousPageUrl() }}" class="pag-btn"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @foreach($customers->getUrlRange(1, $customers->lastPage()) as $page => $url)
                @if($page == $customers->currentPage())
                    <span class="pag-btn pag-active">{{ $page }}</span>
                @elseif($page == 1 || $page == $customers->lastPage() || abs($page - $customers->currentPage()) <= 2)
                    <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                @elseif(abs($page - $customers->currentPage()) == 3)
                    <span class="pag-btn pag-dots">&#8230;</span>
                @endif
            @endforeach

            @if($customers->hasMorePages())
                <a href="{{ $customers->nextPageUrl() }}" class="pag-btn"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="pag-btn pag-disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
        <div class="pag-info">
            Shfaqen {{ $customers->firstItem() }}&ndash;{{ $customers->lastItem() }} nga {{ $customers->total() }}
        </div>
    </div>
    @endif

    @else
    <div class="empty-state" style="padding:48px 0;">
        <div class="empty-icon"><i class="fa-solid fa-users"></i></div>
        <h3>Nuk ka klientë ende</h3>
        <p>Kur të regjistrohen përdorues, do të shfaqen këtu.</p>
    </div>
    @endif

</div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin/orders-index.js') }}"></script>
@endpush