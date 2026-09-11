<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = array_keys(Permission::modules());
        $roles = Permission::roles();

        foreach ($roles as $role => $label) {
            foreach ($modules as $module) {
                $defaults = match($role) {
                    'superadmin' => [true, true, true, true],
                    'admin_cabang' => [true, true, true, false],
                    'sekretaris' => match($module) {
                        'sekolah', 'jadwal', 'absensi', 'rapor-tugas' => [true, true, true, false],
                        'pembayaran' => [true, true, true, false],
                        default => [false, true, false, false],
                    },
                    'bendahara' => match($module) {
                        'pembayaran', 'keuangan' => [true, true, true, false],
                        default => [false, true, false, false],
                    },
                    'admin_sekolah' => match($module) {
                        'pembayaran', 'absensi', 'rapor' => [false, true, true, false],
                        default => [false, true, false, false],
                    },
                    'instruktur' => match($module) {
                        'jadwal', 'absensi', 'rapor' => [false, true, true, false],
                        default => [false, true, false, false],
                    },
                    default => [false, true, false, false],
                };

                Permission::updateOrCreate(
                    ['role' => $role, 'module' => $module],
                    [
                        'can_create' => $defaults[0],
                        'can_read'   => $defaults[1],
                        'can_update' => $defaults[2],
                        'can_delete' => $defaults[3],
                    ]
                );
            }
        }
    }
}