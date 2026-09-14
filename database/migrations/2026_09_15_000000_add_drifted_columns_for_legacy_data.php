<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaikan schema drift untuk data legacy.
 *
 * Beberapa file `create_*` sudah dijalankan di produksi, LALU diedit lagi
 * (menambah kolom) tanpa migration terpisah. Laravel tidak menjalankan ulang
 * migration yang sudah tercatat di tabel `migrations`, sehingga kolom hasil
 * edit-belakangan bisa tidak pernah ada di DB hasil restore.
 *
 * Migration ini menambahkan kolom-kolom tersebut secara eksplisit, dengan
 * guard Schema::hasColumn() supaya AMAN dijalankan baik kolomnya sudah ada
 * (deployment yang migrate-nya setelah edit) maupun belum (migrate sebelum edit).
 *
 * Catatan: kolom `cabang_id` TIDAK ditangani di sini — itu dibuat oleh
 * 2026_08_04_033537_add_cabang_and_logo dan 2026_09_01_003303.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ===================== USERS =====================
        // PENTING: login memakai `username` (lihat LoginRequest::authenticate()).
        // Kalau kolom ini tidak ada, TIDAK ADA yang bisa login.
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->after('name');
            });
        }

        // ===================== JADWALS =====================
        if (!Schema::hasColumn('jadwals', 'jenis_jadwal')) {
            Schema::table('jadwals', function (Blueprint $table) {
                $table->enum('jenis_jadwal', ['sekolah', 'home_private'])
                    ->default('sekolah')
                    ->after('id');
            });
        }

        if (!Schema::hasColumn('jadwals', 'home_private_id')) {
            Schema::table('jadwals', function (Blueprint $table) {
                $table->foreignId('home_private_id')
                    ->nullable()
                    ->after('sekolah_id')
                    ->constrained('home_privates')
                    ->nullOnDelete();
            });
        }

        // sekolah_id perlu nullable untuk jadwal home private
        $this->makeNullable('jadwals', 'sekolah_id');

        // ===================== ABSENSIS =====================
        if (!Schema::hasColumn('absensis', 'home_private_id')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->foreignId('home_private_id')
                    ->nullable()
                    ->after('peserta_id')
                    ->constrained('home_privates')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('absensis', 'keterangan')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->string('keterangan')->nullable()->after('status');
            });
        }

        // ===================== ABSENSI INSTRUKTURS =====================
        if (!Schema::hasColumn('absensi_instrukturs', 'keterangan')) {
            Schema::table('absensi_instrukturs', function (Blueprint $table) {
                $table->string('keterangan')->nullable()->after('status');
            });
        }

        // enum `sakit` pada status: aman di MySQL, dijalankan via raw SQL
        $this->widenAbsensiInstrukturStatus();

        // ===================== PEMBAYARANS =====================
        if (!Schema::hasColumn('pembayarans', 'jenis_peserta')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->enum('jenis_peserta', ['sekolah', 'home_private'])
                    ->default('sekolah')
                    ->after('id');
            });
        }

        if (!Schema::hasColumn('pembayarans', 'home_private_id')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->foreignId('home_private_id')
                    ->nullable()
                    ->after('peserta_id')
                    ->constrained('home_privates')
                    ->nullOnDelete();
            });
        }

        // ===================== RAPORS =====================
        if (!Schema::hasColumn('rapors', 'rapor_tugas_id')) {
            Schema::table('rapors', function (Blueprint $table) {
                $table->foreignId('rapor_tugas_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('rapor_tugas')
                    ->cascadeOnDelete();
            });
        }

        if (!Schema::hasColumn('rapors', 'materi_id')) {
            Schema::table('rapors', function (Blueprint $table) {
                $table->foreignId('materi_id')
                    ->nullable()
                    ->after('semester_id')
                    ->constrained('materis')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('rapors', 'kesimpulan')) {
            Schema::table('rapors', function (Blueprint $table) {
                $table->text('kesimpulan')->nullable();
            });
        }

        if (!Schema::hasColumn('rapors', 'catatan_revisi')) {
            Schema::table('rapors', function (Blueprint $table) {
                // TIDAK pakai after('status'): `status` ditambahkan di blok berikutnya,
                // jadi urutan kolom tidak dijamin ada saat baris ini dijalankan.
                $table->string('catatan_revisi')->nullable();
            });
        }

        if (!Schema::hasColumn('rapors', 'status')) {
            Schema::table('rapors', function (Blueprint $table) {
                // tanpa after(): hindari ketergantungan urutan antar kolom drift
                $table->enum('status', ['draft', 'submitted', 'revision', 'approved'])
                    ->default('draft');
            });
        }

        // nilai_akhir perlu jadi nullable + varchar(2) agar cocok dengan schema target
        $this->normalizeNilaiAkhir();
    }

    public function down(): void
    {
        // Tidak di-rollback: kolom-kolom ini adalah bagian dari schema target.
        // Menghapusnya akan merusak data legacy yang sudah di-backfill.
    }

    /**
     * Ubah kolom jadi nullable (idempoten).
     */
    private function makeNullable(string $table, string $column): void
    {
        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        $current = collect(Schema::getColumns($table))
            ->firstWhere('name', $column);

        // Kolom sudah nullable -> tidak perlu apa-apa
        if ($current && $current['nullable'] === true) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($column) {
            $table->unsignedBigInteger($column)->nullable()->change();
        });
    }

    /**
     * Lebarkan enum status absensi_instrukturs agar mencakup 'sakit'.
     * MySQL-only; dilewati kalau sudah lebar atau driver bukan MySQL.
     */
    private function widenAbsensiInstrukturStatus(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $current = collect(Schema::getColumns('absensi_instrukturs'))
            ->firstWhere('name', 'status');

        if ($current && str_contains($current['type'], "'sakit'")) {
            return; // sudah lebar
        }

        Schema::getConnection()->statement(
            "ALTER TABLE absensi_instrukturs MODIFY COLUMN status ENUM('hadir','izin','sakit','alfa') DEFAULT 'alfa'"
        );
    }

    /**
     * Samakan bentuk kolom nilai_akhir dengan schema target (varchar(2), nullable).
     */
    private function normalizeNilaiAkhir(): void
    {
        if (!Schema::hasColumn('rapors', 'nilai_akhir')) {
            return;
        }

        $current = collect(Schema::getColumns('rapors'))
            ->firstWhere('name', 'nilai_akhir');

        // Sudah sesuai target
        if ($current && $current['nullable'] === true && str_contains($current['type'], 'varchar(2)')) {
            return;
        }

        Schema::table('rapors', function (Blueprint $table) {
            $table->string('nilai_akhir', 2)->nullable()->change();
        });
    }
};
