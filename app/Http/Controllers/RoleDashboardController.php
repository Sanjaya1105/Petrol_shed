<?php

namespace App\Http\Controllers;

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

    protected function renderPage(string $navPrefix, string $heading, string $page): View
    {
        abort_unless(array_key_exists($page, self::SECTION_TITLES), 404);

        return view('dashboard.page', [
            'navPrefix' => $navPrefix,
            'heading' => $heading,
            'page' => $page,
            'sectionTitle' => self::SECTION_TITLES[$page],
        ]);
    }
}
