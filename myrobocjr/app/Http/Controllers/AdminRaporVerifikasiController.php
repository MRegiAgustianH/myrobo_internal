<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Rapor;
use App\Models\RaporTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRaporVerifikasiController extends Controller
{
    public function show(Rapor $rapor)
    {
        $rapor->load([
            'peserta',
            'sekolah',
            'semester',
            'nilaiRapors.indikatorKompetensi.kompetensi'
        ]);

        return view('admin.rapor.verifikasi.show', compact('rapor'));
    }

    public function approve(Rapor $rapor)
    {
        DB::transaction(function () use ($rapor) {

            $rapor->update([
                'status' => 'approved',
                'catatan_revisi' => null
            ]);

            // cek apakah semua rapor sudah approved
            $tugas = $rapor->tugas;

            $selesai = $tugas->rapors()
                ->where('status', 'approved')
                ->count();

            if ($selesai === $tugas->rapors()->count()) {
                $tugas->update(['status' => 'completed']);
            }
        });

        return back()->with('success', 'Rapor disetujui');
    }


    public function revision(Request $request, Rapor $rapor)
    {
        $request->validate([
            'catatan_revisi' => 'required|string'
        ]);

        $rapor->update([
            'status'          => 'revision',
            'catatan_revisi'  => $request->catatan_revisi,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Revisi berhasil dikirim ke instruktur');
    }



    public function approveAll(RaporTugas $raporTugas)
    {
        DB::transaction(function () use ($raporTugas) {

            // approve semua rapor yang submitted
            $raporTugas->rapors()
                ->where('status', 'submitted')
                ->update([
                    'status' => 'approved'
                ]);

            // cek apakah masih ada rapor belum approved
            $sisa = $raporTugas->rapors()
                ->whereNotIn('status', ['approved'])
                ->exists();

            if (! $sisa) {
                $raporTugas->update([
                    'status' => 'completed'
                ]);
            }
        });

        return back()->with(
            'success',
            'Semua rapor yang disubmit berhasil diverifikasi'
        );
    }

}
