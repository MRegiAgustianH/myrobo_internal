<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('home_privates', function (Blueprint $table) {
            $table->id();

            // Data utama
            $table->string('nama_kegiatan');     
            $table->string('nama_peserta');
            $table->string('nama_wali')->nullable();
            $table->string('no_hp')->nullable();

            // Lokasi belajar
            $table->text('alamat')->nullable();

            // Catatan tambahan
            $table->text('catatan')->nullable();

            // Status
            $table->enum('status', ['aktif', 'nonaktif'])
                  ->default('aktif');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('home_privates');
    }
};
