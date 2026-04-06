<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Price;
use App\Models\Pump;
use App\Models\Staff;
use App\Models\Tank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    ];

    public function showDev(string $page): View
    {
        return $this->renderPage('dev', 'Dev', $page);
    }

    public function showAdmin(string $page): View
    {
        abort_unless(in_array($page, ['home', 'categories', 'pumps', 'tanks', 'price', 'staff'], true), 404);

        return $this->renderPage('admin', 'Admin', $page);
    }

    public function showDataEntry(string $page): View
    {
        return $this->renderPage('data-entry', 'Data-entry', $page);
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

        Staff::query()->create([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('admin.show', ['page' => 'staff'])
            ->with('status', 'Staff added successfully.');
    }

    public function deleteAdminStaff(Staff $staff): RedirectResponse
    {
        $staff->delete();

        return redirect()
            ->route('admin.show', ['page' => 'staff'])
            ->with('status', 'Staff deleted successfully.');
    }

    protected function renderPage(string $navPrefix, string $heading, string $page): View
    {
        abort_unless(array_key_exists($page, self::SECTION_TITLES), 404);

        $categories = null;
        if (
            ($navPrefix === 'dev' && in_array($page, ['categories', 'tanks', 'pumps', 'price'], true)) ||
            ($navPrefix === 'admin' && $page === 'price')
        ) {
            $categories = Category::query()->orderBy('id')->get();
        }

        $tanks = null;
        if ($navPrefix === 'dev' && in_array($page, ['tanks', 'pumps'], true)) {
            $tanks = Tank::query()->orderBy('id')->get();
        }

        $prices = null;
        if (in_array($navPrefix, ['dev', 'admin'], true) && $page === 'price') {
            $prices = Price::query()->pluck('price', 'category_id');
        }

        $pumps = null;
        if (in_array($navPrefix, ['dev', 'admin'], true) && $page === 'pumps') {
            $pumps = Pump::query()->orderBy('id')->get();
        }

        $staffMembers = null;
        if ($navPrefix === 'admin' && $page === 'staff') {
            $staffMembers = Staff::query()->orderBy('id')->get();
        }

        return view('dashboard.page', [
            'navPrefix' => $navPrefix,
            'heading' => $heading,
            'page' => $page,
            'sectionTitle' => self::SECTION_TITLES[$page],
            'categories' => $categories,
            'tanks' => $tanks,
            'prices' => $prices,
            'pumps' => $pumps,
            'staffMembers' => $staffMembers,
        ]);
    }

    private function saveRolePrices(Request $request, string $rolePrefix): RedirectResponse
    {
        $validated = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $categoryIds = Category::query()->pluck('id')->all();
        $inputPrices = $validated['prices'] ?? [];

        foreach ($categoryIds as $categoryId) {
            $value = $inputPrices[$categoryId] ?? null;

            if ($value === null || $value === '') {
                Price::query()->where('category_id', $categoryId)->delete();

                continue;
            }

            Price::query()->updateOrCreate(
                ['category_id' => $categoryId],
                ['price' => $value]
            );
        }

        return redirect()
            ->route($rolePrefix.'.show', ['page' => 'price'])
            ->with('status', 'Prices saved successfully.');
    }
}
