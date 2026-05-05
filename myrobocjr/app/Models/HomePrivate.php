<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomePrivate extends Model
{
    use HasFactory;

    protected $table = 'home_privates';

    /**
     * Kolom yang boleh diisi mass-assignment
     */
    protected $fillable = [
        'nama_kegiatan',
        'nama_peserta',
        'nama_wali',
        'no_hp',
        'alamat',
        'catatan',
        'status',
    ];

    /**
     * Default attribute
     */
    protected $attributes = [
        'status' => 'aktif',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'status' => 'string',
    ];

    /* =====================================================
     | RELATIONSHIPS
     |=====================================================*/

    /**
     * Home Private bisa memiliki banyak jadwal
     */
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    /* =====================================================
     | SCOPES
     |=====================================================*/

    /**
     * Scope hanya data aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope pencarian sederhana
     */
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
