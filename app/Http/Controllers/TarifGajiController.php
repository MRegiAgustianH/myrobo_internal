<?php

namespace App\Http\Controllers;

use App\Models\TarifGaji;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class TarifGajiController extends Controller
{
    public function index(Request $request)
    {
        $query = TarifGaji::with('sekolah');

        if ($request->filled('jenis')) {
            $query->where('jenis_jadwal', $request->jenis);
        }

        $tarifs = $query->orderBy('jenis_jadwal')->get();

        return view('tarif-gaji.index', compact('tarifs'));
    }


    public function create()
    {
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        return view('tarif-gaji.create', compact('sekolahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_jadwal' => 'required|in:sekolah,home_private',
            'tarif'        => 'required|numeric|min:0',
            'sekolah_id'   => 'nullable|exists:sekolahs,id',
        ]);

        // home private HARUS tanpa sekolah
        if ($request->jenis_jadwal === 'home_private') {
            $request->merge(['sekolah_id' => null]);
        }

        TarifGaji::updateOrCreate(
            [
                'jenis_jadwal' => $request->jenis_jadwal,
                'sekolah_id'   => $request->sekolah_id,
            ],
            [
                'tarif' => $request->tarif,
            ]
        );

        return redirect()
            ->route('tarif-gaji.index')
            ->with('success', 'Tarif gaji berhasil disimpan');
    }

    public function edit(TarifGaji $tarifGaji)
    {
        $sekolahs = Sekolah::orderBy('nama_sekolah')->get();
        return view('tarif-gaji.edit', compact('tarifGaji', 'sekolahs'));
    }

    public function update(Request $request, TarifGaji $tarifGaji)
    {
        $request->validate([
            'tarif' => 'required|numeric|min:0',
        ]);

        $tarifGaji->update([
            'tarif' => $request->tarif,
        ]);

        return redirect()
            ->route('tarif-gaji.index')
            ->with('success', 'Tarif gaji diperbarui');
    }

    public function destroy(TarifGaji $tarifGaji)
    {
        $tarifGaji->delete();
        return back()->with('success', 'Tarif gaji dihapus');
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'jenis_jadwal' => 'required|in:sekolah,home_private',
            'tarif'        => 'required|numeric|min:0',
            'sekolah_id'   => 'nullable|exists:sekolahs,id',
        ]);

        // home private = tanpa sekolah
        if ($request->jenis_jadwal === 'home_private') {
            $request->merge(['sekolah_id' => null]);
        }

        TarifGaji::updateOrCreate(
            [
                'jenis_jadwal' => $request->jenis_jadwal,
                'sekolah_id'   => $request->sekolah_id,
            ],
            [
                'tarif' => $request->tarif,
            ]
        );

        return back()->with('success', 'Tarif gaji berhasil disimpan');
    }



}
