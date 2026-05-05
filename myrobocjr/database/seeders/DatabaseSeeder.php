<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SekolahSeeder::class,
            UserSeeder::class,
            MateriSeeder::class,
            SemesterSeeder::class,
            KompetensiSeeder::class,
            IndikatorKompetensiSeeder::class,
        ]);
    }
}
