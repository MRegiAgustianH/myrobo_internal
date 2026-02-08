<?php

namespace App\Http\Controllers;

use App\Models\Rapor;
use App\Models\NilaiRapor;
use App\Models\Semester;
use App\Models\Materi;
use App\Models\Peserta;
use App\Models\Sekolah;
use App\Models\RaporTugas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaporController extends Controller
{
    /* =========================
     * MANAJEMEN RAPOR (ADMIN)
     * ========================= */
    public function manajemen()
    {
        $rapors = Rapor::with([
                'sekolah',
                'peserta',
                'semester',
                'materiRef',
                'tugas'
            ])
            ->latest()
            ->get();

        return view('admin.rapor.manajemen', compact('rapors'));
    }

    /* =========================
     * CREATE (sementara / legacy)
     * ========================= */
    public function create()
    {
        return view('admin.rapor.create', [
            'sekolahs'    => Sekolah::orderBy('nama_sekolah')->get(),
            'pesertas'   => [],
            'semesters'  => Semester::orderBy('nama_semester')->get(),
            'materis'    => Materi::orderBy('nama_materi')->get(),
            'kompetensis'=> [],
        ]);
    }

    /* =========================
     * AJAX: Peserta by Sekolah
     * ========================= */
    public function pesertaBySekolah(Sekolah $sekolah)
    {
        return response()->json(
            Peserta::where('sekolah_id', $sekolah->id)
                ->orderBy('nama')
                ->get(['id','nama'])
        );
    }

    /* =========================
     * STORE
     * ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'rapor_tugas_id' => 'required|exists:rapor_tugas,id',
            'sekolah_id'     => 'required|exists:sekolahs,id',
            'peserta_id'     => 'required|exists:pesertas,id',
            'semester_id'    => 'required|exists:semesters,id',
            'materi_id'      => 'required|exists:materis,id',
            'materi'         => 'required|string',
            'nilai_akhir'    => 'required|in:A,B,C',
            'nilai'          => 'required|array',
            'nilai.*'        => 'required|in:C,B,SB',
        ]);

        DB::transaction(function () use ($request) {

            $rapor = Rapor::create([
                'rapor_tugas_id' => $request->rapor_tugas_id,
                'sekolah_id'     => $request->sekolah_id,
                'peserta_id'     => $request->peserta_id,
                'semester_id'    => $request->semester_id,
                'materi_id'      => $request->materi_id,
                'materi'         => $request->materi,
                'nilai_akhir'    => $request->nilai_akhir,
                'kesimpulan'     => $request->kesimpulan,
                'status'         => 'submitted',
            ]);

            foreach ($request->nilai as $indikatorId => $nilai) {
                NilaiRapor::create([
                    'rapor_id'                => $rapor->id,
                    'indikator_kompetensi_id' => $indikatorId,
                    'nilai'                   => $nilai,
                ]);
            }

            // Update status tugas jika perlu
            $rapor->tugas()->update(['status' => 'in_progress']);
        });

        return redirect()
            ->route('rapor.manajemen')
            ->with('success','Rapor berhasil ditambahkan');
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
            'pesertas'    => Peserta::where('sekolah_id', $rapor->sekolah_id)->get(),
            'semesters'   => Semester::orderBy('nama_semester')->get(),
            'materis'     => Materi::orderBy('nama_materi')->get(),
            'kompetensis'=> $rapor->materiRef
                ? $rapor->materiRef->kompetensis()
                    ->with('indikatorKompetensis')
                    ->get()
                : [],
        ]);
    }

    /* =========================
     * UPDATE
     * ========================= */
    public function update(Request $request, Rapor $rapor)
    {
        $request->validate([
            'materi_id'   => 'required|exists:materis,id',
            'materi'      => 'required|string',
            'nilai_akhir' => 'required|in:A,B,C',
            'nilai'       => 'required|array',
            'nilai.*'     => 'required|in:C,B,SB',
        ]);

        DB::transaction(function () use ($request, $rapor) {

            $rapor->update([
                'materi_id'   => $request->materi_id,
                'materi'      => $request->materi,
                'nilai_akhir' => $request->nilai_akhir,
                'kesimpulan'  => $request->kesimpulan,
                'status'      => 'submitted',
            ]);

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
            ->with('success','Rapor berhasil diperbarui');
    }

    /* =========================
     * CETAK
     * ========================= */
    public function cetak(Rapor $rapor)
    {
        $rapor->load([
            'peserta',
            'semester',
            'nilai.indikatorKompetensi.kompetensi'
        ]);

        // F4 size dalam mm → [0, 0, width, height]
        $pdf = Pdf::loadView('admin.rapor.cetak', compact('rapor'))
            ->setPaper([0, 0, 210, 330], 'portrait');

        return $pdf->stream(
            'rapor-'.$rapor->peserta->nama.'.pdf'
        );
    }

    public function cetakInstruktur(Rapor $rapor)
    {
        // pastikan rapor milik tugas instruktur ini
        abort_if(
            $rapor->tugas->instruktur_id !== auth()->id(),
            403
        );

        // hanya boleh cetak jika approved
        abort_if(
            $rapor->status !== 'approved',
            403,
            'Rapor belum disetujui admin'
        );

        return $this->cetak($rapor); // reuse fungsi lama
    }



    /* =========================
     * DELETE
     * ========================= */
    public function destroy(Rapor $rapor)
    {
        $rapor->delete();

        return redirect()
            ->route('rapor.manajemen')
            ->with('success', 'Rapor berhasil dihapus');
    }

    public function loadKompetensi(Materi $materi)
    {
        $kompetensis = $materi->kompetensis()
            ->with('indikatorKompetensis')
            ->orderBy('nama_kompetensi')
            ->get();

        return view(
            'admin.rapor._kompetensi_indikator',
            compact('kompetensis')
        );
    }

}
