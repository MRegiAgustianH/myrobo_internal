<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PesertaTemplateExport;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PesertaController extends Controller
{
    /**
     * LIST PESERTA PER SEKOLAH
     * GET /sekolah/{sekolah}/peserta
     */
    public function index(Sekolah $sekolah)
    {
        return view('admin.peserta.index', [
            'sekolah'  => $sekolah,
            'pesertas' => $sekolah->pesertas()->latest()->get(),
        ]);
    }

    /**
     * SIMPAN PESERTA
     * POST /sekolah/{sekolah}/peserta
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sekolah_id'    => 'required|exists:sekolahs,id',
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas'         => 'nullable|string|max:50',
            'kontak'        => 'nullable|string|max:50',
            'status'        => 'required|in:aktif,tidak',
        ]);

        Peserta::create($data);

        return back()->with('success', 'Peserta berhasil ditambahkan');
    }

    /**
     * UPDATE PESERTA
     * PATCH /peserta/{peserta}
     */
    public function update(Request $request, Peserta $peserta)
    {
        $data = $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas'         => 'nullable|string|max:50',
            'kontak'        => 'nullable|string|max:50',
            'status'        => 'required|in:aktif,tidak',
        ]);

        $peserta->update($data);

        return back()->with('success', 'Peserta berhasil diperbarui');
    }

    /**
     * HAPUS PESERTA
     * DELETE /peserta/{peserta}
     */
    public function destroy(Peserta $peserta)
    {
        $peserta->delete();

        return back()->with('success', 'Peserta berhasil dihapus');
    }

    /**
     * IMPORT PESERTA
     * POST /sekolah/{sekolah}/peserta/import
     */
    public function import(Request $request, Sekolah $sekolah)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $rows = IOFactory::load(
            $request->file('file')->getPathname()
        )->getActiveSheet()->toArray();

        unset($rows[0]); // header

        $success = 0;
        $errors  = [];

        foreach ($rows as $i => $row) {
            $data = [
                'nama'          => trim($row[0] ?? ''),
                'jenis_kelamin' => strtoupper(trim($row[1] ?? '')),
                'kelas'         => trim($row[2] ?? ''),
                'kontak'        => trim($row[3] ?? ''),
            ];

            if (!array_filter($data)) {
                continue;
            }

            $validator = Validator::make($data, [
                'nama'          => 'required|string',
                'jenis_kelamin' => 'required|in:L,P',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row'    => $i + 1,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

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

        if ($errors) {
            return back()->with([
                'import_errors' => $errors,
                'success_count' => $success,
            ]);
        }

        return back()->with('success', "Berhasil mengimport {$success} peserta");
    }

    /**
     * DOWNLOAD TEMPLATE
     * GET /peserta/template/download
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new PesertaTemplateExport,
            'template_peserta.xlsx'
        );
    }
}
