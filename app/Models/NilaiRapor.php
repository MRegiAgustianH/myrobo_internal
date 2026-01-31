<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiRapor extends Model
{

    protected $table = 'rapor_nilais';  
    protected $fillable = [
        'rapor_id',
        'indikator_kompetensi_id',
        'nilai',
    ];

    public function rapor()
    {
        return $this->belongsTo(Rapor::class);
    }

    public function indikatorKompetensi()
    {
        return $this->belongsTo(IndikatorKompetensi::class);
    }

}

