<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materi;
use App\Models\Kompetensi;

class KompetensiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            'Maze Solving' => [
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
                'Pemrograman kompetisi',
            ],

            'Fisher Teknik' => [
                'Pengenalan komponen Lego',
                'Perakitan struktur dasar',
                'Pemahaman mekanik sederhana',
                'Eksperimen desain teknik',
            ],

            'Scratch Programming' => [
                'Pengenalan antarmuka Scratch',
                'Penggunaan block dasar',
                'Logika percabangan dan perulangan',
                'Pembuatan game sederhana',
            ],

            'IOT Basics' => [
                'Pengenalan konsep Internet of Things',
                'Pemahaman sensor dan aktuator',
                'Komunikasi data sederhana',
                'Implementasi IoT dasar',
            ],

        ];

        foreach ($data as $namaMateri => $kompetensis) {

            $materi = Materi::where('nama_materi', $namaMateri)->first();

            if (! $materi) {
                continue; 
            }

            foreach ($kompetensis as $namaKompetensi) {
                Kompetensi::firstOrCreate(
                    [
                        'materi_id'       => $materi->id,
                        'nama_kompetensi' => $namaKompetensi,
                    ]
                );
            }
        }
    }
}
