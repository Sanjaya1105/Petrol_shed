<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $heading.' — '.config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen font-sans antialiased flex md:flex-row overflow-x-hidden" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    @include('partials.dashboard-sidebar', ['navPrefix' => $navPrefix, 'heading' => $heading, 'page' => $page ?? null])

    <div id="dashboard-content" class="flex-1 flex flex-col min-w-0 transition-[margin] duration-200 ease-out">
        <header class="border-b border-gray-200 dark:border-gray-700 bg-white/80 dark:bg-[#161615]/80 backdrop-blur px-4 sm:px-6 py-4 flex items-center gap-3">
            <button
                id="sidebar-toggle"
                type="button"
                class="md:hidden inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm"
                aria-controls="dashboard-sidebar"
                aria-expanded="false"
            >
                Menu
            </button>
            <h1 class="text-xl font-semibold">{{ $heading }}</h1>
        </header>
        <main class="flex-1 p-4 sm:p-6">
            @yield('content')
        </main>
    </div>
    <script>
        (function () {
            const sidebar = document.getElementById('dashboard-sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            const content = document.getElementById('dashboard-content');
            if (!sidebar || !toggle || !overlay || !content) return;

            const closeBtn = document.getElementById('sidebar-close');

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                content.classList.add('ml-72');
                toggle.setAttribute('aria-expanded', 'true');
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                content.classList.remove('ml-72');
                toggle.setAttribute('aria-expanded', 'false');
            };

            toggle.addEventListener('click', () => {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            overlay.addEventListener('click', closeSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    overlay.classList.add('hidden');
                    content.classList.remove('ml-72');
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    content.classList.remove('ml-72');
                }
            });
        })();
    </script>
</body>
</html>
