<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Unique constraint untuk mode periode (tahun_ajaran + semester + periode)
        // NULL values dianggap unik oleh MySQL, jaba tidak konflik dengan data bulanan lama
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->unique(
                ['peserta_id', 'tahun_ajaran', 'semester', 'periode'],
                'unique_pembayaran_sekolah_periode'
            );
            $table->unique(
                ['home_private_id', 'tahun_ajaran', 'semester', 'periode'],
                'unique_pembayaran_home_private_periode'
            );
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropUnique('unique_pembayaran_sekolah_periode');
            $table->dropUnique('unique_pembayaran_home_private_periode');
        });
    }
};