<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    protected $fillable = [

        'sekolah_id',   
        'peserta_id',
        'semester_id',
        'materi',
        'nilai_akhir',
        'kesimpulan',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function nilai()
    {
    return $this->hasMany(NilaiRapor::class);
    }

    public function nilaiRapors()
    {
        return $this->hasMany(NilaiRapor::class);
    }   

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    

}

