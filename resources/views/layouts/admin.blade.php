<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">
    <meta name="shipping-free-min" content="{{ \App\Helpers\ShippingHelper::getShipping()['free_min'] }}">
<meta name="shipping-cost"     content="{{ \App\Helpers\ShippingHelper::getShipping()['cost'] }}">
<meta name="shipping-free-text" content="{{ \App\Helpers\ShippingHelper::getShipping()['free_text'] }}">
<meta name="shipping-flag"     content="{{ ['kosovo'=>'🇽🇰','albania'=>'🇦🇱','macedonia'=>'🇲🇰','serbia'=>'🇷🇸'][\App\Helpers\ShippingHelper::getShipping()['country']] ?? '🇽🇰' }}">
<meta name="shipping-name"     content="{{ ['kosovo'=>'Kosovë','albania'=>'Shqipëri','macedonia'=>'Maqedoni','serbia'=>'Serbi'][\App\Helpers\ShippingHelper::getShipping()['country']] ?? 'Kosovë' }}">
<meta name="shipping-flag" content="{{ \App\Helpers\ShippingHelper::getShipping()['country'] }}">

    <title>@yield('title', 'Admin') — MobiShop</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">

    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
         <div class="sidebar-logo">
    <img src="{{ asset('images/logo.svg') }}" alt="ProMobile" 
         style="height:22px;width:auto;max-width:140px;object-fit:contain;">
</div>
        </div>

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
                    <a href="{{ route('admin.customers.index') }}" class="{{ request()->is('admin/customers*') ? 'active' : '' }}">
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
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
            <div class="sidebar-user-role">🛡️ Admin</div>
        </div>
    </div>
    <div class="sidebar-footer-btns">
    <a href="{{ route('admin.profile') }}" class="sidebar-footer-btn">
    <i class="fa-solid fa-user"></i> Profili
</a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="sidebar-footer-btn sidebar-footer-btn--logout">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Dil
        </a>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
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
    <!-- Butoni për notifications (iOS) -->
    <!-- <button onclick="enableNotifications()" class="topbar-icon-btn" title="Aktivizo Notifications" id="notif-btn">
        <i class="fa-solid fa-bell"></i>
    </button> -->
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

    <script src="{{ asset('js/admin/layout.js') }}"></script>
    
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
                  fetch('/admin/save-token', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
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
    const div = document.createElement('div');
    div.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        background: #fff; border: 1px solid #e2e2e4;
        border-left: 4px solid #00bcd4;
        border-radius: 12px; padding: 16px 20px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        font-family: Inter, sans-serif;
        max-width: 320px;
    `;
    div.innerHTML = `
        <div style="font-weight:700; font-size:14px; color:#1a1c1d; margin-bottom:4px;">🛒 ${payload.notification.title}</div>
        <div style="font-size:13px; color:#717785;">${payload.notification.body}</div>
    `;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 5000);
});
</script>

<script>
function enableNotifications() {
    if (!('Notification' in window)) {
        alert('Ky browser nuk supporton notifications!');
        return;
    }
    
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then((registration) => {
                messaging.getToken({ 
                    vapidKey: "BA8aGaHYSLlFrDrZ3U2X3WCQmHvJQl-HpIjjyS0NXf9vNb1NTHR5BSWuFt9MScvPZKVztyJ757ol4G_xHIvxXL0",
                    serviceWorkerRegistration: registration
                }).then((currentToken) => {
                    if (currentToken) {
                        fetch('/admin/save-token', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ token: currentToken })
                        }).then(() => {
                            document.getElementById('notif-btn').style.color = '#00bcd4';
                            alert('✅ Notifications u aktivizuan!');
                        });
                    }
                });
            });
        } else {
            alert('❌ Notifications u refuzuan!');
        }
    });
}

// Kontrollo nëse tashmë janë aktivizuar
if (Notification.permission === 'granted') {
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('notif-btn');
        if (btn) btn.style.color = '#00bcd4';
    });
}
</script>

@stack('scripts')
</body>
</html>