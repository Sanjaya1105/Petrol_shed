<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

    protected function renderPage(string $navPrefix, string $heading, string $page): View
    {
        abort_unless(array_key_exists($page, self::SECTION_TITLES), 404);

        $categories = null;
        if ($navPrefix === 'dev' && $page === 'categories') {
            $categories = Category::query()->orderBy('id')->get();
        }

        return view('dashboard.page', [
            'navPrefix' => $navPrefix,
            'heading' => $heading,
            'page' => $page,
            'sectionTitle' => self::SECTION_TITLES[$page],
            'categories' => $categories,
        ]);
    }
}
