<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle($request, Closure $next, ...$args)
    {
        $module = $args[0] ?? null;
        $action = $args[1] ?? null;

        if (!$module || !$action) {
            return $next($request);
        }

        // Check if module is in maintenance mode
        if (!module_active($module)) {
            // superadmin can still access maintenance modules
            if (auth()->user()->role !== 'superadmin') {
                abort(403, 'Modul ini sedang dalam maintenance.');
            }
        }

        if (!user_can($module, $action)) {
            abort(403, 'Anda tidak memiliki akses untuk aksi ini.');
        }

        return $next($request);
    }
}