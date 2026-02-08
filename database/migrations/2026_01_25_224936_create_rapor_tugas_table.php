<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rapor_tugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sekolah_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('semester_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('instruktur_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'pending',      // baru dibuat admin
                'in_progress',  // instruktur mulai mengisi
                'completed'     // semua rapor selesai
            ])->default('pending');

            $table->date('deadline')->nullable();

            $table->timestamps();

            $table->unique(['sekolah_id', 'semester_id'], 'unique_sekolah_semester');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_tugas');
    }
};
