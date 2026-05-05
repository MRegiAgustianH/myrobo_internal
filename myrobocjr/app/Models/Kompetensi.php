<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kompetensi extends Model
{
    protected $fillable = ['nama_kompetensi','materi_id'];

    public function indikatorKompetensis()
    {
        return $this->hasMany(IndikatorKompetensi::class);
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

}


