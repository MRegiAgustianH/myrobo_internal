<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sekolah extends Model
{
    //
    Use HasFactory;
    protected $fillable = [
        'nama_sekolah',
        'alamat',
        'kontak',
        'tgl_mulai_kerjasama',
        'tgl_akhir_kerjasama',
    ];

    protected $casts = [
        'tgl_mulai_kerjasama' => 'date',
        'tgl_akhir_kerjasama' => 'date',
    ];

    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

}
