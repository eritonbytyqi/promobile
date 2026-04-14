<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    
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

</body>
</html>
