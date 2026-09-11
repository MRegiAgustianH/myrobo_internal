<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\MateriModul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MateriModulController extends Controller
{
    public function index(Materi $materi)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['superadmin', 'admin', 'admin_cabang', 'sekretaris', 'instruktur'])) {
            abort(403, 'Tidak memiliki akses');
        }

        $materi->load([
            'moduls' => fn ($q) => $q->orderBy('urutan')
        ]);

        $readonly = $user->role === 'instruktur';

        return view('admin.materi.modul.index', compact(
            'materi',
            'readonly'
        ));
    }

    public function store(Request $request, Materi $materi)
    {
        $this->authorizeRole();

        $request->validate([
            'judul_modul' => 'required|string|max:255',
            'urutan'      => 'required|integer|min:1',
            'file_pdf'    => 'required|file|mimes:pdf|max:5120',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        $path = $request->file('file_pdf')
            ->store('materi/' . $materi->id, 'public');

        MateriModul::create([
            'materi_id'   => $materi->id,
            'judul_modul' => $request->judul_modul,
            'urutan'      => $request->urutan,
            'file_pdf'    => $path,
            'status'      => $request->status,
        ]);

        return redirect()
            ->route('materi.modul.index', $materi->id)
            ->with('success', 'Modul berhasil ditambahkan');
    }

    public function update(Request $request, MateriModul $modul)
    {
        $this->authorizeRole();

        $request->validate([
            'judul_modul' => 'required|string|max:255',
            'urutan'      => 'required|integer|min:1',
            'file_pdf'    => 'nullable|file|mimes:pdf|max:5120',
            'status'      => 'required|in:aktif,nonaktif',
        ]);

        if ($request->hasFile('file_pdf')) {
            if ($modul->file_pdf && Storage::disk('public')->exists($modul->file_pdf)) {
                Storage::disk('public')->delete($modul->file_pdf);
            }

            $modul->file_pdf = $request->file('file_pdf')
                ->store('materi/' . $modul->materi_id, 'public');
        }

        $modul->update([
            'judul_modul' => $request->judul_modul,
            'urutan'      => $request->urutan,
            'status'      => $request->status,
        ]);

        return redirect()
            ->route('materi.modul.index', $modul->materi_id)
            ->with('success', 'Modul berhasil diperbarui');
    }

    public function download(MateriModul $modul)
    {
        $this->authorizeRole();

        if (!Storage::disk('public')->exists($modul->file_pdf)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download(
            $modul->file_pdf,
            $modul->judul_modul . '.pdf'
        );
    }

    public function destroy(MateriModul $modul)
    {
        $this->authorizeRole();

        if ($modul->file_pdf && Storage::disk('public')->exists($modul->file_pdf)) {
            Storage::disk('public')->delete($modul->file_pdf);
        }

        $materiId = $modul->materi_id;
        $modul->delete();

        return redirect()
            ->route('materi.modul.index', $materiId)
            ->with('success', 'Modul berhasil dihapus');
    }

    private function authorizeRole()
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['superadmin', 'admin', 'admin_cabang', 'sekretaris', 'instruktur'])) {
            abort(403, 'Anda tidak memiliki akses');
        }
    }

    public function preview(MateriModul $modul)
    {
        $this->authorizeRole();

        $path = storage_path('app/public/' . $modul->file_pdf);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$modul->judul_modul.'.pdf"',
        ]);
    }
}