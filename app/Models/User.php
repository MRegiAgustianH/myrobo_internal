<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'sekolah_id',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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

    public function isInstruktur()
    {
        return $this->role === 'instruktur';
    }

    public function isAdminSekolah()
    {
        return $this->role === 'admin_sekolah';
    }

    public function jadwals()
    {
        return $this->belongsToMany(Jadwal::class, 'jadwal_instruktur');
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function absensiInstrukturs()
    {
        return $this->hasMany(\App\Models\AbsensiInstruktur::class, 'instruktur_id');
    }



}
