<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi SMK Taruna Bangsa')</title>

    <!-- Preconnect for external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com">

    <!-- Preload critical fonts -->
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap">

    <!-- Fonts (non-blocking) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link
            href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
            rel="stylesheet">
    </noscript>

    <!-- Scripts and Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Icons (deferred, pinned version for caching) -->
    <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" defer></script>
</head>

<body class="font-inter antialiased bg-slate-50 text-slate-800">

    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">
        @yield('content')
    </div>

    <!-- Initialize Lucide Icons (waits for DOM + deferred script) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                lucide.createIcons();
            } else {
                // Retry if Lucide hasn't loaded yet
                var interval = setInterval(function () {
                    if (window.lucide) {
                        lucide.createIcons();
                        clearInterval(interval);
                    }
                }, 100);
                // Stop retrying after 5 seconds
                setTimeout(function () { clearInterval(interval); }, 5000);
            }
        });
    </script>
    @stack('scripts')
</body>

</html>