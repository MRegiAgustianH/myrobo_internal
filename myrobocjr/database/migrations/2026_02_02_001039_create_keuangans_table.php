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
        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->string('kategori');
            $table->string('periode')->nullable();
            $table->text('deskripsi')->nullable();
            $table->bigInteger('jumlah');
            $table->unsignedBigInteger('sekolah_id')->nullable();
            $table->unsignedBigInteger('sumber_id')->nullable();
            $table->string('sumber_type')->nullable();
            $table->timestamps();
            $table->index(['tipe', 'tanggal']);
            $table->index(['kategori']);
            $table->index(['sekolah_id']);
            $table->unique(
                ['tipe', 'kategori', 'sumber_id', 'sumber_type', 'periode'],
                'unique_gaji_instruktur_periode'
            );
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangans');
    }
};
