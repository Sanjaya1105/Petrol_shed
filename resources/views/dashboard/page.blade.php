@extends('layouts.dashboard')

@section('title', $sectionTitle.' — '.$heading.' — '.config('app.name'))

@section('content')
    <div class="{{ $navPrefix === 'admin' && in_array($page, ['sales', 'home'], true) ? 'w-full max-w-none' : 'max-w-3xl w-full' }}">
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
        @elseif ($navPrefix === 'dev' && $page === 'pumps')
            <form method="post" action="{{ route('dev.pumps.store') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                @csrf
                <div>
                    <label for="pump_name" class="block text-sm font-medium mb-1">Pump name</label>
                    <input
                        type="text"
                        name="pump_name"
                        id="pump_name"
                        value="{{ old('pump_name') }}"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    >
                    @error('pump_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pump_category_id" class="block text-sm font-medium mb-1">Category</label>
                    <select
                        name="category_id"
                        id="pump_category_id"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    >
                        <option value="" selected disabled>Select category</option>
                        @if ($categories !== null)
                            @foreach ($categories as $categoryOption)
                                <option value="{{ $categoryOption->id }}" {{ (string) old('category_id') === (string) $categoryOption->id ? 'selected' : '' }}>
                                    {{ $categoryOption->category }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pump_tank_id" class="block text-sm font-medium mb-1">Tank</label>
                    <select
                        name="tank_id"
                        id="pump_tank_id"
                        required
                        disabled
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <option value="" selected disabled>Select tank</option>
                        @if ($tanks !== null)
                            @foreach ($tanks as $tankOption)
                                <option
                                    value="{{ $tankOption->id }}"
                                    data-category-id="{{ $tankOption->category_id }}"
                                    {{ (string) old('tank_id') === (string) $tankOption->id ? 'selected' : '' }}
                                >
                                    {{ $tankOption->tank_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('tank_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                    Submit
                </button>
            </form>

            <div class="mt-6 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <h3 class="text-sm font-semibold mb-3">Available pumps</h3>
                @if ($pumps !== null && $pumps->isNotEmpty())
                    <div class="mb-3">
                        <label for="pump_filter_category_id" class="block text-sm font-medium mb-1">Filter by category</label>
                        <select
                            id="pump_filter_category_id"
                            class="w-full sm:w-72 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                            <option value="">All categories</option>
                            @if ($categories !== null)
                                @foreach ($categories as $categoryOption)
                                    <option value="{{ $categoryOption->id }}">{{ $categoryOption->category }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="space-y-2">
                        @foreach ($pumps as $pump)
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border border-gray-200 dark:border-gray-700 rounded-md p-3 js-pump-row" data-category-id="{{ $pump->category_id }}">
                                <p class="text-sm font-medium">{{ $pump->pump_name }}</p>
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        class="rounded-md bg-blue-600 text-white px-3 py-2 text-sm font-medium hover:opacity-90 transition-opacity js-open-pump-modal"
                                        data-pump-id="{{ $pump->id }}"
                                        data-pump-name="{{ $pump->pump_name }}"
                                        data-category-id="{{ $pump->category_id }}"
                                        data-tank-id="{{ $pump->tank_id }}"
                                    >
                                        Update
                                    </button>
                                    <form method="post" action="{{ route('dev.pumps.delete', $pump) }}" onsubmit="return confirm('Are you sure you want to delete this pump?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-red-700 hover:bg-red-800 text-white px-3 py-2 text-sm font-medium transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No pumps added yet.</p>
                @endif
            </div>

            <div id="pump-modal" class="fixed inset-0 z-50 hidden">
                <div class="absolute inset-0 bg-black/50" id="pump-modal-overlay"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-md bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold">Update pump</h3>
                            <button type="button" id="pump-modal-close" class="rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1 text-sm">X</button>
                        </div>
                        <form method="post" id="pump-update-form" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="modal_pump_name" class="block text-sm font-medium mb-1">Pump name</label>
                                <input type="text" name="pump_name" id="modal_pump_name" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label for="modal_pump_category_id" class="block text-sm font-medium mb-1">Category</label>
                                <select name="category_id" id="modal_pump_category_id" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                                    <option value="" disabled>Select category</option>
                                    @if ($categories !== null)
                                        @foreach ($categories as $categoryOption)
                                            <option value="{{ $categoryOption->id }}">{{ $categoryOption->category }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="modal_pump_tank_id" class="block text-sm font-medium mb-1">Tank</label>
                                <select name="tank_id" id="modal_pump_tank_id" required disabled class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none disabled:opacity-60 disabled:cursor-not-allowed">
                                    <option value="" disabled selected>Select tank</option>
                                    @if ($tanks !== null)
                                        @foreach ($tanks as $tankOption)
                                            <option value="{{ $tankOption->id }}" data-category-id="{{ $tankOption->category_id }}">
                                                {{ $tankOption->tank_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <button type="submit" class="rounded-md bg-blue-600 text-white px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                                Save update
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                (function () {
                    const categorySelect = document.getElementById('pump_category_id');
                    const tankSelect = document.getElementById('pump_tank_id');
                    if (!categorySelect || !tankSelect) return;

                    const filterTanks = () => {
                        const selectedCategoryId = categorySelect.value;
                        const tankOptions = Array.from(tankSelect.querySelectorAll('option[data-category-id]'));

                        if (!selectedCategoryId) {
                            tankSelect.value = '';
                            tankSelect.disabled = true;
                            tankOptions.forEach((option) => {
                                option.hidden = true;
                            });
                            return;
                        }

                        tankSelect.disabled = false;
                        let hasSelectedVisible = false;
                        tankOptions.forEach((option) => {
                            const isMatch = option.dataset.categoryId === selectedCategoryId;
                            option.hidden = !isMatch;
                            if (option.selected && isMatch) {
                                hasSelectedVisible = true;
                            }
                        });

                        if (!hasSelectedVisible) {
                            tankSelect.value = '';
                        }
                    };

                    categorySelect.addEventListener('change', filterTanks);
                    filterTanks();
                })();
            </script>
            <script>
                (function () {
                    const filterSelect = document.getElementById('pump_filter_category_id');
                    const rows = Array.from(document.querySelectorAll('.js-pump-row'));
                    if (!filterSelect || rows.length === 0) return;

                    const applyFilter = () => {
                        const selectedCategoryId = filterSelect.value;
                        rows.forEach((row) => {
                            const rowCategoryId = row.dataset.categoryId;
                            const show = !selectedCategoryId || rowCategoryId === selectedCategoryId;
                            row.classList.toggle('hidden', !show);
                        });
                    };

                    filterSelect.addEventListener('change', applyFilter);
                    applyFilter();
                })();
            </script>
            <script>
                (function () {
                    const modal = document.getElementById('pump-modal');
                    const openButtons = document.querySelectorAll('.js-open-pump-modal');
                    const closeBtn = document.getElementById('pump-modal-close');
                    const overlay = document.getElementById('pump-modal-overlay');
                    const form = document.getElementById('pump-update-form');
                    const nameInput = document.getElementById('modal_pump_name');
                    const categorySelect = document.getElementById('modal_pump_category_id');
                    const tankSelect = document.getElementById('modal_pump_tank_id');

                    if (!modal || !form || !nameInput || !categorySelect || !tankSelect) return;

                    const filterModalTanks = () => {
                        const selectedCategoryId = categorySelect.value;
                        const tankOptions = Array.from(tankSelect.querySelectorAll('option[data-category-id]'));

                        if (!selectedCategoryId) {
                            tankSelect.value = '';
                            tankSelect.disabled = true;
                            tankOptions.forEach((option) => {
                                option.hidden = true;
                            });
                            return;
                        }

                        tankSelect.disabled = false;
                        let hasSelectedVisible = false;
                        tankOptions.forEach((option) => {
                            const isMatch = option.dataset.categoryId === selectedCategoryId;
                            option.hidden = !isMatch;
                            if (option.selected && isMatch) {
                                hasSelectedVisible = true;
                            }
                        });

                        if (!hasSelectedVisible) {
                            tankSelect.value = '';
                        }
                    };

                    const closeModal = () => {
                        modal.classList.add('hidden');
                    };

                    openButtons.forEach((button) => {
                        button.addEventListener('click', () => {
                            const pumpId = button.dataset.pumpId;
                            const pumpName = button.dataset.pumpName || '';
                            const categoryId = button.dataset.categoryId || '';
                            const tankId = button.dataset.tankId || '';

                            form.action = `/dev/pumps/${pumpId}`;
                            nameInput.value = pumpName;
                            categorySelect.value = categoryId;
                            filterModalTanks();
                            tankSelect.value = tankId;

                            modal.classList.remove('hidden');
                        });
                    });

                    categorySelect.addEventListener('change', filterModalTanks);
                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (overlay) overlay.addEventListener('click', closeModal);
                })();
            </script>
        @elseif (in_array($navPrefix, ['admin', 'data-entry'], true) && $page === 'pumps')
            <div class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <h3 class="text-sm font-semibold mb-1">Available pumps</h3>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-3">
                    Each row starts with today’s data. Change the date on a pump to load that day’s staff and readings for that pump only. Starting meter is the previous calendar day’s reading for the same pump.
                </p>
                @if ($pumps !== null && $pumps->isNotEmpty())
                    @php
                        $staffLookup = $staffMembers?->keyBy('id') ?? collect();
                    @endphp
                    <form method="post" action="{{ route($navPrefix.'.pumps.sales.bulk') }}">
                        @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold">Pump</th>
                                    <th class="text-left px-3 py-2 font-semibold">Assign staff</th>
                                    <th class="text-left px-3 py-2 font-semibold">Starting meter reading</th>
                                    <th class="text-left px-3 py-2 font-semibold">Meter reading</th>
                                    <th class="text-left px-3 py-2 font-semibold">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pumps as $pump)
                                    @php
                                        $salesRows = $sales ?? collect();
                                        $startingRows = $startingMeters ?? collect();
                                        $selectedDateSale = $salesRows->get($pump->id) ?? $salesRows->get((string) $pump->id);
                                        $priorDaySale = $startingRows->get($pump->id) ?? $startingRows->get((string) $pump->id);
                                        $startMeter = $priorDaySale?->meter_amount;
                                        $missingPreviousReading = $priorDaySale === null || $startMeter === null || $startMeter === '';
                                    @endphp
                                    <tr
                                        class="js-pump-sale-row border-t border-gray-200 dark:border-gray-700 {{ $missingPreviousReading ? 'bg-red-50 dark:bg-red-950/20' : '' }}"
                                        data-pump-id="{{ $pump->id }}"
                                        @if ($missingPreviousReading) style="box-shadow: inset 0 0 0 2px rgb(220 38 38);" @endif
                                    >
                                        <td class="px-3 py-2 align-top">{{ $pump->pump_name }}</td>
                                        <td class="px-3 py-2 align-top">
                                                <select
                                                    name="pumps[{{ $pump->id }}][staff_id]"
                                                    class="js-pump-row-staff w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                >
                                                    <option value="" {{ $selectedDateSale ? '' : 'selected' }}>Select staff</option>
                                                    @if ($staffMembers !== null)
                                                        @foreach ($staffMembers as $staffOption)
                                                            <option value="{{ $staffOption->id }}" {{ (string) ($selectedDateSale->staff_id ?? '') === (string) $staffOption->id ? 'selected' : '' }}>
                                                                {{ $staffOption->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                                <input
                                                    type="number"
                                                    value="{{ $startMeter !== null ? number_format((float) $startMeter, 5, '.', '') : '' }}"
                                                    readonly
                                                    class="js-pump-row-start-meter w-full rounded-md bg-white dark:bg-white text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed {{ $missingPreviousReading ? 'border-2 border-red-600 ring-1 ring-red-500' : 'border border-gray-300 dark:border-gray-600' }}"
                                                >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                                <input
                                                    type="number"
                                                    name="pumps[{{ $pump->id }}][meter_amount]"
                                                    value="{{ $selectedDateSale ? number_format((float) $selectedDateSale->meter_amount, 5, '.', '') : '' }}"
                                                    min="0"
                                                    step="0.00001"
                                                    class="js-pump-row-meter w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                                <input
                                                    type="date"
                                                    name="pumps[{{ $pump->id }}][date]"
                                                    value="{{ now()->toDateString() }}"
                                                    max="{{ now()->toDateString() }}"
                                                    data-prefill-url="{{ route($navPrefix.'.pumps.sale.prefill', $pump) }}"
                                                    onclick="this.showPicker && this.showPicker()"
                                                    onfocus="this.showPicker && this.showPicker()"
                                                    class="js-pump-sale-date w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                >
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#1c1c1c]">
                                    <td colspan="5" class="px-3 py-3 text-right">
                                        <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                                            Update All Pumps
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </form>
                    <script>
                        (function () {
                            document.querySelectorAll('.js-pump-sale-date').forEach(function (input) {
                                input.addEventListener('change', async function () {
                                    const dateVal = this.value;
                                    const baseUrl = this.dataset.prefillUrl;
                                    if (!dateVal || !baseUrl) {
                                        return;
                                    }
                                    const url = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'date=' + encodeURIComponent(dateVal);
                                    try {
                                        const r = await fetch(url, {
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest',
                                            },
                                        });
                                        if (!r.ok) {
                                            return;
                                        }
                                        const d = await r.json();
                                        const tr = input.closest('tr');
                                        if (!tr) {
                                            return;
                                        }
                                        const staff = tr.querySelector('.js-pump-row-staff');
                                        const startIn = tr.querySelector('.js-pump-row-start-meter');
                                        const meterIn = tr.querySelector('.js-pump-row-meter');
                                        if (staff) {
                                            if (d.staff_id != null && d.staff_id !== '') {
                                                staff.value = String(d.staff_id);
                                            } else {
                                                staff.value = '';
                                            }
                                        }
                                        if (meterIn) {
                                            meterIn.value = d.meter_amount || '';
                                        }
                                        if (startIn) {
                                            startIn.value = d.starting_meter || '';
                                            startIn.classList.remove('border-2', 'border-red-600', 'ring-1', 'ring-red-500', 'border', 'border-gray-300', 'dark:border-gray-600');
                                            if (d.missing_previous) {
                                                startIn.classList.add('border-2', 'border-red-600', 'ring-1', 'ring-red-500');
                                            } else {
                                                startIn.classList.add('border', 'border-gray-300', 'dark:border-gray-600');
                                            }
                                        }
                                        tr.classList.remove('bg-red-50', 'dark:bg-red-950/20');
                                        tr.style.boxShadow = '';
                                        if (d.missing_previous) {
                                            tr.classList.add('bg-red-50', 'dark:bg-red-950/20');
                                            tr.style.boxShadow = 'inset 0 0 0 2px rgb(220 38 38)';
                                        }
                                    } catch (e) {
                                        console.error(e);
                                    }
                                });
                            });
                        })();
                    </script>
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No pumps added yet.</p>
                @endif
            </div>
        @elseif (in_array($navPrefix, ['admin', 'dev', 'data-entry'], true) && $page === 'sales')
            @php
                $salesReportCarbon = \Illuminate\Support\Carbon::parse($salesReportDate ?? now()->subDay());
                $salesPriorMeterCarbon = $salesReportCarbon->copy()->subDay();
                $salesPickerMax = $salesDatePickerMax ?? now()->subDay()->toDateString();
                $salesListUrl = route($navPrefix.'.show', ['page' => 'sales']);
                $salesViewMode = $salesView ?? 'staff';
                $salesViewStaffUrl = request()->fullUrlWithQuery(['sales_view' => 'staff']);
                $salesViewPumpsUrl = request()->fullUrlWithQuery(['sales_view' => 'pumps']);
                $salesResetQuery = request()->query();
                unset($salesResetQuery['sales_date']);
                $salesResetDateUrl = request()->url().(count($salesResetQuery) > 0 ? '?'.http_build_query($salesResetQuery) : '');
                $salesPdfDateQuery = array_filter(
                    ['sales_date' => request()->query('sales_date')],
                    fn ($v) => $v !== null && $v !== ''
                );
                $salesPdfQuerySuffix = count($salesPdfDateQuery) > 0 ? '?'.http_build_query($salesPdfDateQuery) : '';
                $staffPdfUrl = route($navPrefix.'.sales.staff.pdf').$salesPdfQuerySuffix;
                $pumpsPdfUrl = route($navPrefix.'.sales.pumps.pdf').$salesPdfQuerySuffix;
            @endphp
            <div class="bg-gradient-to-b from-white to-gray-50 dark:from-[#161615] dark:to-[#121212] border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-6 shadow-sm">
                <h3 class="text-base font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-1">
                    Sales records for {{ $salesReportCarbon->format('l, F j, Y') }}
                </h3>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-3">
                    Report date: <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $salesReportCarbon->toDateString() }}</span>.
                    Starting meter (L) for each pump uses the recorded reading on
                    <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $salesPriorMeterCarbon->toDateString() }}</span>
                    (calendar day before this report).
                </p>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <button
                        type="button"
                        id="sales_date_open_btn"
                        class="inline-flex items-center justify-center rounded-lg border border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 px-3 py-2 text-sm font-medium text-blue-700 dark:text-blue-300 shadow-sm hover:bg-blue-100 dark:hover:bg-blue-900/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-[#161615]"
                        aria-controls="sales_date_input"
                    >
                        Choose report date
                    </button>
                    @if (request()->filled('sales_date'))
                        <a
                            href="{{ $salesResetDateUrl }}"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                        >Reset to yesterday</a>
                    @endif
                </div>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-4">Pick a date from the calendar only (today and future dates are not available).</p>

                {{-- Hidden field: opened via showPicker from the button; keyboard/paste disabled so the date cannot be typed --}}
                <div class="fixed left-0 top-0 -z-10 h-px w-px overflow-hidden opacity-0" aria-hidden="true">
                    <input
                        type="date"
                        id="sales_date_input"
                        name="sales_date"
                        value="{{ $salesReportCarbon->toDateString() }}"
                        max="{{ $salesPickerMax }}"
                        inputmode="none"
                        autocomplete="off"
                        tabindex="-1"
                        class="absolute h-px w-px opacity-0"
                    />
                </div>

                <fieldset class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg p-3 bg-white/80 dark:bg-[#0f0f0f]/80 shadow-sm">
                    <legend class="px-1 text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">Report layout</legend>
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6" role="presentation">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                            <input
                                type="radio"
                                name="sales_layout"
                                value="staff"
                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-[#0a0a0a]"
                                {{ $salesViewMode === 'staff' ? 'checked' : '' }}
                                data-sales-nav-url="{{ $salesViewStaffUrl }}"
                            />
                            Staff sale
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                            <input
                                type="radio"
                                name="sales_layout"
                                value="pumps"
                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-[#0a0a0a]"
                                {{ $salesViewMode === 'pumps' ? 'checked' : '' }}
                                data-sales-nav-url="{{ $salesViewPumpsUrl }}"
                            />
                            Pumps sale
                        </label>
                        <a
                            href="{{ $salesViewMode === 'staff' ? $staffPdfUrl : $pumpsPdfUrl }}"
                            class="inline-flex items-center justify-center rounded-lg bg-emerald-600 dark:bg-emerald-500 text-white px-3 py-2 text-sm font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-[#161615] ml-auto sm:ml-0 shadow-sm"
                        >Generate PDF</a>
                    </div>
                </fieldset>

                @if ($sales !== null && $pumps !== null && $pumps->isNotEmpty())
                    @if ($salesViewMode === 'pumps')
                        @php
                            $salesColl = $sales ?? collect();
                            $salesByPumpId = $salesColl->keyBy(fn ($s) => (string) $s->pump_id);
                            $priceLookup = $prices ?? collect();
                            $priorByPump = $priorDaySalesByPump ?? collect();
                            $pumpSaleRows = collect();
                            foreach ($pumps->sortBy('pump_name', SORT_NATURAL) as $pump) {
                                $sale = $salesByPumpId->get((string) $pump->id);
                                $categoryPrice = $priceLookup->get($pump->category_id);
                                if ($sale === null) {
                                    $pumpSaleRows->push([
                                        'pump_name' => $pump->pump_name,
                                        'staff_name' => '—',
                                        'starting_meter' => null,
                                        'ending_meter' => null,
                                        'difference' => null,
                                        'category_price' => $categoryPrice,
                                        'line_total' => null,
                                    ]);
                                    continue;
                                }
                                $priorSale = $priorByPump->get($sale->pump_id) ?? $priorByPump->get((string) $sale->pump_id);
                                $starting = $priorSale?->meter_amount;
                                $ending = $sale->meter_amount;
                                $diff = null;
                                if ($starting !== null && $starting !== '' && $ending !== null && $ending !== '') {
                                    $diff = (float) $ending - (float) $starting;
                                }
                                $lineTotal = ($diff !== null && $categoryPrice !== null)
                                    ? $diff * (float) $categoryPrice
                                    : null;
                                $staffMember = ($staffMembers ?? collect())->firstWhere('id', $sale->staff_id);
                                if ($staffMember !== null) {
                                    $staffName = ($staffMember->is_active ?? true)
                                        ? $staffMember->name
                                        : $staffMember->name.' (Removed)';
                                } else {
                                    $staffName = $sale->staff_id
                                        ? 'Removed staff (ID: '.$sale->staff_id.')'
                                        : '—';
                                }
                                $pumpSaleRows->push([
                                    'pump_name' => $pump->pump_name,
                                    'staff_name' => $staffName,
                                    'starting_meter' => $starting,
                                    'ending_meter' => $ending,
                                    'difference' => $diff,
                                    'category_price' => $categoryPrice,
                                    'line_total' => $lineTotal,
                                ]);
                            }
                            $pumpsGrandTotal = $pumpSaleRows
                                ->filter(fn ($e) => $e['line_total'] !== null)
                                ->sum(fn ($e) => (float) $e['line_total']);
                            $pumpsPricedCount = $pumpSaleRows->filter(fn ($e) => $e['line_total'] !== null)->count();
                        @endphp
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <table class="min-w-full overflow-hidden text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="text-left px-3 py-2 font-semibold">Pump</th>
                                        <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                        <th class="text-left px-3 py-2 font-semibold">Starting meter (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Ending meter (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Difference (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Per liter price</th>
                                        <th class="text-left px-3 py-2 font-semibold">Line total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pumpSaleRows as $entry)
                                        <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                                            <td class="px-3 py-2 align-top font-medium">{{ $entry['pump_name'] }}</td>
                                            <td class="px-3 py-2 align-top">{{ $entry['staff_name'] }}</td>
                                            <td class="px-3 py-2 align-top">
                                                {{ $entry['starting_meter'] !== null && $entry['starting_meter'] !== '' ? number_format((float) $entry['starting_meter'], 2) : '-' }}
                                            </td>
                                            <td class="px-3 py-2 align-top">
                                                {{ $entry['ending_meter'] !== null && $entry['ending_meter'] !== '' ? number_format((float) $entry['ending_meter'], 2) : '-' }}
                                            </td>
                                            <td class="px-3 py-2 align-top">
                                                {{ $entry['difference'] !== null ? number_format((float) $entry['difference'], 2) : '-' }}
                                            </td>
                                            <td class="px-3 py-2 align-top">
                                                {{ $entry['category_price'] !== null ? number_format((float) $entry['category_price'], 2) : '-' }}
                                            </td>
                                            <td class="px-3 py-2 align-top">
                                                {{ $entry['line_total'] !== null ? number_format((float) $entry['line_total'], 2) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if ($pumpsPricedCount > 0)
                                    <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                                        <tr>
                                            <td class="px-3 py-2 font-semibold text-right" colspan="6">Grand total</td>
                                            <td class="px-3 py-2 font-semibold">{{ number_format((float) $pumpsGrandTotal, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    @else
                        @php
                            $salesColl = $sales ?? collect();
                            $salesByPumpId = $salesColl->keyBy(fn ($s) => (string) $s->pump_id);
                            $priceLookup = $prices ?? collect();
                            $priorByPump = $priorDaySalesByPump ?? collect();
                            $staffPumpRows = collect();
                            foreach ($pumps->sortBy('pump_name', SORT_NATURAL) as $pump) {
                                $sale = $salesByPumpId->get((string) $pump->id);
                                $categoryPrice = $priceLookup->get($pump->category_id);
                                $staffName = '—';
                                $staffIdForRow = '';
                                if ($sale === null) {
                                    $staffPumpRows->push([
                                        'staff_name' => $staffName,
                                        'staff_id_for_row' => $staffIdForRow,
                                        'pump_name' => $pump->pump_name,
                                        'starting_meter' => null,
                                        'ending_meter' => null,
                                        'difference' => null,
                                        'category_price' => $categoryPrice,
                                        'line_total' => null,
                                    ]);
                                    continue;
                                }
                                $staffIdForRow = (string) ($sale->staff_id ?? '');
                                $staffMember = ($staffMembers ?? collect())->firstWhere('id', $sale->staff_id);
                                if ($staffMember !== null) {
                                    $staffName = ($staffMember->is_active ?? true)
                                        ? $staffMember->name
                                        : $staffMember->name.' (Removed)';
                                } else {
                                    $staffName = $sale->staff_id
                                        ? 'Removed staff (ID: '.$sale->staff_id.')'
                                        : '—';
                                }
                                $priorSale = $priorByPump->get($sale->pump_id) ?? $priorByPump->get((string) $sale->pump_id);
                                $starting = $priorSale?->meter_amount;
                                $ending = $sale->meter_amount;
                                $diff = null;
                                if ($starting !== null && $starting !== '' && $ending !== null && $ending !== '') {
                                    $diff = (float) $ending - (float) $starting;
                                }
                                $lineTotal = ($diff !== null && $categoryPrice !== null)
                                    ? $diff * (float) $categoryPrice
                                    : null;
                                $staffPumpRows->push([
                                    'staff_name' => $staffName,
                                    'staff_id_for_row' => $staffIdForRow,
                                    'pump_name' => $pump->pump_name,
                                    'starting_meter' => $starting,
                                    'ending_meter' => $ending,
                                    'difference' => $diff,
                                    'category_price' => $categoryPrice,
                                    'line_total' => $lineTotal,
                                ]);
                            }
                            $staffReportGrandTotal = $staffPumpRows
                                ->filter(fn ($e) => $e['line_total'] !== null)
                                ->sum(fn ($e) => (float) $e['line_total']);
                            $staffPricedCount = $staffPumpRows->filter(fn ($e) => $e['line_total'] !== null)->count();
                            $byStaffKey = $staffPumpRows->groupBy('staff_id_for_row');
                            $staffGroupKeys = $byStaffKey->keys()->sort(function ($a, $b) use ($byStaffKey) {
                                $aEmpty = $a === '';
                                $bEmpty = $b === '';
                                if ($aEmpty !== $bEmpty) {
                                    return $aEmpty ? 1 : -1;
                                }
                                $nameA = (string) ($byStaffKey->get($a)->first()['staff_name'] ?? '');
                                $nameB = (string) ($byStaffKey->get($b)->first()['staff_name'] ?? '');

                                return strnatcasecmp($nameA, $nameB);
                            })->values();
                            $billAmountByStaffId = $billAmountByStaffId ?? collect();
                            $gasAmountByStaffId = $gasAmountByStaffId ?? collect();
                            $oilAmountByStaffId = $oilAmountByStaffId ?? collect();
                            $salaryAmountForSalesDate = $salaryAmountForSalesDate ?? null;
                            $staffSaleGroups = $staffGroupKeys->map(function ($key) use ($byStaffKey, $cashByStaffId, $cashCategoryTotalsByStaff, $billAmountByStaffId, $gasAmountByStaffId, $oilAmountByStaffId, $salaryAmountForSalesDate) {
                                $rows = $byStaffKey->get($key)->sortBy('pump_name', SORT_NATURAL)->values();
                                $groupTotal = $rows
                                    ->filter(fn ($r) => $r['line_total'] !== null)
                                    ->sum(fn ($r) => (float) $r['line_total']);
                                $groupHasTotals = $rows->contains(fn ($r) => $r['line_total'] !== null);
                                $cashTotal = null;
                                $visaMasterTotal = null;
                                $amexTotal = null;
                                $billAmountTotal = null;
                                $gasAmountTotal = null;
                                $oilAmountTotal = null;
                                $shortTotal = null;
                                if ($key !== '') {
                                    $cashTotal = $cashByStaffId->get((int) $key);
                                    if ($cashTotal === null) {
                                        $cashTotal = $cashByStaffId->get((string) $key);
                                    }
                                    $categoryTotals = $cashCategoryTotalsByStaff->get((int) $key) ?? $cashCategoryTotalsByStaff->get((string) $key);
                                    if ($categoryTotals !== null) {
                                        $visaMasterTotal = $categoryTotals->get('visa-master');
                                        $amexTotal = $categoryTotals->get('amex');
                                    }
                                    $billAmountTotal = $billAmountByStaffId->get((int) $key);
                                    if ($billAmountTotal === null) {
                                        $billAmountTotal = $billAmountByStaffId->get((string) $key);
                                    }
                                    $gasAmountTotal = $gasAmountByStaffId->get((int) $key);
                                    if ($gasAmountTotal === null) {
                                        $gasAmountTotal = $gasAmountByStaffId->get((string) $key);
                                    }
                                    $oilAmountTotal = $oilAmountByStaffId->get((int) $key);
                                    if ($oilAmountTotal === null) {
                                        $oilAmountTotal = $oilAmountByStaffId->get((string) $key);
                                    }

                                    $sumOfCollections =
                                        (float) ($cashTotal ?? 0)
                                        + (float) ($visaMasterTotal ?? 0)
                                        + (float) ($amexTotal ?? 0)
                                        + (float) ($billAmountTotal ?? 0)
                                        - (float) ($gasAmountTotal ?? 0)
                                        - (float) ($oilAmountTotal ?? 0)
                                        + (float) ($salaryAmountForSalesDate ?? 0);
                                    $shortTotal = (float) $groupTotal - $sumOfCollections;
                                }

                                return [
                                    'staff_name' => $rows->first()['staff_name'],
                                    'staff_id_for_row' => $key,
                                    'rows' => $rows,
                                    'cash_total' => number_format((float) ($cashTotal ?? 0), 2),
                                    'visa_master_total' => number_format((float) ($visaMasterTotal ?? 0), 2),
                                    'amex_total' => number_format((float) ($amexTotal ?? 0), 2),
                                    'bill_amount' => number_format((float) ($billAmountTotal ?? 0), 2),
                                    'gas_amount' => number_format((float) ($gasAmountTotal ?? 0), 2),
                                    'oil_amount' => number_format((float) ($oilAmountTotal ?? 0), 2),
                                    'salary_amount' => $salaryAmountForSalesDate !== null ? number_format((float) $salaryAmountForSalesDate, 2) : '0.00',
                                    'short_total' => $shortTotal !== null ? number_format((float) $shortTotal, 2) : number_format((float) $groupTotal, 2),
                                    'group_total' => $groupHasTotals ? number_format((float) $groupTotal, 2) : '-',
                                ];
                            });
                            $staffGroupsPerPage = 3;
                            $staffGroupsTotal = $staffSaleGroups->count();
                            $staffGroupsLastPage = max((int) ceil($staffGroupsTotal / $staffGroupsPerPage), 1);
                            $staffGroupsPage = (int) request()->query('sales_staff_page', 1);
                            if ($staffGroupsPage < 1) {
                                $staffGroupsPage = 1;
                            }
                            if ($staffGroupsPage > $staffGroupsLastPage) {
                                $staffGroupsPage = $staffGroupsLastPage;
                            }
                            $staffSaleGroupsPage = $staffSaleGroups
                                ->forPage($staffGroupsPage, $staffGroupsPerPage)
                                ->values();
                            $staffGroupsPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                                $staffSaleGroupsPage,
                                $staffGroupsTotal,
                                $staffGroupsPerPage,
                                $staffGroupsPage,
                                [
                                    'path' => request()->url(),
                                    'pageName' => 'sales_staff_page',
                                    'query' => request()->query(),
                                ]
                            );
                        @endphp
                        <div class="mb-3">
                            <label for="sales_staff_filter" class="block text-sm font-medium mb-1">Filter by staff</label>
                            <select
                                id="sales_staff_filter"
                                class="w-full sm:w-72 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                            >
                                <option value="">All staff</option>
                                @foreach (($staffMembers ?? collect())->sortBy('name')->values() as $staffOption)
                                    <option value="{{ $staffOption->id }}">{{ ($staffOption->is_active ?? true) ? $staffOption->name : $staffOption->name.' (Removed)' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">Pumps are grouped under each staff (one cell spans multiple rows when that staff has more than one pump). Rows with no sale show empty readings; filter by staff hides groups unless <strong>All staff</strong> is selected.</p>
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <table class="min-w-full overflow-hidden text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                        <th class="text-left px-3 py-2 font-semibold">Pump</th>
                                        <th class="text-left px-3 py-2 font-semibold">Starting meter (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Ending meter (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Difference (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Per liter price</th>
                                        <th class="text-left px-3 py-2 font-semibold">Line total</th>
                                        <th class="text-left px-3 py-2 font-semibold">Total</th>
                                        <th class="text-left px-3 py-2 font-semibold">Cash amount</th>
                                        <th class="text-left px-3 py-2 font-semibold">Visa/Master</th>
                                        <th class="text-left px-3 py-2 font-semibold">Amex</th>
                                        <th class="text-left px-3 py-2 font-semibold">Bill amount</th>
                                        <th class="text-left px-3 py-2 font-semibold">Gas Amount</th>
                                        <th class="text-left px-3 py-2 font-semibold">Oil Amount</th>
                                        <th class="text-left px-3 py-2 font-semibold">Salary</th>
                                        <th class="text-left px-3 py-2 font-semibold">Short</th>
                                    </tr>
                                </thead>
                                @foreach ($staffSaleGroupsPage as $group)
                                    <tbody
                                        class="border-t border-gray-200 dark:border-gray-700 js-sales-staff-group"
                                        data-staff-id="{{ $group['staff_id_for_row'] }}"
                                    >
                                        @foreach ($group['rows'] as $idx => $entry)
                                            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50/70 dark:hover:bg-gray-800/40 transition-colors">
                                                @if ($idx === 0)
                                                    <td class="px-3 py-2 align-top font-medium align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['staff_name'] }}</td>
                                                @endif
                                                <td class="px-3 py-2 align-top">{{ $entry['pump_name'] }}</td>
                                                <td class="px-3 py-2 align-top">
                                                    {{ $entry['starting_meter'] !== null && $entry['starting_meter'] !== '' ? number_format((float) $entry['starting_meter'], 2) : '-' }}
                                                </td>
                                                <td class="px-3 py-2 align-top">
                                                    {{ $entry['ending_meter'] !== null && $entry['ending_meter'] !== '' ? number_format((float) $entry['ending_meter'], 2) : '-' }}
                                                </td>
                                                <td class="px-3 py-2 align-top">
                                                    {{ $entry['difference'] !== null ? number_format((float) $entry['difference'], 2) : '-' }}
                                                </td>
                                                <td class="px-3 py-2 align-top">
                                                    {{ $entry['category_price'] !== null ? number_format((float) $entry['category_price'], 2) : '-' }}
                                                </td>
                                                <td class="px-3 py-2 align-top">
                                                    {{ $entry['line_total'] !== null ? number_format((float) $entry['line_total'], 2) : '-' }}
                                                </td>
                                                @if ($idx === 0)
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['group_total'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['cash_total'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['visa_master_total'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['amex_total'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['bill_amount'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['gas_amount'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['oil_amount'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['salary_amount'] }}</td>
                                                    <td class="px-3 py-2 align-top font-semibold align-middle" rowspan="{{ $group['rows']->count() }}">{{ $group['short_total'] }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endforeach
                                @if ($staffPricedCount > 0)
                                    <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                                        <tr>
                                            <td class="px-3 py-2 font-semibold text-right" colspan="7">Grand total</td>
                                            <td class="px-3 py-2 font-semibold">{{ number_format((float) $staffReportGrandTotal, 2) }}</td>
                                            <td class="px-3 py-2" colspan="8"></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                        @if ($staffGroupsPaginator->hasPages())
                            <div class="mt-3">
                                {{ $staffGroupsPaginator->onEachSide(1)->links() }}
                            </div>
                        @endif
                        <script>
                            (function () {
                                const filterSelect = document.getElementById('sales_staff_filter');
                                const groups = Array.from(document.querySelectorAll('.js-sales-staff-group'));
                                if (!filterSelect || groups.length === 0) return;

                                const applyFilter = () => {
                                    const selectedStaffId = filterSelect.value;
                                    groups.forEach((tbody) => {
                                        const rowStaffId = tbody.dataset.staffId;
                                        const isUnassigned = rowStaffId === '';
                                        if (!selectedStaffId) {
                                            tbody.classList.remove('hidden');
                                            return;
                                        }
                                        const show = !isUnassigned && String(rowStaffId) === String(selectedStaffId);
                                        tbody.classList.toggle('hidden', !show);
                                    });
                                };

                                filterSelect.addEventListener('change', applyFilter);
                                applyFilter();
                            })();
                        </script>
                    @endif
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        @if ($pumps === null || $pumps->isEmpty())
                            No pumps configured. Add pumps to see the sales report.
                        @else
                            No sales report available.
                        @endif
                    </p>
                @endif

                <script>
                    (function () {
                        document.querySelectorAll('input[name="sales_layout"][data-sales-nav-url]').forEach((radio) => {
                            radio.addEventListener('change', () => {
                                if (radio.checked) {
                                    window.location.href = radio.getAttribute('data-sales-nav-url');
                                }
                            });
                        });
                    })();
                </script>
                <script>
                    (function () {
                        const openBtn = document.getElementById('sales_date_open_btn');
                        const input = document.getElementById('sales_date_input');
                        const baseUrl = @json($salesListUrl);
                        const maxStr = @json($salesPickerMax);
                        const currentReport = @json($salesReportCarbon->toDateString());
                        if (!openBtn || !input) return;

                        const navigate = (v) => {
                            if (!v) return;
                            if (maxStr && v > maxStr) {
                                alert('Choose a date on or before ' + maxStr + ' (not today or the future).');
                                input.value = currentReport;
                                return;
                            }
                            const url = new URL(baseUrl, window.location.origin);
                            url.searchParams.set('sales_date', v);
                            const cur = new URL(window.location.href);
                            const sv = cur.searchParams.get('sales_view');
                            if (sv) {
                                url.searchParams.set('sales_view', sv);
                            }
                            window.location.href = url.pathname + url.search;
                        };

                        input.addEventListener('keydown', (e) => {
                            if (e.key === 'Tab') return;
                            e.preventDefault();
                        });
                        input.addEventListener('keypress', (e) => e.preventDefault());
                        input.addEventListener('paste', (e) => e.preventDefault());

                        input.addEventListener('change', () => {
                            const v = (input.value || '').trim();
                            if (v === currentReport) return;
                            navigate(v);
                        });

                        openBtn.addEventListener('click', () => {
                            input.value = currentReport;
                            const tryPicker = async () => {
                                try {
                                    if (typeof input.showPicker === 'function') {
                                        await input.showPicker();
                                    } else {
                                        input.focus({ preventScroll: true });
                                        input.click();
                                    }
                                } catch (err) {
                                    input.focus({ preventScroll: true });
                                    input.click();
                                }
                            };
                            void tryPicker();
                        });
                    })();
                </script>
            </div>
        @elseif ($navPrefix === 'admin' && $page === 'tanks')
            <div class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <h3 class="text-sm font-semibold mb-3">Tank to pump network</h3>
                @if ($tanks !== null && $tanks->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold">Tank</th>
                                    <th class="text-left px-3 py-2 font-semibold">Pumps</th>
                                    <th class="text-left px-3 py-2 font-semibold">Restock</th>
                                    <th class="text-left px-3 py-2 font-semibold">Current restock amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tanks as $tank)
                                    @php
                                        $tankPumps = $pumps?->where('tank_id', $tank->id) ?? collect();
                                    @endphp
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="px-3 py-2 align-top font-medium">{{ $tank->tank_name }}</td>
                                        <td class="px-3 py-2 align-top">
                                            @if ($tankPumps->isNotEmpty())
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($tankPumps as $pumpItem)
                                                        <span class="inline-flex items-center rounded-md border border-gray-200 dark:border-gray-700 px-3 py-1 text-sm">
                                                            {{ $pumpItem->pump_name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No pumps assigned</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <button
                                                type="button"
                                                class="rounded-md bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2 text-sm font-medium transition-colors js-open-restock-modal"
                                                data-tank-id="{{ $tank->id }}"
                                                data-tank-name="{{ $tank->tank_name }}"
                                                data-tank-capacity="{{ $tank->tank_capacity }}"
                                                data-available-amount="{{ $tank->available_amount ?? 0 }}"
                                            >
                                                Restock
                                            </button>
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            {{ number_format((float) ($tank->available_amount ?? 0), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No tanks added yet.</p>
                @endif
            </div>
            <div id="restock-modal" class="fixed inset-0 z-50 hidden">
                <div id="restock-modal-overlay" class="absolute inset-0 bg-black/50"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-md bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold">Restock tank</h3>
                            <button type="button" id="restock-modal-close" class="rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1 text-sm">X</button>
                        </div>
                        <form method="post" id="restock-form" class="space-y-3">
                            @csrf
                            <div>
                                <label for="restock_tank_name" class="block text-sm font-medium mb-1">Tank</label>
                                <input id="restock_tank_name" type="text" readonly class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label for="restock_current_capacity" class="block text-sm font-medium mb-1">Current capacity</label>
                                <input id="restock_current_capacity" type="text" readonly class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label for="restock_amount" class="block text-sm font-medium mb-1">Restock amount</label>
                                <input
                                    type="number"
                                    id="restock_amount"
                                    name="restock_amount"
                                    required
                                    min="0.01"
                                    step="0.01"
                                    class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] text-white dark:text-white px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                >
                                <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">Restock amount must be less than or equal to current capacity.</p>
                            </div>
                            <button type="submit" class="rounded-md bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-medium transition-colors">
                                Save restock
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <script>
                (function () {
                    const modal = document.getElementById('restock-modal');
                    const openButtons = document.querySelectorAll('.js-open-restock-modal');
                    const closeBtn = document.getElementById('restock-modal-close');
                    const overlay = document.getElementById('restock-modal-overlay');
                    const form = document.getElementById('restock-form');
                    const tankNameInput = document.getElementById('restock_tank_name');
                    const currentCapacityInput = document.getElementById('restock_current_capacity');
                    const restockAmountInput = document.getElementById('restock_amount');

                    if (!modal || !form || !tankNameInput || !currentCapacityInput || !restockAmountInput) return;

                    const closeModal = () => {
                        modal.classList.add('hidden');
                    };

                    openButtons.forEach((button) => {
                        button.addEventListener('click', () => {
                            const tankId = button.dataset.tankId || '';
                            const tankName = button.dataset.tankName || '';
                            const tankCapacity = button.dataset.tankCapacity || '';
                            form.action = `/admin/tanks/${tankId}/restock`;
                            tankNameInput.value = tankName;
                            currentCapacityInput.value = tankCapacity;
                            restockAmountInput.value = '';
                            restockAmountInput.max = String(tankCapacity);
                            modal.classList.remove('hidden');
                        });
                    });

                    if (closeBtn) closeBtn.addEventListener('click', closeModal);
                    if (overlay) overlay.addEventListener('click', closeModal);
                })();
            </script>
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
        @elseif ($navPrefix === 'admin' && $page === 'staff')
            <form method="post" action="{{ route('admin.staff.store') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                @csrf
                <div>
                    <label for="staff_name" class="block text-sm font-medium mb-1">Add staff</label>
                    <input
                        type="text"
                        name="name"
                        id="staff_name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                    Save
                </button>
            </form>

            <div class="mt-6 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <h3 class="text-sm font-semibold mb-3">Added staff</h3>
                @php
                    $staffList = $staffMembers instanceof \Illuminate\Pagination\LengthAwarePaginator
                        ? collect($staffMembers->items())
                        : ($staffMembers ?? collect());
                @endphp
                @if ($staffList->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach ($staffList as $staff)
                            <div class="h-14 border border-gray-200 dark:border-gray-700 rounded-md px-2 py-1 flex items-center justify-between gap-2">
                                <p class="text-sm truncate">{{ $staff->name }}</p>
                                <form method="post" action="{{ route('admin.staff.delete', $staff) }}" onsubmit="return confirm('Delete this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="staff_page" value="{{ request()->query('staff_page') }}">
                                    <button type="submit" class="text-red-700 dark:text-red-400 text-sm leading-none px-1">X</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    @if ($staffMembers instanceof \Illuminate\Pagination\LengthAwarePaginator && $staffMembers->hasPages())
                        <div class="mt-3">
                            {{ $staffMembers->onEachSide(1)->links() }}
                        </div>
                    @endif
                @else
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No staff added yet.</p>
                @endif
            </div>
        @elseif (in_array($navPrefix, ['dev', 'admin'], true) && $page === 'price')
            @php
                $pricePageUrl = route($navPrefix.'.show', ['page' => 'price']);
                $priceFormDateFormatted = \Illuminate\Support\Carbon::parse($priceFormDate)->format('l, F j, Y');
            @endphp
            @if ($priceFormDate ?? null)
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-3">
                    Use the calendar to pick <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">which day</span> these prices apply to (today or an earlier day). The form reloads when you change the date. Saving updates that day’s rows only; another day keeps its own history.
                </p>
            @endif
            <div class="mb-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <p class="block text-sm font-medium mb-2">Price date</p>
                <div class="flex flex-wrap items-center gap-3 mb-2">
                    <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                        <span class="text-[#706f6c] dark:text-[#A1A09A]">Selected:</span>
                        <button
                            type="button"
                            class="js-price-date-open font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:underline decoration-dotted underline-offset-2 text-left"
                            aria-controls="price_date_input"
                        >{{ $priceFormDateFormatted }}</button>
                        <span class="text-[#706f6c] dark:text-[#A1A09A] text-xs ml-1">({{ $priceFormDate }})</span>
                    </p>
                    <button
                        type="button"
                        class="js-price-date-open inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] shadow-sm hover:bg-gray-50 dark:hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-[#161615]"
                        aria-controls="price_date_input"
                    >
                        Choose price date
                    </button>
                </div>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Opens your device calendar. Only today or earlier; future dates are not available. The date cannot be typed.</p>
                <div class="fixed left-0 top-0 -z-10 h-px w-px overflow-hidden opacity-0" aria-hidden="true">
                    <input
                        type="date"
                        id="price_date_input"
                        value="{{ $priceFormDate }}"
                        max="{{ $priceDateMax }}"
                        inputmode="none"
                        autocomplete="off"
                        tabindex="-1"
                        class="absolute h-px w-px opacity-0"
                    />
                </div>
            </div>
            <script>
                (function () {
                    const input = document.getElementById('price_date_input');
                    const base = @json($pricePageUrl);
                    const maxStr = @json($priceDateMax);
                    const currentDate = @json($priceFormDate);
                    if (!input) return;

                    const go = (v) => {
                        const url = new URL(base, window.location.origin);
                        url.searchParams.set('price_date', v);
                        window.location.href = url.pathname + url.search;
                    };

                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Tab') return;
                        e.preventDefault();
                    });
                    input.addEventListener('keypress', (e) => e.preventDefault());
                    input.addEventListener('paste', (e) => e.preventDefault());

                    input.addEventListener('change', () => {
                        const v = (input.value || '').trim();
                        if (!v || v === currentDate) return;
                        if (maxStr && v > maxStr) {
                            alert('Choose today or an earlier date only.');
                            input.value = currentDate;
                            return;
                        }
                        go(v);
                    });

                    const openPicker = () => {
                        input.value = currentDate;
                        const tryPicker = async () => {
                            try {
                                if (typeof input.showPicker === 'function') {
                                    await input.showPicker();
                                } else {
                                    input.focus({ preventScroll: true });
                                    input.click();
                                }
                            } catch (err) {
                                input.focus({ preventScroll: true });
                                input.click();
                            }
                        };
                        void tryPicker();
                    };

                    document.querySelectorAll('.js-price-date-open').forEach((el) => {
                        el.addEventListener('click', openPicker);
                    });
                })();
            </script>
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                <form method="post" action="{{ route($navPrefix.'.prices.save') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                    @csrf
                    <input type="hidden" name="price_date" value="{{ old('price_date', $priceFormDate) }}">
                    <h3 class="text-sm font-semibold">Fuel prices</h3>
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
                            Save fuel prices
                        </button>
                    @else
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No categories available. Add categories first.</p>
                    @endif
                </form>

                <form method="post" action="{{ route($navPrefix.'.prices.save') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                    @csrf
                    <input type="hidden" name="price_date" value="{{ old('price_date', $priceFormDate) }}">
                    <h3 class="text-sm font-semibold">Gas prices</h3>
                    <div>
                        <label for="gas_price_l" class="block text-sm font-medium mb-1">Gas (L)</label>
                        <input
                            type="number"
                            name="gas_prices[l]"
                            id="gas_price_l"
                            value="{{ old('gas_prices.l', $gasPrices['L'] ?? '') }}"
                            min="0"
                            step="0.01"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </div>
                    <div>
                        <label for="gas_price_m" class="block text-sm font-medium mb-1">Gas (M)</label>
                        <input
                            type="number"
                            name="gas_prices[m]"
                            id="gas_price_m"
                            value="{{ old('gas_prices.m', $gasPrices['M'] ?? '') }}"
                            min="0"
                            step="0.01"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </div>
                    <div>
                        <label for="gas_price_s" class="block text-sm font-medium mb-1">Gas (S)</label>
                        <input
                            type="number"
                            name="gas_prices[s]"
                            id="gas_price_s"
                            value="{{ old('gas_prices.s', $gasPrices['S'] ?? '') }}"
                            min="0"
                            step="0.01"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </div>
                    <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                        Save gas prices
                    </button>
                </form>

                <form method="post" action="{{ route($navPrefix.'.prices.save') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                    @csrf
                    <input type="hidden" name="price_date" value="{{ old('price_date', $priceFormDate) }}">
                    <h3 class="text-sm font-semibold">Oil price</h3>
                    <div>
                        <label for="oil_liters" class="block text-sm font-medium mb-1">Liter amount</label>
                        <input
                            type="number"
                            name="oil_liters"
                            id="oil_liters"
                            value="{{ old('oil_liters') }}"
                            min="0"
                            step="0.0001"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </div>
                    <div>
                        <label for="oil_price" class="block text-sm font-medium mb-1">Oil price (total)</label>
                        <input
                            type="number"
                            name="oil_price"
                            id="oil_price"
                            value="{{ old('oil_price') }}"
                            min="0"
                            step="0.0001"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </div>
                    <div>
                        <label for="oil_unit_price_preview" class="block text-sm font-medium mb-1">Unit price (per liter)</label>
                        <input
                            type="text"
                            id="oil_unit_price_preview"
                            value="{{ $oilPrice !== null ? number_format((float) $oilPrice, 2, '.', '') : '' }}"
                            readonly
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed"
                        >
                    </div>
                    <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                        Save oil price
                    </button>
                </form>
            </div>
            <script>
                (() => {
                    const litersInput = document.getElementById('oil_liters');
                    const totalInput = document.getElementById('oil_price');
                    const unitPreview = document.getElementById('oil_unit_price_preview');
                    if (!litersInput || !totalInput || !unitPreview) return;

                    const recalc = () => {
                        const liters = Number((litersInput.value || '').trim());
                        const total = Number((totalInput.value || '').trim());
                        if (!Number.isFinite(liters) || liters <= 0 || !Number.isFinite(total)) {
                            return;
                        }
                        unitPreview.value = (total / liters).toFixed(2);
                    };

                    litersInput.addEventListener('input', recalc);
                    totalInput.addEventListener('input', recalc);
                    recalc();
                })();
            </script>
        @elseif (in_array($navPrefix, ['admin', 'data-entry'], true) && $page === 'cash-rec')
            <div class="space-y-3">
                <h3 class="text-sm font-semibold">Cash</h3>
                @php
                    $cashDate = $cashRecDate ?? now()->toDateString();
                    $cashDateMax = $cashRecDateMax ?? now()->toDateString();
                    $cashStaffOptions = $cashRecStaffOptions ?? collect();
                    $cashCategoryOptions = $cashRecCategoryOptions ?? ['cash' => 'Cash', 'visa-master' => 'Visa/Master', 'amex' => 'Amex'];
                    $selectedCashCategory = (string) ($cashRecSelectedCategory ?? 'cash');
                    $selectedCashStaff = (string) request()->query('cash_staff_id', '');
                    $cashRecListUrl = route($navPrefix.'.show', ['page' => 'cash-rec']);
                    $cashRecSaveUrl = route($navPrefix.'.cash-rec.save');
                    $cashRecBaseUrl = route($navPrefix.'.show', ['page' => 'cash-rec']);
                    $cashPrefillValues = collect($cashRecExistingValues ?? [])
                        ->map(fn ($value) => is_array($value) ? (string) ($value['amount'] ?? '') : (string) $value)
                        ->filter(fn ($value) => $value !== '')
                        ->values();
                    $cashRecRecords = $cashRecRecords ?? collect();
                    $cashHistoryDate = $cashRecHistoryDate ?? now()->subDay()->toDateString();
                    if ($cashPrefillValues->isEmpty()) {
                        $cashPrefillValues = collect(['']);
                    }
                    if ($selectedCashCategory !== 'cash') {
                        $cashPrefillValues = collect([$cashPrefillValues->first() ?? '']);
                    }
                    $initialCashRowCount = $selectedCashCategory === 'cash'
                        ? max($cashPrefillValues->count() + 1, 2)
                        : 1;
                @endphp
                <div class="overflow-x-auto">
                    <form method="post" action="{{ $cashRecSaveUrl }}">
                        @csrf
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="text-left px-3 py-2 font-semibold">Date</th>
                                <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                <th class="text-left px-3 py-2 font-semibold">Category</th>
                                <th class="text-left px-3 py-2 font-semibold">Amount</th>
                                <th class="text-left px-3 py-2 font-semibold">Total</th>
                                <th class="text-left px-3 py-2 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cash_rec_table_body">
                            @foreach ($cashPrefillValues as $idx => $cashValue)
                                <tr class="border-t border-gray-200 dark:border-gray-700" data-cash-line>
                                    @if ($idx === 0)
                                        <td class="px-3 py-2 align-top" id="cash_rec_date_cell" rowspan="{{ $initialCashRowCount }}">
                                            <input
                                                type="date"
                                                id="cash_rec_date"
                                                name="cash_date"
                                                value="{{ $cashDate }}"
                                                max="{{ $cashDateMax }}"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                            >
                                        </td>
                                        <td class="px-3 py-2 align-top" id="cash_rec_staff_cell" rowspan="{{ $initialCashRowCount }}">
                                            <select
                                                id="cash_rec_staff"
                                                name="cash_staff_id"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                {{ $cashStaffOptions->isEmpty() ? 'disabled' : '' }}
                                            >
                                                <option value="">Select staff</option>
                                                @foreach ($cashStaffOptions as $staffOption)
                                                    <option value="{{ $staffOption->id }}" {{ $selectedCashStaff === (string) $staffOption->id ? 'selected' : '' }}>
                                                        {{ ($staffOption->is_active ?? true) ? $staffOption->name : $staffOption->name.' (Removed)' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-2 align-top" id="cash_rec_category_cell" rowspan="{{ $initialCashRowCount }}">
                                            <select
                                                id="cash_rec_category"
                                                name="cash_category"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                            >
                                                @foreach ($cashCategoryOptions as $optionValue => $optionLabel)
                                                    <option value="{{ $optionValue }}" {{ $selectedCashCategory === $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                    <td class="px-3 py-2 align-top">
                                        <input
                                            type="text"
                                            inputmode="decimal"
                                            name="cash_values[]"
                                            class="js-cash-input w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                            placeholder="Enter amount"
                                            value="{{ $cashValue }}"
                                        >
                                    </td>
                                    @if ($idx === 0)
                                        <td class="px-3 py-2 align-top font-medium" id="cash_rec_total_cell" rowspan="{{ $initialCashRowCount }}">
                                            {{ $cashRecExistingTotal ?? '0.00' }}
                                        </td>
                                        <td class="px-3 py-2 align-top" id="cash_rec_action_cell" rowspan="{{ $initialCashRowCount }}">
                                            <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                                                Save
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            @if ($selectedCashCategory === 'cash')
                                <tr class="border-t border-gray-200 dark:border-gray-700" data-cash-line>
                                    <td class="px-3 py-2 align-top">
                                        <input
                                            type="text"
                                            inputmode="decimal"
                                            name="cash_values[]"
                                            class="js-cash-input w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                            placeholder="Enter amount"
                                        >
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        </table>
                    </form>
                </div>
                <div class="space-y-2">
                    <label for="cash_history_date" class="block text-sm font-medium">View saved records by date</label>
                    <div class="max-w-xs">
                        <input
                            type="date"
                            id="cash_history_date"
                            value="{{ $cashHistoryDate }}"
                            max="{{ $cashDateMax }}"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </div>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="text-left px-3 py-2 font-semibold">Date</th>
                                <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                <th class="text-left px-3 py-2 font-semibold">Category/amounts</th>
                                <th class="text-left px-3 py-2 font-semibold">Cash values</th>
                                <th class="text-left px-3 py-2 font-semibold">Total</th>
                                <th class="text-left px-3 py-2 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cashRecRecords as $rec)
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="px-3 py-2 align-top">{{ \Illuminate\Support\Carbon::parse($rec->date)->toDateString() }}</td>
                                    <td class="px-3 py-2 align-top">{{ $rec->staff?->name ?? '—' }}</td>
                                    <td class="px-3 py-2 align-top">
                                        {{ match ($rec->category ?? 'cash') {
                                            'visa-master' => 'Visa/Master',
                                            'amex' => 'Amex',
                                            default => 'Cash',
                                        } }}
                                    </td>
                                    <td class="px-3 py-2 align-top">
                                        {{ number_format((float) ($rec->cash_total ?? 0), 2) }}
                                    </td>
                                    <td class="px-3 py-2 align-top">{{ number_format((float) ($rec->cash_total ?? 0), 2) }}</td>
                                    <td class="px-3 py-2 align-top">
                                        @php
                                            $editUrl = $cashRecBaseUrl.'?'.http_build_query(array_filter([
                                                'cash_date' => \Illuminate\Support\Carbon::parse($rec->date)->toDateString(),
                                                'cash_staff_id' => $rec->staff_id,
                                                'cash_category' => $rec->category ?? 'cash',
                                                'cash_history_date' => $cashHistoryDate,
                                            ], fn ($v) => $v !== null && $v !== ''));
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <a
                                                href="{{ $editUrl }}"
                                                class="inline-flex rounded-md border border-gray-300 dark:border-gray-600 px-3 py-1.5 text-xs font-medium hover:bg-gray-50 dark:hover:bg-gray-800"
                                            >
                                                Update
                                            </a>
                                            <form method="post" action="{{ route($navPrefix.'.cash-rec.delete', $rec) }}" onsubmit="return confirm('Delete this cash record?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="cash_date" value="{{ $cashDate }}">
                                                <input type="hidden" name="cash_staff_id" value="{{ $selectedCashStaff }}">
                                                <input type="hidden" name="cash_category" value="{{ $selectedCashCategory }}">
                                                <input type="hidden" name="cash_history_date" value="{{ $cashHistoryDate }}">
                                                <input type="hidden" name="cash_history_page" value="{{ request()->query('cash_history_page') }}">
                                                <button
                                                    type="submit"
                                                    class="inline-flex rounded-md border border-red-300 text-red-700 dark:border-red-700 dark:text-red-400 px-3 py-1.5 text-xs font-medium hover:bg-red-50 dark:hover:bg-red-900/30"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td colspan="6" class="px-3 py-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        No saved records for this date.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            </div>
                @if ($cashRecRecords instanceof \Illuminate\Pagination\LengthAwarePaginator && $cashRecRecords->hasPages())
                    <div class="mt-3">
                        {{ $cashRecRecords->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
            <script>
                (function () {
                    const dateInput = document.getElementById('cash_rec_date');
                    const staffSelect = document.getElementById('cash_rec_staff');
                    const categorySelect = document.getElementById('cash_rec_category');
                    const historyDateInput = document.getElementById('cash_history_date');
                    const cashTableBody = document.getElementById('cash_rec_table_body');
                    const totalCell = document.getElementById('cash_rec_total_cell');
                    const dateCell = dateInput?.closest('td');
                    const staffCell = document.getElementById('cash_rec_staff_cell');
                    const categoryCell = document.getElementById('cash_rec_category_cell');
                    const actionCell = document.getElementById('cash_rec_action_cell');
                    const baseUrl = @json($cashRecListUrl);
                    if (!dateInput) return;

                    const buildUrl = () => {
                        const url = new URL(baseUrl, window.location.origin);
                        if (dateInput.value) {
                            url.searchParams.set('cash_date', dateInput.value);
                        }
                        if (staffSelect && staffSelect.value) {
                            url.searchParams.set('cash_staff_id', staffSelect.value);
                        }
                        if (categorySelect && categorySelect.value) {
                            url.searchParams.set('cash_category', categorySelect.value);
                        }
                        if (historyDateInput && historyDateInput.value) {
                            url.searchParams.set('cash_history_date', historyDateInput.value);
                        }

                        return url.toString();
                    };

                    dateInput.addEventListener('change', () => {
                        window.location.href = buildUrl();
                    });
                    if (staffSelect) {
                        staffSelect.addEventListener('change', () => {
                            window.location.href = buildUrl();
                        });
                    }
                    if (categorySelect) {
                        categorySelect.addEventListener('change', () => {
                            window.location.href = buildUrl();
                        });
                    }
                    if (historyDateInput) {
                        historyDateInput.addEventListener('change', () => {
                            window.location.href = buildUrl();
                        });
                    }

                    const openDatePicker = (input) => {
                        if (!input) return;
                        const tryPicker = () => {
                            try {
                                if (typeof input.showPicker === 'function') {
                                    input.showPicker();
                                } else {
                                    input.focus({ preventScroll: true });
                                    input.click();
                                }
                            } catch (err) {
                                input.focus({ preventScroll: true });
                                input.click();
                            }
                        };
                        input.addEventListener('click', tryPicker);
                        input.addEventListener('focus', tryPicker);
                    };
                    openDatePicker(dateInput);
                    openDatePicker(historyDateInput);

                    if (!cashTableBody || !totalCell || !dateCell || !staffCell || !categoryCell || !actionCell || !categorySelect) return;

                    const formatMoneyInput = (value) => {
                        const cleaned = String(value ?? '').replace(/,/g, '').trim();
                        if (cleaned === '') return '';
                        const n = Number(cleaned);
                        if (!Number.isFinite(n)) return '';
                        return n.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                    };

                    const parseMoneyInput = (value) => {
                        const cleaned = String(value ?? '').replace(/,/g, '').trim();
                        if (cleaned === '') return null;
                        const n = Number(cleaned);
                        return Number.isFinite(n) ? n : null;
                    };

                    const normalizeCashInputsForSubmit = () => {
                        cashInputs().forEach((input) => {
                            const numeric = parseMoneyInput(input.value);
                            input.value = numeric === null ? '' : numeric.toFixed(2);
                        });
                    };

                    const cashRows = () => Array.from(cashTableBody.querySelectorAll('tr[data-cash-line]'));
                    const cashInputs = () => Array.from(cashTableBody.querySelectorAll('.js-cash-input'));

                    const syncRowspan = () => {
                        const count = cashRows().length || 1;
                        dateCell.setAttribute('rowspan', String(count));
                        staffCell.setAttribute('rowspan', String(count));
                        categoryCell.setAttribute('rowspan', String(count));
                        totalCell.setAttribute('rowspan', String(count));
                        actionCell.setAttribute('rowspan', String(count));
                    };

                    const recalcTotal = () => {
                        const total = cashInputs().reduce((sum, input) => {
                            const n = parseMoneyInput(input.value);
                            return n === null ? sum : sum + n;
                        }, 0);
                        totalCell.textContent = total.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                    };

                    const addCashRow = () => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-t border-gray-200 dark:border-gray-700';
                        tr.setAttribute('data-cash-line', '');
                        tr.innerHTML = `
                            <td class="px-3 py-2 align-top">
                                <input
                                    type="text"
                                    inputmode="decimal"
                                    name="cash_values[]"
                                    class="js-cash-input w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                    placeholder="Enter cash amount"
                                >
                            </td>
                        `;
                        cashTableBody.appendChild(tr);
                        syncRowspan();
                    };

                    const ensureTrailingEmptyRow = () => {
                        const rows = cashRows();
                        if (rows.length === 0) {
                            addCashRow();
                            return;
                        }
                        const inputs = cashInputs();
                        const last = inputs.at(-1);
                        if (last && (last.value || '').trim() !== '') {
                            addCashRow();
                        }
                    };

                    cashTableBody.addEventListener('input', (event) => {
                        const target = event.target;
                        if (!(target instanceof HTMLInputElement) || !target.classList.contains('js-cash-input')) {
                            return;
                        }
                        recalcTotal();
                        if (categorySelect.value !== 'cash') {
                            return;
                        }
                        const inputs = cashInputs();
                        const last = inputs.at(-1);
                        if (last === target && (target.value || '').trim() !== '') {
                            addCashRow();
                        }
                    });

                    cashTableBody.addEventListener('blur', (event) => {
                        const target = event.target;
                        if (!(target instanceof HTMLInputElement) || !target.classList.contains('js-cash-input')) {
                            return;
                        }
                        const numeric = parseMoneyInput(target.value);
                        target.value = numeric === null ? '' : formatMoneyInput(numeric);
                        recalcTotal();
                    }, true);

                    syncRowspan();
                    cashInputs().forEach((input) => {
                        const numeric = parseMoneyInput(input.value);
                        if (numeric !== null) {
                            input.value = formatMoneyInput(numeric);
                        }
                    });
                    recalcTotal();
                    ensureTrailingEmptyRow();

                    const cashForm = cashTableBody.closest('form');
                    if (cashForm) {
                        cashForm.addEventListener('submit', () => {
                            normalizeCashInputsForSubmit();
                        });
                    }
                })();
            </script>
        @elseif (in_array($navPrefix, ['admin', 'data-entry'], true) && $page === 'bill')
            @php
                $companyRows = $companies ?? collect();
                $billStaffOptions = $billStaffOptions ?? collect();
                $billCompanyOptions = $billCompanyOptions ?? collect();
                $billCategoryOptions = $billCategoryOptions ?? collect();
            @endphp
            <div class="space-y-3 max-w-2xl">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        id="toggle_bill_form"
                        class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity"
                    >
                        Add Company
                    </button>
                    <button
                        type="button"
                        id="open_companies_modal"
                        class="rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                    >
                        View all companies
                    </button>
                </div>
                <form id="bill_company_form" method="post" action="{{ route($navPrefix.'.bill.company.save') }}" class="hidden space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                    @csrf
                    <div>
                        <label for="company_name" class="block text-sm font-medium mb-1">Company</label>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="{{ old('company_name') }}"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                            required
                        >
                    </div>
                    <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                        Save
                    </button>
                </form>
                <form method="post" action="{{ route($navPrefix.'.bill.save') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                    @csrf
                    <h3 class="text-sm font-semibold">Bill</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label for="bill_staff_id" class="block text-sm font-medium mb-1">Staff</label>
                            <select id="bill_staff_id" name="staff_id" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" required>
                                <option value="">Select staff</option>
                                @foreach ($billStaffOptions as $staffOption)
                                    <option value="{{ $staffOption->id }}" @selected((string) old('staff_id') === (string) $staffOption->id)>{{ $staffOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="bill_date" class="block text-sm font-medium mb-1">Date</label>
                            <div id="bill_date_field" class="w-full cursor-pointer rounded-md">
                                <input type="date" id="bill_date" name="date" value="{{ old('date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" class="w-full cursor-pointer rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" required>
                            </div>
                        </div>
                        <div>
                            <label for="bill_company_id" class="block text-sm font-medium mb-1">Company</label>
                            <select id="bill_company_id" name="company_id" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" required>
                                <option value="">Select company</option>
                                @foreach ($billCompanyOptions as $companyOption)
                                    <option value="{{ $companyOption['id'] }}" @selected((string) old('company_id') === (string) $companyOption['id'])>{{ $companyOption['company_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="bill_invoice_number" class="block text-sm font-medium mb-1">Invoice number</label>
                            <input
                                type="text"
                                id="bill_invoice_number"
                                name="invoice_number"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                placeholder="Enter invoice number"
                                value="{{ old('invoice_number') }}"
                                required
                            >
                        </div>
                        <div>
                            <label for="bill_category_id" class="block text-sm font-medium mb-1">Category</label>
                            <select id="bill_category_id" name="category_id" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" required>
                                <option value="">Select category</option>
                                @foreach ($billCategoryOptions as $categoryOption)
                                    <option value="{{ $categoryOption->id }}" @selected((string) old('category_id') === (string) $categoryOption->id)>{{ $categoryOption->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="bill_price" class="block text-sm font-medium mb-1">Price</label>
                            <input type="text" id="bill_price" name="price" readonly class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-[#121212] px-3 py-2 text-sm outline-none" placeholder="Auto from date + category" value="{{ old('price') }}" required>
                        </div>
                        <div>
                            <label for="bill_liters" class="block text-sm font-medium mb-1">Liter amount</label>
                            <input type="number" id="bill_liters" name="liters" min="0" step="0.0001" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" value="{{ old('liters') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label for="bill_value" class="block text-sm font-medium mb-1">Bill value</label>
                            <input type="number" id="bill_value" name="bill_value" min="0" step="0.0001" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" placeholder="Enter bill value or liters" value="{{ old('bill_value') }}">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                            Save
                        </button>
                    </div>
                </form>
            </div>
            <div id="companies_modal_overlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>
            <div id="companies_modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                <div class="w-full max-w-3xl bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold">Companies</h3>
                        <button type="button" id="close_companies_modal" class="rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1 text-xs">Close</button>
                    </div>
                    <div class="p-4 space-y-3">
                        <input
                            type="text"
                            id="companies_search"
                            placeholder="Search company"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                        <div class="overflow-x-auto max-h-[60vh]">
                            <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="text-left px-3 py-2 font-semibold">Company</th>
                                        <th class="text-left px-3 py-2 font-semibold">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="companies_rows">
                                    @forelse ($companyRows as $companyRow)
                                        <tr class="border-t border-gray-200 dark:border-gray-700 company-row" data-company-name="{{ mb_strtolower($companyRow->company_name) }}">
                                            <td class="px-3 py-2 align-top">
                                                <form method="post" action="{{ route($navPrefix.'.bill.company.update', $companyRow) }}" class="flex flex-wrap items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input
                                                        type="text"
                                                        name="company_name"
                                                        value="{{ $companyRow->company_name }}"
                                                        class="min-w-0 flex-1 basis-40 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-2 py-1.5 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                        required
                                                    >
                                                    <button type="submit" class="shrink-0 rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-xs hover:bg-gray-50 dark:hover:bg-gray-800">Update</button>
                                                    <a
                                                        href="{{ route($navPrefix.'.bill.company.report', $companyRow) }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="shrink-0 rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-xs font-medium hover:bg-gray-50 dark:hover:bg-gray-800 whitespace-nowrap"
                                                    >Get report</a>
                                                    <button
                                                        type="button"
                                                        class="js-open-settle-bill shrink-0 rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-xs font-medium hover:bg-gray-50 dark:hover:bg-gray-800 whitespace-nowrap"
                                                    >Settle bill</button>
                                                </form>
                                                <form
                                                    method="post"
                                                    action="{{ route($navPrefix.'.bill.company.settle', $companyRow) }}"
                                                    class="js-settle-bill-form hidden mt-2 flex flex-wrap items-center gap-2"
                                                >
                                                    @csrf
                                                    <label class="inline-flex items-center gap-1 text-xs">
                                                        <input type="checkbox" name="reset_include" value="1" class="rounded border-gray-300 dark:border-gray-600">
                                                        <span>Including today bill</span>
                                                    </label>
                                                    <div class="js-settle-date-wrap rounded-md cursor-pointer">
                                                        <input
                                                            type="date"
                                                            name="reset_date"
                                                            max="{{ now()->toDateString() }}"
                                                            required
                                                            class="js-settle-date-field rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none cursor-pointer"
                                                        >
                                                    </div>
                                                    <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-2 py-1.5 text-xs font-medium hover:opacity-90">Save</button>
                                                </form>
                                            </td>
                                            <td class="px-3 py-2 align-top">
                                                <form method="post" action="{{ route($navPrefix.'.bill.company.delete', $companyRow) }}" onsubmit="return confirm('Delete this company?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-md border border-red-300 text-red-700 dark:border-red-700 dark:text-red-400 px-3 py-1.5 text-xs hover:bg-red-50 dark:hover:bg-red-900/30">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                            <td colspan="2" class="px-3 py-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">No companies added yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                (function () {
                    const openBtn = document.getElementById('open_companies_modal');
                    const closeBtn = document.getElementById('close_companies_modal');
                    const toggleBillFormBtn = document.getElementById('toggle_bill_form');
                    const billCompanyForm = document.getElementById('bill_company_form');
                    const modal = document.getElementById('companies_modal');
                    const overlay = document.getElementById('companies_modal_overlay');
                    const search = document.getElementById('companies_search');
                    const rows = Array.from(document.querySelectorAll('.company-row'));
                    if (!openBtn || !closeBtn || !modal || !overlay || !toggleBillFormBtn || !billCompanyForm) return;

                    toggleBillFormBtn.addEventListener('click', () => {
                        const hidden = billCompanyForm.classList.contains('hidden');
                        billCompanyForm.classList.toggle('hidden', !hidden);
                    });

                    const open = () => {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        overlay.classList.remove('hidden');
                        if (search) search.focus();
                    };
                    const close = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        overlay.classList.add('hidden');
                    };

                    openBtn.addEventListener('click', open);
                    closeBtn.addEventListener('click', close);
                    overlay.addEventListener('click', close);
                    if (search) {
                        search.addEventListener('input', () => {
                            const q = search.value.trim().toLowerCase();
                            rows.forEach((row) => {
                                const name = row.getAttribute('data-company-name') || '';
                                row.classList.toggle('hidden', q !== '' && !name.includes(q));
                            });
                        });
                    }

                    const settleButtons = Array.from(document.querySelectorAll('.js-open-settle-bill'));
                    settleButtons.forEach((btn) => {
                        btn.addEventListener('click', () => {
                            const cell = btn.closest('td');
                            if (!cell) return;
                            const form = cell.querySelector('.js-settle-bill-form');
                            if (!form) return;
                            const isHidden = form.classList.contains('hidden');
                            form.classList.toggle('hidden', !isHidden);
                            if (isHidden) {
                                const dateInput = form.querySelector('input[name="reset_date"]');
                                if (dateInput instanceof HTMLInputElement) {
                                    dateInput.focus();
                                }
                            }
                        });
                    });

                    const settleDateWrappers = Array.from(document.querySelectorAll('.js-settle-date-wrap'));
                    settleDateWrappers.forEach((wrap) => {
                        wrap.addEventListener('click', () => {
                            const input = wrap.querySelector('.js-settle-date-field');
                            if (!(input instanceof HTMLInputElement)) return;
                            if (typeof input.showPicker === 'function') {
                                try {
                                    input.showPicker();
                                } catch (_) {
                                    input.focus();
                                }
                            } else {
                                input.focus();
                            }
                        });
                    });

                    const billDateInput = document.getElementById('bill_date');
                    const billDateField = document.getElementById('bill_date_field');
                    const billCategorySelect = document.getElementById('bill_category_id');
                    const billPriceInput = document.getElementById('bill_price');
                    const billLitersInput = document.getElementById('bill_liters');
                    const billValueInput = document.getElementById('bill_value');
                    const billPriceUrl = @json(route($navPrefix.'.bill.category-price'));

                    const updateBillValueFromLiters = () => {
                        if (!billPriceInput || !billLitersInput || !billValueInput) return;
                        const p = Number((billPriceInput.value || '').trim());
                        const l = Number((billLitersInput.value || '').trim());
                        if (!Number.isFinite(p) || !Number.isFinite(l)) {
                            billValueInput.value = '';
                            return;
                        }
                        billValueInput.value = (p * l).toFixed(4);
                    };

                    const updateBillLitersFromValue = () => {
                        if (!billPriceInput || !billLitersInput || !billValueInput) return;
                        const p = Number((billPriceInput.value || '').trim());
                        const v = Number((billValueInput.value || '').trim());
                        if (!Number.isFinite(p) || p <= 0 || !Number.isFinite(v)) {
                            billLitersInput.value = '';
                            return;
                        }
                        billLitersInput.value = (v / p).toFixed(4);
                    };

                    const fetchBillPrice = async () => {
                        if (!billDateInput || !billCategorySelect || !billPriceInput) return;
                        const date = billDateInput.value;
                        const categoryId = billCategorySelect.value;
                        if (!date || !categoryId) {
                            billPriceInput.value = '';
                            if ((billLitersInput?.value || '').trim() !== '') {
                                updateBillValueFromLiters();
                            } else if ((billValueInput?.value || '').trim() !== '') {
                                updateBillLitersFromValue();
                            }
                            return;
                        }
                        const url = new URL(billPriceUrl, window.location.origin);
                        url.searchParams.set('bill_date', date);
                        url.searchParams.set('category_id', categoryId);
                        try {
                            const resp = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                            if (!resp.ok) throw new Error('Price lookup failed');
                            const data = await resp.json();
                            billPriceInput.value = data.price !== null && data.price !== undefined ? Number(data.price).toFixed(2) : '';
                        } catch (e) {
                            billPriceInput.value = '';
                        }
                        if ((billLitersInput?.value || '').trim() !== '') {
                            updateBillValueFromLiters();
                        } else if ((billValueInput?.value || '').trim() !== '') {
                            updateBillLitersFromValue();
                        }
                    };

                    if (billDateField && billDateInput) {
                        billDateField.addEventListener('click', () => {
                            if (typeof billDateInput.showPicker === 'function') {
                                try {
                                    billDateInput.showPicker();
                                } catch (_) {
                                    billDateInput.focus();
                                }
                            } else {
                                billDateInput.focus();
                            }
                        });
                    }
                    if (billDateInput) {
                        billDateInput.addEventListener('change', fetchBillPrice);
                    }
                    if (billCategorySelect) {
                        billCategorySelect.addEventListener('change', fetchBillPrice);
                    }
                    if (billLitersInput) {
                        billLitersInput.addEventListener('input', updateBillValueFromLiters);
                    }
                    if (billValueInput) {
                        billValueInput.addEventListener('input', updateBillLitersFromValue);
                    }
                })();
            </script>
        @elseif ($navPrefix === 'admin' && $page === 'gas')
            @php
                $gasRowTypes = ['L', 'M', 'S'];
                $gasPrices = $gasPrices ?? collect();
                $gasDetails = $gasDetails ?? collect();
                $gasPreviousDetails = $gasPreviousDetails ?? collect();
                $gasDate = $gasFormDate ?? now()->toDateString();
                $gasDateMax = now()->toDateString();
                $gasStaffMembers = $staffMembers ?? collect();
            @endphp
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold">Gas details</h3>
                    <form method="get" action="{{ route($navPrefix.'.show', ['page' => 'gas']) }}" class="flex items-center gap-2">
                        <label for="gas_date" class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Date</label>
                        <input
                            id="gas_date"
                            name="gas_date"
                            type="date"
                            value="{{ $gasDate }}"
                            max="{{ $gasDateMax }}"
                            onchange="this.form.submit()"
                            class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <form method="post" action="{{ route($navPrefix.'.gas.details.save') }}">
                        @csrf
                        <input type="hidden" name="gas_date" value="{{ $gasDate }}">
                    <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold">Gas type</th>
                                    <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                    <th class="text-left px-3 py-2 font-semibold">Morning balance</th>
                                    <th class="text-left px-3 py-2 font-semibold">Night balance</th>
                                    <th class="text-left px-3 py-2 font-semibold">Today sale</th>
                                    <th class="text-left px-3 py-2 font-semibold">Amount</th>
                                    <th class="text-left px-3 py-2 font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gasRowTypes as $gasType)
                                    @php
                                        $gasKey = strtolower($gasType);
                                        $row = $gasDetails->get($gasType);
                                        $previousRow = $gasPreviousDetails->get($gasType);
                                        $morningBalance = $row?->morning_balance ?? $previousRow?->night_balance;
                                    @endphp
                                    <tr class="border-t border-gray-200 dark:border-gray-700">
                                        <td class="px-3 py-2 align-top">
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                value="{{ $gasPrices[$gasType] ?? '' }}"
                                                placeholder="Gas {{ $gasType }} price"
                                                readonly
                                                data-gas-price
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed"
                                            >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <select
                                                name="gas_data[{{ $gasKey }}][staff_id]"
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] text-black dark:text-black px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                            >
                                                <option value="">Select staff</option>
                                                @foreach ($gasStaffMembers as $member)
                                                    <option
                                                        value="{{ $member->id }}"
                                                        @selected((string) old('gas_data.'.$gasKey.'.staff_id', $row?->staff_id) === (string) $member->id)
                                                    >
                                                        {{ $member->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                value="{{ old('gas_data.'.$gasKey.'.morning_balance', $morningBalance) }}"
                                                readonly
                                                data-gas-morning
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed"
                                            >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                name="gas_data[{{ $gasKey }}][night_balance]"
                                                value="{{ old('gas_data.'.$gasKey.'.night_balance', $row?->night_balance) }}"
                                                data-gas-night
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] text-black dark:text-black px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                            >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                name="gas_data[{{ $gasKey }}][today_sale]"
                                                value="{{ old('gas_data.'.$gasKey.'.today_sale', $row?->today_sale) }}"
                                                readonly
                                                data-gas-sale
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed"
                                            >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                name="gas_data[{{ $gasKey }}][amount]"
                                                value="{{ old('gas_data.'.$gasKey.'.amount', $row?->amount) }}"
                                                readonly
                                                data-gas-amount
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed"
                                            >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                            <button
                                                type="submit"
                                                name="row_key"
                                                value="{{ $gasKey }}"
                                                class="inline-flex items-center rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2"
                                            >
                                                Update
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
                <script>
                    (() => {
                        const rows = Array.from(document.querySelectorAll('table tbody tr'));
                        rows.forEach((row) => {
                            const morningInput = row.querySelector('[data-gas-morning]');
                            const priceInput = row.querySelector('[data-gas-price]');
                            const nightInput = row.querySelector('[data-gas-night]');
                            const saleInput = row.querySelector('[data-gas-sale]');
                            const amountInput = row.querySelector('[data-gas-amount]');
                            if (!morningInput || !priceInput || !nightInput || !saleInput || !amountInput) return;

                            const recalc = () => {
                                const morning = Number((morningInput.value || '').trim());
                                const night = Number((nightInput.value || '').trim());
                                const price = Number((priceInput.value || '').trim());
                                if (!Number.isFinite(morning) || !Number.isFinite(night)) {
                                    saleInput.value = '';
                                    amountInput.value = '';
                                    return;
                                }
                                const sale = morning - night;
                                saleInput.value = sale.toFixed(2);
                                if (Number.isFinite(price)) {
                                    amountInput.value = (sale * price).toFixed(2);
                                } else {
                                    amountInput.value = '';
                                }
                            };

                            nightInput.addEventListener('input', recalc);
                            recalc();
                        });
                    })();
                </script>
            </div>
        @elseif ($navPrefix === 'admin' && $page === 'oil')
            @php
                $oilDate = $oilFormDate ?? now()->toDateString();
                $oilDateMax = $oilDateMax ?? now()->toDateString();
                $oilStaffOptions = $oilStaffOptions ?? collect();
                $oilUnitPrice = $oilUnitPrice ?? null;
                $oilRecords = $oilRecords ?? collect();
            @endphp
            <div class="space-y-4">
                <h3 class="text-sm font-semibold">Oil table</h3>
                <form method="get" action="{{ route($navPrefix.'.show', ['page' => 'oil']) }}" class="max-w-xs">
                    <label for="oil_date" class="block text-sm font-medium mb-1">Date</label>
                    <div id="oil_date_field" class="w-full cursor-pointer rounded-md">
                        <input
                            type="date"
                            id="oil_date"
                            name="oil_date"
                            value="{{ $oilDate }}"
                            max="{{ $oilDateMax }}"
                            onchange="this.form.submit()"
                            class="w-full cursor-pointer rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        >
                    </div>
                </form>

                <form method="post" action="{{ route($navPrefix.'.oil.record.save') }}" class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="oil_date" value="{{ $oilDate }}">
                    <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                <th class="text-left px-3 py-2 font-semibold">Oil amount</th>
                                <th class="text-left px-3 py-2 font-semibold">Unit price</th>
                                <th class="text-left px-3 py-2 font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-3 py-2 align-top">
                                    <select
                                        id="oil_staff_id"
                                        name="staff_id"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                        required
                                    >
                                        <option value="">Select staff</option>
                                        @foreach ($oilStaffOptions as $staffOption)
                                            <option value="{{ $staffOption->id }}" @selected((string) old('staff_id') === (string) $staffOption->id)>{{ $staffOption->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <input
                                        type="number"
                                        id="oil_amount"
                                        name="oil_amount"
                                        min="0"
                                        step="0.01"
                                        value="{{ old('oil_amount') }}"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                        required
                                    >
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <input
                                        type="text"
                                        id="oil_unit_price"
                                        value="{{ $oilUnitPrice !== null ? number_format((float) $oilUnitPrice, 2, '.', '') : '' }}"
                                        readonly
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed"
                                    >
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <input
                                        type="text"
                                        id="oil_total"
                                        value=""
                                        readonly
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-[#0f0f0f] text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                            Save oil record
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="text-left px-3 py-2 font-semibold">Date</th>
                                <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                <th class="text-left px-3 py-2 font-semibold">Oil amount</th>
                                <th class="text-left px-3 py-2 font-semibold">Total</th>
                                <th class="text-left px-3 py-2 font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($oilRecords as $record)
                                @php $oilUpdateFormId = 'oil_update_'.$record->id; @endphp
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="px-3 py-2 align-top">{{ \Illuminate\Support\Carbon::parse($record->date)->toDateString() }}</td>
                                    <td class="px-3 py-2 align-top">
                                            <select
                                                name="staff_id"
                                                form="{{ $oilUpdateFormId }}"
                                                class="w-full sm:w-44 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                required
                                            >
                                                @foreach ($oilStaffOptions as $staffOption)
                                                    <option value="{{ $staffOption->id }}" @selected((int) $record->staff_id === (int) $staffOption->id)>{{ $staffOption->name }}</option>
                                                @endforeach
                                            </select>
                                    </td>
                                    <td class="px-3 py-2 align-top">
                                            <input
                                                type="number"
                                                name="oil_amount"
                                                form="{{ $oilUpdateFormId }}"
                                                min="0"
                                                step="0.01"
                                                value="{{ number_format((float) $record->oil_amount, 2, '.', '') }}"
                                                class="w-full sm:w-32 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                required
                                            >
                                    </td>
                                    <td class="px-3 py-2 align-top">{{ number_format((float) $record->total, 2) }}</td>
                                    <td class="px-3 py-2 align-top">
                                            <div class="flex items-center gap-2">
                                                <form id="{{ $oilUpdateFormId }}" method="post" action="{{ route($navPrefix.'.oil.record.update', $record) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="oil_date" value="{{ $oilDate }}">
                                                </form>
                                                <button type="submit" form="{{ $oilUpdateFormId }}" class="rounded-md border border-gray-300 dark:border-gray-600 px-2 py-1.5 text-xs font-medium hover:bg-gray-50 dark:hover:bg-gray-800">
                                                    Update
                                                </button>
                                                <form method="post" action="{{ route($navPrefix.'.oil.record.delete', $record) }}" onsubmit="return confirm('Delete this oil record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="oil_date" value="{{ $oilDate }}">
                                                    <button type="submit" class="rounded-md border border-red-300 text-red-700 dark:border-red-700 dark:text-red-400 px-2 py-1.5 text-xs font-medium hover:bg-red-50 dark:hover:bg-red-900/30">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td colspan="5" class="px-3 py-3 text-sm text-[#706f6c] dark:text-[#A1A09A]">No oil records for selected date.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                (() => {
                    const oilDateInput = document.getElementById('oil_date');
                    const oilDateField = document.getElementById('oil_date_field');
                    const amountInput = document.getElementById('oil_amount');
                    const unitPriceInput = document.getElementById('oil_unit_price');
                    const totalInput = document.getElementById('oil_total');
                    if (oilDateField && oilDateInput) {
                        oilDateField.addEventListener('click', () => {
                            if (typeof oilDateInput.showPicker === 'function') {
                                try {
                                    oilDateInput.showPicker();
                                } catch (_) {
                                    oilDateInput.focus();
                                }
                            } else {
                                oilDateInput.focus();
                            }
                        });
                    }
                    if (!amountInput || !unitPriceInput || !totalInput) return;

                    const recalc = () => {
                        const qty = Number((amountInput.value || '').trim());
                        const unit = Number((unitPriceInput.value || '').trim());
                        if (!Number.isFinite(qty) || !Number.isFinite(unit)) {
                            totalInput.value = '';
                            return;
                        }
                        totalInput.value = (qty * unit).toFixed(2);
                    };

                    amountInput.addEventListener('input', recalc);
                    recalc();
                })();
            </script>
        @elseif ($navPrefix === 'admin' && $page === 'slary')
            @php
                $salaryDateDefault = old('salary_date', $salaryFormDate ?? now()->toDateString());
                $salaryDateMax = $salaryDateMax ?? now()->toDateString();
            @endphp
            <div class="max-w-xl space-y-3">
                <h3 class="text-sm font-semibold">Salary</h3>
                <form method="get" action="{{ route($navPrefix.'.show', ['page' => 'slary']) }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                    <div>
                        <label for="salary_date" class="block text-sm font-medium mb-1">Date</label>
                        <div id="salary_date_field" class="w-full cursor-pointer rounded-md">
                            <input
                                type="date"
                                id="salary_date"
                                name="salary_date"
                                value="{{ $salaryDateDefault }}"
                                max="{{ $salaryDateMax }}"
                                class="w-full cursor-pointer rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                required
                            >
                        </div>
                    </div>
                </form>
                <form method="post" action="{{ route($navPrefix.'.slary.save') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                    @csrf
                    <input type="hidden" name="salary_date" value="{{ $salaryDateDefault }}">
                    <div>
                        <label for="salary_amount" class="block text-sm font-medium mb-1">Amount</label>
                        <input
                            type="number"
                            id="salary_amount"
                            name="amount"
                            value="{{ old('amount', $salaryAmount !== null ? number_format((float) $salaryAmount, 2, '.', '') : '') }}"
                            min="0"
                            step="0.01"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                            required
                        >
                    </div>
                    <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-4 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                        Save
                    </button>
                </form>
            </div>
            <script>
                (() => {
                    const salaryDateInput = document.getElementById('salary_date');
                    const salaryDateField = document.getElementById('salary_date_field');
                    if (!salaryDateInput || !salaryDateField) return;

                    salaryDateField.addEventListener('click', () => {
                        if (typeof salaryDateInput.showPicker === 'function') {
                            try {
                                salaryDateInput.showPicker();
                            } catch (_) {
                                salaryDateInput.focus();
                            }
                        } else {
                            salaryDateInput.focus();
                        }
                    });
                    salaryDateInput.addEventListener('change', () => {
                        salaryDateInput.form?.submit();
                    });
                })();
            </script>
        @elseif ($navPrefix === 'admin' && $page === 'home')
            @php
                $homeRows = $homeCategoryPriceRows ?? collect();
                $homeDate = $homePriceDate ?? now()->toDateString();
                $homeStaffRows = $homeStaffRows ?? collect();
            @endphp
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#161615] p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold">Today category prices</h3>
                        <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $homeDate }}</span>
                    </div>
                    @if ($homeRows->isNotEmpty())
                        <div class="space-y-2">
                            @foreach ($homeRows as $row)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2">
                                    <span class="text-sm font-medium">{{ $row['category'] }}</span>
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ $row['price'] !== null ? number_format((float) $row['price'], 2) : '-' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No categories/prices available for today.</p>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#161615] p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold">Current staff details</h3>
                        <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Active: {{ $homeStaffRows->count() }}</span>
                    </div>
                    @if ($homeStaffRows->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($homeStaffRows as $staff)
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 text-sm">
                                    <span class="font-medium">{{ $staff->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No active staff found.</p>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm">
                This is the <strong>{{ $sectionTitle }}</strong> section. Add your content here.
            </p>
        @endif
    </div>
@endsection
