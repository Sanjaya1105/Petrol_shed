@extends('layouts.dashboard')

@section('title', $sectionTitle.' — '.$heading.' — '.config('app.name'))

@section('content')
    <div class="max-w-3xl w-full">
        <h2 class="text-lg font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ $sectionTitle }}</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
            Signed in as {{ auth()->user()->name }} ({{ auth()->user()->username }}).
        </p>

        @if (session('status'))
            <p class="mb-4 text-sm text-green-700 dark:text-green-400">{{ session('status') }}</p>
        @endif

        @if ($navPrefix === 'dev' && $page === 'categories')
            <form method="post" action="{{ route('dev.categories.store') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                @csrf
                <div>
                    <label for="category" class="block text-sm font-medium mb-1">Category</label>
                    <input
                        type="text"
                        name="category"
                        id="category"
                        value="{{ old('category') }}"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    >
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                    Submit
                </button>
            </form>

            <div class="mt-6 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <h3 class="text-sm font-semibold mb-3">Available categories</h3>

                @if ($categories !== null && $categories->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($categories as $item)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-md p-3">
                                <form method="post" action="{{ route('dev.categories.update', $item) }}" class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start">
                                    @csrf
                                    @method('PUT')
                                    <input
                                        type="text"
                                        name="category"
                                        value="{{ old('category', $item->category) }}"
                                        required
                                        class="flex-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                    >
                                    <button type="submit" class="rounded-md bg-blue-600 text-white px-3 py-2 text-sm font-medium hover:opacity-90 transition-opacity w-full sm:w-auto">
                                        Update
                                    </button>
                                </form>
                                <form method="post" action="{{ route('dev.categories.delete', $item) }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-700 hover:bg-red-800 text-white px-3 py-2 text-sm font-medium transition-colors w-full sm:w-auto">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No categories added yet.</p>
                @endif
            </div>
        @elseif ($navPrefix === 'dev' && $page === 'tanks')
            <form method="post" action="{{ route('dev.tanks.store') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                @csrf
                <div>
                    <label for="tank_name" class="block text-sm font-medium mb-1">Tank name</label>
                    <input
                        type="text"
                        name="tank_name"
                        id="tank_name"
                        value="{{ old('tank_name') }}"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    >
                    @error('tank_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tank_capacity" class="block text-sm font-medium mb-1">Tank capacity</label>
                    <input
                        type="number"
                        name="tank_capacity"
                        id="tank_capacity"
                        value="{{ old('tank_capacity') }}"
                        required
                        min="0"
                        step="0.01"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    >
                    @error('tank_capacity')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium mb-1">Category</label>
                    <select
                        name="category_id"
                        id="category_id"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    >
                        <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select category</option>
                        @if ($categories !== null)
                            @foreach ($categories as $item)
                                <option value="{{ $item->id }}" {{ (string) old('category_id') === (string) $item->id ? 'selected' : '' }}>
                                    {{ $item->category }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                    Submit
                </button>
            </form>

            <div class="mt-6 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <h3 class="text-sm font-semibold mb-3">Available tanks</h3>

                @if ($tanks !== null && $tanks->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($tanks as $tank)
                            <details class="border border-gray-200 dark:border-gray-700 rounded-md p-3 group">
                                <summary class="list-none cursor-pointer flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <p class="text-sm font-medium">
                                        {{ (string) session('updated_tank_id') === (string) $tank->id ? session('updated_tank_name') : $tank->tank_name }}
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-md bg-blue-600 text-white px-3 py-2 text-sm font-medium">Update</span>
                                    </div>
                                </summary>

                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <form method="post" action="{{ route('dev.tanks.update', $tank) }}" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <input
                                            type="text"
                                            name="tank_name"
                                            value="{{ $tank->tank_name }}"
                                            required
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                        >
                                        <input
                                            type="number"
                                            name="tank_capacity"
                                            value="{{ $tank->tank_capacity }}"
                                            min="0"
                                            step="0.01"
                                            required
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                        >
                                        <select
                                            name="category_id"
                                            required
                                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                        >
                                            @if ($categories !== null)
                                                @foreach ($categories as $categoryOption)
                                                    <option value="{{ $categoryOption->id }}" {{ (int) $tank->category_id === (int) $categoryOption->id ? 'selected' : '' }}>
                                                        {{ $categoryOption->category }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <button type="submit" class="rounded-md bg-blue-600 text-white px-3 py-2 text-sm font-medium hover:opacity-90 transition-opacity w-full sm:w-auto">
                                            Save update
                                        </button>
                                    </form>
                                    <form method="post" action="{{ route('dev.tanks.delete', $tank) }}" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-700 hover:bg-red-800 text-white px-3 py-2 text-sm font-medium transition-colors w-full sm:w-auto">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </details>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No tanks added yet.</p>
                @endif
            </div>
        @elseif (in_array($navPrefix, ['dev', 'admin'], true) && $page === 'price')
            <form method="post" action="{{ route($navPrefix.'.prices.save') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                @csrf
                @if ($categories !== null && $categories->isNotEmpty())
                    @foreach ($categories as $item)
                        <div>
                            <label for="price_{{ $item->id }}" class="block text-sm font-medium mb-1">{{ $item->category }}</label>
                            <input
                                type="number"
                                name="prices[{{ $item->id }}]"
                                id="price_{{ $item->id }}"
                                value="{{ old('prices.'.$item->id, $prices[$item->id] ?? '') }}"
                                min="0"
                                step="0.01"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                            >
                        </div>
                    @endforeach
                    <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                        Save prices
                    </button>
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No categories available. Add categories first.</p>
                @endif
            </form>
        @else
            <p class="text-sm">
                This is the <strong>{{ $sectionTitle }}</strong> section. Add your content here.
            </p>
        @endif
    </div>
@endsection
