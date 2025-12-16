<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
        User::updateOrCreate(
            ['email' => 'admin@myrobo.id'],
            [
                'name'     => 'Admin MyRobo',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // INSTRUKTUR
        User::updateOrCreate(
            ['email' => 'instruktur@myrobo.id'],
            [
                'name'     => 'Instruktur MyRobo',
                'password' => Hash::make('instruktur123'),
                'role'     => 'instruktur',
            ]
        );

        // ADMIN SEKOLAH
        User::updateOrCreate(
            ['email' => 'adminsekolah@myrobo.id'],
            [
                'name'     => 'Admin Sekolah',
                'password' => Hash::make('sekolah123'),
                'role'     => 'admin_sekolah',
            ]
        );
    }
}
