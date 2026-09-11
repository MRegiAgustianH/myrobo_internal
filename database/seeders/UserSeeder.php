<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SUPERADMIN
        User::updateOrCreate(
            ['email' => 'superadmin@myrobo.id'],
            [
                'username' => 'superadmin',
                'name'     => 'Superadmin MyRobo',
                'password' => Hash::make('superadmin123'),
                'role'     => 'superadmin',
            ]
        );

        $cjr = \App\Models\Cabang::where('kode_cabang', 'CJR001')->first();
        $bdg = \App\Models\Cabang::where('kode_cabang', 'BDG001')->first();

        // ADMIN CABANG CIANJUR
        User::updateOrCreate(
            ['email' => 'admin_cjr@myrobo.id'],
            [
                'username' => 'admincjr',
                'name'     => 'Admin Cabang Cianjur',
                'password' => Hash::make('cjr123'),
                'role'     => 'admin_cabang',
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );

        // ADMIN CABANG BANDUNG
        User::updateOrCreate(
            ['email' => 'admin_bdg@myrobo.id'],
            [
                'username' => 'adminbdg',
                'name'     => 'Admin Cabang Bandung',
                'password' => Hash::make('bdg123'),
                'role'     => 'admin_cabang',
                'cabang_id'=> $bdg ? $bdg->id : null,
            ]
        );

        // BENDAHARA CABANG CIANJUR
        User::updateOrCreate(
            ['email' => 'bendahara_cjr@myrobo.id'],
            [
                'username' => 'bendaharacjr',
                'name'     => 'Bendahara Cianjur',
                'password' => Hash::make('bendahara123'),
                'role'     => 'bendahara',
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );

        // SEKRETARIS CABANG CIANJUR
        User::updateOrCreate(
            ['email' => 'sekretaris_cjr@myrobo.id'],
            [
                'username' => 'sekretariscjr',
                'name'     => 'Sekretaris Cianjur',
                'password' => Hash::make('sekretaris123'),
                'role'     => 'sekretaris',
                'cabang_id'=> $cjr ? $cjr->id : null,
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
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'bagus@myrobo.id'],
            [
                'username' => 'bagus',
                'name'     => 'Bagus',
                'password' => Hash::make('bagus123'),
                'role'     => 'instruktur',
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'gina@myrobo.id'],
            [
                'username' => 'gina',
                'name'     => 'Gina',
                'password' => Hash::make('gina123'),
                'role'     => 'instruktur',
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'abiyyatun@myrobo.id'],
            [
                'username' => 'abiyyatun',
                'name'     => 'Abiyyatun',
                'password' => Hash::make('abiyyatun123'),
                'role'     => 'instruktur',
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'rizki@myrobo.id'],
            [
                'username' => 'rizki',
                'name'     => 'Rizki',
                'password' => Hash::make('rizki123'),
                'role'     => 'instruktur',
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'devina@myrobo.id'],
            [
                'username' => 'devina',
                'name'     => 'Devina',
                'password' => Hash::make('devina123'),
                'role'     => 'instruktur',
                'cabang_id'=> $cjr ? $cjr->id : null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'nabila@myrobo.id'],
            [
                'username' => 'nabila',
                'name'     => 'Nabila',
                'password' => Hash::make('nabila123'),
                'role'     => 'instruktur',
                'cabang_id'=> $cjr ? $cjr->id : null,
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
