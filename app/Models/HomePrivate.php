<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomePrivate extends Model
{
    use HasFactory;

    protected $table = 'home_privates';

    protected $fillable = [
        'cabang_id',
        'nama_kegiatan',
        'nama_peserta',
        'nama_wali',
        'no_hp',
        'alamat',
        'catatan',
        'status',
    ];

    protected $attributes = [
        'status' => 'aktif',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSearch($query, $keyword)
    {
        if (!$keyword) return $query;

        return $query->where(function ($q) use ($keyword) {
            $q->where('nama_kegiatan', 'like', "%{$keyword}%")
              ->orWhere('nama_peserta', 'like', "%{$keyword}%")
              ->orWhere('nama_wali', 'like', "%{$keyword}%");
        });
    }
}