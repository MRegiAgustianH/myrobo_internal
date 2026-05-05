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
        Schema::create('rapor_nilais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rapor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('indikator_kompetensi_id')
                ->constrained('indikator_kompetensis')
                ->cascadeOnDelete();

            $table->enum('nilai', ['C', 'B', 'SB']);

            $table->timestamps();

            // 1 indikator hanya boleh dinilai sekali per rapor
            $table->unique(['rapor_id', 'indikator_kompetensi_id']);
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor_nilais');
    }
};
