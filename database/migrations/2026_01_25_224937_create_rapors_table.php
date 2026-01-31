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
        Schema::create('rapors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sekolah_id')->constrained();
            $table->foreignId('peserta_id')->constrained();
            $table->foreignId('semester_id')->constrained();

            $table->string('nilai_akhir');  
            $table->text('materi');
            $table->text('kesimpulan')->nullable();

            $table->timestamps();

            $table->unique(['peserta_id', 'semester_id']);
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapors');
    }
};
