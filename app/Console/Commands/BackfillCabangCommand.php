<?php

namespace App\Console\Commands;

use App\Models\Cabang;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Backfill `cabang_id` untuk data legacy yang dibuat sebelum fitur cabang ada.
 *
 * Kenapa perlu: 6 controller memfilter `where('cabang_id', $cabangId)`.
 * Baris ber-cabang_id NULL tidak akan muncul untuk admin_cabang / sekretaris /
 * bendahara. Catatan penting: Laravel mengubah `where('cabang_id', null)`
 * menjadi `whereNull`, jadi kalau backfill setengah jalan, admin_cabang justru
 * melihat DATA LAMA SAJA (bukan halaman kosong) — terlihat seperti data hantu.
 *
 * Command ini idempoten: hanya menyentuh baris yang cabang_id-nya masih NULL.
 * Mode --dry-run menghitung hasil yang SAMA dengan eksekusi sebenarnya, karena
 * target dihitung dengan "peta efektif" (NULL diperlakukan sebagai nilai yang
 * akan diisi), bukan dari state DB yang belum berubah.
 */
class BackfillCabangCommand extends Command
{
    protected $signature = 'legacy:backfill-cabang
                            {--kode=CJR001 : Kode cabang tujuan untuk data yang tidak punya relasi}
                            {--include-orphan-jadwal : Ikut isi jadwal yang induknya kosong (mis. jenis_jadwal=sekolah tapi sekolah_id NULL)}
                            {--dry-run : Tampilkan rencana perubahan tanpa menyimpan}';

    protected $description = 'Isi cabang_id untuk data legacy (sebelum fitur cabang)';

    private bool $dryRun = false;

    /** @var array<string,int> id => cabang_id efektif, per tabel */
    private array $maps = [];

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');
        $kode = $this->option('kode');

        $cabang = Cabang::where('kode_cabang', $kode)->first();

        if (!$cabang) {
            $this->error("Cabang dengan kode '{$kode}' tidak ditemukan.");
            $this->line('Buat dulu cabangnya, mis:');
            $this->line("  php artisan tinker --execute=\"App\\Models\\Cabang::firstOrCreate(['kode_cabang' => '{$kode}'], ['nama_cabang' => 'CIANJUR', 'is_aktif' => true]);\"");

            return self::FAILURE;
        }

        $target = (int) $cabang->id;

        $this->info(($this->dryRun ? '[DRY RUN] ' : '') . "Cabang tujuan: {$cabang->nama_cabang} (id={$target}, kode={$cabang->kode_cabang})");
        $this->newLine();

        if (!$this->dryRun && !$this->confirm('Lanjutkan backfill cabang_id?', true)) {
            $this->warn('Dibatalkan.');

            return self::SUCCESS;
        }

        // URUTAN PENTING: induk dulu, baru anak yang menurunkan cabang_id darinya.
        $plan = [
            'sekolahs' => $this->planSekolahs($target),
            'home_privates' => $this->planHomePrivates($target),
            'users' => $this->planUsers($target),
            'rapor_tugas' => $this->planRaporTugas(),
            'jadwals' => $this->planJadwals(),
            'tarif_gajis' => $this->planTarifGajis(),
            'keuangans' => $this->planKeuangans($target),
        ];

        $total = 0;

        foreach ($plan as $table => $changes) {
            $count = count($changes);

            if ($count > 0 && !$this->dryRun) {
                foreach (array_chunk($changes, 500, true) as $chunk) {
                    foreach ($chunk as $id => $cabangId) {
                        DB::table($table)->where('id', $id)->update(['cabang_id' => $cabangId]);
                    }
                }
            }

            $total += $count;

            $this->line(sprintf(
                '  %-16s %s %d baris',
                $table,
                $this->dryRun ? '->' : 'OK',
                $count
            ));
        }

        $this->reportSkipped($plan);
        $this->newLine();

        if ($this->dryRun) {
            $this->warn("Total baris yang AKAN diubah: {$total}");
            $this->line('Jalankan tanpa --dry-run untuk menerapkan.');
        } else {
            $this->info("Selesai. Total baris diubah: {$total}");
        }

