<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Role register — {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex items-center justify-center p-6 font-sans antialiased" style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
    <div class="w-full max-w-md">
        <h1 class="text-xl font-semibold mb-6 text-center">Role register</h1>

        @if (session('status'))
            <p class="mb-4 text-sm text-green-700 dark:text-green-400 text-center" role="status">{{ session('status') }}</p>
        @endif

        <form method="post" action="{{ url('/role') }}" class="space-y-4 bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-6">
            @csrf
            <div>
                <label for="role_name" class="block text-sm font-medium mb-1">Role name</label>
                <input
                    type="text"
                    name="role_name"
                    id="role_name"
                    value="{{ old('role_name') }}"
                    required
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                >
                @error('role_name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="role_number" class="block text-sm font-medium mb-1">Role number</label>
                <input
                    type="number"
                    name="role_number"
                    id="role_number"
                    value="{{ old('role_number') }}"
                    required
                    min="0"
                    step="1"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                >
                @error('role_number')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                Save role
            </button>
        </form>
    </div>
</body>
</html>
