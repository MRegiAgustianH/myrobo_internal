<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwals';
    protected $fillable = [
        'sekolah_id',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    public function instrukturs()
    {
        return $this->belongsToMany(User::class, 'jadwal_instruktur');
    }

    public function materis()
    {
        return $this->belongsToMany(Materi::class, 'jadwal_materi');
    }
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
    

}


