<?php

namespace App\Imports;

use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaImport implements ToModel, WithHeadingRow
{
    protected $sekolahId;

    public function __construct($sekolahId)
    {
        $this->sekolahId = $sekolahId;
    }

    public function model(array $row)
    {
        return new Peserta([
            'sekolah_id'    => $this->sekolahId,
            'nama'          => $row['nama'],
            'jenis_kelamin' => $row['jenis_kelamin'], 
            'kelas'         => $row['kelas'] ?? null,
            'kontak'        => $row['kontak'] ?? null,
            'status'        => 'aktif',
        ]);
    }
}
