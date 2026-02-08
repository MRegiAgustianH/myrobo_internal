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

            $table->foreignId('rapor_tugas_id')->constrained()->cascadeOnDelete();

            $table->foreignId('sekolah_id')->constrained()->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->foreignId('materi_id')->nullable()->constrained()->nullOnDelete();

            $table->text('materi')->nullable();
            $table->string('nilai_akhir', 2)->nullable();
            $table->text('kesimpulan')->nullable();

            $table->enum('status', ['draft','submitted','revision','approved'])
                ->default('draft');
            $table->string('catatan_revisi')->nullable();

            $table->timestamps();

            $table->unique(['rapor_tugas_id', 'peserta_id']);
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
