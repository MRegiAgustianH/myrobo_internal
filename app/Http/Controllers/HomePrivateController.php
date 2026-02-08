<?php

namespace App\Http\Controllers;

use App\Models\HomePrivate;
use Illuminate\Http\Request;

class HomePrivateController extends Controller
{
    public function index()
    {
        $homePrivates = HomePrivate::latest()->get();
        return view('admin.home_private.index', compact('homePrivates'));
    }

    public function create()
    {
        return view('admin.home_private.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'nama_peserta'    => 'required|string|max:255',
            'nama_wali'     => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'catatan'       => 'nullable|string',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        HomePrivate::create($request->all());

        return redirect()
            ->route('home-private.index')
            ->with('success', 'Home Private berhasil ditambahkan');
    }

    public function edit(HomePrivate $homePrivate)
    {
        return view('admin.home_private.edit', compact('homePrivate'));
    }

    public function update(Request $request, HomePrivate $homePrivate)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'nama_peserta'    => 'required|string|max:255',
            'nama_wali'     => 'nullable|string|max:255',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'catatan'       => 'nullable|string',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        $homePrivate->update($request->all());

        return redirect()
            ->route('home-private.index')
            ->with('success', 'Home Private berhasil diperbarui');
    }

    public function destroy(HomePrivate $homePrivate)
    {
        $homePrivate->delete();

        return back()->with('success', 'Home Private berhasil dihapus');
    }
}
