<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'sekolah_id',
        'cabang_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isInstruktur()
    {
        return $this->role === 'instruktur';
    }

    public function isAdminSekolah()
    {
        return $this->role === 'admin_sekolah';
    }

    public function isAdminCabang()
    {
        return $this->role === 'admin_cabang';
    }

    public function jadwals()
    {
        return $this->belongsToMany(Jadwal::class, 'jadwal_instruktur');
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function absensiInstrukturs()
    {
        return $this->hasMany(\App\Models\AbsensiInstruktur::class, 'instruktur_id');
    }

    public function raporTugas()
    {
        return $this->hasMany(RaporTugas::class, 'instruktur_id');
    }
}