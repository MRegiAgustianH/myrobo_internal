<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Rapor;
use App\Models\RaporTugas;
use App\Models\Sekolah;
use App\Models\Semester;
use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RaporTugasController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cabangId = null;

        if ($user->role === 'superadmin') {
            $cabangId = request('cabang_id');
        } elseif (in_array($user->role, ['admin_cabang', 'sekretaris'])) {
            $cabangId = $user->cabang_id;
        }

        $query = RaporTugas::with(['sekolah', 'semester', 'instruktur'])
            ->withCount('rapors')
            ->when($cabangId, fn($q) => $q->where('cabang_id', $cabangId))
            ->latest();

        $tugas = $query->paginate(10)->withQueryString();
        $cabangs = Cabang::orderBy('nama_cabang')->get();

        return view('admin.rapor_tugas.index', compact('tugas', 'cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cabang_id'     => 'nullable|exists:cabangs,id',
            'sekolah_id'    => [
                'required',
                'exists:sekolahs,id',
                Rule::unique('rapor_tugas')
                    ->where(fn ($q) => $q->where('semester_id', $request->semester_id)),
            ],
            'semester_id'   => 'required|exists:semesters,id',
            'instruktur_id' => 'required|exists:users,id',
            'deadline'      => 'nullable|date',
        ], [
            'sekolah_id.unique' => 'Tugas rapor untuk sekolah dan semester ini sudah ada.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = auth()->user();

                $raporTugas = RaporTugas::create([
                    'cabang_id'     => $request->cabang_id ?? $user->cabang_id,
                    'sekolah_id'    => $request->sekolah_id,
                    'semester_id'   => $request->semester_id,
                    'instruktur_id' => $request->instruktur_id,
                    'deadline'      => $request->deadline,
                    'status'        => 'pending',
                ]);

                $pesertas = Peserta::where('sekolah_id', $request->sekolah_id)
                    ->select('id')
                    ->get();

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
            if (str_contains($e->getMessage(), 'unique')) {
                return back()
                    ->withErrors(['sekolah_id' => 'Tugas rapor untuk sekolah dan semester ini sudah ada.'])
                    ->withInput();
            }

            throw $e;
        }
    }

    public function show(RaporTugas $raporTugas)
    {
        $raporTugas->load([
            'sekolah',
            'semester',
            'instruktur',
            'rapors.peserta'
        ]);

        return view('admin.rapor_tugas.show', compact('raporTugas'));
    }
    public function edit(RaporTugas $raporTugas)
    {
        $raporTugas->load(['sekolah', 'semester', 'instruktur', 'cabang']);

        $cabangs = Cabang::orderBy('nama_cabang')->get();
        $instrukturs = User::where('role', 'instruktur')
            ->when($raporTugas->cabang_id, fn($q) => $q->where('cabang_id', $raporTugas->cabang_id))
            ->get();

        return view('admin.rapor_tugas.edit', compact('raporTugas', 'cabangs', 'instrukturs'));
    }

    public function update(Request $request, RaporTugas $raporTugas)
    {
        $request->validate([
            'cabang_id'     => 'nullable|exists:cabangs,id',
            'instruktur_id' => 'required|exists:users,id',
            'deadline'      => 'nullable|date',
        ]);

        $data = $request->only(['cabang_id', 'instruktur_id', 'deadline']);
        $data['cabang_id'] = $data['cabang_id'] ?? auth()->user()->cabang_id;

        $raporTugas->update($data);

        return redirect()->route('admin.rapor-tugas.show', $raporTugas->id)
            ->with('success', 'Tugas rapor berhasil diperbarui.');
    }

    public function destroy(RaporTugas $raporTugas)
    {
        $raporTugas->delete();

        return redirect()->route('admin.rapor-tugas.index')
            ->with('success', 'Tugas rapor berhasil dihapus.');
    }
}