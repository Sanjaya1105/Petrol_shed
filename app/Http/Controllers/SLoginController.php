<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SLoginController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->dashboardUrl());
        }

        return view('auth.s_login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ], $remember)) {
            return back()->withErrors([
                'username' => __('The provided credentials do not match our records.'),
            ])->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->dashboardUrl());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('s_login');
    }
}
