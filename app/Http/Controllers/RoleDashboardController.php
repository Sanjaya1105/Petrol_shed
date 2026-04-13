<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Price;
use App\Models\Pump;
use App\Models\Sale;
use App\Models\Staff;
use App\Models\Tank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    /** @var array<string, string> */
    protected const SECTION_TITLES = [
        'home' => 'Home',
        'categories' => 'Categories',
        'pumps' => 'Pumps',
        'tanks' => 'Tanks',
        'price' => 'Price',
        'staff' => 'Staff',
        'sales' => 'Sales',
    ];

    public function showDev(string $page): View
    {
        return $this->renderPage('dev', 'Dev', $page, null);
    }

    public function showAdmin(Request $request, string $page): View
    {
        abort_unless(in_array($page, ['home', 'categories', 'pumps', 'tanks', 'price', 'staff', 'sales'], true), 404);

        return $this->renderPage('admin', 'Admin', $page, $request);
    }

    public function showDataEntry(string $page): View
    {
        return $this->renderPage('data-entry', 'Data-entry', $page, null);
    }

    public function downloadAdminSalesStaffPdf(Request $request)
    {
        $salesData = $this->loadAdminSalesForReport($request);
        $pumps = Pump::query()->orderBy('id')->get();
        $prices = $this->pricesLookupForReportDate($salesData['salesReportDate']);
        $staffMembers = Staff::query()->orderBy('id')->get();

        $staffPdfPayload = $this->buildStaffSalePdfTablePayload(
            $salesData['sales'],
            $salesData['priorDaySalesByPump'],
            $pumps,
            $prices,
            $staffMembers
        );

        $reportCarbon = Carbon::parse($salesData['salesReportDate']);

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = app('dompdf.wrapper');

        return $pdf
            ->loadView('pdf.sales-staff', [
                'reportDate' => $salesData['salesReportDate'],
                'reportDateFormatted' => $reportCarbon->format('l, F j, Y'),
                'priorDateLabel' => $reportCarbon->copy()->subDay()->toDateString(),
                'tableGroups' => $staffPdfPayload['groups'],
                'grandTotalFormatted' => $staffPdfPayload['grandTotalFormatted'],
                'showGrandTotal' => $staffPdfPayload['showGrandTotal'],
                'showReportTable' => $pumps->isNotEmpty(),
            ])
            ->setPaper('a4', 'landscape')
            ->download('sales-staff-'.$salesData['salesReportDate'].'.pdf');
    }

    public function downloadAdminSalesPumpsPdf(Request $request)
    {
        $salesData = $this->loadAdminSalesForReport($request);
        $pumps = Pump::query()->orderBy('id')->get();
        $prices = $this->pricesLookupForReportDate($salesData['salesReportDate']);
        $staffMembers = Staff::query()->orderBy('id')->get();

        $pumpsPdf = $this->buildPumpsSalePdfPayload(
            $salesData['sales'],
            $salesData['priorDaySalesByPump'],
            $pumps,
            $prices,
            $staffMembers
        );

        $reportCarbon = Carbon::parse($salesData['salesReportDate']);

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = app('dompdf.wrapper');

        return $pdf
            ->loadView('pdf.sales-pumps', [
                'reportDate' => $salesData['salesReportDate'],
                'reportDateFormatted' => $reportCarbon->format('l, F j, Y'),
                'priorDateLabel' => $reportCarbon->copy()->subDay()->toDateString(),
                'rows' => $pumpsPdf['rows'],
                'grandTotalFormatted' => $pumpsPdf['grandTotalFormatted'],
                'showGrandTotal' => $pumpsPdf['showGrandTotal'],
                'showReportTable' => $pumps->isNotEmpty(),
            ])
            ->setPaper('a4', 'landscape')
            ->download('sales-pumps-'.$salesData['salesReportDate'].'.pdf');
    }

    public function storeDevCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
        ]);

        Category::query()->create([
            'category' => $validated['category'],
        ]);

        return redirect()
            ->route('dev.show', ['page' => 'categories'])
            ->with('status', 'Category saved successfully.');
    }

    public function updateDevCategory(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
        ]);

        $category->update([
            'category' => $validated['category'],
        ]);

        return redirect()
            ->route('dev.show', ['page' => 'categories'])
            ->with('status', 'Category updated successfully.');
    }

    public function deleteDevCategory(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('dev.show', ['page' => 'categories'])
            ->with('status', 'Category deleted successfully.');
    }

    public function storeDevTank(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tank_name' => ['required', 'string', 'max:255'],
            'tank_capacity' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:category,id'],
        ]);

        Tank::query()->create([
            'tank_name' => $validated['tank_name'],
            'tank_capacity' => $validated['tank_capacity'],
            'category_id' => $validated['category_id'],
        ]);

        return redirect()
            ->route('dev.show', ['page' => 'tanks'])
            ->with('status', 'Tank saved successfully.');
    }

    public function updateDevTank(Request $request, Tank $tank): RedirectResponse
    {
        $validated = $request->validate([
            'tank_name' => ['required', 'string', 'max:255'],
            'tank_capacity' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:category,id'],
        ]);

        $tank->update([
            'tank_name' => $validated['tank_name'],
            'tank_capacity' => $validated['tank_capacity'],
            'category_id' => $validated['category_id'],
        ]);

        return redirect()
            ->route('dev.show', ['page' => 'tanks'])
            ->with('status', 'Tank updated successfully.')
            ->with('updated_tank_id', $tank->id)
            ->with('updated_tank_name', $tank->tank_name);
    }

    public function deleteDevTank(Tank $tank): RedirectResponse
    {
        $tank->delete();

        return redirect()
            ->route('dev.show', ['page' => 'tanks'])
            ->with('status', 'Tank deleted successfully.');
    }

    public function storeDevPump(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pump_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:category,id'],
            'tank_id' => [
                'required',
                'integer',
                Rule::exists('tanks', 'id')->where(function ($query) use ($request) {
                    $query->where('category_id', $request->input('category_id'));
                }),
            ],
        ]);

        Pump::query()->create([
            'pump_name' => $validated['pump_name'],
            'category_id' => $validated['category_id'],
            'tank_id' => $validated['tank_id'],
        ]);

        return redirect()
            ->route('dev.show', ['page' => 'pumps'])
            ->with('status', 'Pump saved successfully.');
    }

    public function updateDevPump(Request $request, Pump $pump): RedirectResponse
    {
        $validated = $request->validate([
            'pump_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:category,id'],
            'tank_id' => [
                'required',
                'integer',
                Rule::exists('tanks', 'id')->where(function ($query) use ($request) {
                    $query->where('category_id', $request->input('category_id'));
                }),
            ],
        ]);

        $pump->update([
            'pump_name' => $validated['pump_name'],
            'category_id' => $validated['category_id'],
            'tank_id' => $validated['tank_id'],
        ]);

        return redirect()
            ->route('dev.show', ['page' => 'pumps'])
            ->with('status', 'Pump updated successfully.');
    }

    public function deleteDevPump(Pump $pump): RedirectResponse
    {
        $pump->delete();

        return redirect()
            ->route('dev.show', ['page' => 'pumps'])
            ->with('status', 'Pump deleted successfully.');
    }

    public function saveDevPrices(Request $request): RedirectResponse
    {
        return $this->saveRolePrices($request, 'dev');
    }

    public function saveAdminPrices(Request $request): RedirectResponse
    {
        return $this->saveRolePrices($request, 'admin');
    }

    public function storeAdminStaff(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $inputName = trim($validated['name']);
        $existingStaff = Staff::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($inputName)])
            ->first();

        if ($existingStaff !== null) {
            $existingStaff->update([
                'name' => $inputName,
                'is_active' => true,
            ]);

            return redirect()
                ->route('admin.show', ['page' => 'staff'])
                ->with('status', 'Staff re-activated successfully.');
        }

        Staff::query()->create([
            'name' => $inputName,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.show', ['page' => 'staff'])
            ->with('status', 'Staff added successfully.');
    }

    public function deleteAdminStaff(Staff $staff): RedirectResponse
    {
        $staff->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('admin.show', ['page' => 'staff'])
            ->with('status', 'Staff removed successfully.');
    }

    public function saveAdminPumpSale(Request $request, Pump $pump): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'integer', Rule::exists('staff', 'id')->where('is_active', true)],
            'meter_amount' => ['required', 'numeric', 'min:0'],
            'date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $saleDate = $validated['date'] ?? now()->toDateString();
        $newMeterAmount = (float) $validated['meter_amount'];

        DB::transaction(function () use ($pump, $saleDate, $validated, $newMeterAmount): void {
            $tank = Tank::query()->lockForUpdate()->find($pump->tank_id);
            if ($tank === null) {
                throw ValidationException::withMessages([
                    'meter_amount' => 'Selected pump is not linked to a tank.',
                ]);
            }

            $sale = Sale::query()
                ->where('pump_id', $pump->id)
                ->where('date', $saleDate)
                ->first();

            $previousMeterAmount = $sale ? (float) $sale->meter_amount : 0.0;
            $consumptionDelta = $newMeterAmount - $previousMeterAmount;
            $currentAvailable = (float) ($tank->available_amount ?? 0);
            $updatedAvailable = $currentAvailable - $consumptionDelta;

            $tank->update([
                'available_amount' => $updatedAvailable,
            ]);

            Sale::query()->updateOrCreate(
                [
                    'pump_id' => $pump->id,
                    'date' => $saleDate,
                ],
                [
                    'staff_id' => $validated['staff_id'],
                    'meter_amount' => $newMeterAmount,
                    'date' => $saleDate,
                ]
            );
        });

        return redirect()
            ->route('admin.show', ['page' => 'pumps'])
            ->with('status', 'Pump sale details saved successfully.');
    }

    public function prefillAdminPumpSale(Request $request, Pump $pump): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $saleDate = Carbon::parse($validated['date'])->toDateString();
        $priorDate = Carbon::parse($saleDate)->subDay()->toDateString();

        $sale = Sale::query()
            ->where('pump_id', $pump->id)
            ->where('date', $saleDate)
            ->orderByDesc('id')
            ->first();

        $priorSale = Sale::query()
            ->where('pump_id', $pump->id)
            ->where('date', $priorDate)
            ->orderByDesc('id')
            ->first();

        $startMeter = $priorSale?->meter_amount;
        $missingPrevious = $priorSale === null || $startMeter === null || $startMeter === '';

        return response()->json([
            'staff_id' => $sale?->staff_id,
            'meter_amount' => $sale ? number_format((float) $sale->meter_amount, 2, '.', '') : '',
            'starting_meter' => ($startMeter !== null && $startMeter !== '') ? number_format((float) $startMeter, 2, '.', '') : '',
            'missing_previous' => $missingPrevious,
        ]);
    }

    public function restockAdminTank(Request $request, Tank $tank): RedirectResponse
    {
        $validated = $request->validate([
            'restock_amount' => [
                'required',
                'numeric',
                'gt:0',
                'lte:'.$tank->tank_capacity,
            ],
        ]);

        $tank->update([
            'available_amount' => (float) $validated['restock_amount'],
        ]);

        return redirect()
            ->route('admin.show', ['page' => 'tanks'])
            ->with('status', 'Tank restocked successfully.');
    }

    protected function renderPage(string $navPrefix, string $heading, string $page, ?Request $request = null): View
    {
        $request ??= request();

        abort_unless(array_key_exists($page, self::SECTION_TITLES), 404);

        $categories = null;
        if (
            ($navPrefix === 'dev' && in_array($page, ['categories', 'tanks', 'pumps', 'price'], true)) ||
            ($navPrefix === 'admin' && $page === 'price')
        ) {
            $categories = Category::query()->orderBy('id')->get();
        }

        $tanks = null;
        if (
            ($navPrefix === 'dev' && in_array($page, ['tanks', 'pumps'], true)) ||
            ($navPrefix === 'admin' && in_array($page, ['tanks', 'pumps'], true))
        ) {
            $tanks = Tank::query()->orderBy('id')->get();
        }

        $prices = null;
        $priceFormDate = null;
        $priceDateMax = null;
        if (in_array($navPrefix, ['dev', 'admin'], true) && $page === 'price') {
            $priceFormDate = $this->resolvePriceFormDate($request);
            $priceDateMax = now()->toDateString();
            $prices = $this->pricesForFormDate($priceFormDate);
        }

        $pumps = null;
        if (
            ($navPrefix === 'dev' && $page === 'pumps') ||
            ($navPrefix === 'admin' && in_array($page, ['pumps', 'tanks', 'sales'], true))
        ) {
            $pumps = Pump::query()->orderBy('id')->get();
        }

        $staffMembers = null;
        if ($navPrefix === 'admin' && in_array($page, ['staff', 'pumps'], true)) {
            $staffMembers = Staff::query()
                ->where('is_active', true)
                ->orderBy('id')
                ->get();
        }
        if ($navPrefix === 'admin' && $page === 'sales') {
            $staffMembers = Staff::query()
                ->orderBy('id')
                ->get();
        }

        $sales = null;
        $startingMeters = null;
        if ($navPrefix === 'admin' && $page === 'pumps') {
            $today = now()->toDateString();
            $priorDate = Carbon::parse($today)->subDay()->toDateString();

            $sales = Sale::query()
                ->whereDate('date', $today)
                ->orderByDesc('id')
                ->get()
                ->keyBy('pump_id');

            $startingMeters = Sale::query()
                ->whereDate('date', $priorDate)
                ->orderByDesc('id')
                ->get()
                ->keyBy('pump_id');
        }

        $priorDaySalesByPump = null;
        $salesReportDate = null;
        $salesDatePickerMax = null;
        $salesView = 'staff';
        if ($navPrefix === 'admin' && $page === 'sales') {
            $yesterday = now()->subDay()->toDateString();
            $salesDatePickerMax = $yesterday;

            $salesView = $request->query('sales_view') === 'pumps' ? 'pumps' : 'staff';

            $salesData = $this->loadAdminSalesForReport($request);
            $salesReportDate = $salesData['salesReportDate'];
            $sales = $salesData['sales'];
            $priorDaySalesByPump = $salesData['priorDaySalesByPump'];
            $prices = $this->pricesLookupForReportDate($salesReportDate);
        }

        return view('dashboard.page', [
            'navPrefix' => $navPrefix,
            'heading' => $heading,
            'page' => $page,
            'sectionTitle' => self::SECTION_TITLES[$page],
            'categories' => $categories,
            'tanks' => $tanks,
            'prices' => $prices,
            'priceFormDate' => $priceFormDate,
            'priceDateMax' => $priceDateMax,
            'pumps' => $pumps,
            'staffMembers' => $staffMembers,
            'sales' => $sales,
            'startingMeters' => $startingMeters,
            'priorDaySalesByPump' => $priorDaySalesByPump,
            'salesReportDate' => $salesReportDate,
            'salesDatePickerMax' => $salesDatePickerMax,
            'salesView' => $salesView,
        ]);
    }

    /**
     * @return array{salesReportDate: string, sales: Collection<int, Sale>, priorDaySalesByPump: Collection<(int|string), Sale>}
     */
    private function loadAdminSalesForReport(Request $request): array
    {
        $yesterday = now()->subDay()->toDateString();
        $salesReportDate = $yesterday;
        $picked = $request->query('sales_date');
        if (is_string($picked) && $picked !== '') {
            try {
                $candidate = Carbon::parse($picked)->startOfDay();
                if ($candidate->lt(now()->startOfDay())) {
                    $salesReportDate = $candidate->toDateString();
                }
            } catch (\Throwable) {
                // keep default (yesterday)
            }
        }

        $priorCalendarDate = Carbon::parse($salesReportDate)->subDay()->toDateString();

        $sales = Sale::query()
            ->whereDate('date', $salesReportDate)
            ->orderByDesc('id')
            ->get();

        $priorDaySalesByPump = Sale::query()
            ->whereDate('date', $priorCalendarDate)
            ->orderByDesc('id')
            ->get()
            ->keyBy('pump_id');

        return [
            'salesReportDate' => $salesReportDate,
            'sales' => $sales,
            'priorDaySalesByPump' => $priorDaySalesByPump,
        ];
    }

    /**
     * Staff-sale PDF: one block per staff (rowspan), sub-rows per pump — mirrors on-screen Staff sale.
     *
     * @return array{groups: list<array{staff: string, lines: list<array<string, string>>}>, grandTotalFormatted: string|null, showGrandTotal: bool}
     */
    private function buildStaffSalePdfTablePayload(
        Collection $sales,
        Collection $priorDaySalesByPump,
        Collection $pumps,
        Collection $prices,
        Collection $staffMembers,
    ): array {
        if ($pumps->isEmpty()) {
            return [
                'groups' => [],
                'grandTotalFormatted' => null,
                'showGrandTotal' => false,
            ];
        }

        $priorByPump = $priorDaySalesByPump;
        $salesByPumpId = $sales->keyBy(fn ($s) => (string) $s->pump_id);

        /** @var list<array{staff_id_key: string, staff: string, pump_name: string, starting: string, ending: string, difference: string, price: string, line_total: string}> $flat */
        $flat = [];
        $grandSum = 0.0;
        $pricedAny = false;

        foreach ($pumps->sortBy('pump_name', SORT_NATURAL)->values() as $pump) {
            $sale = $salesByPumpId->get((string) $pump->id);
            $categoryPrice = $prices->get($pump->category_id);

            if ($sale === null) {
                $flat[] = [
                    'staff_id_key' => '',
                    'staff' => '—',
                    'pump_name' => $pump->pump_name,
                    'starting' => '-',
                    'ending' => '-',
                    'difference' => '-',
                    'price' => $categoryPrice !== null ? number_format((float) $categoryPrice, 2) : '-',
                    'line_total' => '-',
                ];

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

            if ($lineTotal !== null) {
                $grandSum += (float) $lineTotal;
                $pricedAny = true;
            }

            $staffMember = $staffMembers->firstWhere('id', $sale->staff_id);
            if ($staffMember !== null) {
                $staffName = ($staffMember->is_active ?? true)
                    ? $staffMember->name
                    : $staffMember->name.' (Removed)';
            } else {
                $staffName = $sale->staff_id
                    ? 'Removed staff (ID: '.$sale->staff_id.')'
                    : '—';
            }

            $flat[] = [
                'staff_id_key' => (string) ($sale->staff_id ?? ''),
                'staff' => $staffName,
                'pump_name' => $pump->pump_name,
                'starting' => $starting !== null && $starting !== ''
                    ? number_format((float) $starting, 2)
                    : '-',
                'ending' => $ending !== null && $ending !== ''
                    ? number_format((float) $ending, 2)
                    : '-',
                'difference' => $diff !== null ? number_format((float) $diff, 2) : '-',
                'price' => $categoryPrice !== null ? number_format((float) $categoryPrice, 2) : '-',
                'line_total' => $lineTotal !== null ? number_format((float) $lineTotal, 2) : '-',
            ];
        }

        $grouped = collect($flat)->groupBy('staff_id_key');
        $sortedKeys = $grouped->keys()->sort(function ($a, $b) use ($grouped) {
            $aEmpty = $a === '';
            $bEmpty = $b === '';
            if ($aEmpty !== $bEmpty) {
                return $aEmpty ? 1 : -1;
            }
            $nameA = (string) ($grouped->get($a)->first()['staff'] ?? '');
            $nameB = (string) ($grouped->get($b)->first()['staff'] ?? '');

            return strnatcasecmp($nameA, $nameB);
        })->values();

        $groups = [];
        foreach ($sortedKeys as $key) {
            $items = $grouped->get($key)->sortBy('pump_name', SORT_NATURAL)->values();
            $lines = [];
            foreach ($items as $row) {
                $lines[] = [
                    'pump_name' => $row['pump_name'],
                    'starting' => $row['starting'],
                    'ending' => $row['ending'],
                    'difference' => $row['difference'],
                    'price' => $row['price'],
                    'line_total' => $row['line_total'],
                ];
            }
            $groups[] = [
                'staff' => $items->first()['staff'],
                'lines' => $lines,
            ];
        }

        return [
            'groups' => $groups,
            'grandTotalFormatted' => $pricedAny ? number_format($grandSum, 2) : null,
            'showGrandTotal' => $pricedAny,
        ];
    }

    /**
     * @return array{rows: list<array<string, string>>, grandTotalFormatted: string|null, showGrandTotal: bool}
     */
    private function buildPumpsSalePdfPayload(
        Collection $sales,
        Collection $priorDaySalesByPump,
        Collection $pumps,
        Collection $prices,
        Collection $staffMembers,
    ): array {
        if ($pumps->isEmpty()) {
            return [
                'rows' => [],
                'grandTotalFormatted' => null,
                'showGrandTotal' => false,
            ];
        }

        $priorByPump = $priorDaySalesByPump;
        $salesByPumpId = $sales->keyBy(fn ($s) => (string) $s->pump_id);

        $grandSum = 0.0;
        $pricedAny = false;
        $rows = [];

        foreach ($pumps->sortBy('pump_name', SORT_NATURAL)->values() as $pump) {
            $sale = $salesByPumpId->get((string) $pump->id);
            $categoryPrice = $prices->get($pump->category_id);

            if ($sale === null) {
                $rows[] = [
                    'pump_name' => $pump->pump_name,
                    'staff_name' => '—',
                    'starting' => '-',
                    'ending' => '-',
                    'difference' => '-',
                    'price' => $categoryPrice !== null ? number_format((float) $categoryPrice, 2) : '-',
                    'line_total' => '-',
                ];

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

            if ($lineTotal !== null) {
                $grandSum += (float) $lineTotal;
                $pricedAny = true;
            }

            $staffMember = $staffMembers->firstWhere('id', $sale->staff_id);
            if ($staffMember !== null) {
                $staffName = ($staffMember->is_active ?? true)
                    ? $staffMember->name
                    : $staffMember->name.' (Removed)';
            } else {
                $staffName = $sale->staff_id
                    ? 'Removed staff (ID: '.$sale->staff_id.')'
                    : '—';
            }

            $rows[] = [
                'pump_name' => $pump->pump_name,
                'staff_name' => $staffName,
                'starting' => $starting !== null && $starting !== ''
                    ? number_format((float) $starting, 2)
                    : '-',
                'ending' => $ending !== null && $ending !== ''
                    ? number_format((float) $ending, 2)
                    : '-',
                'difference' => $diff !== null ? number_format((float) $diff, 2) : '-',
                'price' => $categoryPrice !== null ? number_format((float) $categoryPrice, 2) : '-',
                'line_total' => $lineTotal !== null ? number_format((float) $lineTotal, 2) : '-',
            ];
        }

        return [
            'rows' => $rows,
            'grandTotalFormatted' => $pricedAny ? number_format($grandSum, 2) : null,
            'showGrandTotal' => $pricedAny,
        ];
    }

    /**
     * Price form date from query (?price_date=) — defaults to today; cannot be in the future.
     */
    private function resolvePriceFormDate(Request $request): string
    {
        $today = now()->startOfDay();
        $default = $today->toDateString();
        $raw = $request->query('price_date');
        if (! is_string($raw) || $raw === '') {
            return $default;
        }
        try {
            $picked = Carbon::parse($raw)->startOfDay();
        } catch (\Throwable) {
            return $default;
        }
        if ($picked->gt($today)) {
            return $default;
        }

        return $picked->toDateString();
    }

    /**
     * Prices saved for the given calendar date (one row per category per date).
     *
     * @return Collection<int|string, mixed>
     */
    private function pricesForFormDate(string $date): Collection
    {
        return Price::query()
            ->whereDate('date', $date)
            ->pluck('price', 'category_id');
    }

    /**
     * Latest price per category in effect on the given report date (most recent row with date &lt;= report date).
     *
     * @return Collection<int|string, mixed>
     */
    private function pricesLookupForReportDate(string $reportDate): Collection
    {
        $latestPerCategory = DB::table('prices')
            ->select('category_id', DB::raw('MAX(date) as max_date'))
            ->whereDate('date', '<=', $reportDate)
            ->groupBy('category_id');

        return Price::query()
            ->joinSub($latestPerCategory, 't', function ($join) {
                $join->on('prices.category_id', '=', 't.category_id')
                    ->on('prices.date', '=', 't.max_date');
            })
            ->pluck('prices.price', 'prices.category_id');
    }

    private function saveRolePrices(Request $request, string $rolePrefix): RedirectResponse
    {
        $validated = $request->validate([
            'price_date' => ['required', 'date', 'before_or_equal:today'],
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $categoryIds = Category::query()->pluck('id')->all();
        $inputPrices = $validated['prices'] ?? [];
        $date = Carbon::parse($validated['price_date'])->toDateString();

        foreach ($categoryIds as $categoryId) {
            $value = $inputPrices[$categoryId] ?? null;

            if ($value === null || $value === '') {
                Price::query()
                    ->where('category_id', $categoryId)
                    ->whereDate('date', $date)
                    ->delete();

                continue;
            }

            Price::query()->updateOrCreate(
                [
                    'category_id' => $categoryId,
                    'date' => $date,
                ],
                ['price' => $value]
            );
        }

        $back = route($rolePrefix.'.show', ['page' => 'price']).'?'.http_build_query(['price_date' => $date]);

        return redirect()
            ->to($back)
            ->with('status', 'Prices saved successfully.');
    }
}
