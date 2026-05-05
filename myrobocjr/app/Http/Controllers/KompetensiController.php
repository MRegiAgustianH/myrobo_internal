<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kompetensi;
use Illuminate\Http\Request;

class KompetensiController extends Controller
{
    /**
     * Daftar kompetensi per materi
     */
    public function index(Materi $materi)
    {
        $kompetensis = $materi->kompetensis()
            ->withCount('indikatorKompetensis')
            ->orderBy('nama_kompetensi')
            ->get();

        return view('admin.materi.kompetensi.index', compact('materi', 'kompetensis'));
    }

    /**
     * Simpan kompetensi (CREATE – via modal)
     */
    public function store(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'nama_kompetensi' => [
                'required',
                'string',
                'max:255',
                // unik per materi
                'unique:kompetensis,nama_kompetensi,NULL,id,materi_id,' . $materi->id,
            ],
        ]);

        $materi->kompetensis()->create($validated);

        return redirect()
            ->route('materi.kompetensi.index', $materi->id)
            ->with('success', 'Kompetensi berhasil ditambahkan');
    }

    /**
     * Update kompetensi (EDIT – via modal)
     */
    public function update(Request $request, Materi $materi, Kompetensi $kompetensi)
    {
        $this->ensureKompetensiMilikMateri($materi, $kompetensi);

        $validated = $request->validate([
            'nama_kompetensi' => [
                'required',
                'string',
                'max:255',
                'unique:kompetensis,nama_kompetensi,' . $kompetensi->id . ',id,materi_id,' . $materi->id,
            ],
        ]);

        $kompetensi->update($validated);

        return redirect()
            ->route('materi.kompetensi.index', $materi->id)
            ->with('success', 'Kompetensi berhasil diperbarui');
    }

    /**
     * Hapus kompetensi (DELETE – via modal)
     */
    public function destroy(Materi $materi, Kompetensi $kompetensi)
    {
        $this->ensureKompetensiMilikMateri($materi, $kompetensi);

        if ($kompetensi->indikatorKompetensis()->exists()) {
            return back()->with(
                'error',
                'Kompetensi tidak dapat dihapus karena masih memiliki indikator'
            );
        }

        $kompetensi->delete();

        return redirect()
            ->route('materi.kompetensi.index', $materi->id)
            ->with('success', 'Kompetensi berhasil dihapus');
    }

    /**
     * Guard relasi materi → kompetensi
     */
    private function ensureKompetensiMilikMateri(
        Materi $materi,
        Kompetensi $kompetensi
    ): void {
        if ($kompetensi->materi_id !== $materi->id) {
            abort(404);
        }
    }
}
