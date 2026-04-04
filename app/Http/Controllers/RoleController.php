<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function register(): View
    {
        return view('role.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:255'],
            'role_number' => ['required', 'integer', 'min:0', 'unique:roles,role_number'],
        ]);

        Role::create($validated);

        return redirect()->route('role.register')->with('status', 'Role registered successfully.');
    }
}
