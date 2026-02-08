<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    protected $fillable = [
        'rapor_tugas_id',
        'sekolah_id',
        'peserta_id',
        'semester_id',
        'materi_id',
        'materi',
        'nilai_akhir',
        'catatan_revisi',
        'kesimpulan',
        'status',
    ];

    public function tugas()
    {
        return $this->belongsTo(RaporTugas::class, 'rapor_tugas_id');
    }

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

    public function materiRef()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }


    

}

