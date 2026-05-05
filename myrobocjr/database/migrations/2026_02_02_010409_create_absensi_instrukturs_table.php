<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi_instrukturs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('instruktur_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])
                ->default('alfa');

            $table->string('keterangan')->nullable();

            $table->timestamps();

            $table->unique(
                ['jadwal_id', 'instruktur_id', 'tanggal'],
                'unique_absensi_instruktur'
            );
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_instrukturs');
    }
};
