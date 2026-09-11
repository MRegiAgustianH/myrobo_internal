<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'role',
        'module',
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
    ];

    protected $casts = [
        'can_create' => 'boolean',
        'can_read'   => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
    ];

    // ponytail: hardcode modules here, move to config if roles/modules grow
    public static function modules(): array
    {
        return [
            'sekolah'       => 'Sekolah',
            'cabang'        => 'Cabang',
            'users'         => 'Pengguna',
            'home-private'  => 'Home Private',
            'jadwal'        => 'Jadwal',
            'materi'        => 'Materi & Modul',
            'keuangan'      => 'Keuangan',
            'pembayaran'    => 'Pembayaran',
            'absensi'       => 'Absensi',
            'rapor-tugas'   => 'Tugas Rapor',
            'rapor'         => 'Rapor',
            'tarif-gaji'    => 'Tarif Gaji',
        ];
    }

    public static function roles(): array
    {
        return [
            'superadmin'    => 'Superadmin',
            'admin_cabang'  => 'Admin Cabang',
            'sekretaris'    => 'Sekretaris',
            'bendahara'     => 'Bendahara',
            'admin_sekolah' => 'Admin Sekolah',
            'instruktur'    => 'Instruktur',
        ];
    }
}