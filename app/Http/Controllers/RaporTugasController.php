<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Rapor;
use App\Models\RaporTugas;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RaporTugasController extends Controller
{
    /* =========================
     * LIST TUGAS RAPOR
     * ========================= */
    public function index()
    {
        $tugas = RaporTugas::with([
                'sekolah',
                'semester',
                'instruktur'
            ])
            ->withCount('rapors')
            ->latest()
            ->get();

        return view('admin.rapor_tugas.index', compact('tugas'));
    }

    /* =========================
     * SIMPAN TUGAS
     * ========================= */
    public function store(Request $request)
    {
        // ✅ VALIDASI (UX LEVEL)
        $request->validate([
            'sekolah_id' => [
                'required',
                'exists:sekolahs,id',
                Rule::unique('rapor_tugas')
                    ->where(fn ($q) => $q->where('semester_id', $request->semester_id)),
            ],
            'semester_id'   => 'required|exists:semesters,id',
            'instruktur_id' => 'required|exists:users,id',
            'deadline'      => 'nullable|date',
        ], [
            'sekolah_id.unique' =>
                'Tugas rapor untuk sekolah dan semester ini sudah ada.',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // 1️⃣ BUAT TUGAS RAPOR
                $raporTugas = RaporTugas::create([
                    'sekolah_id'    => $request->sekolah_id,
                    'semester_id'   => $request->semester_id,
                    'instruktur_id' => $request->instruktur_id,
                    'deadline'      => $request->deadline,
                    'status'        => 'pending',
                ]);

                // 2️⃣ AMBIL SEMUA PESERTA SEKOLAH
                $pesertas = Peserta::where('sekolah_id', $request->sekolah_id)
                    ->select('id')
                    ->get();

                // 3️⃣ GENERATE RAPOR KOSONG (BULK INSERT)
                $rapors = $pesertas->map(fn ($peserta) => [
                    'rapor_tugas_id' => $raporTugas->id,
                    'sekolah_id'     => $request->sekolah_id,
                    'peserta_id'     => $peserta->id,
                    'semester_id'    => $request->semester_id,
                    'status'         => 'draft',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ])->toArray();

                if (!empty($rapors)) {
                    Rapor::insert($rapors);
                }
            });

            return back()->with('success', 'Tugas rapor berhasil dibuat.');

        } catch (\Throwable $e) {

            // 🔒 JIKA LOLOS VALIDASI TAPI KEKENA UNIQUE DB (RACE CONDITION)
            if (str_contains($e->getMessage(), 'unique')) {
                return back()
                    ->withErrors([
                        'sekolah_id' =>
                            'Tugas rapor untuk sekolah dan semester ini sudah ada.',
                    ])
                    ->withInput();
            }

            // ❌ ERROR LAIN
            throw $e;
        }
    }


    /* =========================
     * DETAIL TUGAS (MONITORING)
     * ========================= */
    public function show(RaporTugas $raporTugas)
    {
        $raporTugas->load([
            'sekolah',
            'semester',
            'instruktur',
            'rapors.peserta'
        ]);

        return view(
            'admin.rapor_tugas.show',
            compact('raporTugas')
        );
    }
}
