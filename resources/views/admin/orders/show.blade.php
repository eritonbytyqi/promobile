@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}">
    <style>
    /* ── ORDERS SHOW — RESPONSIVE ─────────────────────────── */
    .order-show-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        align-items: start;
    }
    .order-left  { display: flex; flex-direction: column; gap: 20px; }
    .order-right { display: flex; flex-direction: column; gap: 20px; }

    /* Header card */
    .order-header-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 20px 24px;
    }
    .order-header-left { display: flex; align-items: center; gap: 16px; }
    .order-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--accent, #e63946), #c1121f);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #fff; flex-shrink: 0;
    }
    .order-id-title { font-size: 20px; font-weight: 800; color: var(--text, #111); }
    .order-id-date  { font-size: 12px; color: var(--text-muted, #888); margin-top: 2px; }

    /* Items table */
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table thead tr { border-bottom: 2px solid var(--border, #eee); }
    .items-table thead th {
        padding: 10px 16px;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .6px;
        color: var(--text-muted, #999); text-align: left;
    }
    .items-table tbody tr { border-bottom: 1px solid var(--border, #f0f0f0); }
    .items-table tbody tr:last-child { border-bottom: none; }
    .items-table td { padding: 14px 16px; vertical-align: middle; }
    .items-table tfoot td {
        padding: 14px 16px;
        border-top: 2px solid var(--border, #eee);
    }

    /* Product thumb */
    .prod-thumb {
        width: 44px; height: 44px;
        border-radius: 8px; object-fit: cover;
        border: 1px solid var(--border, #eee);
    }
    .prod-thumb-placeholder {
        width: 44px; height: 44px;
        border-radius: 8px;
        background: var(--bg, #f5f5f5);
        border: 1px solid var(--border, #eee);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted, #ccc); font-size: 16px;
        flex-shrink: 0;
    }

    /* Qty badge */
    .qty-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px;
        background: var(--bg, #f5f5f5);
        border: 1px solid var(--border, #eee);
        border-radius: 6px;
        font-size: 13px; font-weight: 700;
    }

    /* Status badge */
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 100px;
        font-size: 12px; font-weight: 700;
    }

    /* Client info rows */
    .client-row {
        display: flex; align-items: center; gap: 10px;
        font-size: 13px; color: var(--text, #333);
    }
    .client-row i {
        width: 16px; font-size: 12px;
        color: var(--text-muted, #999); flex-shrink: 0;
    }
    .client-divider {
        height: 1px; background: var(--border, #eee);
        margin: 8px 0;
    }

    /* Action buttons full width */
    .btn-full { width: 100%; justify-content: center; }

    /* Stripe ID box */
    .stripe-box {
        background: rgba(0,89,181,.06);
        border-radius: 8px; padding: 10px 12px; margin-top: 4px;
    }
    .stripe-box-label { font-size: 11px; color: var(--text-muted, #999); margin-bottom: 4px; }
    .stripe-box-val {
        font-size: 11px; font-family: monospace;
        color: #0059b5; word-break: break-all;
    }

    /* Mobile items card */
    .mobile-items { display: none; }
    .mobile-item-row {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border, #f0f0f0);
    }
    .mobile-item-row:last-child { border-bottom: none; }
    .mobile-item-info { flex: 1; min-width: 0; }
    .mobile-item-name { font-size: 13px; font-weight: 600; color: var(--text, #111); }
    .mobile-item-meta { font-size: 11px; color: var(--text-muted, #999); margin-top: 2px; }
    .mobile-item-price { font-size: 14px; font-weight: 800; color: var(--accent, #e63946); white-space: nowrap; }

    /* Responsive */
    @media (max-width: 900px) {
        .order-show-grid {
            grid-template-columns: 1fr;
        }
        .order-right { order: -1; } /* right panel goes on top on mobile */
        .table-wrap  { display: none; }
        .mobile-items { display: block; }
    }
    @media (max-width: 480px) {
        .order-header-card { padding: 14px 16px; }
        .order-id-title { font-size: 17px; }
    }
    </style>
@endpush

@section('title', 'Porosia #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))
@section('page-title', 'Detajet e Porosisë')

@section('breadcrumb')
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <a href="{{ url('/admin/orders') }}">Porositë</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
@endsection

@section('content')
@php
    $statusMap = [
        'pending'          => ['label' => 'Në Pritje',      'color' => '#e67e22', 'bg' => 'rgba(243,156,18,.12)'],
        'awaiting_payment' => ['label' => 'Pret Pagesën',   'color' => '#0059b5', 'bg' => 'rgba(0,89,181,.12)'],
        'confirmed'        => ['label' => 'Konfirmuar',     'color' => '#2980b9', 'bg' => 'rgba(52,152,219,.12)'],
        'shipped'          => ['label' => 'Dërguar',        'color' => '#8e44ad', 'bg' => 'rgba(155,89,182,.12)'],
        'delivered'        => ['label' => 'Dorëzuar',       'color' => '#27ae60', 'bg' => 'rgba(46,204,113,.12)'],
        'cancelled'        => ['label' => 'Anuluar',        'color' => '#c0392b', 'bg' => 'rgba(231,76,60,.12)'],
        'payment_failed'   => ['label' => 'Pagesa Dështoi', 'color' => '#c0392b', 'bg' => 'rgba(231,76,60,.12)'],
    ];
    $st = $statusMap[$order->status] ?? ['label' => ucfirst($order->status ?? '—'), 'color' => '#888', 'bg' => '#f0f0f0'];
@endphp

<div class="page-wrap">

{{-- PAGE HEADER --}}


<div class="order-show-grid">

    {{-- ── LEFT ────────────────────────────────────────────── --}}
    <div class="order-left">

        {{-- HEADER CARD --}}
        <div class="card">
            <div class="order-header-card">
                <div class="order-header-left">
                    <div class="order-icon">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div>
                        <div class="order-id-title">
                            {{ $order->order_number ?? '#'.str_pad($order->uuid, 5, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="order-id-date">
                            {{ $order->created_at?->format('d M Y, H:i') ?? '—' }}
                        </div>
                    </div>
                </div>
                <span class="status-badge"
                      style="color:{{ $st['color'] }};background:{{ $st['bg'] }};">
                    <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                    {{ $st['label'] }}
                </span>
            </div>
        </div>

        {{-- PRODUKTET — Desktop Table --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="section-icon" style="background:rgba(230,57,70,.12);color:var(--accent);">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <span class="card-title">Produktet e Porositura</span>
                </div>
            </div>

            {{-- Desktop --}}
            <div class="table-wrap">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produkti</th>
                            <th>Çmimi</th>
                            <th>Sasia</th>
                            <th style="text-align:right;">Nëntotali</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        @php
                            $img = optional($item->product)->images?->firstWhere('is_primary', true)
                                ?? optional($item->product)->images?->first();
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    @if($img)
                                        <img src="{{ asset('storage/'.$img->image_path) }}" class="prod-thumb" alt="">
                                    @else
                                        <div class="prod-thumb-placeholder"><i class="fa-solid fa-box"></i></div>
                                    @endif
                                    <div>
                                        <div style="font-weight:600;font-size:13px;color:var(--text);">
                                            {{ optional($item->product)->name ?? 'Produkt i fshirë' }}
                                        </div>
                                        @if(optional($item->product)->sku)
                                        <div style="font-size:11px;color:var(--text-muted);">SKU: {{ $item->product->sku }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:13px;color:var(--text-muted);">
                                {{ number_format($item->unit_price ?? 0, 2) }} €
                            </td>
                            <td><span class="qty-badge">{{ $item->quantity }}</span></td>
                            <td style="text-align:right;">
                                <span style="font-size:15px;font-weight:800;color:var(--accent);">
                                    {{ number_format($item->subtotal ?? (($item->unit_price ?? 0) * $item->quantity), 2) }} €
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;font-weight:700;font-size:13px;color:var(--text-muted);">
                                Totali i Porosisë:
                            </td>
                            <td style="text-align:right;">
                                <span style="font-size:20px;font-weight:800;color:var(--accent);">
                                    {{ number_format($order->total_amount ?? 0, 2) }} €
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Mobile items --}}
            <div class="mobile-items">
                @foreach($order->items as $item)
                @php
                    $img = optional($item->product)->images?->firstWhere('is_primary', true)
                        ?? optional($item->product)->images?->first();
                @endphp
                <div class="mobile-item-row">
                    @if($img)
                        <img src="{{ asset('storage/'.$img->image_path) }}" class="prod-thumb" alt="">
                    @else
                        <div class="prod-thumb-placeholder"><i class="fa-solid fa-box"></i></div>
                    @endif
                    <div class="mobile-item-info">
                        <div class="mobile-item-name">
                            {{ optional($item->product)->name ?? 'Produkt i fshirë' }}
                        </div>
                        <div class="mobile-item-meta">
                            {{ number_format($item->unit_price ?? 0, 2) }} € × {{ $item->quantity }}
                        </div>
                    </div>
                    <div class="mobile-item-price">
                        {{ number_format($item->subtotal ?? (($item->unit_price ?? 0) * $item->quantity), 2) }} €
                    </div>
                </div>
                @endforeach
                <div style="padding:14px 16px;border-top:2px solid var(--border,#eee);display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-weight:700;font-size:13px;color:var(--text-muted);">Totali:</span>
                    <span style="font-size:18px;font-weight:800;color:var(--accent);">
                        {{ number_format($order->total_amount ?? 0, 2) }} €
                    </span>
                </div>
            </div>
        </div>

        {{-- SHËNIME --}}
        @if($order->notes)
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="section-icon" style="background:rgba(230,57,70,.12);color:var(--accent);">
                        <i class="fa-solid fa-note-sticky"></i>
                    </div>
                    <span class="card-title">Shënime</span>
                </div>
            </div>
            <div style="padding:16px 20px;">
                <p style="font-size:14px;color:var(--text-muted);line-height:1.7;margin:0;">{{ $order->notes }}</p>
            </div>
        </div>
        @endif

    </div>{{-- /order-left --}}

    {{-- ── RIGHT ───────────────────────────────────────────── --}}
    <div class="order-right">

        {{-- NDRYSHO STATUSIN --}}
     {{-- NDRYSHO STATUSIN --}}
<div class="card">
    <div class="card-header">
        <div class="card-header-left">
            <div class="section-icon" style="background:rgba(230,57,70,.12);color:var(--accent);">
                <i class="fa-solid fa-rotate"></i>
            </div>
            <span class="card-title">Ndrysho Statusin</span>
        </div>
    </div>
    <div style="padding:16px 20px;">
        @if(in_array($order->status, ['cancelled', 'payment_failed']))
            <div style="background:rgba(231,76,60,.08);border:1px solid rgba(231,76,60,.2);border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-ban" style="color:#c0392b;"></i>
                <span style="font-size:13px;color:#c0392b;font-weight:600;">
                    Kjo porosi është anuluar dhe nuk mund të ndryshohet më.
                </span>
            </div>
        @else
            <label style="font-size:12px;font-weight:600;color:var(--text-muted);display:block;margin-bottom:6px;">
                Statusi aktual
            </label>
            <select onchange="openStatusModal('{{ $order->uuid }}', this.value, '{{ addslashes($order->customer_name) }}', '{{ $order->order_number }}')"
                    class="form-select"
                    style="width:100%;padding:9px 12px;border-radius:9px;border:1px solid var(--border,#eee);font-size:13px;background:#fff;">
                @foreach([
                    'pending'          => 'Në Pritje',
                    'awaiting_payment' => 'Pret Pagesën',
                    'confirmed'        => 'Konfirmuar',
                    'shipped'          => 'Dërguar',
                    'delivered'        => 'Dorëzuar',
                    'cancelled'        => 'Anuluar',
                ] as $val => $lbl)
                    <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>
                        {{ $lbl }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>
</div>

        {{-- INFO KLIENTIT --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="section-icon" style="background:rgba(46,196,182,.12);color:#2ec4b6;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <span class="card-title">Informacioni i Klientit</span>
                </div>
            </div>
            <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">

                {{-- Avatar + emri --}}
                <div style="display:flex;align-items:center;gap:12px;padding-bottom:12px;border-bottom:1px solid var(--border,#eee);">
                    <div style="width:40px;height:40px;border-radius:50%;background:rgba(46,196,182,.12);color:#2ec4b6;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;flex-shrink:0;">
                        {{ strtoupper(substr($order->customer_name ?? 'K', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $order->customer_name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">Klient</div>
                    </div>
                </div>

                @if($order->customer_email)
                <div class="client-row">
                    <i class="fa-solid fa-envelope"></i>
                    <span>{{ $order->customer_email }}</span>
                </div>
                @endif

                @if($order->customer_phone)
                <div class="client-row">
                    <i class="fa-solid fa-phone"></i>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                @endif

                @if($order->shipping_address)
                <div class="client-row" style="align-items:flex-start;">
                    <i class="fa-solid fa-location-dot" style="margin-top:2px;"></i>
                    <span style="line-height:1.5;">
                        {{ $order->shipping_address }}
                        @if($order->city), {{ $order->city }}@endif
                    </span>
                </div>
                @endif

                @if($order->payment_method)
                <div class="client-divider"></div>
                <div class="client-row">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>{{ $order->payment_method === 'bank' ? 'Kartë krediti (Stripe)' : 'Cash në dorëzim' }}</span>
                </div>
                @endif

                @if($order->payment_intent_id)
                <div class="stripe-box">
                    <div class="stripe-box-label">Stripe Payment ID</div>
                    <div class="stripe-box-val">{{ $order->payment_intent_id }}</div>
                </div>
                @endif

            </div>
        </div>

        {{-- REFUND --}}
        @if($order->payment_intent_id && !in_array($order->status, ['cancelled', 'payment_failed']))
        <div class="card">
            <div class="card-header">
                <div class="card-header-left">
                    <div class="section-icon" style="background:rgba(243,156,18,.12);color:#e67e22;">
                        <i class="fa-solid fa-rotate-left"></i>
                    </div>
                    <span class="card-title">Kthe Pagesën (Refund)</span>
                </div>
            </div>
            <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                <p style="font-size:12px;color:var(--text-muted);margin:0;">
                    Zgjedh sasinë për çdo produkt. Lëre 0 për produktet që nuk i kthen.
                </p>
                <form action="{{ route('payment.refund', $order->id) }}" method="POST"
                      onsubmit="return confirmRefund()">
                    @csrf
                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                        @foreach($order->items as $item)
                        <div style="background:var(--bg,#f8f9fa);border-radius:10px;padding:10px 12px;border:1px solid var(--border,#eee);">
                            <div style="display:flex;justify-content:space-between;margin-bottom:7px;">
                                <span style="font-size:13px;font-weight:600;color:var(--text);">
                                    {{ optional($item->product)->name ?? 'Produkt' }}
                                </span>
                                <span style="font-size:12px;color:var(--text-muted);">
                                    {{ number_format($item->unit_price, 2) }} €/copë
                                </span>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:11px;color:var(--text-muted);">Kthe (max {{ $item->quantity }}):</span>
                                <input type="number" name="items[{{ $item->id }}][quantity]"
                                       min="0" max="{{ $item->quantity }}" value="0"
                                       class="form-control"
                                       style="width:65px;padding:5px 8px;font-size:13px;border-radius:7px;border:1px solid var(--border,#ddd);"
                                       oninput="updateRefundTotal()">
                                <input type="hidden" name="items[{{ $item->id }}][unit_price]" value="{{ $item->unit_price }}">
                                <input type="hidden" name="items[{{ $item->id }}][max]" value="{{ $item->quantity }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="background:rgba(243,156,18,.08);border:1px solid rgba(243,156,18,.3);border-radius:10px;padding:10px 14px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:13px;font-weight:600;color:#e67e22;">Shuma për kthim:</span>
                        <span style="font-size:16px;font-weight:800;color:#e67e22;" id="refundTotal">0.00 €</span>
                    </div>
                    <button type="submit" class="btn btn-full"
                            style="background:rgba(243,156,18,.12);color:#e67e22;border:1px solid rgba(243,156,18,.3);">
                        <i class="fa-solid fa-rotate-left"></i> Kthe Pagesën
                    </button>
                </form>

                <form action="{{ route('payment.refund', $order->id) }}" method="POST"
                      onsubmit="return confirm('A jeni i sigurt? Do të kthehet e gjithë pagesa prej {{ number_format($order->total_amount, 2) }} €!')">
                    @csrf
                    <input type="hidden" name="refund_all" value="1">
                    <button type="submit" class="btn btn-danger btn-full">
                        <i class="fa-solid fa-ban"></i> Kthe Gjithçka ({{ number_format($order->total_amount, 2) }} €)
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- VEPRIMET --}}
        <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ url('/admin/orders') }}" class="btn btn-ghost btn-full">
                <i class="fa-solid fa-arrow-left"></i> Kthehu te Porositë
            </a>
            <form action="{{ url('/admin/orders/'.$order->id) }}" method="POST"
                  onsubmit="return confirm('Fshi porosinë?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-full">
                    <i class="fa-solid fa-trash"></i> Fshi Porosinë
                </button>
            </form>
        </div>

    </div>{{-- /order-right --}}

</div>{{-- /order-show-grid --}}

</div>{{-- /page-wrap --}}
@include('admin.orders.partials.status-modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/orders-show.js') }}"></script>
@endpush