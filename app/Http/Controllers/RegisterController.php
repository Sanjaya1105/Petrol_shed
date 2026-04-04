<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        $roles = Role::query()->orderBy('role_number')->get();

        return view('auth.register', ['roles' => $roles]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $email = strtolower($validated['username']).'@register.local';

        User::create([
            'name' => $validated['name'],
            'email' => $email,
            'phone' => $validated['phone'],
            'username' => $validated['username'],
            'role_id' => $validated['role_id'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('register')->with('status', 'Registration successful. You can sign in with your username.');
    }
}
