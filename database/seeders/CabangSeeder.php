<?php

namespace Database\Seeders;

use App\Models\Cabang;
use Illuminate\Database\Seeder;

class CabangSeeder extends Seeder
{
    public function run(): void
    {
        Cabang::updateOrCreate(
            ['kode_cabang' => 'CJR001'],
            [
                'nama_cabang' => 'CIANJUR',
                'alamat'      => 'Jl. Raya Cianjur No. 12',
                'kontak'      => '081234567890',
                'is_aktif'    => true,
            ]
        );

        Cabang::updateOrCreate(
            ['kode_cabang' => 'BDG001'],
            [
                'nama_cabang' => 'BANDUNG',
                'alamat'      => 'Jl. Raya Bandung No. 45',
                'kontak'      => '081298765432',
                'is_aktif'    => true,
            ]
        );
    }
}