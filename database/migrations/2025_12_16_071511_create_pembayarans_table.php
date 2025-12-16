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

                $table->foreignId('peserta_id')->constrained()->cascadeOnDelete();
                $table->foreignId('sekolah_id')->constrained()->cascadeOnDelete();

                $table->date('tanggal_bayar')->nullable();
                $table->integer('bulan'); // 1–12
                $table->integer('tahun');

                $table->decimal('jumlah', 12, 2)->nullable();
                $table->enum('status', ['lunas', 'belum'])->default('belum');

                $table->timestamps();

                $table->unique(['peserta_id', 'bulan', 'tahun']);
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
