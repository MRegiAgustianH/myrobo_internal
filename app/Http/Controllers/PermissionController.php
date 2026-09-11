<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $modules = Permission::modules();
        $roles = Permission::roles();

        // Build permission matrix: [role][module] = permission
        $perms = Permission::all()->keyBy(fn($p) => $p->role . '.' . $p->module);
        $moduleSettings = \App\Models\ModuleSetting::all()->keyBy('module');

        return view('admin.permissions.index', compact('modules', 'roles', 'perms', 'moduleSettings'));
    }

    public function update(Request $request)
    {
        $modules = array_keys(Permission::modules());
        $roles = array_keys(Permission::roles());

        foreach ($roles as $role) {
            foreach ($modules as $module) {
                Permission::updateOrCreate(
                    ['role' => $role, 'module' => $module],
                    [
                        'can_create' => $request->boolean("{$role}.{$module}.can_create"),
                        'can_read'   => $request->boolean("{$role}.{$module}.can_read"),
                        'can_update' => $request->boolean("{$role}.{$module}.can_update"),
                        'can_delete' => $request->boolean("{$role}.{$module}.can_delete"),
                    ]
                );
            }
        }

        return back()->with('success', 'Pengaturan hak akses berhasil disimpan.');
    }
    public function toggleModule(Request $request)
    {
        $request->validate([
            'module'          => 'required|string',
            'is_active'       => 'boolean',
            'maintenance_message' => 'nullable|string|max:255',
        ]);

        \App\Models\ModuleSetting::updateOrCreate(
            ['module' => $request->module],
            [
                'is_active' => $request->boolean('is_active'),
                'maintenance_message' => $request->maintenance_message,
            ]
        );

        return back()->with('success', 'Status modul berhasil diperbarui.');
    }
}