<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    public function dev(): View
    {
        return view('dashboard.dev');
    }

    public function admin(): View
    {
        return view('dashboard.admin');
    }

    public function dataEntry(): View
    {
        return view('dashboard.data-entry');
    }
}
