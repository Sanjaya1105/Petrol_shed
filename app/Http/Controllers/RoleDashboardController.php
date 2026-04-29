<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Bill;
use App\Models\BillReset;
use App\Models\CashCollection;
use App\Models\Company;
use App\Models\GasPrice;
use App\Models\GasDetail;
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
use Illuminate\Support\Facades\Schema;
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
        'cash-rec' => 'Cash Rec',
        'bill' => 'Bill',
        'gas' => 'Gas',
        'theme' => 'Theme',
    ];

    public function showDev(string $page): View
    {
        return $this->renderPage('dev', 'Dev', $page, null);
    }

    public function showAdmin(Request $request, string $page): View
    {
        abort_unless(in_array($page, ['home', 'categories', 'pumps', 'tanks', 'price', 'staff', 'sales', 'cash-rec', 'bill', 'gas'], true), 404);

        return $this->renderPage('admin', 'Admin', $page, $request);
    }

    public function showDataEntry(Request $request, string $page): View
    {
        return $this->renderPage('data-entry', 'Data-entry', $page, $request);
    }

    public function downloadAdminSalesStaffPdf(Request $request)
    {
        $salesData = $this->loadAdminSalesForReport($request);
        $pumps = Pump::query()->orderBy('id')->get();
        $prices = $this->pricesLookupForReportDate($salesData['salesReportDate']);
        $staffMembers = Staff::query()->orderBy('id')->get();
        $cashByStaffId = CashCollection::query()
            ->whereDate('date', $salesData['salesReportDate'])
            ->where('category', 'cash')
            ->select('staff_id', DB::raw('SUM(cash_total) as total_cash'))
            ->groupBy('staff_id')
            ->pluck('total_cash', 'staff_id');
        $cashCategoryTotalsByStaff = CashCollection::query()
            ->whereDate('date', $salesData['salesReportDate'])
            ->select('staff_id', 'category', DB::raw('SUM(cash_total) as total_cash'))
            ->groupBy('staff_id', 'category')
            ->get()
            ->groupBy('staff_id')
            ->map(fn ($rows) => $rows->pluck('total_cash', 'category'));
        $billAmountByStaffId = Bill::query()
            ->whereDate('date', $salesData['salesReportDate'])
            ->select('staff_id', DB::raw('SUM(bill_value) as total_bills'))
            ->groupBy('staff_id')
            ->pluck('total_bills', 'staff_id');
        $gasAmountByStaffId = $this->gasAmountByStaffForDate($salesData['salesReportDate']);

        $staffPdfPayload = $this->buildStaffSalePdfTablePayload(
            $salesData['sales'],
            $salesData['priorDaySalesByPump'],
            $pumps,
            $prices,
            $staffMembers,
            $cashByStaffId,
            $cashCategoryTotalsByStaff,
            $billAmountByStaffId,
            $gasAmountByStaffId
        );

        $reportCarbon = Carbon::parse($salesData['salesReportDate']);
        $logoDataUri = $this->salesPdfLogoDataUri();

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
                'logoDataUri' => $logoDataUri,
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
        $logoDataUri = $this->salesPdfLogoDataUri();

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
                'logoDataUri' => $logoDataUri,
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

    public function saveAdminCashRec(Request $request): RedirectResponse
    {
        return $this->saveRoleCashRec($request, 'admin');
    }

    public function saveDataEntryCashRec(Request $request): RedirectResponse
    {
        return $this->saveRoleCashRec($request, 'data-entry');
    }

    public function deleteAdminCashRec(Request $request, CashCollection $cashCollection): RedirectResponse
    {
        return $this->deleteRoleCashRec($request, $cashCollection, 'admin');
    }

    public function deleteDataEntryCashRec(Request $request, CashCollection $cashCollection): RedirectResponse
    {
        return $this->deleteRoleCashRec($request, $cashCollection, 'data-entry');
    }

    public function saveAdminCompany(Request $request): RedirectResponse
    {
        return $this->saveRoleCompany($request, 'admin');
    }

    public function saveDataEntryCompany(Request $request): RedirectResponse
    {
        return $this->saveRoleCompany($request, 'data-entry');
    }

    public function saveAdminBill(Request $request): RedirectResponse
    {
        return $this->saveRoleBill($request, 'admin');
    }

    public function saveDataEntryBill(Request $request): RedirectResponse
    {
        return $this->saveRoleBill($request, 'data-entry');
    }

    public function getAdminBillCategoryPrice(Request $request): JsonResponse
    {
        return $this->getRoleBillCategoryPrice($request);
    }

    public function getDataEntryBillCategoryPrice(Request $request): JsonResponse
    {
        return $this->getRoleBillCategoryPrice($request);
    }

    public function updateAdminCompany(Request $request, Company $company): RedirectResponse
    {
        return $this->updateRoleCompany($request, $company, 'admin');
    }

    public function updateDataEntryCompany(Request $request, Company $company): RedirectResponse
    {
        return $this->updateRoleCompany($request, $company, 'data-entry');
    }

    public function deleteAdminCompany(Company $company): RedirectResponse
    {
        return $this->deleteRoleCompany($company, 'admin');
    }

    public function deleteDataEntryCompany(Company $company): RedirectResponse
    {
        return $this->deleteRoleCompany($company, 'data-entry');
    }

    public function downloadAdminCompanyBillsPdf(Company $company)
    {
        return $this->downloadRoleCompanyBillsPdf($company);
    }

    public function downloadDataEntryCompanyBillsPdf(Company $company)
    {
        return $this->downloadRoleCompanyBillsPdf($company);
    }

    public function settleAdminCompany(Request $request, Company $company): RedirectResponse
    {
        return $this->settleRoleCompany($request, $company, 'admin');
    }

    public function settleDataEntryCompany(Request $request, Company $company): RedirectResponse
    {
        return $this->settleRoleCompany($request, $company, 'data-entry');
    }

    public function saveAdminGasDetails(Request $request): RedirectResponse
    {
        return $this->saveRoleGasDetails($request, 'admin');
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

    public function deleteAdminStaff(Request $request, Staff $staff): RedirectResponse
    {
        $staff->update([
            'is_active' => false,
        ]);

        $query = array_filter([
            'staff_page' => $request->input('staff_page'),
        ], fn ($v) => is_string($v) ? $v !== '' : $v !== null);

        $back = route('admin.show', ['page' => 'staff']);
        if (count($query) > 0) {
            $back .= '?'.http_build_query($query);
        }

        return redirect()
            ->to($back)
            ->with('status', 'Staff removed successfully.');
    }

    public function saveAdminPumpSale(Request $request, Pump $pump): RedirectResponse
    {
        return $this->saveRolePumpSale($request, $pump, 'admin');
    }

    public function saveDataEntryPumpSale(Request $request, Pump $pump): RedirectResponse
    {
        return $this->saveRolePumpSale($request, $pump, 'data-entry');
    }

    public function saveAdminPumpSalesBulk(Request $request): RedirectResponse
    {
        return $this->saveRolePumpSalesBulk($request, 'admin');
    }

    public function saveDataEntryPumpSalesBulk(Request $request): RedirectResponse
    {
        return $this->saveRolePumpSalesBulk($request, 'data-entry');
    }

    private function saveRolePumpSale(Request $request, Pump $pump, string $rolePrefix): RedirectResponse
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
            ->route($rolePrefix.'.show', ['page' => 'pumps'])
            ->with('status', 'Pump sale details saved successfully.');
    }

    private function saveRolePumpSalesBulk(Request $request, string $rolePrefix): RedirectResponse
    {
        $validated = $request->validate([
            'pumps' => ['required', 'array'],
            'pumps.*.staff_id' => ['nullable', 'integer', Rule::exists('staff', 'id')->where('is_active', true)],
            'pumps.*.meter_amount' => ['nullable', 'numeric', 'min:0'],
            'pumps.*.date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $rows = $validated['pumps'] ?? [];
        $processed = 0;

        DB::transaction(function () use ($rows, &$processed): void {
            $pumpIds = collect(array_keys($rows))
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->values()
                ->all();

            if ($pumpIds === []) {
                return;
            }

            /** @var Collection<int, Pump> $pumps */
            $pumps = Pump::query()
                ->whereIn('id', $pumpIds)
                ->get()
                ->keyBy('id');

            foreach ($rows as $pumpIdRaw => $row) {
                $pumpId = (int) $pumpIdRaw;
                $pump = $pumps->get($pumpId);
                if (! $pump instanceof Pump) {
                    continue;
                }

                $staffId = isset($row['staff_id']) && $row['staff_id'] !== '' ? (int) $row['staff_id'] : null;
                $meterAmount = isset($row['meter_amount']) && $row['meter_amount'] !== '' ? (float) $row['meter_amount'] : null;
                $saleDate = isset($row['date']) && $row['date'] !== ''
                    ? Carbon::parse((string) $row['date'])->toDateString()
                    : now()->toDateString();

                // Skip untouched rows; bulk form should not force every row update.
                if ($staffId === null && $meterAmount === null) {
                    continue;
                }
                if ($staffId === null || $meterAmount === null) {
                    throw ValidationException::withMessages([
                        'pumps.'.$pumpId.'.meter_amount' => 'Both staff and meter reading are required for updated rows.',
                    ]);
                }

                $tank = Tank::query()->lockForUpdate()->find($pump->tank_id);
                if ($tank === null) {
                    throw ValidationException::withMessages([
                        'pumps.'.$pumpId.'.meter_amount' => 'Selected pump is not linked to a tank.',
                    ]);
                }

                $sale = Sale::query()
                    ->where('pump_id', $pump->id)
                    ->where('date', $saleDate)
                    ->first();

                $previousMeterAmount = $sale ? (float) $sale->meter_amount : 0.0;
                $consumptionDelta = $meterAmount - $previousMeterAmount;
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
                        'staff_id' => $staffId,
                        'meter_amount' => $meterAmount,
                        'date' => $saleDate,
                    ]
                );
                $processed++;
            }
        });

        return redirect()
            ->route($rolePrefix.'.show', ['page' => 'pumps'])
            ->with('status', $processed > 0 ? 'Pump sale details saved successfully.' : 'No pump rows were updated.');
    }

    public function prefillAdminPumpSale(Request $request, Pump $pump): JsonResponse
    {
        return $this->prefillRolePumpSale($request, $pump);
    }

    public function prefillDataEntryPumpSale(Request $request, Pump $pump): JsonResponse
    {
        return $this->prefillRolePumpSale($request, $pump);
    }

    private function prefillRolePumpSale(Request $request, Pump $pump): JsonResponse
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
            'meter_amount' => $sale ? number_format((float) $sale->meter_amount, 5, '.', '') : '',
            'starting_meter' => ($startMeter !== null && $startMeter !== '') ? number_format((float) $startMeter, 5, '.', '') : '',
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
        $gasPrices = collect();
        $gasDetails = collect();
        $gasPreviousDetails = collect();
        $gasFormDate = null;
        $priceFormDate = null;
        $priceDateMax = null;
        $homePriceDate = null;
        $homeCategoryPriceRows = collect();
        $homeStaffRows = collect();
        if (in_array($navPrefix, ['dev', 'admin'], true) && in_array($page, ['price', 'gas'], true)) {
            $priceFormDate = $this->resolvePriceFormDate($request);
            $priceDateMax = now()->toDateString();
            if ($page === 'price') {
                $prices = $this->pricesForFormDate($priceFormDate);
                $gasPrices = $this->gasPricesForFormDate($priceFormDate);
            } else {
                // On Gas page, use latest available price up to selected date.
                $gasPrices = $this->gasPricesLookupForReportDate($priceFormDate);
            }
            if ($page === 'gas') {
                $gasFormDate = $this->resolveGasFormDate($request);
                $gasDetails = $this->gasDetailsForFormDate($gasFormDate);
                $previousDate = Carbon::parse($gasFormDate)->subDay()->toDateString();
                $gasPreviousDetails = $this->gasDetailsForFormDate($previousDate);
            }
        }
        if ($navPrefix === 'admin' && $page === 'home') {
            $homePriceDate = now()->toDateString();
            $categories = Category::query()->orderBy('id')->get();
            $todayPrices = $this->pricesLookupForReportDate($homePriceDate);
            $homeCategoryPriceRows = $categories->map(function ($category) use ($todayPrices) {
                $price = $todayPrices->get($category->id);
                if ($price === null) {
                    $price = $todayPrices->get((string) $category->id);
                }

                return [
                    'id' => $category->id,
                    'category' => $category->category,
                    'price' => $price !== null ? (float) $price : null,
                ];
            });
            $homeStaffRows = Staff::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $pumps = null;
        if (
            ($navPrefix === 'dev' && in_array($page, ['pumps', 'sales'], true)) ||
            (in_array($navPrefix, ['admin', 'data-entry'], true) && in_array($page, ['pumps', 'tanks', 'sales'], true))
        ) {
            $pumpQuery = Pump::query();
            if ($page === 'pumps') {
                $pumpQuery->select('pumps.*');
            } else {
                $pumpQuery->orderBy('id');
            }
            $pumps = $pumpQuery->get();
            if ($page === 'pumps' && $pumps->isNotEmpty()) {
                $categoryNamesById = Category::query()
                    ->pluck('category', 'id')
                    ->map(fn ($name) => is_string($name) ? strtolower(trim($name)) : '');
                $categoryRank = [
                    'diesel' => 1,
                    'superdiesel' => 2,
                    'petrol' => 3,
                    'superpetrol' => 4,
                    'kerosene' => 999,
                ];
                $extractNumericSuffix = static function (?string $name): int {
                    if (! is_string($name) || $name === '') {
                        return PHP_INT_MAX;
                    }
                    if (preg_match('/(\d+)\s*$/', $name, $m) === 1) {
                        return (int) $m[1];
                    }

                    return PHP_INT_MAX;
                };

                $pumps = $pumps->sort(function ($a, $b) use ($categoryNamesById, $categoryRank, $extractNumericSuffix) {
                    $catA = (string) ($categoryNamesById->get($a->category_id) ?? '');
                    $catB = (string) ($categoryNamesById->get($b->category_id) ?? '');
                    $normA = str_replace(['-', ' '], '', $catA);
                    $normB = str_replace(['-', ' '], '', $catB);

                    $rankA = $categoryRank[$normA] ?? 5;
                    $rankB = $categoryRank[$normB] ?? 5;
                    if ($rankA !== $rankB) {
                        return $rankA <=> $rankB;
                    }

                    if ($normA === 'petrol' && $normB === 'petrol') {
                        $numA = $extractNumericSuffix($a->pump_name);
                        $numB = $extractNumericSuffix($b->pump_name);
                        if ($numA !== $numB) {
                            return $numA <=> $numB;
                        }
                    }

                    return strnatcasecmp((string) $a->pump_name, (string) $b->pump_name);
                })->values();
            }
        }

        $staffMembers = null;
        if ($navPrefix === 'admin' && $page === 'staff') {
            $staffMembers = Staff::query()
                ->where('is_active', true)
                ->orderBy('id')
                ->paginate(10, ['*'], 'staff_page')
                ->withQueryString();
        } elseif ($navPrefix === 'admin' && $page === 'pumps') {
            $staffMembers = Staff::query()
                ->where('is_active', true)
                ->orderBy('id')
                ->get();
        } elseif ($navPrefix === 'admin' && $page === 'gas') {
            $staffMembers = Staff::query()
                ->where('is_active', true)
                ->orderBy('id')
                ->get();
        }
        if (in_array($navPrefix, ['admin', 'dev', 'data-entry'], true) && $page === 'sales') {
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
        $cashByStaffId = collect();
        $cashCategoryTotalsByStaff = collect();
        $billAmountByStaffId = collect();
        $gasAmountByStaffId = collect();
        if (in_array($navPrefix, ['admin', 'dev', 'data-entry'], true) && $page === 'sales') {
            $yesterday = now()->subDay()->toDateString();
            $salesDatePickerMax = $yesterday;

            $salesView = $request->query('sales_view') === 'pumps' ? 'pumps' : 'staff';

            $salesData = $this->loadAdminSalesForReport($request);
            $salesReportDate = $salesData['salesReportDate'];
            $sales = $salesData['sales'];
            $priorDaySalesByPump = $salesData['priorDaySalesByPump'];
            $prices = $this->pricesLookupForReportDate($salesReportDate);
            $cashByStaffId = CashCollection::query()
                ->whereDate('date', $salesReportDate)
                ->where('category', 'cash')
                ->select('staff_id', DB::raw('SUM(cash_total) as total_cash'))
                ->groupBy('staff_id')
                ->pluck('total_cash', 'staff_id');
            $cashCategoryTotalsByStaff = CashCollection::query()
                ->whereDate('date', $salesReportDate)
                ->select('staff_id', 'category', DB::raw('SUM(cash_total) as total_cash'))
                ->groupBy('staff_id', 'category')
                ->get()
                ->groupBy('staff_id')
                ->map(fn ($rows) => $rows->pluck('total_cash', 'category'));
            $billAmountByStaffId = Bill::query()
                ->whereDate('date', $salesReportDate)
                ->select('staff_id', DB::raw('SUM(bill_value) as total_bills'))
                ->groupBy('staff_id')
                ->pluck('total_bills', 'staff_id');
            $gasAmountByStaffId = $this->gasAmountByStaffForDate($salesReportDate);
        }

        $cashRecDate = null;
        $cashRecDateMax = null;
        $cashRecStaffOptions = collect();
        $cashRecCategoryOptions = [
            'cash' => 'Cash',
            'visa-master' => 'Visa/Master',
            'amex' => 'Amex',
        ];
        $cashRecSelectedCategory = 'cash';
        $cashRecExistingValues = [];
        $cashRecExistingTotal = null;
        $cashRecHistoryDate = null;
        $cashRecRecords = collect();
        $companies = collect();
        $billStaffOptions = collect();
        $billCompanyOptions = collect();
        $billCategoryOptions = collect();
        if ($request !== null && in_array($navPrefix, ['admin', 'data-entry'], true) && $page === 'cash-rec') {
            $today = now()->startOfDay();
            $cashRecDateMax = $today->toDateString();
            $cashRecDate = $cashRecDateMax;
            $cashRecHistoryDate = $today->copy()->subDay()->toDateString();

            $picked = $request->query('cash_date');
            if (is_string($picked) && $picked !== '') {
                try {
                    $candidate = Carbon::parse($picked)->startOfDay();
                    if ($candidate->lte($today)) {
                        $cashRecDate = $candidate->toDateString();
                    }
                } catch (\Throwable) {
                    // keep default (today)
                }
            }
            $pickedHistory = $request->query('cash_history_date');
            if (is_string($pickedHistory) && $pickedHistory !== '') {
                try {
                    $candidate = Carbon::parse($pickedHistory)->startOfDay();
                    if ($candidate->lte($today)) {
                        $cashRecHistoryDate = $candidate->toDateString();
                    }
                } catch (\Throwable) {
                    // keep default (yesterday)
                }
            }
            $cashRecStaffOptions = Staff::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'is_active']);

            $pickedStaffId = $request->query('cash_staff_id');
            $pickedCategory = $request->query('cash_category');
            if (is_string($pickedCategory) && array_key_exists($pickedCategory, $cashRecCategoryOptions)) {
                $cashRecSelectedCategory = $pickedCategory;
            }
            if (is_string($pickedStaffId) && $pickedStaffId !== '') {
                $existingRow = CashCollection::query()
                    ->where('staff_id', (int) $pickedStaffId)
                    ->whereDate('date', $cashRecDate)
                    ->where('category', $cashRecSelectedCategory)
                    ->first();
                if ($existingRow !== null) {
                    $vals = $existingRow->cash_values;
                    if (is_array($vals)) {
                        $cashRecExistingValues = collect($vals)
                            ->map(function ($v) {
                                if (is_array($v)) {
                                    return is_numeric($v['amount'] ?? null)
                                        ? number_format((float) $v['amount'], 2, '.', '')
                                        : '';
                                }

                                return is_numeric($v) ? number_format((float) $v, 2, '.', '') : '';
                            })
                            ->filter(fn ($v) => $v !== '')
                            ->values()
                            ->all();
                    }
                    if (count($cashRecExistingValues) === 0) {
                        $cashRecExistingValues = [number_format((float) ($existingRow->cash_total ?? 0), 2, '.', '')];
                    }
                    $cashRecExistingTotal = $existingRow->cash_total !== null
                        ? number_format((float) $existingRow->cash_total, 2)
                        : null;
                }
            }

            $cashRecQuery = CashCollection::query()
                ->with('staff')
                ->whereDate('date', $cashRecHistoryDate)
                ->orderByDesc('id');
            $cashRecRecords = $cashRecQuery
                ->paginate(5, ['*'], 'cash_history_page')
                ->withQueryString();
        }
        if ($request !== null && in_array($navPrefix, ['admin', 'data-entry'], true) && $page === 'bill') {
            $companies = Company::query()->orderBy('company_name')->orderBy('id')->get();
            $billStaffOptions = Staff::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);
            $billCompanyOptions = $companies->map(fn ($c) => ['id' => $c->id, 'company_name' => $c->company_name]);
            $billCategoryOptions = Category::query()->orderBy('category')->get(['id', 'category']);
        }

        return view('dashboard.page', [
            'navPrefix' => $navPrefix,
            'heading' => $heading,
            'page' => $page,
            'sectionTitle' => self::SECTION_TITLES[$page],
            'categories' => $categories,
            'tanks' => $tanks,
            'prices' => $prices,
            'gasPrices' => $gasPrices,
            'gasDetails' => $gasDetails,
            'gasPreviousDetails' => $gasPreviousDetails,
            'gasFormDate' => $gasFormDate,
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
            'cashByStaffId' => $cashByStaffId,
            'cashCategoryTotalsByStaff' => $cashCategoryTotalsByStaff,
            'billAmountByStaffId' => $billAmountByStaffId,
            'gasAmountByStaffId' => $gasAmountByStaffId,
            'cashRecDate' => $cashRecDate,
            'cashRecDateMax' => $cashRecDateMax,
            'cashRecStaffOptions' => $cashRecStaffOptions,
            'cashRecCategoryOptions' => $cashRecCategoryOptions,
            'cashRecSelectedCategory' => $cashRecSelectedCategory,
            'cashRecExistingValues' => $cashRecExistingValues,
            'cashRecExistingTotal' => $cashRecExistingTotal,
            'cashRecHistoryDate' => $cashRecHistoryDate,
            'cashRecRecords' => $cashRecRecords,
            'companies' => $companies,
            'billStaffOptions' => $billStaffOptions,
            'billCompanyOptions' => $billCompanyOptions,
            'billCategoryOptions' => $billCategoryOptions,
            'homePriceDate' => $homePriceDate,
            'homeCategoryPriceRows' => $homeCategoryPriceRows,
            'homeStaffRows' => $homeStaffRows,
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
     * Staff-sale PDF: one separate table per staff with summary columns.
     *
     * @return array{groups: list<array{staff: string, lines: list<array<string, string>>, group_total: string, cash_total: string, visa_master_total: string, amex_total: string, bill_amount: string, gas_amount: string, short_total: string}>, grandTotalFormatted: string|null, showGrandTotal: bool}
     */
    private function buildStaffSalePdfTablePayload(
        Collection $sales,
        Collection $priorDaySalesByPump,
        Collection $pumps,
        Collection $prices,
        Collection $staffMembers,
        Collection $cashByStaffId,
        Collection $cashCategoryTotalsByStaff,
        Collection $billAmountByStaffId,
        Collection $gasAmountByStaffId,
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
            $groupTotal = 0.0;
            foreach ($items as $row) {
                $lineTotalNumeric = is_numeric(str_replace(',', '', (string) $row['line_total']))
                    ? (float) str_replace(',', '', (string) $row['line_total'])
                    : null;
                if ($lineTotalNumeric !== null) {
                    $groupTotal += $lineTotalNumeric;
                }
                $lines[] = [
                    'pump_name' => $row['pump_name'],
                    'starting' => $row['starting'],
                    'ending' => $row['ending'],
                    'difference' => $row['difference'],
                    'price' => $row['price'],
                    'line_total' => $row['line_total'],
                ];
            }
            $cashTotal = null;
            $visaMasterTotal = null;
            $amexTotal = null;
            $billAmountTotal = null;
            $gasAmountTotal = null;
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
                $shortTotal = $groupTotal - (
                    (float) ($cashTotal ?? 0)
                    + (float) ($visaMasterTotal ?? 0)
                    + (float) ($amexTotal ?? 0)
                    + (float) ($billAmountTotal ?? 0)
                    + (float) ($gasAmountTotal ?? 0)
                );
            }
            $groups[] = [
                'staff' => $items->first()['staff'],
                'lines' => $lines,
                'group_total' => number_format((float) $groupTotal, 2),
                'cash_total' => $cashTotal !== null ? number_format((float) $cashTotal, 2) : '-',
                'visa_master_total' => $visaMasterTotal !== null ? number_format((float) $visaMasterTotal, 2) : '-',
                'amex_total' => $amexTotal !== null ? number_format((float) $amexTotal, 2) : '-',
                'bill_amount' => $billAmountTotal !== null ? number_format((float) $billAmountTotal, 2) : '-',
                'gas_amount' => $gasAmountTotal !== null ? number_format((float) $gasAmountTotal, 2) : '-',
                'short_total' => $shortTotal !== null ? number_format((float) $shortTotal, 2) : '-',
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
     * Gas prices saved for the given calendar date.
     *
     * @return Collection<int|string, mixed>
     */
    private function gasPricesForFormDate(string $date): Collection
    {
        return GasPrice::query()
            ->whereDate('date', $date)
            ->pluck('price', 'gas_type');
    }

    /**
     * Latest gas price per type in effect on the given date (date <= report date).
     *
     * @return Collection<int|string, mixed>
     */
    private function gasPricesLookupForReportDate(string $reportDate): Collection
    {
        $latestPerType = DB::table('gas_price')
            ->select('gas_type', DB::raw('MAX(date) as max_date'))
            ->whereDate('date', '<=', $reportDate)
            ->groupBy('gas_type');

        $byReportDate = GasPrice::query()
            ->joinSub($latestPerType, 't', function ($join) {
                $join->on('gas_price.gas_type', '=', 't.gas_type')
                    ->on('gas_price.date', '=', 't.max_date');
            })
            ->pluck('gas_price.price', 'gas_price.gas_type');

        $latestAnyDate = GasPrice::query()
            ->select('gas_type', DB::raw('MAX(date) as max_date'))
            ->groupBy('gas_type');

        $latestOverall = GasPrice::query()
            ->joinSub($latestAnyDate, 't', function ($join) {
                $join->on('gas_price.gas_type', '=', 't.gas_type')
                    ->on('gas_price.date', '=', 't.max_date');
            })
            ->pluck('gas_price.price', 'gas_price.gas_type');

        return $latestOverall->merge($byReportDate);
    }

    /**
     * Gas amount summed by staff for a specific report date.
     *
     * @return Collection<int|string, float>
     */
    private function gasAmountByStaffForDate(string $reportDate): Collection
    {
        $gasPriceLookup = $this->gasPricesLookupForReportDate($reportDate);

        return GasDetail::query()
            ->whereDate('date', $reportDate)
            ->whereNotNull('staff_id')
            ->get(['staff_id', 'gas_type', 'morning_balance', 'night_balance', 'today_sale', 'amount'])
            ->groupBy('staff_id')
            ->map(function (Collection $rows) use ($gasPriceLookup) {
                return (float) $rows->sum(function ($row) use ($gasPriceLookup) {
                    if ($row->amount !== null) {
                        return (float) $row->amount;
                    }
                    $gasType = strtoupper((string) $row->gas_type);
                    $gasPrice = $gasPriceLookup->get($gasType) ?? $gasPriceLookup->get(strtolower($gasType));
                    if ($gasPrice === null) {
                        return 0.0;
                    }
                    if ($row->today_sale !== null) {
                        return (float) $row->today_sale * (float) $gasPrice;
                    }
                    if ($row->morning_balance !== null && $row->night_balance !== null) {
                        return ((float) $row->morning_balance - (float) $row->night_balance) * (float) $gasPrice;
                    }

                    return 0.0;
                });
            });
    }

    /**
     * Gas details saved for the given calendar date.
     *
     * @return Collection<int|string, GasDetail>
     */
    private function gasDetailsForFormDate(string $date): Collection
    {
        if (! Schema::hasTable('gas_data')) {
            return collect();
        }

        return GasDetail::query()
            ->whereDate('date', $date)
            ->get()
            ->keyBy('gas_type');
    }

    /**
     * Gas form date from query (?gas_date=) — defaults to today; cannot be in the future.
     */
    private function resolveGasFormDate(Request $request): string
    {
        $today = now()->startOfDay();
        $default = $today->toDateString();
        $raw = $request->query('gas_date');
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
            'prices' => ['nullable', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
            'gas_prices' => ['nullable', 'array'],
            'gas_prices.l' => ['nullable', 'numeric', 'min:0'],
            'gas_prices.m' => ['nullable', 'numeric', 'min:0'],
            'gas_prices.s' => ['nullable', 'numeric', 'min:0'],
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

        $gasTypeMap = [
            'l' => 'L',
            'm' => 'M',
            's' => 'S',
        ];
        $inputGasPrices = $validated['gas_prices'] ?? [];
        foreach ($gasTypeMap as $inputKey => $gasType) {
            $value = $inputGasPrices[$inputKey] ?? null;

            if ($value === null || $value === '') {
                GasPrice::query()
                    ->where('gas_type', $gasType)
                    ->whereDate('date', $date)
                    ->delete();

                continue;
            }

            GasPrice::query()->updateOrCreate(
                [
                    'gas_type' => $gasType,
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

    private function saveRoleCashRec(Request $request, string $rolePrefix): RedirectResponse
    {
        $validated = $request->validate([
            'cash_date' => ['required', 'date', 'before_or_equal:today'],
            'cash_staff_id' => ['required', 'integer', Rule::exists('staff', 'id')->where('is_active', true)],
            'cash_category' => ['required', Rule::in(['cash', 'visa-master', 'amex'])],
            'cash_values' => ['required', 'array'],
            'cash_values.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $date = Carbon::parse($validated['cash_date'])->toDateString();
        $staffId = (int) $validated['cash_staff_id'];
        $selectedCategory = (string) $validated['cash_category'];
        $rawValues = $validated['cash_values'] ?? [];
        $entries = collect($rawValues)
            ->map(function ($v) use ($selectedCategory) {
                $amountRaw = $v === null ? '' : trim((string) $v);
                if ($amountRaw === '') {
                    return null;
                }

                return [
                    'category' => $selectedCategory,
                    'amount' => round((float) $amountRaw, 2),
                ];
            })
            ->filter(fn ($row) => $row !== null)
            ->values();

        if ($selectedCategory !== 'cash') {
            $first = $entries->first();
            $entries = $first !== null ? collect([$first]) : collect();
        }

        if ($entries->count() === 0) {
            throw ValidationException::withMessages([
                'cash_values' => 'Enter at least one cash amount.',
            ]);
        }
        $entriesArray = $entries->all();

        CashCollection::query()->updateOrCreate(
            [
                'staff_id' => $staffId,
                'date' => $date,
                'category' => $selectedCategory,
            ],
            [
                'cash_total' => (float) collect($entriesArray)->sum(fn ($row) => (float) $row['amount']),
                'cash_values' => array_map(
                    fn ($entry) => ['category' => $selectedCategory, 'amount' => (float) $entry['amount']],
                    $entriesArray
                ),
            ]
        );

        $back = route($rolePrefix.'.show', ['page' => 'cash-rec']).'?'.http_build_query([
            'cash_date' => $date,
            'cash_staff_id' => $staffId,
            'cash_category' => $selectedCategory,
        ]);

        return redirect()
            ->to($back)
            ->with('status', 'Cash record saved successfully.');
    }

    private function deleteRoleCashRec(Request $request, CashCollection $cashCollection, string $rolePrefix): RedirectResponse
    {
        $cashCollection->delete();

        $query = array_filter([
            'cash_date' => $request->input('cash_date'),
            'cash_staff_id' => $request->input('cash_staff_id'),
            'cash_category' => $request->input('cash_category'),
            'cash_history_date' => $request->input('cash_history_date'),
            'cash_history_page' => $request->input('cash_history_page'),
        ], fn ($v) => is_string($v) ? $v !== '' : $v !== null);

        $back = route($rolePrefix.'.show', ['page' => 'cash-rec']);
        if (count($query) > 0) {
            $back .= '?'.http_build_query($query);
        }

        return redirect()
            ->to($back)
            ->with('status', 'Cash record deleted successfully.');
    }

    private function saveRoleCompany(Request $request, string $rolePrefix): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        Company::query()->create([
            'company_name' => trim($validated['company_name']),
        ]);

        return redirect()
            ->route($rolePrefix.'.show', ['page' => 'bill'])
            ->with('status', 'Company saved successfully.');
    }

    private function getRoleBillCategoryPrice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bill_date' => ['required', 'date'],
            'category_id' => ['required', 'integer', 'exists:category,id'],
        ]);

        $reportDate = Carbon::parse($validated['bill_date'])->toDateString();
        $prices = $this->pricesLookupForReportDate($reportDate);
        $price = $prices->get((int) $validated['category_id']) ?? $prices->get((string) $validated['category_id']);

        return response()->json([
            'price' => $price !== null ? (float) $price : null,
        ]);
    }

    private function saveRoleBill(Request $request, string $rolePrefix): RedirectResponse
    {
        $request->merge([
            'invoice_number' => trim((string) $request->input('invoice_number')),
        ]);

        $validated = $request->validate([
            'staff_id' => ['required', 'integer', 'exists:staff,id'],
            'date' => ['required', 'date'],
            'company_id' => ['required', 'integer', 'exists:company,id'],
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bill', 'invoice_number')->where(function ($query) use ($request) {
                    return $query
                        ->where('company_id', (int) $request->input('company_id'))
                        ->where('date', Carbon::parse((string) $request->input('date'))->toDateString());
                }),
            ],
            'category_id' => ['required', 'integer', 'exists:category,id'],
            'price' => ['required', 'numeric', 'gt:0'],
            'liters' => ['nullable', 'numeric', 'decimal:0,4', 'gt:0', 'required_without:bill_value'],
            'bill_value' => ['nullable', 'numeric', 'decimal:0,4', 'gt:0', 'required_without:liters'],
        ]);

        $price = (float) $validated['price'];
        $liters = isset($validated['liters']) ? (float) $validated['liters'] : null;
        $billValue = isset($validated['bill_value']) ? (float) $validated['bill_value'] : null;
        if ($liters === null && $billValue !== null) {
            $liters = round($billValue / $price, 4);
        } elseif ($billValue === null && $liters !== null) {
            $billValue = round($liters * $price, 4);
        }
        if ($liters === null || $billValue === null) {
            throw ValidationException::withMessages([
                'bill_value' => 'Enter either liter amount or bill value.',
            ]);
        }

        Bill::query()->create([
            'staff_id' => (int) $validated['staff_id'],
            'date' => Carbon::parse($validated['date'])->toDateString(),
            'company_id' => (int) $validated['company_id'],
            'invoice_number' => trim($validated['invoice_number']),
            'category_id' => (int) $validated['category_id'],
            'price' => $price,
            'liters' => $liters,
            'bill_value' => $billValue,
        ]);

        return redirect()
            ->route($rolePrefix.'.show', ['page' => 'bill'])
            ->with('status', 'Bill saved successfully.');
    }

    private function updateRoleCompany(Request $request, Company $company, string $rolePrefix): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        $company->update([
            'company_name' => trim($validated['company_name']),
        ]);

        return redirect()
            ->route($rolePrefix.'.show', ['page' => 'bill'])
            ->with('status', 'Company updated successfully.');
    }

    private function deleteRoleCompany(Company $company, string $rolePrefix): RedirectResponse
    {
        $company->delete();

        return redirect()
            ->route($rolePrefix.'.show', ['page' => 'bill'])
            ->with('status', 'Company deleted successfully.');
    }

    private function downloadRoleCompanyBillsPdf(Company $company)
    {
        $today = now()->toDateString();
        $latestReset = BillReset::query()
            ->where('company_id', $company->id)
            ->whereDate('reset_date', '<=', $today)
            ->orderByDesc('reset_date')
            ->orderByDesc('id')
            ->first();

        $billsQuery = Bill::query()
            ->where('company_id', $company->id)
            ->with(['staff', 'category']);

        if ($latestReset !== null) {
            $resetDate = Carbon::parse($latestReset->reset_date)->toDateString();
            if ((int) $latestReset->reset_include === 1) {
                // include only after reset date (exclude reset date)
                $billsQuery->whereDate('date', '>', $resetDate);
            } else {
                // include from reset date (include reset date)
                $billsQuery->whereDate('date', '>=', $resetDate);
            }
            $billsQuery->whereDate('date', '<=', $today);
        }

        $bills = $billsQuery
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        $total = (float) $bills->sum(fn (Bill $b) => (float) $b->bill_value);

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = app('dompdf.wrapper');

        return $pdf
            ->loadView('pdf.company-bills', [
                'companyName' => $company->company_name,
                'bills' => $bills,
                'totalFormatted' => number_format($total, 2),
                'showRows' => $bills->isNotEmpty(),
                'generatedAt' => now()->format('Y-m-d H:i'),
            ])
            ->setPaper('a4', 'landscape')
            ->download($this->companyBillsPdfFilename($company));
    }

    private function companyBillsPdfFilename(Company $company): string
    {
        $slug = preg_replace('/[^a-zA-Z0-9_-]+/', '-', trim($company->company_name));
        $slug = trim((string) $slug, '-') ?: 'company';

        return 'company-bills-'.$company->id.'-'.$slug.'.pdf';
    }

    private function settleRoleCompany(Request $request, Company $company, string $rolePrefix): RedirectResponse
    {
        $validated = $request->validate([
            'reset_date' => ['required', 'date', 'before_or_equal:today'],
            'reset_include' => ['nullable', 'boolean'],
        ]);

        BillReset::query()->create([
            'company_id' => $company->id,
            'reset_date' => Carbon::parse($validated['reset_date'])->toDateString(),
            'reset_include' => $request->boolean('reset_include') ? 1 : 0,
        ]);

        return redirect()
            ->route($rolePrefix.'.show', ['page' => 'bill'])
            ->with('status', 'Bill settle record saved for '.$company->company_name.'.');
    }

    private function saveRoleGasDetails(Request $request, string $rolePrefix): RedirectResponse
    {
        if (! Schema::hasTable('gas_data')) {
            return redirect()
                ->route($rolePrefix.'.show', ['page' => 'gas'])
                ->with('status', 'Gas details table is missing. Please run migrations first.');
        }

        $validated = $request->validate([
            'gas_date' => ['required', 'date', 'before_or_equal:today'],
            'gas_data' => ['nullable', 'array'],
            'gas_data.*.staff_id' => ['nullable', 'integer', 'exists:staff,id'],
            'gas_data.*.night_balance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $date = Carbon::parse($validated['gas_date'])->toDateString();
        $previousDate = Carbon::parse($date)->subDay()->toDateString();
        $previousRows = $this->gasDetailsForFormDate($previousDate);
        $gasPrices = $this->gasPricesLookupForReportDate($date);
        $details = $validated['gas_data'] ?? [];
        if (! is_array($details) || $details === []) {
            return redirect()
                ->to(route($rolePrefix.'.show', ['page' => 'gas']).'?'.http_build_query(['gas_date' => $date]))
                ->with('status', 'No gas row data provided.');
        }
        $rowKey = strtolower((string) $request->input('row_key', ''));
        if ($rowKey !== '' && isset($details[$rowKey])) {
            $details = [$rowKey => $details[$rowKey]];
        }
        $typeMap = ['l' => 'L', 'm' => 'M', 's' => 'S'];

        foreach ($details as $inputKey => $row) {
            $normalizedKey = strtolower((string) $inputKey);
            $gasType = $typeMap[$normalizedKey] ?? null;
            if ($gasType === null) {
                continue;
            }

            $staffId = isset($row['staff_id']) && $row['staff_id'] !== '' ? (int) $row['staff_id'] : null;
            $previousRow = $previousRows->get($gasType);
            $existingRow = GasDetail::query()
                ->where('gas_type', $gasType)
                ->whereDate('date', $date)
                ->first();
            $morning = $previousRow?->night_balance ?? $existingRow?->morning_balance;
            $night = $row['night_balance'] ?? null;
            $gasPrice = $gasPrices->get($gasType) ?? $gasPrices->get(strtolower($gasType));
            if ($morning !== null && $night !== null) {
                $sale = (float) $morning - (float) $night;
            } else {
                $sale = null;
            }
            if ($sale !== null && $gasPrice !== null) {
                $amount = $sale * (float) $gasPrice;
            } else {
                $amount = null;
            }

            if ($staffId === null && $morning === null && $night === null && $sale === null && $amount === null) {
                GasDetail::query()
                    ->where('gas_type', $gasType)
                    ->whereDate('date', $date)
                    ->delete();
                continue;
            }

            // Gas values must always be tied to a staff member for sales report mapping.
            if ($staffId === null && ($night !== null || $sale !== null || $amount !== null)) {
                throw ValidationException::withMessages([
                    'gas_data.'.$normalizedKey.'.staff_id' => 'Select staff before updating this gas row.',
                ]);
            }

            GasDetail::query()->updateOrCreate(
                [
                    'gas_type' => $gasType,
                    'date' => $date,
                ],
                [
                    'staff_id' => $staffId,
                    'morning_balance' => $morning,
                    'night_balance' => $night,
                    'today_sale' => $sale,
                    'amount' => $amount,
                ]
            );
        }

        $back = route($rolePrefix.'.show', ['page' => 'gas']).'?'.http_build_query(['gas_date' => $date]);

        return redirect()
            ->to($back)
            ->with('status', 'Gas details saved successfully.');
    }

    private function salesPdfLogoDataUri(): ?string
    {
        $logoPath = public_path('img/logo.png');
        if (!is_file($logoPath)) {
            return null;
        }

        $contents = @file_get_contents($logoPath);
        if ($contents === false) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($contents);
    }
}
