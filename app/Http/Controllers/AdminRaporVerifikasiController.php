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
        $this->authorizeRapor($rapor);

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
        $this->authorizeRapor($rapor);

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
        $this->authorizeRapor($rapor);
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
        $this->authorizeRaporTugas($raporTugas);

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


    private function authorizeRapor(Rapor $rapor): void
    {
        $user = auth()->user();

        if ($user->role === 'superadmin') {
            return;
        }

        if (in_array($user->role, ['admin_cabang', 'sekretaris'])) {
            $cabangId = $rapor->tugas?->cabang_id ?? $rapor->sekolah?->cabang_id;
            abort_if($cabangId !== $user->cabang_id, 403);
            return;
        }

        if ($user->role === 'admin' ) {
            return;
        }

        abort(403);
    }

    private function authorizeRaporTugas(RaporTugas $raporTugas): void
    {
        $user = auth()->user();

        if ($user->role === 'superadmin' || $user->role === 'admin') {
            return;
        }

        if (in_array($user->role, ['admin_cabang', 'sekretaris'])) {
            $cabangId = $raporTugas->cabang_id ?? $raporTugas->sekolah?->cabang_id;
            abort_if($cabangId !== $user->cabang_id, 403);
            return;
        }

        abort(403);
    }
}