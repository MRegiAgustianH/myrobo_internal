<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kompetensi extends Model
{
    protected $fillable = ['nama_kompetensi'];

    public function indikatorKompetensis()
    {
        return $this->hasMany(IndikatorKompetensi::class);
    }

}


