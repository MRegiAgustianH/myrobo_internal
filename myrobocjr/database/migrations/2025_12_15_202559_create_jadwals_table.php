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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_jadwal', ['sekolah', 'home_private'])->default('sekolah');
            $table->foreignId('sekolah_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('home_private_id')->nullable()->constrained('home_privates')->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->enum('hari',['senin','selasa','rabu','kamis','jumat','sabtu','minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('lokasi')->nullable();
            $table->enum('status',['aktif','nonaktif'])->default('aktif');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
