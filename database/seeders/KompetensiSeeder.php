<?php

namespace Database\Seeders;

use App\Models\Kompetensi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KompetensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $kompetensis = [
            'Mendeskripsikan bagian hardware & software',
            'Pengenalan instruksi pemrograman motor',
            'Pemrograman khusus simpangan',
            'Pemrograman khusus putar (turn)',
            'Pemrograman khusus warna track',
            'Pemrograman khusus ikut garis',
            'Pemrograman khusus putih stop',
            'Pemrograman khusus find',
            'Pemrograman khusus sensor line',
            'Pemrograman kombinasi',
            'Pemrograman Kompetisi',
        ];

        foreach ($kompetensis as $kompetensi) {
            Kompetensi::firstOrCreate([
                'nama_kompetensi' => $kompetensi
            ]);
        }
    }
}
