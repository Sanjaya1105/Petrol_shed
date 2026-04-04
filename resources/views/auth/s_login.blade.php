<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex items-center justify-center p-6 font-sans antialiased" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <div class="w-full max-w-md">
        <h1 class="text-xl font-semibold mb-6 text-center">Login</h1>

        <form method="post" action="{{ route('s_login.store') }}" class="space-y-4 bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-6">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium mb-1">Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                >
                @error('username')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" value="1" class="rounded border-gray-300 dark:border-gray-600" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-sm">Remember me</label>
            </div>
            <button type="submit" class="w-full rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                Sign in
            </button>
        </form>
    </div>
</body>
</html>
