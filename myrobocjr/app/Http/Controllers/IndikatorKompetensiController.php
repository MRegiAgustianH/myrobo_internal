<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kompetensi;
use App\Models\IndikatorKompetensi;
use Illuminate\Http\Request;

class IndikatorKompetensiController extends Controller
{
    /**
     * Daftar indikator kompetensi
     */
    public function index(Materi $materi, Kompetensi $kompetensi)
    {
        $this->ensureRelation($materi, $kompetensi);

        $indikators = $kompetensi->indikatorKompetensis()
            ->orderBy('nama_indikator')
            ->get();

        return view(
            'admin.materi.kompetensi.indikator.index',
            compact('materi', 'kompetensi', 'indikators')
        );
    }

    /**
     * Form tambah indikator
     */
    public function create(Materi $materi, Kompetensi $kompetensi)
    {
        $this->ensureRelation($materi, $kompetensi);

        return view(
            'admin.materi.kompetensi.indikator.create',
            compact('materi', 'kompetensi')
        );
    }

    /**
     * Simpan indikator
     */
    public function store(
        Request $request,
        Materi $materi,
        Kompetensi $kompetensi
    ) {
        $this->ensureRelation($materi, $kompetensi);

        $request->validate([
            'nama_indikator' => 'required|string|max:255',
        ]);

        $kompetensi->indikatorKompetensis()->create([
            'nama_indikator' => $request->nama_indikator,
        ]);

        return redirect()
            ->route(
                'materi.kompetensi.indikator.index',
                [$materi->id, $kompetensi->id]
            )
            ->with('success', 'Indikator berhasil ditambahkan');
    }

    /**
     * Form edit indikator
     */
    public function edit(
        Materi $materi,
        Kompetensi $kompetensi,
        IndikatorKompetensi $indikator
    ) {
        $this->ensureRelation($materi, $kompetensi);
        $this->ensureIndicator($kompetensi, $indikator);

        return view(
            'admin.materi.kompetensi.indikator.edit',
            compact('materi', 'kompetensi', 'indikator')
        );
    }

    /**
     * Update indikator
     */
    public function update(
        Request $request,
        Materi $materi,
        Kompetensi $kompetensi,
        IndikatorKompetensi $indikator
    ) {
        $this->ensureRelation($materi, $kompetensi);
        $this->ensureIndicator($kompetensi, $indikator);

        $request->validate([
            'nama_indikator' => 'required|string|max:255',
        ]);

        $indikator->update([
            'nama_indikator' => $request->nama_indikator,
        ]);

        return redirect()
            ->route(
                'materi.kompetensi.indikator.index',
                [$materi->id, $kompetensi->id]
            )
            ->with('success', 'Indikator berhasil diperbarui');
    }

    /**
     * Hapus indikator
     */
    public function destroy(Materi $materi,Kompetensi $kompetensi,IndikatorKompetensi $indikator) {
        $this->ensureRelation($materi, $kompetensi);
        $this->ensureIndicator($kompetensi, $indikator);

        if ($indikator->raporNilais()->exists()) {
            return back()->with(
                'error',
                'Indikator tidak dapat dihapus karena sudah digunakan pada rapor'
            );
        }

        $indikator->delete();

        return redirect()
            ->route(
                'materi.kompetensi.indikator.index',
                [$materi->id, $kompetensi->id]
            )
            ->with('success', 'Indikator berhasil dihapus');
    }

    /**
     * Pastikan kompetensi milik materi
     */
    private function ensureRelation(Materi $materi, Kompetensi $kompetensi): void
    {
        if ($kompetensi->materi_id !== $materi->id) {
            abort(404);
        }
    }

    /**
     * Pastikan indikator milik kompetensi
     */
    private function ensureIndicator(
        Kompetensi $kompetensi,
        IndikatorKompetensi $indikator
    ): void {
        if ($indikator->kompetensi_id !== $kompetensi->id) {
            abort(404);
        }
    }
}
