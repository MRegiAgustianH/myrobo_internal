<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiInstruktur extends Model
{
    protected $fillable = [
        'jadwal_id',
        'instruktur_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function instruktur()
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
