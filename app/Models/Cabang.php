<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cabang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabang',
        'kode_cabang',
        'alamat',
        'kontak',
        'logo',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function sekolahs()
    {
        return $this->hasMany(Sekolah::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function homePrivates()
    {
        return $this->hasMany(HomePrivate::class);
    }

    public function keuangans()
    {
        return $this->hasMany(Keuangan::class);
    }
}
