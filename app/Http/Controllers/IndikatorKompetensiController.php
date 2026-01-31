<?php

namespace App\Http\Controllers;

use App\Models\Kompetensi;
use App\Models\IndikatorKompetensi;
use Illuminate\Http\Request;

class IndikatorKompetensiController extends Controller
{
    public function index(Kompetensi $kompetensi)
    {
        $indikators = $kompetensi->indikatorKompetensis()
            ->orderBy('nama_indikator')
            ->get();

        return view('indikator.index', compact('kompetensi', 'indikators'));
    }

    public function create(Kompetensi $kompetensi)
    {
        return view('indikator.create', compact('kompetensi'));
    }

    public function store(Request $request, Kompetensi $kompetensi)
    {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
        ]);

        $kompetensi->indikatorKompetensis()->create([
            'nama_indikator' => $request->nama_indikator,
        ]);

        return redirect()
            ->route('kompetensi.indikator.index', $kompetensi->id)
            ->with('success', 'Indikator berhasil ditambahkan');
    }

    public function edit(Kompetensi $kompetensi, IndikatorKompetensi $indikator)
    {
        return view('indikator.edit', compact('kompetensi', 'indikator'));
    }

    public function update(
        Request $request,
        Kompetensi $kompetensi,
        IndikatorKompetensi $indikator
    ) {
        $request->validate([
            'nama_indikator' => 'required|string|max:255',
        ]);

        $indikator->update([
            'nama_indikator' => $request->nama_indikator,
        ]);

        return redirect()
            ->route('kompetensi.indikator.index', $kompetensi->id)
            ->with('success', 'Indikator berhasil diperbarui');
    }

    public function destroy(Kompetensi $kompetensi, IndikatorKompetensi $indikator)
    {
        // Proteksi: indikator sudah dipakai rapor
        if ($indikator->raporNilais()->count() > 0) {
            return back()->with(
                'error',
                'Indikator tidak dapat dihapus karena sudah digunakan pada rapor'
            );
        }

        $indikator->delete();

        return redirect()
            ->route('kompetensi.indikator.index', $kompetensi->id)
            ->with('success', 'Indikator berhasil dihapus');
    }
}