        return self::SUCCESS;
    }

    // =====================================================================
    // PETA EFEKTIF
    // NULL diperlakukan sebagai $fallback, karena baris tsb memang akan
    // diisi. Ini yang membuat --dry-run dan eksekusi nyata menghasilkan
    // angka yang sama.
    // =====================================================================

    private function map(string $table, int $fallback): array
    {
        if (!isset($this->maps[$table])) {
            $this->maps[$table] = DB::table($table)
                ->select('id', 'cabang_id')
                ->get()
                ->mapWithKeys(fn ($r) => [(int) $r->id => (int) ($r->cabang_id ?? $fallback)])
                ->all();
        }

        return $this->maps[$table];
    }

    // =====================================================================
    // PERENCANAAN PER TABEL
    // Setiap method mengembalikan array [id => cabang_id] yang PERLU diubah.
    // =====================================================================

    /**
     * Tanpa pointer induk -> assign ke cabang tujuan.
     */
    private function planSekolahs(int $target): array
    {
        return $this->rowsNeedingCabang('sekolahs', $target);
    }

    /**
     * Tanpa pointer induk -> assign ke cabang tujuan.
     */
    private function planHomePrivates(int $target): array
    {
        return $this->rowsNeedingCabang('home_privates', $target);
    }

    /**
     * admin_sekolah diturunkan dari sekolahnya; role lain -> cabang tujuan.
     */
    private function planUsers(int $target): array
    {
        $sekolahMap = $this->map('sekolahs', $target);
        $changes = [];

        foreach ($this->nullCabangRows('users') as $row) {
            $derived = null;

            if ($row->role === 'admin_sekolah' && $row->sekolah_id !== null) {
                $derived = $sekolahMap[(int) $row->sekolah_id] ?? null;
            }

            $changes[(int) $row->id] = $derived ?? $target;
        }

        return $changes;
    }

    /**
     * sekolah_id NOT NULL -> derivasi total.
     */
    private function planRaporTugas(): array
    {
        if (!$this->hasColumn('rapor_tugas', 'sekolah_id')) {
            return [];
        }

        $sekolahMap = $this->map('sekolahs', $this->targetId());

        return $this->deriveVia('rapor_tugas', 'sekolah_id', $sekolahMap);
    }

    /**
     * jenis_jadwal='sekolah' -> sekolah_id; 'home_private' -> home_private_id.
     *
     * Jadwal yang induknya NULL (mis. jenis_jadwal='sekolah' tapi sekolah_id NULL)
     * TIDAK bisa diturunkan. Secara default dibiarkan NULL supaya tidak salah tebak.
     * Pakai --include-orphan-jadwal untuk memaksanya ke cabang tujuan.
     */
    private function planJadwals(): array
    {
        $target = $this->targetId();
        $sekolahMap = $this->map('sekolahs', $target);
        $hpMap = $this->map('home_privates', $target);

        $changes = $this->deriveVia('jadwals', 'sekolah_id', $sekolahMap);

        foreach ($this->deriveVia('jadwals', 'home_private_id', $hpMap) as $id => $cabangId) {
            // jangan timpa hasil derivasi sekolah
            $changes[$id] ??= $cabangId;
        }

        if ($this->option('include-orphan-jadwal')) {
            foreach ($this->nullCabangRows('jadwals') as $row) {
                if (!isset($changes[(int) $row->id])) {
                    $changes[(int) $row->id] = $target;
                }
            }
        }

        return $changes;
    }

    /**
     * sekolah_id, else home_private_id.
     *
     * PENTING: baris fallback global (jenis_jadwal='home_private' dengan
     * sekolah_id DAN home_private_id dua-duanya NULL) SENGAJA dibiarkan NULL,
     * karena dipakai sebagai tarif default semua cabang (lihat Jadwal::tarif()).
     */
    private function planTarifGajis(): array
    {
        $target = $this->targetId();
        $sekolahMap = $this->map('sekolahs', $target);
        $hpMap = $this->map('home_privates', $target);

        $changes = $this->deriveVia('tarif_gajis', 'sekolah_id', $sekolahMap);

        foreach ($this->deriveVia('tarif_gajis', 'home_private_id', $hpMap) as $id => $cabangId) {
            $changes[$id] ??= $cabangId;
        }

        return $changes;
    }

    /**
     * keuangans: sekolah_id -> sekolahs; else sumber Pembayaran -> pembayarans;
     * else sumber User (gaji instruktur) -> users.cabang_id; else cabang tujuan.
     */
    private function planKeuangans(int $target): array
    {
        if (!$this->hasColumn('keuangans', 'cabang_id')) {
            return [];
        }

        $sekolahMap = $this->map('sekolahs', $target);
        $userMap = $this->map('users', $target);

        $changes = [];

        foreach ($this->nullCabangRows('keuangans') as $row) {
            $derived = null;

            // 1) langsung lewat sekolah_id
            if ($row->sekolah_id !== null) {
                $derived = $sekolahMap[(int) $row->sekolah_id] ?? null;
            }

            // 2) lewat sumber polymorphic
            if ($derived === null && $row->sumber_id !== null && $row->sumber_type !== null) {
                $derived = $this->deriveFromSumber($row, $sekolahMap, $userMap);
            }

            // 3) sisanya (mis. gaji yang instrukturnya belum punya cabang) -> tujuan
            $changes[(int) $row->id] = $derived ?? $target;
        }

        return $changes;
    }

    /**
     * keuangans.sumber_id -> tabel sumber -> cabang_id.
     *
     * `pembayarans` TIDAK punya kolom cabang_id (scoping-nya transitif lewat
     * sekolahs), jadi harus diturunkan lewat sekolah_id-nya.
     */
    private function deriveFromSumber(object $row, array $sekolahMap, array $userMap): ?int
    {
        $type = $row->sumber_type;

        if ($type === Pembayaran::class) {
            $sekolahId = DB::table('pembayarans')
                ->where('id', $row->sumber_id)
                ->value('sekolah_id');

            return $sekolahId !== null ? ($sekolahMap[(int) $sekolahId] ?? null) : null;
        }

        if ($type === User::class) {
            return $userMap[(int) $row->sumber_id] ?? null;
        }

        return null;
    }

    // =====================================================================
    // HELPER
    // =====================================================================

    private function targetId(): int
    {
        return (int) Cabang::where('kode_cabang', $this->option('kode'))->value('id');
    }

    /**
     * Baris yang cabang_id-nya masih NULL.
     */
    private function nullCabangRows(string $table): \Illuminate\Support\Collection
    {
        if (!$this->hasColumn($table, 'cabang_id')) {
            return collect();
        }

        return DB::table($table)->whereNull('cabang_id')->get();
    }

    /**
     * Semua baris NULL -> $target.
     */
    private function rowsNeedingCabang(string $table, int $target): array
    {
        return $this->nullCabangRows($table)
            ->mapWithKeys(fn ($r) => [(int) $r->id => $target])
            ->all();
    }

    /**
     * Isi cabang_id dari peta induk lewat kolom FK.
     */
    private function deriveVia(string $table, string $foreignKey, array $parentMap): array
    {
        if (!$this->hasColumn($table, $foreignKey) || !$this->hasColumn($table, 'cabang_id')) {
            return [];
        }

        $changes = [];

        foreach ($this->nullCabangRows($table) as $row) {
            $fk = $row->{$foreignKey};

            if ($fk === null) {
                continue;
            }

            $derived = $parentMap[(int) $fk] ?? null;

            if ($derived !== null) {
                $changes[(int) $row->id] = $derived;
            }
        }

        return $changes;
    }

    /**
     * Laporkan baris yang sengaja TIDAK diubah, supaya tidak jadi kejutan.
     * Dihitung dari rencana, sehingga angka dry-run = angka sebenarnya.
     */
    private function reportSkipped(array $plan): void
    {
        $skipped = [];

        foreach (['sekolahs', 'home_privates', 'users', 'rapor_tugas', 'jadwals', 'tarif_gajis', 'keuangans'] as $table) {
            if (!$this->hasColumn($table, 'cabang_id')) {
                continue;
            }

            $total = DB::table($table)->count();
            $willChange = count($plan[$table] ?? []);

            // Baris yang sudah punya cabang_id + yang tidak masuk rencana
            $remaining = $total - $willChange;

            if ($remaining > 0) {
                $skipped[] = "{$table}={$remaining}";
            }
        }

        if ($skipped) {
            $this->newLine();
            $this->line('  Tetap tanpa perubahan: ' . implode(', ', $skipped));
            $this->line('  (termasuk baris yang sudah punya cabang_id)');
        }

        // Jelaskan baris yang sengaja dilewati karena induknya tidak ada,
        // supaya tidak jadi kejutan (data ini tidak akan terlihat admin_cabang).
        $orphans = $this->findOrphanJadwals();

        if ($orphans->isNotEmpty()) {
            $this->newLine();
            $this->warn('  PERHATIAN: ' . $orphans->count() . ' jadwal tidak punya induk, jadi cabang_id-nya dibiarkan NULL:');

            foreach ($orphans as $o) {
                $this->line("    - jadwal id={$o->id} ({$o->jenis_jadwal}) \"{$o->nama_kegiatan}\"");
            }

            $this->line('  Jadwal ini TIDAK akan terlihat oleh admin_cabang.');
            $this->line('  Kalau memang milik cabang tujuan, jalankan ulang dengan --include-orphan-jadwal');
        }
    }

    /**
     * Jadwal yang cabang_id-nya NULL dan tidak punya induk untuk diturunkan.
     */
    private function findOrphanJadwals(): \Illuminate\Support\Collection
    {
        if (!$this->hasColumn('jadwals', 'cabang_id')) {
            return collect();
        }

        return DB::table('jadwals')
            ->whereNull('cabang_id')
            ->whereNull('sekolah_id')
            ->whereNull('home_private_id')
            ->get(['id', 'jenis_jadwal', 'nama_kegiatan']);
    }

    private function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }
}
