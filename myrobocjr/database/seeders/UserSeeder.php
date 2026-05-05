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
                'username' => 'admin',
                'name'     => 'Admin MyRobo',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // INSTRUKTUR
        User::updateOrCreate(
            ['email' => 'aling@myrobo.id'],
            [
                'username' => 'aling',
                'name'     => 'Aling',
                'password' => Hash::make('aling123'),
                'role'     => 'instruktur',
            ]
        );
        User::updateOrCreate(
            ['email' => 'bagus@myrobo.id'],
            [
                'username' => 'bagus',
                'name'     => 'Bagus',
                'password' => Hash::make('bagus123'),
                'role'     => 'instruktur',
            ]
        );
        User::updateOrCreate(
            ['email' => 'gina@myrobo.id'],
            [
                'username' => 'gina',
                'name'     => 'Gina',
                'password' => Hash::make('gina123'),
                'role'     => 'instruktur',
            ]
        );
        User::updateOrCreate(
            ['email' => 'abiyyatun@myrobo.id'],
            [
                'username' => 'abiyyatun',
                'name'     => 'Abiyyatun',
                'password' => Hash::make('abiyyatun123'),
                'role'     => 'instruktur',
            ]
        );
        User::updateOrCreate(
            ['email' => 'rizki@myrobo.id'],
            [
                'username' => 'rizki',
                'name'     => 'Rizki',
                'password' => Hash::make('rizki123'),
                'role'     => 'instruktur',
            ]
        );
        User::updateOrCreate(
            ['email' => 'devina@myrobo.id'],
            [
                'username' => 'devina',
                'name'     => 'Devina',
                'password' => Hash::make('devina123'),
                'role'     => 'instruktur',
            ]
        );
        User::updateOrCreate(
            ['email' => 'nabila@myrobo.id'],
            [
                'username' => 'nabila',
                'name'     => 'Nabila',
                'password' => Hash::make('nabila123'),
                'role'     => 'instruktur',
            ]
        );
        
        

        // ADMIN SEKOLAH
        User::updateOrCreate(
            ['email' => 'adminsekolah@myrobo.id'],
            [
                'username' => 'adminsekolah',
                'name'     => 'Admin Sekolah',
                'password' => Hash::make('sekolah123'),
                'role'     => 'admin_sekolah',
                'sekolah_id' => 1,
            ]
        );
    }
}
