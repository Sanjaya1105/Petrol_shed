<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserMatchesRoleNumber
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roleNumber): Response
    {
        $user = $request->user();
        if ($user === null) {
            return redirect()->route('s_login');
        }

        $user->loadMissing('role');

        if ($user->role === null || (string) $user->role->role_number !== $roleNumber) {
            abort(403);
        }

        return $next($request);
    }
}
