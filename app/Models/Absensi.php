<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    //
    protected $fillable = [
        'jadwal_id',
        'peserta_id',
        'status',
        'tanggal',
        'keterangan'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    
}
