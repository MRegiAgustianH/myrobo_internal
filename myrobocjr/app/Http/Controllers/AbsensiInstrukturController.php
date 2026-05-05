<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\AbsensiInstruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiInstrukturController extends Controller
{
    /**
     * Simpan absensi instruktur (manual oleh instruktur)
     */
    public function store(Request $request, Jadwal $jadwal)
    {
        // ===============================
        // AUTH: HANYA INSTRUKTUR TERJADWAL
        // ===============================
        if (
            auth()->user()->role !== 'instruktur' ||
            !$jadwal->instrukturs->contains(auth()->id())
        ) {
            abort(403);
        }

        // ===============================
        // VALIDASI
        // ===============================
        $request->validate([
            'status'     => 'required|in:hadir,izin,sakit,alfa',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // ===============================
        // BATAS WAKTU ABSENSI
        // ===============================
        if (
            auth()->user()->isInstruktur() &&
            !$jadwal->isDalamJamAbsensi()
        ) {
            return back()->with('error', 'Absensi instruktur hanya bisa diisi saat jam jadwal.');
        }


        DB::transaction(function () use ($request, $jadwal) {

            AbsensiInstruktur::updateOrCreate(
                [
                    'jadwal_id'      => $jadwal->id,
                    'instruktur_id' => auth()->id(),
                    'tanggal'       => $jadwal->tanggal_mulai,
                ],
                [
                    'status'     => $request->status,
                    'keterangan' => $request->keterangan,
                ]
            );
        });

        return back()->with('success', 'Absensi instruktur berhasil disimpan');
    }
}
