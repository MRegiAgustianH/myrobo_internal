<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_id',
        'nama_sekolah',
        'alamat',
        'kontak',
        'logo',
        'nominal_pembayaran',
        'jumlah_periode',
        'pertemuan_per_periode',
        'tgl_mulai_kerjasama',
        'tgl_akhir_kerjasama',
    ];

    protected $casts = [
        'tgl_mulai_kerjasama' => 'date',
        'tgl_akhir_kerjasama' => 'date',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }

    public function raporTugas()
    {
        return $this->hasMany(RaporTugas::class);
    }
}