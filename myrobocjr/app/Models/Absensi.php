<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'jadwal_id',
        'peserta_id',
        'home_private_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ===============================
    // RELATIONS
    // ===============================

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // ABSENSI SEKOLAH
    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    // ABSENSI HOME PRIVATE
    public function homePrivate()
    {
        return $this->belongsTo(HomePrivate::class);
    }

    // ===============================
    // HELPERS (OPTIONAL TAPI RAPI)
    // ===============================

    public function isSekolah(): bool
    {
        return !is_null($this->peserta_id);
    }

    public function isHomePrivate()
    {
        return !is_null($this->home_private_id);
    }

    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function isHadir(): bool
    {
        return $this->status === 'hadir';
    }


}
