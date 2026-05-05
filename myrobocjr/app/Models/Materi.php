<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'nama_materi',
        'deskripsi',
        'status',
    ];

    public function jadwals()
    {
        return $this->belongsToMany(Jadwal::class, 'jadwal_materi');
    }

    public function moduls()
    {
        return $this->hasMany(MateriModul::class)->orderBy('urutan');
    }
    
    public function kompetensis()
    {
        return $this->hasMany(Kompetensi::class);
    }
}
