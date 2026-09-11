<?php

namespace App\Http\Controllers;

use App\Models\HomePrivate;
use App\Models\Cabang;
use Illuminate\Http\Request;

class HomePrivateController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = HomePrivate::latest();

        if ($user->role === 'admin_cabang') {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'superadmin' && request('cabang_id')) {
            $query->where('cabang_id', request('cabang_id'));
        }

        $homePrivates = $query->paginate(10);
        $cabangs = Cabang::orderBy('nama_cabang')->get();

        return view('admin.home_private.index', compact('homePrivates', 'cabangs'));
    }

    public function create()
    {
        return view('admin.home_private.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cabang_id'      => 'nullable|exists:cabangs,id',
            'nama_kegiatan'  => 'required|string|max:255',
            'nama_peserta'   => 'required|string|max:255',
            'nama_wali'      => 'nullable|string|max:255',
            'no_hp'          => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'catatan'        => 'nullable|string',
            'status'         => 'required|in:aktif,nonaktif',
        ]);

        $data = $request->all();

        if (auth()->user()->role === 'admin_cabang' && empty($data['cabang_id'])) {
            $data['cabang_id'] = auth()->user()->cabang_id;
        }

        HomePrivate::create($data);

        return redirect()->route('home-private.index')
            ->with('success', 'Home Private berhasil ditambahkan');
    }

    public function edit(HomePrivate $homePrivate)
    {
        return view('admin.home_private.edit', compact('homePrivate'));
    }

    public function update(Request $request, HomePrivate $homePrivate)
    {
        $request->validate([
            'cabang_id'      => 'nullable|exists:cabangs,id',
            'nama_kegiatan'  => 'required|string|max:255',
            'nama_peserta'   => 'required|string|max:255',
            'nama_wali'      => 'nullable|string|max:255',
            'no_hp'          => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'catatan'        => 'nullable|string',
            'status'         => 'required|in:aktif,nonaktif',
        ]);

        $homePrivate->update($request->all());

        return redirect()->route('home-private.index')
            ->with('success', 'Home Private berhasil diperbarui');
    }

    public function destroy(HomePrivate $homePrivate)
    {
        $homePrivate->delete();

        return back()->with('success', 'Home Private berhasil dihapus');
    }
}