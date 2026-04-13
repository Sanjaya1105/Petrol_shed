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
        @elseif ($navPrefix === 'admin' && $page === 'pumps')
            <div class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                <h3 class="text-sm font-semibold mb-1">Available pumps</h3>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-3">
                    Each row starts with today’s data. Change the date on a pump to load that day’s staff and readings for that pump only. Starting meter is the previous calendar day’s reading for the same pump.
                </p>
                @if ($pumps !== null && $pumps->isNotEmpty())
                    @php
                        $staffLookup = $staffMembers?->keyBy('id') ?? collect();
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold">Pump</th>
                                    <th class="text-left px-3 py-2 font-semibold">Assign staff</th>
                                    <th class="text-left px-3 py-2 font-semibold">Starting meter reading</th>
                                    <th class="text-left px-3 py-2 font-semibold">Meter reading</th>
                                    <th class="text-left px-3 py-2 font-semibold">Date</th>
                                    <th class="text-left px-3 py-2 font-semibold">Action</th>
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
                                            <form method="post" action="{{ route('admin.pumps.sale.save', $pump) }}" class="space-y-2">
                                                @csrf
                                                <select
                                                    name="staff_id"
                                                    required
                                                    class="js-pump-row-staff w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                >
                                                    <option value="" disabled {{ $selectedDateSale ? '' : 'selected' }}>Select staff</option>
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
                                                    value="{{ $startMeter !== null ? number_format((float) $startMeter, 2, '.', '') : '' }}"
                                                    readonly
                                                    class="js-pump-row-start-meter w-full rounded-md bg-white dark:bg-white text-black dark:text-black px-3 py-2 text-sm outline-none cursor-not-allowed {{ $missingPreviousReading ? 'border-2 border-red-600 ring-1 ring-red-500' : 'border border-gray-300 dark:border-gray-600' }}"
                                                >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                                <input
                                                    type="number"
                                                    name="meter_amount"
                                                    value="{{ $selectedDateSale ? number_format((float) $selectedDateSale->meter_amount, 2, '.', '') : '' }}"
                                                    min="0"
                                                    step="0.01"
                                                    required
                                                    class="js-pump-row-meter w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                                <input
                                                    type="date"
                                                    name="date"
                                                    value="{{ now()->toDateString() }}"
                                                    max="{{ now()->toDateString() }}"
                                                    required
                                                    data-prefill-url="{{ route('admin.pumps.sale.prefill', $pump) }}"
                                                    onclick="this.showPicker && this.showPicker()"
                                                    onfocus="this.showPicker && this.showPicker()"
                                                    class="js-pump-sale-date w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                                                >
                                        </td>
                                        <td class="px-3 py-2 align-top">
                                                <button type="submit" class="rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC] text-white dark:text-[#1b1b18] px-3 py-2 text-sm font-medium hover:opacity-90 transition-opacity">
                                                    Update
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
        @elseif ($navPrefix === 'admin' && $page === 'sales')
            @php
                $salesReportCarbon = \Illuminate\Support\Carbon::parse($salesReportDate ?? now()->subDay());
                $salesPriorMeterCarbon = $salesReportCarbon->copy()->subDay();
                $salesPickerMax = $salesDatePickerMax ?? now()->subDay()->toDateString();
                $salesListUrl = route('admin.show', ['page' => 'sales']);
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
                $staffPdfUrl = route('admin.sales.staff.pdf').$salesPdfQuerySuffix;
                $pumpsPdfUrl = route('admin.sales.pumps.pdf').$salesPdfQuerySuffix;
            @endphp
            <div class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
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
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-[#0a0a0a] px-3 py-2 text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] shadow-sm hover:bg-gray-50 dark:hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-[#161615]"
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

                <fieldset class="mb-4 border border-gray-200 dark:border-gray-700 rounded-md p-3 bg-gray-50/80 dark:bg-gray-900/30">
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
                            class="inline-flex items-center justify-center rounded-md bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-3 py-2 text-sm font-medium hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-[#161615] ml-auto sm:ml-0"
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
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
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
                                        <tr class="border-t border-gray-200 dark:border-gray-700">
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
                            $staffSaleGroups = $staffGroupKeys->map(function ($key) use ($byStaffKey) {
                                $rows = $byStaffKey->get($key)->sortBy('pump_name', SORT_NATURAL)->values();

                                return [
                                    'staff_name' => $rows->first()['staff_name'],
                                    'staff_id_for_row' => $key,
                                    'rows' => $rows,
                                ];
                            });
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
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="text-left px-3 py-2 font-semibold">Staff</th>
                                        <th class="text-left px-3 py-2 font-semibold">Pump</th>
                                        <th class="text-left px-3 py-2 font-semibold">Starting meter (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Ending meter (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Difference (L)</th>
                                        <th class="text-left px-3 py-2 font-semibold">Per liter price</th>
                                        <th class="text-left px-3 py-2 font-semibold">Line total</th>
                                    </tr>
                                </thead>
                                @foreach ($staffSaleGroups as $group)
                                    <tbody
                                        class="border-t border-gray-200 dark:border-gray-700 js-sales-staff-group"
                                        data-staff-id="{{ $group['staff_id_for_row'] }}"
                                    >
                                        @foreach ($group['rows'] as $idx => $entry)
                                            <tr class="border-t border-gray-200 dark:border-gray-700">
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @endforeach
                                @if ($staffPricedCount > 0)
                                    <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                                        <tr>
                                            <td class="px-3 py-2 font-semibold text-right" colspan="6">Grand total</td>
                                            <td class="px-3 py-2 font-semibold">{{ number_format((float) $staffReportGrandTotal, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
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
                @if ($staffMembers !== null && $staffMembers->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach ($staffMembers as $staff)
                            <div class="h-14 border border-gray-200 dark:border-gray-700 rounded-md px-2 py-1 flex items-center justify-between gap-2">
                                <p class="text-sm truncate">{{ $staff->name }}</p>
                                <form method="post" action="{{ route('admin.staff.delete', $staff) }}" onsubmit="return confirm('Delete this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 dark:text-red-400 text-sm leading-none px-1">X</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
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
            <form method="post" action="{{ route($navPrefix.'.prices.save') }}" class="space-y-4 bg-white dark:bg-[#161615] border border-gray-200 dark:border-gray-700 rounded-lg p-4 sm:p-5">
                @csrf
                <input type="hidden" name="price_date" value="{{ old('price_date', $priceFormDate) }}">
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
