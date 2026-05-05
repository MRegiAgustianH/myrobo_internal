<?php

namespace App\Http\Controllers;

use App\Models\Rapor;
use App\Models\Materi;
use App\Models\Peserta;
use App\Models\RaporTugas;
use App\Models\NilaiRapor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstrukturRaporController extends Controller
{
    /**
     * ===============================
     * FORM ISI / EDIT RAPOR
     * ===============================
     * GET:
     * /instruktur/rapor-tugas/{raporTugas}/peserta/{peserta}/rapor
     */
    public function edit(RaporTugas $raporTugas, Peserta $peserta)
    {
        $user = auth()->user();

        $rapor = Rapor::where('rapor_tugas_id', $raporTugas->id)
        ->where('peserta_id', $peserta->id)
        ->first();

        if ($rapor && $rapor->status === 'approved') {
            abort(403, 'Rapor sudah disetujui dan tidak dapat diubah');
        }

        // 🔐 Proteksi: hanya instruktur yg ditugaskan
        abort_if($raporTugas->instruktur_id !== $user->id, 403);

        // 🔐 Proteksi: peserta harus milik sekolah tugas
        abort_if($peserta->sekolah_id !== $raporTugas->sekolah_id, 404);

        // Ambil rapor (harus sudah digenerate admin)
        $rapor = Rapor::where('rapor_tugas_id', $raporTugas->id)
            ->where('peserta_id', $peserta->id)
            ->firstOrFail();

        // Materi master
        $materis = Materi::orderBy('nama_materi')->get();

        // Kompetensi (jika materi sudah dipilih sebelumnya)
        $kompetensis = [];
        if ($rapor->materi_id) {
            $kompetensis = Materi::find($rapor->materi_id)
                ?->kompetensis()
                ->with('indikatorKompetensis')
                ->get() ?? [];
        }

        return view('instruktur.rapor.form', [
            'raporTugas'  => $raporTugas,
            'peserta'     => $peserta,
            'rapor'       => $rapor,
            'materis'     => $materis,
            'kompetensis' => $kompetensis,
            'readonly'    => false, // instruktur boleh isi
        ]);
    }

    /**
     * ===============================
     * SIMPAN / UPDATE RAPOR (DRAFT)
     * ===============================
     * POST:
     * /instruktur/rapor-tugas/{raporTugas}/peserta/{peserta}/rapor
     */
    public function store(Request $request,RaporTugas $raporTugas,Peserta $peserta) {
        abort_if($raporTugas->instruktur_id !== auth()->id(), 403);

        $request->validate([
            'materi_id'   => 'required|exists:materis,id',
            'materi'      => 'required|string',
            'nilai_akhir' => 'required|in:A,B,C',
            'nilai'       => 'required|array',
            'nilai.*'     => 'required|in:C,B,SB',
            'kesimpulan'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $raporTugas, $peserta) {

            /** =============================
             * 1️⃣ SIMPAN / UPDATE RAPOR
             * ============================= */
            $rapor = Rapor::updateOrCreate(
                [
                    'rapor_tugas_id' => $raporTugas->id,
                    'peserta_id'     => $peserta->id,
                ],
                [
                    'sekolah_id'  => $raporTugas->sekolah_id,
                    'semester_id' => $raporTugas->semester_id,
                    'materi_id'   => $request->materi_id,
                    'materi'      => $request->materi,
                    'nilai_akhir' => $request->nilai_akhir,
                    'kesimpulan'  => $request->kesimpulan,
                    'status'      => 'submitted', 
                ]
            );

            /** =============================
             * 2️⃣ RESET NILAI LAMA
             * ============================= */
            $rapor->nilaiRapors()->delete();

            foreach ($request->nilai as $indikatorId => $nilai) {
                NilaiRapor::create([
                    'rapor_id'                => $rapor->id,
                    'indikator_kompetensi_id' => $indikatorId,
                    'nilai'                   => $nilai,
                ]);
            }

            /** =============================
             * 3️⃣ UPDATE STATUS TUGAS
             * ============================= */
            if ($raporTugas->status === 'pending') {
                $raporTugas->update([
                    'status' => 'in_progress'
                ]);
            }
        });

        return redirect()
            ->route('instruktur.rapor-tugas.show', $raporTugas->id)
            ->with('success', 'Rapor berhasil dikirim untuk verifikasi');
    }

    /**
     * ===============================
     * SUBMIT RAPOR KE ADMIN
     * ===============================
     * POST:
     * /instruktur/rapor/{rapor}/submit
     */
    public function submit(Rapor $rapor)
    {
        $user = auth()->user();

        abort_if($rapor->raporTugas->instruktur_id !== $user->id, 403);

        // Validasi minimal sebelum submit
        if (
            ! $rapor->materi_id ||
            ! $rapor->nilaiRapors()->exists()
        ) {
            return back()->with(
                'error',
                'Rapor belum lengkap dan tidak dapat disubmit'
            );
        }

        $rapor->update([
            'status' => 'submitted'
        ]);

        // Update status tugas jika semua rapor selesai
        if (
            $rapor->raporTugas
                ->rapors()
                ->whereIn('status', ['draft','revision'])
                ->count() === 0
        ) {
            $rapor->raporTugas->update([
                'status' => 'completed'
            ]);
        }

        return back()->with(
            'success',
            'Rapor berhasil disubmit ke admin'
        );
    }
}
