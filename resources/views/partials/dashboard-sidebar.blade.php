@php
    $items = [
        ['page' => 'home', 'label' => 'Home'],
        ['page' => 'categories', 'label' => 'Categories'],
        ['page' => 'pumps', 'label' => 'Pumps'],
        ['page' => 'tanks', 'label' => 'Tanks'],
        ['page' => 'price', 'label' => 'Price'],
    ];
    $routeName = $navPrefix.'.show';
@endphp

<aside class="w-56 shrink-0 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-[#161615] min-h-screen flex flex-col">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <p class="text-xs uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A] mb-1">Panel</p>
        <p class="text-lg font-semibold">{{ $heading }}</p>
    </div>
    <nav class="flex-1 p-3 space-y-1" aria-label="Main">
        @foreach ($items as $item)
            @php $active = isset($page) && $page === $item['page']; @endphp
            <a
                href="{{ route($routeName, ['page' => $item['page']]) }}"
                class="block rounded-md px-3 py-2 text-sm font-medium transition-colors
                    {{ $active
                        ? 'bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18]'
                        : 'text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-gray-100 dark:hover:bg-gray-800' }}"
            >
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
    <div class="p-3 border-t border-gray-200 dark:border-gray-700">
        <form method="post" action="{{ route('s_logout') }}">
            @csrf
            <button type="submit" class="w-full text-left rounded-md px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors">
                Log out
            </button>
        </form>
    </div>
</aside>
