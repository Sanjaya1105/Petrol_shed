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
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen font-sans antialiased flex" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    @include('partials.dashboard-sidebar', ['navPrefix' => $navPrefix, 'heading' => $heading, 'page' => $page ?? null])

    <div class="flex-1 flex flex-col min-w-0">
        <header class="border-b border-gray-200 dark:border-gray-700 bg-white/80 dark:bg-[#161615]/80 backdrop-blur px-6 py-4">
            <h1 class="text-xl font-semibold">{{ $heading }}</h1>
        </header>
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
