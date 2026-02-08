<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jadwal;
use App\Models\Materi;
use App\Models\Sekolah;
use App\Models\HomePrivate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JadwalController extends Controller
{
    public function index()
    {
        $sekolahs     = Sekolah::all();
        $homePrivates = HomePrivate::aktif()->get();
        $instrukturs  = User::where('role', 'instruktur')->get();
        $materis      = Materi::where('status', 'aktif')->get();

        // ===============================
        // ROLE-BASED JADWAL
        // ===============================
        if (auth()->user()->role === 'instruktur') {
            $jadwals = auth()->user()
                ->jadwals()
                ->with(['sekolah', 'homePrivate', 'instrukturs', 'materis'])
                ->get();
        } else {
            $jadwals = Jadwal::with(['sekolah', 'homePrivate', 'instrukturs', 'materis'])->get();
        }

        // ===============================
        // MAP DATA UNTUK JAVASCRIPT
        // ===============================
        $jadwalMap = [];

        foreach ($jadwals as $j) {
            $jadwalMap[$j->id] = [
                'id' => $j->id,

                'jenis_jadwal' => $j->jenis_jadwal,

                // sekolah
                'sekolah_id'   => $j->sekolah_id,
                'sekolah_nama' => $j->sekolah?->nama_sekolah,

                // home private
                'home_private_id'   => $j->home_private_id,
                'home_private_nama' => $j->homePrivate?->nama_peserta,

                // inti jadwal
                'nama_kegiatan'   => $j->nama_kegiatan,
                'tanggal_mulai'   => $j->tanggal_mulai,
                'tanggal_selesai' => $j->tanggal_selesai,
                'jam_mulai'       => $j->jam_mulai,
                'jam_selesai'     => $j->jam_selesai,
                'status'          => $j->status,

                // relasi
                'instrukturs'      => $j->instrukturs->pluck('id')->toArray(),
                'materis'          => $j->materis->pluck('id')->toArray(),
                'instruktur_nama'  => $j->instrukturs->pluck('name')->toArray(),
                'materi_nama'      => $j->materis->pluck('nama_materi')->toArray(),
            ];
        }

        return view('jadwal.index', compact(
            'jadwals',
            'sekolahs',
            'homePrivates',
            'instrukturs',
            'materis',
            'jadwalMap'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_jadwal' => 'required|in:sekolah,home_private',

            'sekolah_id' =>
                'nullable|required_if:jenis_jadwal,sekolah|exists:sekolahs,id',

            'home_private_id' =>
                'nullable|required_if:jenis_jadwal,home_private|exists:home_privates,id',

            'nama_kegiatan'   => 'required|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required|after:jam_mulai',

            'instrukturs'   => 'required|array|min:1',
            'instrukturs.*' => 'exists:users,id',

            'materis'   => 'nullable|array|max:2',
            'materis.*' => 'exists:materis,id',
        ]);

        // ===============================
        // CEK BENTROK INSTRUKTUR
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
            'jenis_jadwal'    => $request->jenis_jadwal,
            'sekolah_id'      => $request->jenis_jadwal === 'sekolah'
                                ? $request->sekolah_id
                                : null,
            'home_private_id' => $request->jenis_jadwal === 'home_private'
                                ? $request->home_private_id
                                : null,
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
            'message' => 'Jadwal berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'jenis_jadwal' => 'required|in:sekolah,home_private',

            'sekolah_id' =>
                'nullable|required_if:jenis_jadwal,sekolah|exists:sekolahs,id',

            'home_private_id' =>
                'nullable|required_if:jenis_jadwal,home_private|exists:home_privates,id',

            'nama_kegiatan'   => 'required|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required|after:jam_mulai',

            'instrukturs'   => 'required|array|min:1',
            'instrukturs.*' => 'exists:users,id',

            'materis'   => 'nullable|array|max:2',
            'materis.*' => 'exists:materis,id',

            'status' => 'nullable|in:aktif,nonaktif',
        ]);

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
            'jenis_jadwal'    => $request->jenis_jadwal,
            'sekolah_id'      => $request->jenis_jadwal === 'sekolah'
                                ? $request->sekolah_id
                                : null,
            'home_private_id' => $request->jenis_jadwal === 'home_private'
                                ? $request->home_private_id
                                : null,
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


    public function storeRecurring(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:bulanan,semester',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',

            'jenis_jadwal' => 'required|in:sekolah,home_private',
            'sekolah_id' =>
                'nullable|required_if:jenis_jadwal,sekolah|exists:sekolahs,id',
            'home_private_id' =>
                'nullable|required_if:jenis_jadwal,home_private|exists:home_privates,id',

            'nama_kegiatan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',

            'instrukturs' => 'required|array|min:1',
            'instrukturs.*' => 'exists:users,id',

            'materis' => 'nullable|array|max:2',
            'materis.*' => 'exists:materis,id',
        ]);

        // ===============================
        // KONVERSI HARI KE FORMAT CARBON
        // ===============================
        $mapHari = [
            'senin' => Carbon::MONDAY,
            'selasa' => Carbon::TUESDAY,
            'rabu' => Carbon::WEDNESDAY,
            'kamis' => Carbon::THURSDAY,
            'jumat' => Carbon::FRIDAY,
            'sabtu' => Carbon::SATURDAY,
            'minggu' => Carbon::SUNDAY,
        ];

        $start = Carbon::parse($request->tanggal_mulai);
        $end   = Carbon::parse($request->tanggal_selesai);
        $targetDay = $mapHari[$request->hari];

        // ===============================
        // GENERATE TANGGAL
        // ===============================
        $tanggalList = [];

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            if ($date->dayOfWeek === $targetDay) {
                $tanggalList[] = $date->toDateString();
            }
        }

        if (count($tanggalList) === 0) {
            throw ValidationException::withMessages([
                'hari' => 'Tidak ada tanggal yang sesuai dalam rentang tersebut.'
            ]);
        }

        // ===============================
        // CEK BENTROK SEMUA TANGGAL
        // ===============================
        foreach ($tanggalList as $tanggal) {
            foreach ($request->instrukturs as $instrukturId) {
                $bentrok = Jadwal::whereHas('instrukturs', function ($q) use ($instrukturId) {
                        $q->where('users.id', $instrukturId);
                    })
                    ->whereDate('tanggal_mulai', '<=', $tanggal)
                    ->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->where(function ($q) use ($request) {
                        $q->where('jam_mulai', '<', $request->jam_selesai)
                        ->where('jam_selesai', '>', $request->jam_mulai);
                    })
                    ->exists();

                if ($bentrok) {
                    throw ValidationException::withMessages([
                        'instrukturs' =>
                            "Bentrok jadwal pada tanggal {$tanggal}"
                    ]);
                }
            }
        }

        // ===============================
        // SIMPAN DALAM TRANSACTION
        // ===============================
        DB::transaction(function () use ($tanggalList, $request) {

            foreach ($tanggalList as $tanggal) {
                $jadwal = Jadwal::create([
                    'jenis_jadwal'    => $request->jenis_jadwal,
                    'sekolah_id'      => $request->jenis_jadwal === 'sekolah'
                        ? $request->sekolah_id
                        : null,
                    'home_private_id' => $request->jenis_jadwal === 'home_private'
                        ? $request->home_private_id
                        : null,
                    'nama_kegiatan'   => $request->nama_kegiatan,
                    'hari'            => $request->hari,
                    'tanggal_mulai'   => $tanggal,
                    'tanggal_selesai' => $tanggal,
                    'jam_mulai'       => $request->jam_mulai,
                    'jam_selesai'     => $request->jam_selesai,
                    'status'          => 'aktif',
                ]);

                $jadwal->instrukturs()->sync($request->instrukturs);
                $jadwal->materis()->sync($request->materis ?? []);
            }

        });

        return response()->json([
            'message' => 'Jadwal ' . $request->mode . ' berhasil dibuat',
            'total_pertemuan' => count($tanggalList)
        ]);
    }
}
