<?php

namespace Database\Seeders;

use App\Models\ModuleSetting;
use Illuminate\Database\Seeder;

class ModuleSettingSeeder extends Seeder
{
    public function run(): void
    {
        $modules = ModuleSetting::modules();

        foreach ($modules as $key => $label) {
            ModuleSetting::updateOrCreate(
                ['module' => $key],
                ['is_active' => true, 'maintenance_message' => null]
            );
        }
    }
}