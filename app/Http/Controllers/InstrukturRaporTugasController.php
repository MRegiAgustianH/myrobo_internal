<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\NilaiRapor;
use App\Models\RaporTugas;
use App\Models\Peserta;
use App\Models\Rapor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstrukturRaporTugasController extends Controller
{
    public function index()
    {
        $tugas = RaporTugas::with(['sekolah','semester'])
            ->withCount([
                'rapors',
                'rapors as rapors_selesai_count' => function ($q) {
                    $q->whereIn('status', ['submitted', 'approved']);
                }
            ])
            ->where('instruktur_id', Auth::id())
            ->latest()
            ->get();

        return view('instruktur.rapor_tugas.index', compact('tugas'));
    }


    public function show(RaporTugas $raporTugas)
    {
        abort_if(
            $raporTugas->instruktur_id !== auth()->id(),
            403
        );

        // Ambil rapor + peserta
        $rapors = $raporTugas->rapors()
            ->with('peserta')
            ->orderBy('peserta_id')
            ->get();

        return view(
            'instruktur.rapor_tugas.show',
            compact('raporTugas', 'rapors')
        );
    }


    public function create(RaporTugas $raporTugas, Peserta $peserta)
    {
        // 🔐 Proteksi: hanya instruktur yg ditugaskan
        abort_if(
            $raporTugas->instruktur_id !== Auth::id(),
            403
        );

        // Ambil rapor jika sudah ada
        $rapor = Rapor::where('rapor_tugas_id', $raporTugas->id)
            ->where('peserta_id', $peserta->id)
            ->first();

        // Materi untuk dipilih instruktur
        $materis = Materi::orderBy('nama_materi')->get();

        return view(
            'instruktur.rapor.form',
            compact('raporTugas','peserta','rapor','materis')
        );
    }

    

}
