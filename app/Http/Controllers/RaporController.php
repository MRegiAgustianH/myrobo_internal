<?php

namespace App\Http\Controllers;

use App\Models\Rapor;
use App\Models\NilaiRapor;
use App\Models\Semester;
use App\Models\Kompetensi;
use App\Models\Peserta;
use App\Models\Sekolah;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaporController extends Controller
{
    /* =========================
     * MANAJEMEN RAPOR (CARD)
     * ========================= */
    public function manajemen()
    {
        $rapors = Rapor::with(['sekolah','peserta','semester'])
            ->latest()
            ->get();

        return view('admin.rapor.manajemen', compact('rapors'));
    }

    /* =========================
     * CREATE
     * ========================= */
    public function create()
    {
        return view('admin.rapor.create', [
            'sekolahs'     => Sekolah::orderBy('nama_sekolah')->get(),
            'pesertas'    => Peserta::orderBy('nama')->get(),
            'semesters'   => Semester::orderBy('nama_semester')->get(),
            'kompetensis' => Kompetensi::with('indikatorKompetensis')->get(),
        ]);
    }

    /* =========================
     * STORE
     * ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'sekolah_id'  => 'required|exists:sekolahs,id',
            'peserta_id'  => 'required|exists:pesertas,id',
            'semester_id' => 'required|exists:semesters,id',
            'materi'      => 'required|string',
            'nilai_akhir' => 'required|in:A,B,C',
            'nilai'       => 'required|array',
            'nilai.*'     => 'required|in:C,B,SB',
            'kesimpulan'  => 'nullable|string',
        ]);

        $exists = Rapor::where('peserta_id', $request->peserta_id)
            ->where('semester_id', $request->semester_id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'peserta_id' =>
                        'Rapor untuk peserta ini pada semester tersebut sudah ada. Silakan edit rapor yang tersedia.'
                ]);
        }


        DB::transaction(function () use ($request) {

            $rapor = Rapor::create([
                'sekolah_id'  => $request->sekolah_id,
                'peserta_id'  => $request->peserta_id,
                'semester_id' => $request->semester_id,
                'materi'      => $request->materi,
                'nilai_akhir' => $request->nilai_akhir,
                'kesimpulan'  => $request->kesimpulan,
            ]);

            foreach ($request->nilai as $indikatorId => $nilai) {
                NilaiRapor::create([
                    'rapor_id'                => $rapor->id,
                    'indikator_kompetensi_id' => $indikatorId,
                    'nilai'                   => $nilai,
                ]);
            }
        });

        return redirect()
            ->route('rapor.manajemen')
            ->with('success', 'Rapor berhasil ditambahkan');
    }

    /* =========================
     * EDIT
     * ========================= */
    public function edit(Rapor $rapor)
    {
        $rapor->load('nilai');

        return view('admin.rapor.edit', [
            'rapor'       => $rapor,
            'sekolahs'    => Sekolah::orderBy('nama_sekolah')->get(),
            'pesertas'    => Peserta::orderBy('nama')->get(),
            'semesters'   => Semester::orderBy('nama_semester')->get(),
            'kompetensis' => Kompetensi::with('indikatorKompetensis')->get(),
        ]);
    }

    /* =========================
     * UPDATE
     * ========================= */
    public function update(Request $request, Rapor $rapor)
    {
        $request->validate([
            'sekolah_id'  => 'required|exists:sekolahs,id',
            'peserta_id'  => 'required|exists:pesertas,id',
            'semester_id' => 'required|exists:semesters,id',
            'materi'      => 'required|string',
            'nilai_akhir' => 'required|in:A,B,C',
            'nilai'       => 'required|array',
            'nilai.*'     => 'required|in:C,B,SB',
            'kesimpulan'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $rapor) {

            $rapor->update([
                'sekolah_id'  => $request->sekolah_id,
                'peserta_id'  => $request->peserta_id,
                'semester_id' => $request->semester_id,
                'materi'      => $request->materi,
                'nilai_akhir' => $request->nilai_akhir,
                'kesimpulan'  => $request->kesimpulan,
            ]);

            // Hapus nilai lama agar tidak dobel
            $rapor->nilai()->delete();

            foreach ($request->nilai as $indikatorId => $nilai) {
                NilaiRapor::create([
                    'rapor_id'                => $rapor->id,
                    'indikator_kompetensi_id' => $indikatorId,
                    'nilai'                   => $nilai,
                ]);
            }
        });

        return redirect()
            ->route('rapor.manajemen')
            ->with('success', 'Rapor berhasil diperbarui');
    }

    /* =========================
     * DELETE
     * ========================= */
    public function destroy(Rapor $rapor)
    {
        $rapor->delete();

        return back()->with('success', 'Rapor berhasil dihapus');
    }

    
    public function cetak($id)
    {
        $rapor = Rapor::with([
            'peserta',
            'semester',
            'nilaiRapors.indikatorKompetensi.kompetensi'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.rapor.cetak', compact('rapor'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('rapor-'.$rapor->peserta->nama.'.pdf');
    }


}
