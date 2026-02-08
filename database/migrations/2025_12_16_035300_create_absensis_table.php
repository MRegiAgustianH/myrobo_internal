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

            // PESERTA SEKOLAH
            $table->foreignId('peserta_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // PESERTA HOME PRIVATE
            $table->foreignId('home_private_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('tanggal');
            $table->enum('status', ['hadir','izin','sakit','alfa'])->default('alfa');
            $table->string('keterangan')->nullable();

            $table->timestamps();

            // UNIQUE UNTUK PESERTA SEKOLAH
            $table->unique(
                ['jadwal_id', 'tanggal', 'peserta_id'],
                'unique_absensi_peserta'
            );

            // UNIQUE UNTUK HOME PRIVATE
            $table->unique(
                ['jadwal_id', 'tanggal', 'home_private_id'],
                'unique_absensi_home_private'
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
