<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use App\Imports\PesertaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PesertaTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class PesertaController extends Controller
{
    public function bySekolah(Sekolah $sekolah)
    {
        $pesertas = $sekolah->pesertas()->latest()->get();
        return view('admin.peserta.index', compact('sekolah','pesertas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sekolah_id' => 'required|exists:sekolahs,id',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'status' => 'nullable|in:aktif,tidak',
        ]);

        Peserta::create([
            'sekolah_id' => $request->sekolah_id,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kelas' => $request->kelas,
            'kontak' => $request->kontak,
            'status' => $request->status ?? 'aktif',
        ]);

        return back()->with('success','Peserta berhasil ditambahkan');
    }


    public function update(Request $request, Peserta $peserta)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $peserta->update($request->all());

        return back()->with('success','Peserta berhasil diperbarui');
    }

    public function destroy(Peserta $peserta)
    {
        $peserta->delete();
        return back()->with('success','Peserta berhasil dihapus');
    }

    public function import(Request $request, Sekolah $sekolah)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        unset($rows[0]); // hapus header

        $success = 0;
        $errors = [];

        foreach ($rows as $index => $row) {

            // Mapping kolom
            $data = [
                'nama'          => trim($row[0] ?? ''),
                'jenis_kelamin' => strtoupper(trim($row[1] ?? '')),
                'kelas'         => trim($row[2] ?? ''),
                'kontak'        => trim($row[3] ?? ''),
            ];

            // Skip baris kosong total
            if (!array_filter($data)) {
                continue;
            }

            // VALIDASI
            $validator = Validator::make($data, [
                'nama'          => 'required|string',
                'jenis_kelamin' => 'required|in:L,P',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row'    => $index + 1, // nomor baris excel
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // SIMPAN
            Peserta::create([
                'sekolah_id'    => $sekolah->id,
                'nama'          => $data['nama'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'kelas'         => $data['kelas'] ?: null,
                'kontak'        => $data['kontak'] ?: null,
                'status'        => 'aktif',
            ]);

            $success++;
        }

        // HASIL
        if (count($errors) > 0) {
            return back()->with([
                'import_errors' => $errors,
                'success_count' => $success,
            ]);
        }

        return back()->with('success', "Berhasil mengimport {$success} peserta");
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new PesertaTemplateExport,
            'template_peserta.xlsx'
        );
    }


}

