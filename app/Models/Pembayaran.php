<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'peserta_id',
        'sekolah_id',
        'tanggal_bayar',
        'bulan',
        'tahun',
        'jumlah',
        'status',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah'        => 'decimal:2',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
}

