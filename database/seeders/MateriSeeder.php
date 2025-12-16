<?php

namespace Database\Seeders;

use App\Models\Materi;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_materi' => 'Maze Solving',
                'deskripsi' => 'Algoritma pencarian jalur',
                'status' => 'aktif',
            ],
            [
                'nama_materi' => 'Fisher Teknik',
                'deskripsi' => 'Lego',
                'status' => 'aktif',
            ],
            [
                'nama_materi' => 'Scratch Programming',
                'deskripsi' => 'Pemrograman dasar menggunakan Scratch',
                'status' => 'aktif',
            ],
            [
                'nama_materi' => 'IOT Basics',
                'deskripsi' => 'Pengenalan Internet of Things',
                'status' => 'aktif',
            ],
        ];

        foreach ($data as $item) {
            Materi::create($item);
        }
    }
}
