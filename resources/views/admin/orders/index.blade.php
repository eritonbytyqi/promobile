@extends('layouts.admin')

@section('title', 'Porositë')
@section('page-title', 'Porositë')

@section('breadcrumb')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/orders.css') }}">
@endpush
    <a href="{{ url('/admin') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
    <span>Porositë</span>
@endsection

@section('content')

{{-- STATS --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    @php $allOrders = $orders->getCollection(); @endphp
    <div class="stat-card">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#f59e0b;margin-bottom:8px;">Në Pritje</div>
        <div style="font-size:32px;font-weight:800;color:#1d1d1f;">{{ $allOrders->whereIn('status',['pending','awaiting_payment'])->count() }}</div>
        <div style="font-size:12px;color:#8e8e93;margin-top:4px;">Porosi të reja</div>
    </div>
    <div class="stat-card">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#06b6d4;margin-bottom:8px;">Konfirmuara</div>
        <div style="font-size:32px;font-weight:800;color:#1d1d1f;">{{ $allOrders->where('status','confirmed')->count() }}</div>
        <div style="font-size:12px;color:#8e8e93;margin-top:4px;">Duke u procesuar</div>
    </div>
    <div class="stat-card">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#7c6fff;margin-bottom:8px;">Dërguara</div>
        <div style="font-size:32px;font-weight:800;color:#1d1d1f;">{{ $allOrders->where('status','shipped')->count() }}</div>
        <div style="font-size:12px;color:#8e8e93;margin-top:4px;">Rrugës drejt klientit</div>
    </div>
    <div class="stat-card">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#10b981;margin-bottom:8px;">Të Kompletuara</div>
        <div style="font-size:32px;font-weight:800;color:#1d1d1f;">{{ $allOrders->where('status','delivered')->count() }}</div>
        <div style="font-size:12px;color:#8e8e93;margin-top:4px;">Dorëzuar me sukses</div>
    </div>
</div>

{{-- ORDERS TABLE --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Të Gjitha Porositë</span>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;background:#f5f5f7;border:1px solid rgba(0,0,0,0.1);border-radius:9px;padding:0 12px;height:36px;">
                <i class="fa-solid fa-magnifying-glass" style="color:#8e8e93;font-size:12px;"></i>
                <input type="text" id="searchInput" placeholder="Kërko..." oninput="filterTable()"
                       style="background:none;border:none;outline:none;color:#1d1d1f;font-size:13px;width:160px;">
            </div>
            <select id="statusFilter" class="form-select" style="width:140px;padding:8px 10px;height:36px;" onchange="filterTable()">
                <option value="">Të gjitha</option>
                <option value="pending">Në Pritje</option>
                <option value="awaiting_payment">Pret Pagesën</option>
                <option value="confirmed">Konfirmuar</option>
                <option value="shipped">Dërguar</option>
                <option value="delivered">Dorëzuar</option>
                <option value="cancelled">Anuluar</option>
            </select>
        </div>
    </div>

    {{-- BULK BAR --}}
    <div class="bulk-bar" id="bulkBar">
        <span><strong id="bulkCount">0</strong> porosi të zgjedhura</span>
        <form action="{{ url('/admin/orders/bulk-delete') }}" method="POST" id="bulkForm" onsubmit="return confirmBulk()" style="display:flex;gap:8px;align-items:center;">
            @csrf @method('DELETE')
            <div id="bulkInputs"></div>
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fa-solid fa-trash"></i> Fshi të zgjedhurat
            </button>
        </form>
        <button onclick="clearSelection()" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-xmark"></i> Anulo
        </button>
    </div>

    {{-- DESKTOP TABLE --}}
    <div class="table-wrap">
        <table id="ordersTable">
            <thead>
                <tr>
                    <th style="width:36px;"><input type="checkbox" id="selectAll" onchange="toggleAll(this)"></th>
                    <th>#</th>
                    <th>Klienti</th>
                    <th>Produktet</th>
                    <th>Totali</th>
                    <th>Statusi</th>
                    <th>Data</th>
                    <th>Veprimet</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                @php
                    $productNames = $order->items->map(fn($i) => optional($i->product)->name ?? 'Produkt i fshirë')->filter();
                    $isFinal = in_array($order->status, ['cancelled','payment_failed','delivered']);
                    $statusMap = [
                        'pending'          => ['label'=>'Në Pritje',    'color'=>'#a05a00', 'bg'=>'rgba(245,158,11,0.12)', 'icon'=>'clock'],
                        'awaiting_payment' => ['label'=>'Pret Pagesën', 'color'=>'#0059b5', 'bg'=>'rgba(0,89,181,0.12)',   'icon'=>'hourglass-half'],
                        'confirmed'        => ['label'=>'Konfirmuar',   'color'=>'#0e7490', 'bg'=>'rgba(6,182,212,0.12)',  'icon'=>'circle-check'],
                        'shipped'          => ['label'=>'Dërguar',      'color'=>'#6d28d9', 'bg'=>'rgba(167,139,250,0.12)','icon'=>'truck'],
                        'delivered'        => ['label'=>'Dorëzuar',     'color'=>'#1a7a1a', 'bg'=>'rgba(16,185,129,0.12)', 'icon'=>'check-double'],
                        'cancelled'        => ['label'=>'Anuluar',      'color'=>'#c0392b', 'bg'=>'rgba(255,59,48,0.12)',  'icon'=>'ban'],
                        'payment_failed'   => ['label'=>'Dështoi',      'color'=>'#c0392b', 'bg'=>'rgba(255,59,48,0.12)',  'icon'=>'circle-xmark'],
                    ];
                    $st = $statusMap[$order->status] ?? ['label'=>ucfirst($order->status??'—'),'color'=>'#8e8e93','bg'=>'#f5f5f7','icon'=>'circle'];
                @endphp
                <tr data-status="{{ $order->status }}">
                    <td><input type="checkbox" class="row-check" value="{{ $order->uuid }}" onchange="updateBulkBar()"></td>
                    <td><span style="font-size:13px;font-weight:700;color:#1d1d1f;">#{{ str_pad($order->uuid,5,'0',STR_PAD_LEFT) }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#7c6fff,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0;">
                                {{ strtoupper(substr($order->customer_name??'K',0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:#1d1d1f;">{{ $order->customer_name ?? '—' }}</div>
                                <div style="font-size:11px;color:#8e8e93;">{{ $order->customer_phone ?? $order->customer_email ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:500;color:#1d1d1f;">{{ $order->items->count() }} produkt{{ $order->items->count()!==1?'e':'' }}</div>
                        <div style="font-size:11px;color:#8e8e93;margin-top:2px;">{{ $productNames->take(2)->implode(', ') }}{{ $productNames->count()>2?'...':'' }}</div>
                    </td>
                    <td><span style="font-size:15px;font-weight:800;color:#7c6fff;">{{ number_format($order->total_amount??0,2) }} €</span></td>
                    <td>
                        <span class="status-badge" style="color:{{ $st['color'] }};background:{{ $st['bg'] }};">
                            <i class="fa-solid fa-{{ $st['icon'] }}" style="font-size:10px;"></i>
                            {{ $st['label'] }}
                        </span>
                    </td>
                 <td style="font-size:12px;color:#8e8e93;">
    {{ $order->created_at?->timezone('Europe/Warsaw')->format('d M Y') ?? '—' }}
    <div style="font-size:11px;">
        {{ $order->created_at?->timezone('Europe/Warsaw')->format('H:i') ?? '' }}
    </div>
</td>

                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <a href="{{ url('/admin/orders/'.$order->uuid) }}" class="btn btn-secondary btn-sm" style="padding:6px 10px;" title="Shiko">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                          @if($isFinal)
    <span style="font-size:11px;color:{{ $st['color'] }};padding:5px 8px;font-weight:600;">
        <i class="fa-solid fa-{{ $st['icon'] }}"></i> {{ $st['label'] }}
    </span>
@else
    <button onclick="openStatusModal('{{ $order->uuid }}', '{{ $order->status }}', '{{ addslashes($order->customer_name) }}', '{{ $order->order_number }}')"
            style="padding:5px 10px;border-radius:7px;border:1px solid rgba(0,0,0,0.1);background:#f5f5f7;color:#1d1d1f;font-size:12px;cursor:pointer;font-family:inherit;">
        <span style="color:{{ $st['color'] }};">{{ $st['label'] }}</span>
        <i class="fa-solid fa-chevron-down" style="font-size:9px;margin-left:4px;color:#8e8e93;"></i>
    </button>
@endif
                            <form action="{{ url('/admin/orders/'.$order->uuid) }}" method="POST" style="display:inline;" onsubmit="return confirm('Fshi porosinë?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:6px 10px;" title="Fshi">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:#8e8e93;">
                        <i class="fa-solid fa-bag-shopping" style="font-size:32px;display:block;margin-bottom:12px;opacity:0.3;"></i>
                        Nuk ka porosi ende.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="mobile-orders" style="display:none;">
        @forelse($orders as $order)
        @php
            $isFinalM = in_array($order->status, ['cancelled','payment_failed','delivered']);
            $stMM = [
                'pending'          => ['label'=>'Në Pritje',    'color'=>'#a05a00','bg'=>'rgba(245,158,11,0.12)','icon'=>'clock'],
                'awaiting_payment' => ['label'=>'Pret Pagesën', 'color'=>'#0059b5','bg'=>'rgba(0,89,181,0.12)', 'icon'=>'hourglass-half'],
                'confirmed'        => ['label'=>'Konfirmuar',   'color'=>'#0e7490','bg'=>'rgba(6,182,212,0.12)', 'icon'=>'circle-check'],
                'shipped'          => ['label'=>'Dërguar',      'color'=>'#6d28d9','bg'=>'rgba(167,139,250,0.12)','icon'=>'truck'],
                'delivered'        => ['label'=>'Dorëzuar',     'color'=>'#1a7a1a','bg'=>'rgba(16,185,129,0.12)','icon'=>'check-double'],
                'cancelled'        => ['label'=>'Anuluar',      'color'=>'#c0392b','bg'=>'rgba(255,59,48,0.12)', 'icon'=>'ban'],
                'payment_failed'   => ['label'=>'Dështoi',      'color'=>'#c0392b','bg'=>'rgba(255,59,48,0.12)', 'icon'=>'circle-xmark'],
            ];
            $smm = $stMM[$order->status] ?? ['label'=>ucfirst($order->status),'color'=>'#8e8e93','bg'=>'#f5f5f7','icon'=>'circle'];
            $pnm = $order->items->map(fn($i)=>optional($i->product)->name??'')->filter();
        @endphp
        <div class="order-mobile-card" data-status="{{ $order->status }}">
            <input type="checkbox" class="row-check" value="{{ $order->uuid }}" onchange="updateBulkBar()" style="margin-top:4px;">
            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#7c6fff,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:white;flex-shrink:0;">
                {{ strtoupper(substr($order->customer_name??'K',0,1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-weight:700;font-size:13px;color:#1d1d1f;">#{{ str_pad($order->id,5,'0',STR_PAD_LEFT) }}</span>
                    <span style="font-size:15px;font-weight:800;color:#7c6fff;">{{ number_format($order->total_amount??0,2) }} €</span>
                </div>
                <div style="font-weight:600;font-size:13px;color:#1d1d1f;margin-bottom:2px;">{{ $order->customer_name??'—' }}</div>
                <div style="font-size:11px;color:#8e8e93;margin-bottom:8px;">
                    {{ $order->customer_phone??$order->customer_email??'' }}
                    @if($order->created_at) · {{ $order->created_at->timezone('Europe/Warsaw')->format('d M Y, H:i') }} @endif


                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span class="status-badge" style="color:{{ $smm['color'] }};background:{{ $smm['bg'] }};">
                        <i class="fa-solid fa-{{ $smm['icon'] }}" style="font-size:10px;"></i>
                        {{ $smm['label'] }}
                    </span>

                    @if(!$isFinalM)
                    <form action="{{ url('/admin/orders/'.$order->uuid) }}" method="POST">
                        @csrf @method('PUT')
                        <select name="status" onchange="this.form.submit()" style="padding:4px 8px;border-radius:7px;border:1px solid rgba(0,0,0,0.1);background:#f5f5f7;color:#1d1d1f;font-size:11px;">
                            @foreach(['pending'=>'Në Pritje','awaiting_payment'=>'Pret Pagesën','confirmed'=>'Konfirmuar','shipped'=>'Dërguar','delivered'=>'Dorëzuar','cancelled'=>'Anuluar'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ $order->status===$val?'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endif

                    <a href="{{ url('/admin/orders/'.$order->uuid) }}" style="padding:5px 10px;border-radius:7px;border:1px solid rgba(0,0,0,0.1);background:#f5f5f7;color:#6e6e73;font-size:11px;text-decoration:none;">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <form action="{{ url('/admin/orders/'.$order->uuid) }}" method="POST" onsubmit="return confirm('Fshi porosinë?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:5px 10px;border-radius:7px;border:1px solid rgba(255,59,48,0.2);background:rgba(255,59,48,0.08);color:#ff3b30;font-size:11px;cursor:pointer;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:48px;color:#8e8e93;">
            <i class="fa-solid fa-bag-shopping" style="font-size:28px;display:block;margin-bottom:12px;opacity:0.3;"></i>
            Nuk ka porosi ende.
        </div>
        @endforelse
    </div>

   @if($orders->hasPages())
<div style="padding:16px 20px;border-top:1px solid rgba(0,0,0,0.06);display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
    {{-- Previous --}}
    @if($orders->onFirstPage())
        <span style="padding:6px 12px;border-radius:8px;border:1px solid #e2e2e4;color:#aeaeb2;font-size:13px;">‹</span>
    @else
        <a href="{{ $orders->previousPageUrl() }}" style="padding:6px 12px;border-radius:8px;border:1px solid #e2e2e4;color:#1d1d1f;font-size:13px;text-decoration:none;">‹</a>
    @endif

    {{-- Pages --}}
    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
        @if($page == $orders->currentPage())
            <span style="padding:6px 12px;border-radius:8px;background:#00bcd4;color:#fff;font-size:13px;font-weight:600;">{{ $page }}</span>
        @else
            <a href="{{ $url }}" style="padding:6px 12px;border-radius:8px;border:1px solid #e2e2e4;color:#1d1d1f;font-size:13px;text-decoration:none;">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if($orders->hasMorePages())
        <a href="{{ $orders->nextPageUrl() }}" style="padding:6px 12px;border-radius:8px;border:1px solid #e2e2e4;color:#1d1d1f;font-size:13px;text-decoration:none;">›</a>
    @else
        <span style="padding:6px 12px;border-radius:8px;border:1px solid #e2e2e4;color:#aeaeb2;font-size:13px;">›</span>
    @endif
</div>
@endif
</div>
{{-- MODAL EMAIL --}}
<div id="statusModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;max-width:560px;width:90%;max-height:90vh;overflow-y:auto;">
        
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="margin:0;font-size:17px;font-weight:700;color:#1d1d1f;">Ndrysho Statusin</h3>
            <button onclick="closeStatusModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#8e8e93;">✕</button>
        </div>

        <div style="margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#8e8e93;display:block;margin-bottom:6px;">STATUSI I RI</label>
            <select id="modalStatus" class="form-select" style="width:100%;">
                <option value="pending">Në Pritje</option>
                <option value="awaiting_payment">Pret Pagesën</option>
                <option value="confirmed">Konfirmuar</option>
                <option value="shipped">Dërguar</option>
                <option value="delivered">Dorëzuar</option>
                <option value="cancelled">Anuluar</option>
            </select>
        </div>

        <div style="background:#f5f5f7;border-radius:10px;padding:14px;margin-bottom:16px;">
            <label style="font-size:12px;font-weight:600;color:#8e8e93;display:block;margin-bottom:8px;">
                📧 EMAILI QË DO T'I DËRGOHET KLIENTIT (mund ta editosh)
            </label>
            <div style="margin-bottom:8px;">
                <label style="font-size:11px;color:#8e8e93;">Subjekti:</label>
                <input type="text" id="modalEmailSubject" class="form-control" style="margin-top:4px;">
            </div>
            <div>
                <label style="font-size:11px;color:#8e8e93;">Mesazhi:</label>
                <textarea id="modalEmailBody" class="form-control" rows="5" style="margin-top:4px;resize:vertical;"></textarea>
            </div>
        </div>

        <div style="display:flex;gap:10px;">
            <button onclick="submitStatus(true)" class="btn btn-primary" style="flex:1;justify-content:center;">
                <i class="fa-solid fa-envelope"></i> Ndrysho & Dërgo Email
            </button>
            <button onclick="submitStatus(false)" class="btn btn-secondary" style="flex:1;justify-content:center;">
                <i class="fa-solid fa-rotate"></i> Ndrysho pa Email
            </button>
        </div>
    </div>
</div>

@include('admin.orders.partials.status-modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/orders-index.js') }}"></script>
@endpush
