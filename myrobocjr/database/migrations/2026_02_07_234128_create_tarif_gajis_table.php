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
        Schema::create('tarif_gajis', function (Blueprint $table) {
        $table->id();
        $table->enum('jenis_jadwal', ['sekolah', 'home_private']);

        $table->foreignId('sekolah_id')
            ->nullable()
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('home_private_id')
            ->nullable()
            ->constrained('home_privates')
            ->cascadeOnDelete();

        $table->unsignedInteger('tarif');
        $table->timestamps();

        $table->unique([
            'jenis_jadwal',
            'sekolah_id',
            'home_private_id'
        ]);
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_gajis');
    }
};
