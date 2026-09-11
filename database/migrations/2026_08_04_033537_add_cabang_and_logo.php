<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =============================== TABEL CABANG ===============================
        Schema::create('cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cabang');
            $table->string('kode_cabang')->unique();
            $table->string('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // =============================== KOLONOM CABANG_ID ===============================
        // Sekolah
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('logo')->nullable()->after('nominal_pembayaran');
        });

        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->after('sekolah_id')->constrained()->nullOnDelete();
        });

        // Jadwal
        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Home Private
        Schema::table('home_privates', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Keuangan
        Schema::table('keuangans', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Tarif Gaji
        Schema::table('tarif_gajis', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn(['cabang_id', 'logo']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('home_privates', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('keuangans', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('tarif_gajis', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::dropIfExists('cabangs');
    }
};
