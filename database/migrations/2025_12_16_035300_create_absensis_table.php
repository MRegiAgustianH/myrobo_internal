<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('peserta_id')
                ->constrained()
                ->cascadeOnDelete();

            // 👉 ABSENSI HARIAN
            $table->date('tanggal');

            // 👉 STATUS ABSENSI
            $table->enum('status', [
                'hadir',
                'izin',
                'sakit',
                'alfa'
            ])->default('alfa');

            $table->text('keterangan')->nullable();

            $table->timestamps();

            // 👉 UNIQUE PER HARI
            $table->unique(
                ['jadwal_id', 'peserta_id', 'tanggal'],
                'absensis_unique_per_hari'
            );
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
