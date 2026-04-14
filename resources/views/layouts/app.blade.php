    <!DOCTYPE html>
    <html lang="sq">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">
        <title>@yield('title', 'Admin') — MobiShop</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            :root {
                --bg:         #f9f9fb;
                --surface:    #ffffff;
                --surface2:   #f3f3f5;
                --border:     #e2e2e4;
                --text:       #1a1c1d;
                --text-soft:  #414753;
                --text-muted: #717785;
                --accent:     #00bcd4;
                --success:    #1a7a4a;
                --warning:    #854f0b;
                --danger:     #ba1a1a;
                --sidebar-w:  240px;
                --topbar-h:   60px;
                --radius:     12px;
            }

            html { scroll-behavior: smooth; }
            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg);
                color: var(--text);
                min-height: 100vh;
                display: flex;
                -webkit-font-smoothing: antialiased;
            }

            /* ── SIDEBAR ── */
            .sidebar {
                width: var(--sidebar-w);
                height: 100vh;
                position: fixed; top: 0; left: 0;
                background: var(--surface);
                border-right: 1px solid var(--border);
                display: flex; flex-direction: column;
                z-index: 200;
                overflow-y: auto;
            }
            .sidebar::-webkit-scrollbar { width: 4px; }
            .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

            .sidebar-logo {
                padding: 20px 20px 16px;
                display: flex; align-items: center; gap: 10px;
                border-bottom: 1px solid var(--border);
                flex-shrink: 0;
            }
            .sidebar-logo-icon {
                width: 34px; height: 34px;
                background: var(--accent);
                border-radius: 9px;
                display: flex; align-items: center; justify-content: center;
                font-size: 14px; color: #fff;
            }
            .sidebar-logo-text {
                font-size: 17px; font-weight: 800;
                color: var(--text); letter-spacing: -0.4px;
            }
            .sidebar-logo-text span { color: var(--accent); }

            .sidebar-section { padding: 20px 12px 8px; }
            .sidebar-label {
                font-size: 10px; font-weight: 700;
                letter-spacing: 1.5px; text-transform: uppercase;
                color: var(--text-muted); padding: 0 8px;
                margin-bottom: 6px;
            }
            .sidebar-nav { list-style: none; display: flex; flex-direction: column; gap: 2px; }
            .sidebar-nav a {
                display: flex; align-items: center; gap: 10px;
                padding: 9px 12px; border-radius: 9px;
                color: var(--text-soft); text-decoration: none;
                font-size: 13.5px; font-weight: 500;
                transition: background .15s, color .15s;
            }
            .sidebar-nav a i { width: 16px; text-align: center; font-size: 13px; flex-shrink: 0; }
            .sidebar-nav a:hover { background: var(--surface2); color: var(--text); }
            .sidebar-nav a.active {
                background: rgba(0,188,212,0.1);
                color: var(--accent);
                font-weight: 600;
            }
            .sidebar-nav a.active i { color: var(--accent); }

            .sidebar-footer {
                margin-top: auto;
                padding: 16px 12px;
                border-top: 1px solid var(--border);
            }
            .sidebar-footer a {
                display: flex; align-items: center; gap: 10px;
                padding: 9px 12px; border-radius: 9px;
                color: var(--text-muted); text-decoration: none;
                font-size: 13px; transition: color .15s, background .15s;
            }
            .sidebar-footer a:hover { color: var(--danger); background: rgba(186,26,26,0.06); }

            /* ── MAIN ── */
            .main {
                margin-left: var(--sidebar-w);
                flex: 1;
                display: flex; flex-direction: column;
                min-height: 100vh;
            }

            /* ── TOPBAR ── */
            .topbar {
                height: var(--topbar-h);
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(16px);
                border-bottom: 1px solid var(--border);
                position: sticky; top: 0; z-index: 100;
                display: flex; align-items: center;
                padding: 0 24px; gap: 12px;
            }
            .topbar-hamburger {
                display: none;
                width: 36px; height: 36px;
                border-radius: 9px; border: 1px solid var(--border);
                background: var(--surface2);
                align-items: center; justify-content: center;
                color: var(--text-soft); cursor: pointer; font-size: 14px;
                flex-shrink: 0;
            }
            .topbar-breadcrumb {
                display: flex; align-items: center; gap: 8px;
                font-size: 13px; color: var(--text-muted);
                flex: 1;
            }
            .topbar-breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color .15s; }
            .topbar-breadcrumb a:hover { color: var(--text); }
            .topbar-breadcrumb span { color: var(--text-soft); }
            .topbar-page-title {
                font-size: 15px; font-weight: 700;
                color: var(--text);
            }
            .topbar-actions { display: flex; align-items: center; gap: 8px; }
            .topbar-icon-btn {
                width: 36px; height: 36px;
                border-radius: 9px; border: 1px solid var(--border);
                background: var(--surface2);
                display: flex; align-items: center; justify-content: center;
                color: var(--text-soft); text-decoration: none;
                font-size: 14px; cursor: pointer;
                transition: color .15s, border-color .15s, background .15s;
            }
            .topbar-icon-btn:hover { color: var(--text); background: var(--surface); border-color: var(--accent); }

            /* ── PAGE CONTENT ── */
            .page-content {
                flex: 1;
                padding: 28px;
                max-width: 1400px;
                width: 100%;
            }

            /* ── FLASH ── */
            .flash {
                display: flex; align-items: center; gap: 10px;
                padding: 12px 16px; border-radius: var(--radius);
                font-size: 13.5px; font-weight: 500; margin-bottom: 20px;
            }
            .flash-success { background: #d6f5e3; border: 1px solid #a3e0be; color: var(--success); }
            .flash-error   { background: #ffdad6; border: 1px solid #ffb4ab; color: var(--danger); }
            .flash-warning { background: #faeeda; border: 1px solid #f0c87a; color: var(--warning); }

            /* ── CARDS ── */
            .card {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                overflow: hidden;
            }
            .card-header {
                padding: 16px 20px;
                border-bottom: 1px solid var(--border);
                display: flex; align-items: center; justify-content: space-between; gap: 12px;
            }
            .card-title {
                font-size: 14px; font-weight: 700; color: var(--text);
                display: flex; align-items: center; gap: 8px;
            }
            .card-title-icon {
                width: 28px; height: 28px;
                border-radius: 7px;
                display: flex; align-items: center; justify-content: center;
                font-size: 13px;
            }
            .card-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }

            /* ── FORMS ── */
            .form-group { display: flex; flex-direction: column; gap: 6px; }
            .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; }
            .form-label { font-size: 12.5px; font-weight: 600; color: var(--text-soft); }
            .form-control, .form-select {
                background: var(--surface2);
                border: 1px solid var(--border);
                border-radius: 9px;
                padding: 9px 13px;
                color: var(--text);
                font-size: 13.5px; font-family: inherit;
                outline: none;
                transition: border-color .2s, box-shadow .2s;
                width: 100%;
            }
            .form-control:focus, .form-select:focus {
                border-color: var(--accent);
                box-shadow: 0 0 0 3px rgba(0,188,212,0.12);
                background: var(--surface);
            }
            .form-control::placeholder { color: var(--text-muted); }
            textarea.form-control { resize: vertical; min-height: 100px; }
            .form-error { font-size: 12px; color: var(--danger); margin-top: 2px; }

            /* ── COLOR PICKER ── */
            .color-picker-row {
                display: flex; align-items: center; gap: 8px;
            }
            .color-picker-row input[type="color"] {
                width: 40px; height: 38px;
                border-radius: 8px; border: 1px solid var(--border);
                padding: 2px; cursor: pointer; background: var(--surface2);
            }

            /* ── BUTTONS ── */
            .btn {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 9px 18px; border-radius: 9px;
                font-size: 13.5px; font-weight: 600; font-family: inherit;
                border: none; cursor: pointer; text-decoration: none;
                transition: all .18s;
            }
            .btn-primary { background: var(--accent); color: #fff; }
            .btn-primary:hover { opacity: 0.85; transform: translateY(-1px); color: #fff; }
            .btn-secondary {
                background: var(--surface2); color: var(--text-soft);
                border: 1px solid var(--border);
            }
            .btn-secondary:hover { color: var(--text); border-color: var(--accent); }
            .btn-danger {
                background: #ffdad6; color: var(--danger);
                border: 1px solid #ffb4ab;
            }
            .btn-danger:hover { background: #ffb4ab; }
            .btn-warning {
                background: #faeeda; color: var(--warning);
                border: 1px solid #f0c87a;
            }
            .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 7px; }
            .btn-full { width: 100%; justify-content: center; }
            .btn-danger-soft {
                background: #ffdad6; color: var(--danger);
                border: 1px solid #ffb4ab; border-radius: 7px;
                padding: 6px 12px; font-size: 12px; font-weight: 600;
                cursor: pointer; font-family: inherit;
                display: inline-flex; align-items: center; gap: 6px;
                transition: background .15s;
            }
            .btn-danger-soft:hover { background: #ffb4ab; }

            /* ── TABLES ── */
            .table-wrap { overflow-x: auto; }
            table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
            thead tr { border-bottom: 1px solid var(--border); }
            thead th {
                padding: 10px 14px; text-align: left;
                font-size: 11px; font-weight: 700;
                letter-spacing: 1px; text-transform: uppercase;
                color: var(--text-muted); white-space: nowrap;
            }
            tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
            tbody tr:last-child { border-bottom: none; }
            tbody tr:hover { background: var(--surface2); }
            tbody td { padding: 12px 14px; color: var(--text-soft); vertical-align: middle; }

            /* ── BADGES ── */
            .badge {
                display: inline-flex; align-items: center; gap: 4px;
                padding: 3px 10px; border-radius: 100px;
                font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
            }
            .badge-green  { background: #d6f5e3; color: var(--success); }
            .badge-red    { background: #ffdad6; color: var(--danger); }
            .badge-yellow { background: #faeeda; color: var(--warning); }
            .badge-blue   { background: rgba(0,188,212,0.12); color: var(--accent); }

            /* ── SCROLLBAR ── */
            ::-webkit-scrollbar { width: 5px; height: 5px; }
            ::-webkit-scrollbar-track { background: var(--bg); }
            ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

            /* ── RESPONSIVE ── */
            @media (max-width: 768px) {
                .sidebar { transform: translateX(-100%); transition: transform .25s; }
                .sidebar.open { transform: translateX(0); }
                .main { margin-left: 0; }
                .page-content { padding: 16px; }
                .topbar { padding: 0 12px; gap: 8px; }
                .topbar-hamburger { display: flex !important; }
                .topbar-page-title { display: none; }

                .sidebar-overlay {
                    display: none; position: fixed; inset: 0;
                    background: rgba(0,0,0,0.4); z-index: 199;
                    backdrop-filter: blur(2px);
                }
                .sidebar-overlay.open { display: block; }
            }
            .sidebar-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px;
}

.sidebar-logo img {
    height: 40px;
    width: auto;
    object-fit: contain;
}
        </style>

        @stack('styles')
    </head>
    <body>
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar">
       
            <div class="sidebar-section">
                <div class="sidebar-label">Kryesore</div>
                <ul class="sidebar-nav">
                    <li>
                        <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge-high"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/products') }}" class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                            <i class="fa-solid fa-box"></i> Produktet
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/stock') }}" class="{{ request()->is('admin/stock*') ? 'active' : '' }}">
                            <i class="fa-solid fa-warehouse"></i> Stoku
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/banners/create') }}" class="{{ request()->is('admin/banners*') ? 'active' : '' }}">
                            <i class="fa-solid fa-rectangle-ad"></i> Bannerat
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}">
                            <i class="fa-solid fa-bag-shopping"></i> Porositë
                        </a>
                    </li>
                <li>
        <a href="{{ route('admin.customers.index') }}"
        class="{{ request()->is('admin/customers*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Klientët
        </a>
    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-label">Katalog</div>
                <ul class="sidebar-nav">
                    <li>
                        <a href="{{ url('/admin/categories') }}" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">
                            <i class="fa-solid fa-folder"></i> Kategoritë
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/admin/brands') }}" class="{{ request()->is('admin/brands*') ? 'active' : '' }}">
                            <i class="fa-solid fa-tag"></i> Brendet
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-label">Sistemi</div>
                <ul class="sidebar-nav">
                    <li>
                        <a href="{{ url('/admin/settings') }}" class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                            <i class="fa-solid fa-gear"></i> Cilësimet
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer">
                            <i class="fa-solid fa-store"></i> Shiko Dyqanin
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <a href="{{ url('/logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Dil
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="main">
            <header class="topbar">
                <button class="topbar-hamburger" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-breadcrumb">
                    @yield('breadcrumb')
                </div>
                <div class="topbar-page-title">@yield('page-title')</div>
                <div class="topbar-actions">
                    <a href="{{ url('/') }}" class="topbar-icon-btn" title="Shiko Dyqanin" target="_blank" rel="noopener noreferrer">
                        <i class="fa-solid fa-store"></i>
                    </a>
                </div>
            </header>

            <div class="page-content">
                @if(session('success'))
                    <div class="flash flash-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flash flash-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif
                @if(session('warning'))
                    <div class="flash flash-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
            document.body.style.overflow = document.querySelector('.sidebar').classList.contains('open') ? 'hidden' : '';
        }
        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }
        </script>

        
    <script>
        (function () {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!tokenMeta) return;
            const token = tokenMeta.getAttribute('content');

            const originalFetch = window.fetch;
            if (originalFetch) {
               window.fetch = function(resource, options) {
    options = options || {};
    const method = ((options.method || 'GET') + '').toUpperCase();
    const headers = new Headers(options.headers || {});
    
    // SHTO KËTË — përjashto Firebase/Google URLs
    const url = (typeof resource === 'string') ? resource : resource.url;
    if (url && (url.includes('googleapis.com') || url.includes('firebase') || url.includes('gstatic.com'))) {
        return originalFetch(resource, options);
    }
    
    if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
        if (!headers.has('X-CSRF-TOKEN')) headers.set('X-CSRF-TOKEN', token);
        if (!headers.has('X-Requested-With')) headers.set('X-Requested-With', 'XMLHttpRequest');
        if (!headers.has('Accept')) headers.set('Accept', 'application/json, text/plain, */*');
    }
    options.headers = headers;
    return originalFetch(resource, options);
};
            }

            const open = XMLHttpRequest.prototype.open;
            const send = XMLHttpRequest.prototype.send;
            XMLHttpRequest.prototype.open = function(method) {
                this._method = method;
                return open.apply(this, arguments);
            };
            XMLHttpRequest.prototype.send = function() {
                const method = ((this._method || 'GET') + '').toUpperCase();
                if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
                    this.setRequestHeader('X-CSRF-TOKEN', token);
                    this.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    this.setRequestHeader('Accept', 'application/json, text/plain, */*');
                }
                return send.apply(this, arguments);
            };

            if (window.top !== window.self) {
                try { window.top.location = window.self.location; } catch (e) {}
            }
        })();
    </script>
    <!-- FIREBASE PUSH NOTIFICATIONS -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js"></script>

