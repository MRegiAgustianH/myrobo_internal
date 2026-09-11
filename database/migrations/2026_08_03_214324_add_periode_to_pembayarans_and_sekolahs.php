<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =============================== PEMBAYARAN ===============================
        Schema::table('pembayarans', function (Blueprint $table) {
            // periode 1-6 dalam satu semester (nullable = data lama berbasis bulan)
            $table->unsignedTinyInteger('periode')->nullable()->after('tahun');
            $table->enum('semester', ['ganjil', 'genap'])->nullable()->after('periode');
            $table->string('tahun_ajaran', 15)->nullable()->after('semester'); // contoh: 2025/2026
        });

        // =============================== SEKOLAH ===============================
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->unsignedTinyInteger('jumlah_periode')->default(6)->after('nominal_pembayaran');
            $table->unsignedTinyInteger('pertemuan_per_periode')->default(4)->after('jumlah_periode');
        });
    }

    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['periode', 'semester', 'tahun_ajaran']);
        });

        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['jumlah_periode', 'pertemuan_per_periode']);
        });
    }
};
