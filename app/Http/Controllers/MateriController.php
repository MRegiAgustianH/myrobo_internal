<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index()
    {
        $materis = Materi::latest()->get();
        return view('admin.materi.index', compact('materis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_materi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        Materi::create($request->all());

        return back()->with('success', 'Materi berhasil ditambahkan');
    }

    public function update(Request $request, Materi $materi)
    {
        $request->validate([
            'nama_materi' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        $materi->update($request->all());

        return back()->with('success', 'Materi berhasil diperbarui');
    }

    public function destroy(Materi $materi)
    {
        $materi->delete();
        return back()->with('success', 'Materi berhasil dihapus');
    }
}
