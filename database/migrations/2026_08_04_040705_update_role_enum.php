<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum role untuk menambah superadmin & admin_cabang
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin','admin','admin_cabang','admin_sekolah','instruktur','bendahara','sekretaris') DEFAULT 'instruktur'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','instruktur','admin_sekolah','bendahara','sekretaris') DEFAULT 'instruktur'");
    }
};
