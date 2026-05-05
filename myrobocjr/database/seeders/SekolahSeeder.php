<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_sekolah' => 'SMP Islam Al Azhar 20 Cianjur',
                'alamat' => 'Jl. Dr. Muwardi No.182, Bojongherang, Kec. Cianjur, Kabupaten Cianjur, Jawa Barat 43216',
                'kontak' => '085722266706',
                'tgl_mulai_kerjasama' => '2025-01-01',
                'tgl_akhir_kerjasama' => '2025-12-31',
            ],
            [
                'nama_sekolah' => 'SMP Islam Kreatif Cianjur',
                'alamat' => 'Jl. Nasional III No.109, Sawah Gede, Kec. Cianjur, Kabupaten Cianjur, Jawa Barat 43212',
                'kontak' => '000000000000',
                'tgl_mulai_kerjasama' => '2025-02-01',
                'tgl_akhir_kerjasama' => '2025-12-31',
            ],
            [
                'nama_sekolah' => 'SD Islam Kreatif Cianjur',
                'alamat' => 'Jl. Nasional III No.109, Sawah Gede, Kec. Cianjur, Kabupaten Cianjur, Jawa Barat 43212',
                'kontak' => '000000000000',
                'tgl_mulai_kerjasama' => '2025-02-01',
                'tgl_akhir_kerjasama' => '2025-12-31',
            ],
            [
                'nama_sekolah' => 'Tayyibah Global Islamic School',
                'alamat' => 'Jl. KH. Abdullah Bin Nuh Kavling B-Elka Residence No. 2 Cianjur, Indonesia 43212 Jawa Barat',
                'kontak' => '000000000000',
                'tgl_mulai_kerjasama' => '2025-02-01',
                'tgl_akhir_kerjasama' => '2025-12-31',
            ],
            [
                'nama_sekolah' => 'Innovative School Cianjur',
                'alamat' => 'Jl. Didi Prawirakusumah RT 03 Rw 01 Maleber Karangtengah Cianjur',
                'kontak' => '000000000000',
                'tgl_mulai_kerjasama' => '2025-02-01',
                'tgl_akhir_kerjasama' => '2025-12-31',
            ],
        ];

        foreach ($data as $item) {
            Sekolah::create($item);
        }
    }
}