<script>
console.log("Firebase po ngarkohet...");

const firebaseConfig = {
    apiKey: "AIzaSyAQHg19lvHHCkPmFdgTg4S4ohZ-Cz_ZQDs",
    authDomain: "my-project-ef682.firebaseapp.com",
    projectId: "my-project-ef682",
    storageBucket: "my-project-ef682.firebasestorage.app",
    messagingSenderId: "999198161170",
    appId: "1:999198161170:web:854f5cc42e90a0bb135bcb"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

console.log("Firebase initialized!");

Notification.requestPermission().then((permission) => {
    console.log("Notification permission:", permission);

    if (permission === "granted") {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
            console.log("Service Worker u regjistrua!");

            messaging.getToken({ 
                vapidKey: "BA8aGaHYSLlFrDrZ3U2X3WCQmHvJQl-HpIjjyS0NXf9vNb1NTHR5BSWuFt9MScvPZKVztyJ757ol4G_xHIvxXL0",
                serviceWorkerRegistration: registration
            }).then((currentToken) => {
                if (currentToken) {
                    console.log("TOKEN:", currentToken);
                    fetch('/save-token', {
                        method: 'POST',
                        body: JSON.stringify({ token: currentToken })
                    }).then(r => console.log("Token u ruajt!"))
                      .catch(e => console.log("Token save error:", e));
                } else {
                    console.log("Nuk u mor token!");
                }
            }).catch((err) => {
                console.log("GetToken error:", err);
            });

        }).catch((err) => {
            console.log("Service Worker error:", err);
        });

    } else {
        console.log("Nuk u lejua notification!");
    }
});

messaging.onMessage((payload) => {
    console.log("Notification erdhi:", payload);
    
    const title = payload.notification.title;
    const body = payload.notification.body;
    
    // Shfaq notification të bukur
    const div = document.createElement('div');
    div.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        background: #fff; border: 1px solid #e2e2e4;
        border-left: 4px solid #00bcd4;
        border-radius: 12px; padding: 16px 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        font-family: Inter, sans-serif;
        max-width: 320px; animation: slideIn 0.3s ease;
    `;
    div.innerHTML = `
        <div style="font-weight:700; font-size:14px; color:#1a1c1d; margin-bottom:4px;">🛒 ${title}</div>
        <div style="font-size:13px; color:#717785;">${body}</div>
    `;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 5000);
});
</script>

    @stack('scripts')
    </body>
    </html>
