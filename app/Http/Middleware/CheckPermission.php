<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->isAdmin()) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        
        // Always allow dashboard and logout
        if (in_array($routeName, ['admin.dashboard', 'logout'])) {
            return $next($request);
        }

        // 1. Check exact match first
        if ($user->hasPermission($routeName)) {
            return $next($request);
        }

        // 2. Fallback: Check if they have access to the ".index" or ".create" of this section
        // Example: admin.cards.derived.pdf -> check admin.cards.derived.index
        // Example: admin.students.import.store -> check admin.students.import.create
        $parts = explode('.', $routeName);
        if (count($parts) > 1) {
            array_pop($parts);
            $basePath = implode('.', $parts);

            if ($user->hasPermission($basePath . '.index') || $user->hasPermission($basePath . '.create')) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
