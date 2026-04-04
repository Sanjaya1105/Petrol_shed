<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dev — {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen p-6 font-sans antialiased" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <header class="max-w-3xl mx-auto flex items-center justify-between gap-4 mb-10">
        <h1 class="text-2xl font-semibold">Dev</h1>
        <form method="post" action="{{ route('s_logout') }}">
            @csrf
            <button type="submit" class="text-sm px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                Log out
            </button>
        </form>
    </header>
    <main class="max-w-3xl mx-auto">
        <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">Signed in as {{ auth()->user()->name }} ({{ auth()->user()->username }}).</p>
    </main>
</body>
</html>
