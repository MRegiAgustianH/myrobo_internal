<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleSetting extends Model
{
    protected $fillable = [
        'module',
        'is_active',
        'maintenance_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function modules(): array
    {
        return Permission::modules();
    }

    public static function isActive(string $module): bool
    {
        $setting = static::where('module', $module)->first();
        return $setting ? $setting->is_active : true;
    }
}