<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PesertaTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        // template kosong (tanpa data)
        return [];
    }

    public function headings(): array
    {
        return [
            'nama',
            'jenis_kelamin',
            'kelas',
            'kontak',
        ];
    }
}
