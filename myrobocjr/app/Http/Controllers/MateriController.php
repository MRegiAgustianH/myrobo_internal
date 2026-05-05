<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    /**
     * Daftar materi
     */
    public function index()
    {
        $materis = Materi::withCount('kompetensis')
            ->orderBy('nama_materi')
            ->get();

        $readonly = auth()->user()->role === 'instruktur';

        return view('admin.materi.index', compact('materis', 'readonly'));
    }

    /**
     * Simpan materi
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama_materi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        Materi::create($validated);

        return back()->with('success', 'Materi berhasil ditambahkan');
    }

    /**
     * Update materi
     */
    public function update(Request $request, Materi $materi)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'nama_materi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        $materi->update($validated);

        return back()->with('success', 'Materi berhasil diperbarui');
    }

    /**
     * Hapus materi
     */
    public function destroy(Materi $materi)
    {
        $this->authorizeAdmin();

        if ($materi->kompetensis()->exists()) {
            return back()->with(
                'error',
                'Materi tidak dapat dihapus karena masih memiliki kompetensi'
            );
        }

        $materi->delete();

        return back()->with('success', 'Materi berhasil dihapus');
    }

    /**
     * Proteksi admin
     */
    private function authorizeAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }
    }
}
