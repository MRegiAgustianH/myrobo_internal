<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    protected $fillable = [
        'cabang_id',
        'tanggal',
        'tipe',
        'kategori',
        'periode',
        'deskripsi',
        'jumlah',
        'sekolah_id',
        'sumber_id',
        'sumber_type',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
}