<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kompetensi;
use App\Models\IndikatorKompetensi;

class IndikatorKompetensiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Mendeskripsikan bagian hardware & software' => [
                'Merangkai blok rangkaian robot line tracing',
                'Dapat menginstal aplikasi arduino, mengaktifkan dan instalasi driver dan pemrogramannya',
            ],

            'Pengenalan instruksi pemrograman motor' => [
                'Instruksi pemrograman motor kiri dan kanan',
            ],

            'Pemrograman khusus simpangan' => [
                'Gerakan kombinasi sesuai instruksi pemrograman simpangan T atau P',
            ],

            'Pemrograman khusus putar (turn)' => [
                'Instruksi pemrograman putar kanan atau kiri',
            ],

            'Pemrograman khusus warna track' => [
                'Instruksi pemrograman mengubah warna jalur track hitam menjadi putih',
            ],

            'Pemrograman khusus ikut garis' => [
                'Instruksi pemrograman mengikuti garis sampai waktu yang ditentukan selesai',
            ],

            'Pemrograman khusus putih stop' => [
                'Instruksi pemrograman gerakan untuk mengikuti garis sampai semua sensor tidak terkena garis',
            ],

            'Pemrograman khusus find' => [
                'Instruksi pemrograman untuk menemukan garis dan berhenti',
            ],

            'Pemrograman khusus sensor line' => [
                'Pemrograman robot mengikuti garis sampai satu atau lebih sensor yang ditentukan terkena garis',
            ],

            'Pemrograman kombinasi' => [
                'Dapat memprogram sesuai dengan instruksi yang boleh dikeluarkan dalam program',
            ],

            'Pemrograman Kompetisi' => [
                'Dapat memprogram dengan efektif dan efisien sesuai kebutuhan track',
                'Instruksi pemrograman gerakan motor kiri dan kanan',
            ],
        ];

        foreach ($data as $namaKompetensi => $indikators) {
            $kompetensi = Kompetensi::where('nama_kompetensi', $namaKompetensi)->first();

            if (!$kompetensi) {
                continue;
            }

            foreach ($indikators as $indikator) {
                IndikatorKompetensi::firstOrCreate([
                    'kompetensi_id' => $kompetensi->id,
                    'nama_indikator' => $indikator,
                ]);
            }
        }
    }
}
