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
        Schema::create('pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('kelas')->nullable();
            $table->string('kontak')->nullable();
            $table->timestamps();
            $table->enum('status', ['aktif', 'tidak'])->default('aktif');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesertas');
    }
};
