<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    //
    protected $fillable = [
    'sekolah_id',
    'nama',
    'jenis_kelamin',
    'kelas',
    'kontak',
    'status',
];
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

}
