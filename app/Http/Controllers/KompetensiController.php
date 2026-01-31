<?php

namespace App\Http\Controllers;

use App\Models\Kompetensi;
use Illuminate\Http\Request;

class KompetensiController extends Controller
{
    public function index()
    {
        $kompetensis = Kompetensi::withCount('indikatorKompetensis')
            ->orderBy('nama_kompetensi')
            ->get();

        return view('kompetensi.index', compact('kompetensis'));
    }


    public function create()
    {
        return view('kompetensi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kompetensi' => 'required|string|max:255',
        ]);

        Kompetensi::create([
            'nama_kompetensi' => $request->nama_kompetensi,
        ]);

        return redirect()
            ->route('kompetensi.index')
            ->with('success', 'Kompetensi berhasil ditambahkan');
    }

    public function edit(Kompetensi $kompetensi)
    {
        return view('kompetensi.edit', compact('kompetensi'));
    }

    public function update(Request $request, Kompetensi $kompetensi)
    {
        $request->validate([
            'nama_kompetensi' => 'required|string|max:255',
        ]);

        $kompetensi->update([
            'nama_kompetensi' => $request->nama_kompetensi,
        ]);

        return redirect()
            ->route('kompetensi.index')
            ->with('success', 'Kompetensi berhasil diperbarui');
    }

    public function destroy(Kompetensi $kompetensi)
    {
        // Proteksi: tidak boleh hapus jika sudah punya indikator
        if ($kompetensi->indikatorKompetensis()->count() > 0) {
            return back()->with('error', 'Kompetensi tidak dapat dihapus karena memiliki indikator');
        }

        $kompetensi->delete();

        return redirect()
            ->route('kompetensi.index')
            ->with('success', 'Kompetensi berhasil dihapus');
    }
}
