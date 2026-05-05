<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    //
    protected $fillable = [
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
}
