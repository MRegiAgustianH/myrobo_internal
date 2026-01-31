<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorKompetensi extends Model
{
    protected $fillable = ['kompetensi_id', 'nama_indikator'];

    public function kompetensi()
    {
        return $this->belongsTo(Kompetensi::class);
    }

    public function raporNilais()
    {
        return $this->hasMany(NilaiRapor::class);
    }

    
}


