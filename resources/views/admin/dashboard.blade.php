@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/products.css') }}">
<style>
/* ── DASHBOARD STYLES ─────────────────────────────────── */

/* Stats grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 22px 24px;
    border: 1px solid var(--border, #eee);
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    cursor: default;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(0,0,0,.07);
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 110px; height: 110px;
    border-radius: 50%;
    opacity: .08;
    transform: translate(28px, -28px);
}
.stat-card.red::before    { background: #e63946; }
.stat-card.teal::before   { background: #2ec4b6; }
.stat-card.purple::before { background: #9b59b6; }
.stat-card.green::before  { background: #2ecc71; }
.stat-card.blue::before   { background: #3498db; }
.stat-card.orange::before { background: #f39c12; }

.stat-icon {
    width: 42px; height: 42px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
    margin-bottom: 14px;
}
.stat-card.red    .stat-icon { background: rgba(230,57,70,.12);  color: #e63946; }
.stat-card.teal   .stat-icon { background: rgba(46,196,182,.12); color: #2ec4b6; }
.stat-card.purple .stat-icon { background: rgba(155,89,182,.12); color: #9b59b6; }
.stat-card.green  .stat-icon { background: rgba(46,204,113,.12); color: #2ecc71; }
.stat-card.blue   .stat-icon { background: rgba(52,152,219,.12); color: #3498db; }
.stat-card.orange .stat-icon { background: rgba(243,156,18,.12); color: #f39c12; }

.stat-value {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -1px;
    color: var(--text, #1a1a2e);
    line-height: 1;
    margin-bottom: 4px;
}
.stat-label {
    font-size: 12.5px;
    color: var(--text-muted, #888);
    font-weight: 500;
    margin-bottom: 6px;
}
.stat-sub {
    font-size: 11px;
    color: var(--text-muted, #aaa);
    display: flex;
    align-items: center;
    gap: 4px;
}
.stat-sub .up   { color: #2ecc71; font-weight: 600; }
.stat-sub .down { color: #e63946; font-weight: 600; }

/* Profit highlight card — full width */
.profit-highlight {
    background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(230,57,70,.25);
}
.profit-highlight::before {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}
.profit-highlight::after {
    content: '';
    position: absolute;
    right: 60px; bottom: -60px;
    width: 150px; height: 150px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.ph-left { position: relative; z-index: 1; }
.ph-label {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    opacity: .75;
    margin-bottom: 6px;
}
.ph-value {
    font-size: 42px;
    font-weight: 900;
    letter-spacing: -2px;
    line-height: 1;
    margin-bottom: 6px;
}
.ph-sub { font-size: 13px; opacity: .7; }

.ph-right {
    display: flex;
    gap: 32px;
    position: relative;
    z-index: 1;
    flex-wrap: wrap;
}
.ph-stat { text-align: center; }
.ph-stat-val {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.5px;
    line-height: 1;
}
.ph-stat-lbl { font-size: 11px; opacity: .7; margin-top: 3px; }
.ph-divider {
    width: 1px;
    background: rgba(255,255,255,.2);
    align-self: stretch;
}

/* Dashboard 2-col grid */
.dash-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-top: 0;
}

/* Chart */
.chart-wrap {
    height: 260px;
    position: relative;
    margin-top: 8px;
}

