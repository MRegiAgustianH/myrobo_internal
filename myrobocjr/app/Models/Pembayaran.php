<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = [
        'peserta_id',
        'home_private_id',
        'sekolah_id',
        'jenis_peserta',
        'tanggal_bayar',
        'bulan',
        'tahun',
        'jumlah',
        'status',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function homePrivate()
    {
        return $this->belongsTo(HomePrivate::class);
    }

    public function isSekolah()
    {
        return $this->jenis_peserta === 'sekolah';
    }

    public function isHomePrivate()
    {
        return $this->jenis_peserta === 'home_private';
    }

    public function nominal()
    {
        return $this->isHomePrivate() ? 450000 : 150000;
    }
}

