<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::latest()->get();
        return view('admin.sekolah.index', compact('sekolahs'));
    }

    public function create()
    {
        return view('admin.sekolah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sekolah'           => 'required|string|max:255',
            'alamat'                 => 'required|string',
            'kontak'                 => 'required|string|max:50',
            'tgl_mulai_kerjasama'    => 'required|date',
            'tgl_akhir_kerjasama'    => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
        ]);


        Sekolah::create($request->all());

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
            'nama_sekolah'           => 'required|string|max:255',
            'alamat'                 => 'required|string',
            'kontak'                 => 'required|string|max:50',
            'tgl_mulai_kerjasama'    => 'required|date',
            'tgl_akhir_kerjasama'    => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
        ]);


        $sekolah->update($request->all());

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroy(Sekolah $sekolah)
    {
        $sekolah->delete();

        return redirect()->route('sekolah.index')
            ->with('success', 'Data sekolah berhasil dihapus.');
    }
}