/* Orders table */
.orders-table { width: 100%; border-collapse: collapse; }
.orders-table thead tr { border-bottom: 2px solid var(--border, #eee); }
.orders-table thead th {
    padding: 10px 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--text-muted, #999);
    text-align: left;
}
.orders-table tbody tr {
    border-bottom: 1px solid var(--border, #f0f0f0);
    transition: background .15s;
}
.orders-table tbody tr:hover { background: var(--bg, #f8f9fa); }
.orders-table tbody tr:last-child { border-bottom: none; }
.orders-table td { padding: 11px 8px; font-size: 13px; }

.order-num-cell {
    font-family: monospace;
    font-size: 12px;
    color: var(--text-muted, #999);
}
.customer-cell { font-weight: 600; color: var(--text, #222); }

/* Status badges */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.status-pill.dorezuar   { background: rgba(46,204,113,.12); color: #27ae60; }
.status-pill.pending    { background: rgba(243,156,18,.12);  color: #e67e22; }
.status-pill.processing { background: rgba(52,152,219,.12);  color: #2980b9; }
.status-pill.cancelled  { background: rgba(231,76,60,.12);   color: #c0392b; }
.status-pill.default    { background: rgba(149,165,166,.12); color: #7f8c8d; }

/* Recent products list */
.prod-list-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border, #f0f0f0);
}
.prod-list-row:last-child { border-bottom: none; }
.prod-list-thumb {
    width: 40px; height: 40px;
    border-radius: 9px;
    object-fit: cover;
    border: 1px solid var(--border, #eee);
    background: var(--bg, #f8f9fa);
    flex-shrink: 0;
}
.prod-list-no-img {
    width: 40px; height: 40px;
    border-radius: 9px;
    background: var(--bg, #f0f0f0);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    color: var(--text-muted, #ccc);
    flex-shrink: 0;
}
.prod-list-info { flex: 1; min-width: 0; }
.prod-list-name {
    font-size: 13px; font-weight: 600;
    color: var(--text, #222);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.prod-list-cat { font-size: 11px; color: var(--text-muted, #999); margin-top: 2px; }
.prod-list-price {
    font-size: 13px; font-weight: 700;
    color: var(--accent, #e63946);
    white-space: nowrap;
}

/* Top products bar chart */
.top-bar-row { display: flex; flex-direction: column; gap: 12px; margin-top: 4px; }
.top-bar-item {}
.top-bar-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}
.top-bar-name { font-size: 12.5px; font-weight: 600; color: var(--text, #222); }
.top-bar-val  { font-size: 12px; font-weight: 700; color: var(--accent, #e63946); }
.top-bar-bg {
    height: 7px;
    background: var(--border, #eee);
    border-radius: 99px;
    overflow: hidden;
}
.top-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #e63946, #c1121f);
    border-radius: 99px;
    transition: width .6s ease;
}

/* Quick actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 18px 12px;
    background: #fff;
    border: 1px solid var(--border, #eee);
    border-radius: 12px;
    text-decoration: none;
    color: var(--text, #333);
    font-size: 12.5px;
    font-weight: 600;
    transition: all .2s;
    cursor: pointer;
}
.quick-action-btn:hover {
    border-color: var(--accent, #e63946);
    color: var(--accent, #e63946);
    box-shadow: 0 4px 16px rgba(230,57,70,.1);
    transform: translateY(-1px);
}
.quick-action-btn i {
    font-size: 20px;
    width: 44px; height: 44px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(230,57,70,.08);
    color: var(--accent, #e63946);
}

/* Empty */
.empty-small {
    text-align: center; padding: 28px 0;
    color: var(--text-muted, #aaa); font-size: 13px;
}
.empty-small i { font-size: 28px; margin-bottom: 8px; display: block; opacity: .35; }

/* Responsive */
@media (max-width: 1100px) {
    .stats-grid    { grid-template-columns: repeat(2, 1fr); }
    .quick-actions { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .dash-grid     { grid-template-columns: 1fr; }
    .ph-value      { font-size: 30px; }
    .ph-right      { gap: 18px; }
}
@media (max-width: 480px) {
    .stats-grid    { grid-template-columns: 1fr 1fr; }
    .quick-actions { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
<div class="page-wrap">

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="page-header-left">
        <div class="breadcrumb-row">
            <span>Dashboard</span>
        </div>
        <h1 class="page-title">Dashboard</h1>
    </div>
    <div style="font-size:12.5px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
        <i class="fa-solid fa-clock"></i>
        {{ now()->format('d M Y, H:i') }}
    </div>
</div>

{{-- PROFIT HIGHLIGHT BANNER --}}
<div class="profit-highlight">
    <div class="ph-left">
        <div class="ph-label"><i class="fa-solid fa-sack-dollar" style="margin-right:6px;"></i>Fitimi Total — Porositë e Dorëzuara</div>
        <div class="ph-value">{{ number_format($deliveredRevenue, 2) }} €</div>
        <div class="ph-sub">Nga {{ $deliveredCount }} porosi të dorëzuara gjithsej</div>
    </div>
    <div class="ph-right">
        <div class="ph-stat">
            <div class="ph-stat-val">{{ number_format($todayDeliveredRevenue, 2) }} €</div>
            <div class="ph-stat-lbl">Sot</div>
        </div>
        <div class="ph-divider"></div>
        <div class="ph-stat">
            <div class="ph-stat-val">{{ number_format($monthDeliveredRevenue, 2) }} €</div>
            <div class="ph-stat-lbl">Këtë muaj</div>
        </div>
        <div class="ph-divider"></div>
        <div class="ph-stat">
            <div class="ph-stat-val">{{ number_format($avgOrderValue, 2) }} €</div>
            <div class="ph-stat-lbl">Mesatarja</div>
        </div>
    </div>
</div>

{{-- STATS GRID --}}
<div class="stats-grid">

    <div class="stat-card red">
        <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
        <div class="stat-value">{{ number_format($ordersCount) }}</div>
        <div class="stat-label">Totali i Porosive</div>
        <div class="stat-sub"><i class="fa-solid fa-circle-dot"></i> Të gjitha statuset</div>
    </div>

    <div class="stat-card green">
        <div class="stat-icon"><i class="fa-solid fa-truck-ramp-box"></i></div>
        <div class="stat-value">{{ number_format($deliveredCount) }}</div>
        <div class="stat-label">Porosi të Dorëzuara</div>
        <div class="stat-sub">
            @if($ordersCount > 0)
                <span class="up">{{ number_format($deliveredCount / $ordersCount * 100, 1) }}%</span> e totalit
            @else <span>—</span>
            @endif
        </div>
    </div>

    <div class="stat-card orange">
        <div class="stat-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <div class="stat-value">{{ number_format($pendingCount) }}</div>
        <div class="stat-label">Porosi në Pritje</div>
        <div class="stat-sub"><i class="fa-solid fa-circle-dot" style="color:#f39c12;"></i> Duke u procesuar</div>
    </div>

    <div class="stat-card blue">
        <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
        <div class="stat-value">{{ number_format($productsCount) }}</div>
        <div class="stat-label">Produktet</div>
        <div class="stat-sub">
            @if($outOfStock > 0)
                <span class="down"><i class="fa-solid fa-triangle-exclamation"></i> {{ $outOfStock }} pa stok</span>
            @else
                <span class="up"><i class="fa-solid fa-check"></i> Të gjitha në stok</span>
            @endif
        </div>
    </div>

    <div class="stat-card teal">
        <div class="stat-icon"><i class="fa-solid fa-tag"></i></div>
        <div class="stat-value">{{ number_format($categoriesCount) }}</div>
        <div class="stat-label">Kategoritë</div>
        <div class="stat-sub"><i class="fa-solid fa-circle-dot"></i> Kategori aktive</div>
    </div>

    <div class="stat-card purple">
        <div class="stat-icon"><i class="fa-solid fa-cubes"></i></div>
        <div class="stat-value">{{ number_format($itemsSold) }}</div>
        <div class="stat-label">Produkte të Shitura</div>
        <div class="stat-sub"><i class="fa-solid fa-circle-dot"></i> Nga dorëzimet</div>
    </div>

</div>

{{-- QUICK ACTIONS --}}
<div class="quick-actions">
    <a href="{{ url('/admin/products/create') }}" class="quick-action-btn">
        <i class="fa-solid fa-plus"></i>
        Shto Produkt
    </a>
    <a href="{{ url('/admin/orders') }}" class="quick-action-btn">
        <i class="fa-solid fa-bag-shopping"></i>
        Porositë
    </a>
    <a href="{{ url('/admin/products') }}" class="quick-action-btn">
        <i class="fa-solid fa-box"></i>
        Produktet
    </a>
    <a href="{{ url('/admin/categories') }}" class="quick-action-btn">
        <i class="fa-solid fa-tag"></i>
        Kategoritë
    </a>
</div>

{{-- MAIN GRID --}}
<div class="dash-grid">

    {{-- CHART --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);">
                    <i class="fa-solid fa-chart-area"></i>
                </div>
                <span class="card-title">Të Ardhurat — {{ now()->year }}</span>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TOP PRODUCTS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>
                <span class="card-title">Top Produktet</span>
                <span class="badge badge-success" style="margin-left:8px;">nga dorëzimet</span>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            @if($topProducts->count() > 0)
            <div class="top-bar-row">
                @foreach($topProducts as $tp)
                @php $maxRev = $topProducts->max('revenue'); @endphp
                <div class="top-bar-item">
                    <div class="top-bar-head">
                        <span class="top-bar-name">
                            {{ $tp->product?->name ?? 'Produkt #'.$tp->product_id }}
                        </span>
                        <span class="top-bar-val">{{ number_format($tp->revenue, 2) }} €</span>
                    </div>
                    <div class="top-bar-bg">
                        <div class="top-bar-fill"
                             style="width:{{ $maxRev > 0 ? ($tp->revenue/$maxRev*100) : 0 }}%;">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-small">
                <i class="fa-solid fa-ranking-star"></i>
                Nuk ka të dhëna ende.
            </div>
            @endif
        </div>
    </div>

    {{-- LATEST ORDERS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <span class="card-title">Porositë e Fundit</span>
            </div>
            <a href="{{ url('/admin/orders') }}" class="btn btn-ghost btn-sm">
                Shiko të gjitha <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
            </a>
        </div>
        <div style="padding:0 20px 20px;">
            @if($latestOrders->count() > 0)
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Klienti</th>
                        <th>Totali</th>
                        <th>Statusi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($latestOrders as $order)
                @php
                    $st = strtolower($order->status ?? '');
                    $pillClass = match(true) {
                        in_array($st, ['delivered','dorezuar','dorëzuar']) => 'dorezuar',
                        in_array($st, ['pending','awaiting_payment'])      => 'pending',
                        in_array($st, ['processing','confirmed','shipped']) => 'processing',
                        in_array($st, ['cancelled','payment_failed'])      => 'cancelled',
                        default => 'default'
                    };
                @endphp
                <tr>
                    <td class="order-num-cell">#{{ $order->order_number }}</td>
                    <td class="customer-cell">{{ $order->customer_name ?? '—' }}</td>
                    <td style="font-weight:700;color:var(--accent);">
                        {{ number_format($order->total_amount, 2) }} €
                    </td>
                    <td>
                        <span class="status-pill {{ $pillClass }}">
                            <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                            {{ $order->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-small">
                <i class="fa-solid fa-bag-shopping"></i>
                Nuk ka porosi ende.
            </div>
            @endif
        </div>
    </div>

    {{-- LATEST PRODUCTS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="section-icon" style="background:rgba(230,57,70,0.12);color:var(--accent);">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <span class="card-title">Produktet e Fundit</span>
            </div>
            <a href="{{ url('/admin/products') }}" class="btn btn-ghost btn-sm">
                Shiko të gjitha <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
            </a>
        </div>
        <div style="padding:0 20px 20px;">
            @if($latestProducts->count() > 0)
            @foreach($latestProducts as $prod)
            @php
                $pImg = $prod->images?->firstWhere('is_primary', true)
                      ?? $prod->images?->first();
            @endphp
            <div class="prod-list-row">
                @if($pImg)
                    <img src="{{ asset('storage/'.$pImg->image_path) }}"
                         class="prod-list-thumb" alt="">
                @else
                    <div class="prod-list-no-img"><i class="fa-solid fa-box"></i></div>
                @endif
                <div class="prod-list-info">
                    <div class="prod-list-name">{{ $prod->name }}</div>
                    <div class="prod-list-cat">{{ $prod->category->name ?? '—' }}</div>
                </div>
                <div class="prod-list-price">{{ number_format($prod->price ?? 0, 2) }} €</div>
                <a href="{{ url('/admin/products/'.$prod->id.'/edit') }}"
                   class="btn btn-ghost btn-sm btn-icon">
                    <i class="fa-solid fa-pen"></i>
                </a>
            </div>
            @endforeach
            @else
            <div class="empty-small">
                <i class="fa-solid fa-box-open"></i>
                Nuk ka produkte ende.
            </div>
            @endif
        </div>
    </div>

</div>{{-- /dash-grid --}}

</div>{{-- /page-wrap --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('revenueChart');
if (ctx) {
    const labels   = @json($chartData->pluck('month_label'));
    const revenues = @json($chartData->pluck('revenue'));
    const counts   = @json($chartData->pluck('orders_count'));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Të Ardhurat (€)',
                    data: revenues,
                    backgroundColor: 'rgba(230,57,70,0.15)',
                    borderColor: '#e63946',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y',
                },
                {
                    label: 'Porosi',
                    data: counts,
                    type: 'line',
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52,152,219,0.07)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: '#3498db',
                    pointRadius: 4,
                    fill: true,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 12 }, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.datasetIndex === 0
                            ? ` ${Number(ctx.raw).toFixed(2)} €`
                            : ` ${ctx.raw} porosi`
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear', position: 'left',
                    grid: { color: 'rgba(0,0,0,.04)' },
                    ticks: { callback: v => v + ' €', font: { size: 11 } }
                },
                y1: {
                    type: 'linear', position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { font: { size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}
</script>
@endpush