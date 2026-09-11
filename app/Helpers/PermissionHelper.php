<?php

use App\Models\Permission;

if (!function_exists('user_can')) {
    function user_can(string $module, string $action): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // superadmin always has full access
        if ($user->role === 'superadmin') return true;

        // map action to column
        $column = match($action) {
            'create'  => 'can_create',
            'read'    => 'can_read',
            'update'  => 'can_update',
            'delete'  => 'can_delete',
            default   => null,
        };

        if (!$column) return true; // unknown action = allow (e.g. export, cetak)

        $perm = Permission::where('role', $user->role)
            ->where('module', $module)
            ->first();

        // if no permission record exists, fall back to true (backward compat)
        return $perm ? $perm->$column : true;
    }
}
if (!function_exists('module_active')) {
    function module_active(string $module): bool
    {
        return \App\Models\ModuleSetting::isActive($module);
    }
}