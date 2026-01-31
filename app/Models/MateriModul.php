<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriModul extends Model
{
    //
    protected $fillable = [
        'materi_id',
        'judul_modul',
        'file_pdf',
        'urutan',
        'status'
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }
}
