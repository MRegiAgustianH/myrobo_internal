<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jadwal;
use App\Models\Materi;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JadwalController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::all();

        $instrukturs = User::where('role', 'instruktur')->get();

        $materis = Materi::where('status', 'aktif')->get();

        // ===============================
        // ROLE-BASED JADWAL
        // ===============================
        if (auth()->user()->role === 'instruktur') {
            $jadwals = auth()->user()
                ->jadwals()
                ->with(['sekolah', 'instrukturs', 'materis'])
                ->get();
        } else {
            $jadwals = Jadwal::with(['sekolah', 'instrukturs', 'materis'])->get();
        }

        // ===============================
        // MAP DATA UNTUK JAVASCRIPT
        // ===============================
        $jadwalMap = [];

        foreach ($jadwals as $j) {
        $jadwalMap[$j->id] = [
            'id' => $j->id,

            // sekolah
            'sekolah_id'   => $j->sekolah_id,
            'sekolah_nama' => $j->sekolah?->nama_sekolah,

            // inti jadwal
            'nama_kegiatan'   => $j->nama_kegiatan,
            'tanggal_mulai'   => $j->tanggal_mulai,
            'tanggal_selesai' => $j->tanggal_selesai,
            'jam_mulai'       => $j->jam_mulai,
            'jam_selesai'     => $j->jam_selesai,
            'status'          => $j->status,

            
            'instrukturs' => $j->instrukturs->pluck('id')->toArray(),
            'materis'     => $j->materis->pluck('id')->toArray(),

            'instruktur_nama' => $j->instrukturs->pluck('name')->toArray(),
            'materi_nama'     => $j->materis->pluck('nama_materi')->toArray(),
        ];
    }


        return view('jadwal.index', compact(
            'jadwals',
            'sekolahs',
            'instrukturs',
            'materis',
            'jadwalMap'
        ));
    }


   public function store(Request $request)
{
    $request->validate([
        'sekolah_id'       => 'required',
        'nama_kegiatan'    => 'required',
        'tanggal_mulai'    => 'required|date',
        'tanggal_selesai'  => 'required|date',
        'jam_mulai'        => 'required',
        'jam_selesai'      => 'required',
        'instrukturs'      => 'required|array|min:1',
        'materis'          => 'nullable|array|max:2', // 🔴 BATAS 2
        'materis.*'        => 'exists:materis,id',
    ]);

    // ===============================
    // VALIDASI JUMLAH MATERI
    // ===============================
    if ($request->filled('materis') && count($request->materis) > 2) {
        throw ValidationException::withMessages([
            'materis' => 'Jadwal sudah memiliki 2 materi'
        ]);
    }

    // ===============================
    // CEK BENTROK JADWAL INSTRUKTUR
    // ===============================
    foreach ($request->instrukturs as $instrukturId) {
        $bentrok = Jadwal::whereHas('instrukturs', function ($q) use ($instrukturId) {
                $q->where('users.id', $instrukturId);
            })
            ->whereDate('tanggal_mulai', '<=', $request->tanggal_selesai)
            ->whereDate('tanggal_selesai', '>=', $request->tanggal_mulai)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrok) {
            throw ValidationException::withMessages([
                'instrukturs' =>
                    'Jadwal bentrok: instruktur sudah memiliki jadwal di waktu tersebut.'
            ]);
        }
    }

    // ===============================
    // SIMPAN JADWAL
    // ===============================
    $jadwal = Jadwal::create([
        'sekolah_id'      => $request->sekolah_id,
        'nama_kegiatan'   => $request->nama_kegiatan,
        'tanggal_mulai'   => $request->tanggal_mulai,
        'tanggal_selesai' => $request->tanggal_selesai,
        'jam_mulai'       => $request->jam_mulai,
        'jam_selesai'     => $request->jam_selesai,
        'status'          => $request->status ?? 'aktif',
    ]);

    $jadwal->instrukturs()->sync($request->instrukturs);
    $jadwal->materis()->sync($request->materis ?? []);

    return redirect()
        ->route('jadwal.index')
        ->with('success', 'Jadwal berhasil ditambahkan');
}




    public function update(Request $request, Jadwal $jadwal)
{
    $request->validate([
        'sekolah_id'       => 'required|exists:sekolahs,id',
        'nama_kegiatan'    => 'required|string',
        'tanggal_mulai'    => 'required|date',
        'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        'jam_mulai'        => 'required',
        'jam_selesai'      => 'required|after:jam_mulai',
        'instrukturs'      => 'required|array|min:1',
        'instrukturs.*'    => 'exists:users,id',
        'materis'          => 'nullable|array|max:2', // 🔴 BATAS 2
        'materis.*'        => 'exists:materis,id',
        'status'           => 'nullable|in:aktif,nonaktif',
    ]);

    // ===============================
    // VALIDASI JUMLAH MATERI
    // ===============================
    if ($request->filled('materis') && count($request->materis) > 2) {
        throw ValidationException::withMessages([
            'materis' => 'Jadwal sudah memiliki 2 materi'
        ]);
    }

    // ===============================
    // CEK BENTROK (KECUALI JADWAL INI)
    // ===============================
    foreach ($request->instrukturs as $instrukturId) {

        $bentrok = Jadwal::where('id', '!=', $jadwal->id)
            ->whereHas('instrukturs', function ($q) use ($instrukturId) {
                $q->where('users.id', $instrukturId);
            })
            ->whereDate('tanggal_mulai', '<=', $request->tanggal_selesai)
            ->whereDate('tanggal_selesai', '>=', $request->tanggal_mulai)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrok) {
            throw ValidationException::withMessages([
                'instrukturs' =>
                    'Jadwal bentrok: instruktur sudah memiliki jadwal di waktu tersebut.'
            ]);
        }
    }

    // ===============================
    // UPDATE JADWAL
    // ===============================
    $jadwal->update([
        'sekolah_id'      => $request->sekolah_id,
        'nama_kegiatan'   => $request->nama_kegiatan,
        'tanggal_mulai'   => $request->tanggal_mulai,
        'tanggal_selesai' => $request->tanggal_selesai,
        'jam_mulai'       => $request->jam_mulai,
        'jam_selesai'     => $request->jam_selesai,
        'status'          => $request->status ?? 'aktif',
    ]);

    $jadwal->instrukturs()->sync($request->instrukturs);
    $jadwal->materis()->sync($request->materis ?? []);

    return response()->json([
        'message' => 'Jadwal berhasil diperbarui'
    ]);
}


    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return back()->with('success', 'Jadwal dihapus');
    }
}

