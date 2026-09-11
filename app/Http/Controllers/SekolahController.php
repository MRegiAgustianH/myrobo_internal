<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SekolahController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Sekolah::with('cabang')->latest();

        // ADMIN CABANG: hanya lihat sekolah di cabangnya
        if ($user->role === 'admin_cabang') {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'superadmin' && request('cabang_id')) {
            $query->where('cabang_id', request('cabang_id'));
        }

        $sekolahs = $query->paginate(10);
        $cabangs = Cabang::orderBy('nama_cabang')->get();
        return view('admin.sekolah.index', compact('sekolahs', 'cabangs'));
    }

    public function create()
    {
        return view('admin.sekolah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cabang_id'              => 'nullable|exists:cabangs,id',
            'nama_sekolah'           => 'required|string|max:255',
            'alamat'                 => 'required|string',
            'kontak'                 => 'required|string|max:50',
            'logo'                   => 'nullable|image|max:2048',
            'nominal_pembayaran'     => 'required|numeric|min:0',
            'jumlah_periode'         => 'nullable|integer|min:1|max:12',
            'pertemuan_per_periode'  => 'nullable|integer|min:1|max:12',
            'tgl_mulai_kerjasama'    => 'required|date',
            'tgl_akhir_kerjasama'    => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
        ]);

        $data = $request->all();

        // AUTO-SET CABANG_ID UNTUK ADMIN CABANG
        if (auth()->user()->role === 'admin_cabang' && empty($data['cabang_id'])) {
            $data['cabang_id'] = auth()->user()->cabang_id;
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logo-sekolah', 'public');
        }

        Sekolah::create($data);

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil ditambahkan.');
    }

    public function edit(Sekolah $sekolah)
    {
        return view('admin.sekolah.edit', compact('sekolah'));
    }

    public function update(Request $request, Sekolah $sekolah)
    {
        $request->validate([
            'cabang_id'              => 'nullable|exists:cabangs,id',
            'nama_sekolah'           => 'required|string|max:255',
            'alamat'                 => 'required|string',
            'kontak'                 => 'required|string|max:50',
            'logo'                   => 'nullable|image|max:2048',
            'nominal_pembayaran'     => 'required|numeric|min:0',
            'jumlah_periode'         => 'nullable|integer|min:1|max:12',
            'pertemuan_per_periode'  => 'nullable|integer|min:1|max:12',
            'tgl_mulai_kerjasama'    => 'required|date',
            'tgl_akhir_kerjasama'    => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
        ]);

        $data = $request->all();

        // AUTO-SET CABANG_ID UNTUK ADMIN CABANG
        if (auth()->user()->role === 'admin_cabang' && empty($data['cabang_id'])) {
            $data['cabang_id'] = auth()->user()->cabang_id;
        }

        if ($request->hasFile('logo')) {
            if ($sekolah->logo) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $data['logo'] = $request->file('logo')->store('logo-sekolah', 'public');
        }

        $sekolah->update($data);

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(Sekolah $sekolah)
    {
        if ($sekolah->logo) {
            Storage::disk('public')->delete($sekolah->logo);
        }

        $sekolah->delete();

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }
}