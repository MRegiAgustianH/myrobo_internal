<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifGaji extends Model
{
    protected $fillable = [
        'jenis_jadwal',
        'tarif',
        'sekolah_id',
        'home_private_id',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function homePrivate()
    {
        return $this->belongsTo(HomePrivate::class);
    }
}
