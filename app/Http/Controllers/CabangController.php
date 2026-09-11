<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::latest()->paginate(10);
        return view('admin.cabang.index', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'kode_cabang' => 'required|string|max:10|unique:cabangs,kode_cabang',
            'alamat'      => 'nullable|string',
            'kontak'      => 'nullable|string|max:50',
            'logo'        => 'nullable|image|max:2048',

        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logo-cabang', 'public');
        }

        $data['is_aktif'] = $request->boolean('is_aktif', true);

        Cabang::create($data);

        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Cabang $cabang)
    {
        $data = $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'kode_cabang' => 'required|string|max:10|unique:cabangs,kode_cabang,' . $cabang->id,
            'alamat'      => 'nullable|string',
            'kontak'      => 'nullable|string|max:50',
            'logo'        => 'nullable|image|max:2048',

        ]);

        if ($request->hasFile('logo')) {
            if ($cabang->logo) {
                Storage::disk('public')->delete($cabang->logo);
            }
            $data['logo'] = $request->file('logo')->store('logo-cabang', 'public');
        }

        $data['is_aktif'] = $request->boolean('is_aktif', true);

        $cabang->update($data);

        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Cabang $cabang)
    {
        if ($cabang->logo) {
            Storage::disk('public')->delete($cabang->logo);
        }

        $cabang->delete();

        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}