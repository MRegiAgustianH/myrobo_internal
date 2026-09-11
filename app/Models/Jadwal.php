<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwals';
    protected $fillable = [
        'cabang_id',
        'jenis_jadwal',
        'sekolah_id',
        'home_private_id',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function instrukturs()
    {
        return $this->belongsToMany(User::class, 'jadwal_instruktur');
    }

    public function materis()
    {
        return $this->belongsToMany(Materi::class, 'jadwal_materi');
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function homePrivate()
    {
        return $this->belongsTo(HomePrivate::class);
    }

    public function absensiInstrukturs()
    {
        return $this->hasMany(AbsensiInstruktur::class);
    }

    public function isSekolah(): bool
    {
        return $this->jenis_jadwal === 'sekolah';
    }

    public function isHomePrivate(): bool
    {
        return $this->jenis_jadwal === 'home_private';
    }

    public function isDalamJamAbsensi(): bool
    {
        $now = Carbon::now();

        if (!$now->isSameDay($this->tanggal_mulai)) {
            return false;
        }

        $mulai  = Carbon::parse($this->tanggal_mulai.' '.$this->jam_mulai);
        $selesai = Carbon::parse($this->tanggal_mulai.' '.$this->jam_selesai);

        return $now->between($mulai, $selesai);
    }

    public function tarifInstruktur(): int
    {
        if ($this->jenis_jadwal === 'sekolah') {
            return TarifGaji::where('jenis_jadwal', 'sekolah')
                ->where('sekolah_id', $this->sekolah_id)
                ->value('tarif') ?? 0;
        }

        $tarifSpesifik = TarifGaji::where('jenis_jadwal', 'home_private')
            ->where('home_private_id', $this->home_private_id)
            ->value('tarif');

        if ($tarifSpesifik !== null) {
            return $tarifSpesifik;
        }

        return TarifGaji::where('jenis_jadwal', 'home_private')
            ->whereNull('home_private_id')
            ->value('tarif') ?? 0;
    }
}