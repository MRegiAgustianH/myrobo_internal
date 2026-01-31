<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Semester::create([
            'nama_semester' => "I"
        ]);
        Semester::create([
            'nama_semester' => "II"
        ]);
    }
}
