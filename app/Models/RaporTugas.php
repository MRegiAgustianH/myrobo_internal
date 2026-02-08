<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaporTugas extends Model
{
    protected $table = 'rapor_tugas';

    protected $fillable = [
        'sekolah_id',
        'semester_id',
        'instruktur_id',
        'status',
        'deadline',
    ];

    /* =====================
     * RELATIONS
     * ===================== */

    // 1 tugas → banyak rapor
    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function instruktur()
    {
        return $this->belongsTo(User::class, 'instruktur_id');
    }

    /* =====================
     * HELPERS
     * ===================== */

    // Progress rapor (x / total)
    public function progress()
    {
        $total = $this->rapors()->count();
        $approved = $this->rapors()->where('status', 'approved')->count();

        return [
            'approved' => $approved,
            'total'    => $total,
        ];
    }
}
