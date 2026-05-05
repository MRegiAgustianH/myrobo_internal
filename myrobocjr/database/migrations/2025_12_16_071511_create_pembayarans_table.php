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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            /*
            |-------------------------------------------------
            | IDENTITAS PESERTA
            |-------------------------------------------------
            | salah satu WAJIB terisi:
            | - peserta_id (sekolah)
            | - home_private_id (home private)
            */
            $table->enum('jenis_peserta', ['sekolah', 'home_private']);
            $table->foreignId('peserta_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('home_private_id')->nullable()->constrained('home_privates')->nullOnDelete();

            /*
            |-------------------------------------------------
            | SEKOLAH (KHUSUS SEKOLAH)
            |-------------------------------------------------
            */
            $table->foreignId('sekolah_id')->nullable()->constrained()->nullOnDelete();

            /*
            |-------------------------------------------------
            | PERIODE PEMBAYARAN
            |-------------------------------------------------
            */
            $table->integer('bulan');  // 1–12
            $table->integer('tahun');  // 2024, 2025, dst

            /*
            |-------------------------------------------------
            | DETAIL PEMBAYARAN
            |-------------------------------------------------
            */
            $table->date('tanggal_bayar')->nullable();
            $table->integer('jumlah')->nullable(); // 150000 / 450000
            $table->enum('status', ['lunas', 'belum'])->default('belum');

            $table->timestamps();

            /*
            |-------------------------------------------------
            | UNIQUE KEY (AMAN)
            |-------------------------------------------------
            | - Sekolah: 1 peserta 1 bulan
            | - Home private: 1 peserta home private 1 bulan
            */
            $table->unique(
                ['peserta_id', 'bulan', 'tahun'],
                'unique_pembayaran_sekolah'
            );

            $table->unique(
                ['home_private_id', 'bulan', 'tahun'],
                'unique_pembayaran_home_private'
            );
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
