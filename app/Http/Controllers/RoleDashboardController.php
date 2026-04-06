<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Price;
use App\Models\Tank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    ];

    public function showDev(string $page): View
    {
        return $this->renderPage('dev', 'Dev', $page);
    }

    public function showAdmin(string $page): View
    {
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

    public function saveDevPrices(Request $request): RedirectResponse
    {
        return $this->saveRolePrices($request, 'dev');
    }

    public function saveAdminPrices(Request $request): RedirectResponse
    {
        return $this->saveRolePrices($request, 'admin');
    }

    protected function renderPage(string $navPrefix, string $heading, string $page): View
    {
        abort_unless(array_key_exists($page, self::SECTION_TITLES), 404);

        $categories = null;
        if (
            ($navPrefix === 'dev' && in_array($page, ['categories', 'tanks', 'price'], true)) ||
            ($navPrefix === 'admin' && $page === 'price')
        ) {
            $categories = Category::query()->orderBy('id')->get();
        }

        $tanks = null;
        if ($navPrefix === 'dev' && $page === 'tanks') {
            $tanks = Tank::query()->orderBy('id')->get();
        }

        $prices = null;
        if (in_array($navPrefix, ['dev', 'admin'], true) && $page === 'price') {
            $prices = Price::query()->pluck('price', 'category_id');
        }

        return view('dashboard.page', [
            'navPrefix' => $navPrefix,
            'heading' => $heading,
            'page' => $page,
            'sectionTitle' => self::SECTION_TITLES[$page],
            'categories' => $categories,
            'tanks' => $tanks,
            'prices' => $prices,
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
