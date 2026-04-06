@extends('layouts.dashboard')

@section('title', $sectionTitle.' — '.$heading.' — '.config('app.name'))

@section('content')
    <div class="max-w-3xl">
        <h2 class="text-lg font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ $sectionTitle }}</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
            Signed in as {{ auth()->user()->name }} ({{ auth()->user()->username }}).
        </p>

        @if (session('status'))
            <p class="mb-4 text-sm text-green-700 dark:text-green-400">{{ session('status') }}</p>
        @endif

        @if ($navPrefix === 'dev' && $page === 'categories')
            <form method="post" action="{{ route('dev.categories.store') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-5">
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

            <div class="mt-6 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                <h3 class="text-sm font-semibold mb-3">Available categories</h3>

                @if ($categories !== null && $categories->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($categories as $item)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-md p-3">
                                <form method="post" action="{{ route('dev.categories.update', $item) }}" class="flex gap-2 items-start">
                                    @csrf
                                    @method('PUT')
                                    <input
                                        type="text"
                                        name="category"
                                        value="{{ old('category', $item->category) }}"
                                        required
                                        class="flex-1 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                    >
                                    <button type="submit" class="rounded-md bg-blue-600 text-white px-3 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                                        Update
                                    </button>
                                </form>
                                <form method="post" action="{{ route('dev.categories.delete', $item) }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-700 hover:bg-red-800 text-white px-3 py-2 text-sm font-medium transition-colors">
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
        @else
            <p class="text-sm">
                This is the <strong>{{ $sectionTitle }}</strong> section. Add your content here.
            </p>
        @endif
    </div>
@endsection
