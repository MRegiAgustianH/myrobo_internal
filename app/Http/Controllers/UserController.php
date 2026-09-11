<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = User::latest();

        // ADMIN CABANG: hanya lihat user di cabangnya
        if ($user->role === 'admin_cabang') {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'superadmin' && request('cabang_id')) {
            $query->where('cabang_id', request('cabang_id'));
        }

        $users = $query->paginate(10);
        $sekolahs = Sekolah::all();
        $cabangs = Cabang::orderBy('nama_cabang')->get();

        return view('admin.users.index', compact('users', 'sekolahs', 'cabangs'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required',
            'username'   => 'required|string|max:50|unique:users,username',
            'email'      => 'required|email|unique:users',
            'role'       => 'required|in:superadmin,admin_cabang,admin_sekolah,instruktur,bendahara,sekretaris',
            'cabang_id'  => 'nullable|exists:cabangs,id',
            'sekolah_id' => $request->role === 'admin_sekolah'
                ? 'required|exists:sekolahs,id'
                : 'nullable',
            'password'   => 'nullable|min:6',
        ]);

        // SECURITY ACCESS CONTROL: admin_cabang cannot create superadmin or admin_cabang
        if (auth()->user()->role !== 'superadmin' && in_array($data['role'], ['superadmin', 'admin_cabang'])) {
            abort(403, 'Anda tidak memiliki hak untuk membuat role ini.');
        }

        // AUTO-SET CABANG_ID UNTUK ADMIN CABANG
        if (auth()->user()->role === 'admin_cabang' && empty($data['cabang_id'])) {
            $data['cabang_id'] = auth()->user()->cabang_id;
        }

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        User::create($data);

        return back()->with('success', 'User berhasil ditambahkan');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'username'   => 'required|string|max:50|unique:users,username,' . $user->id,
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:superadmin,admin_cabang,admin_sekolah,instruktur,bendahara,sekretaris',
            'cabang_id'  => 'nullable|exists:cabangs,id',
            'sekolah_id' => $request->role === 'admin_sekolah'
                ? 'required|exists:sekolahs,id'
                : 'nullable',
        ]);

        // SECURITY ACCESS CONTROL: admin_cabang cannot update role to superadmin or admin_cabang
        if (auth()->user()->role !== 'superadmin' && in_array($data['role'], ['superadmin', 'admin_cabang'])) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah ke role ini.');
        }

        // JIKA ROLE = ADMIN SEKOLAH, pastikan 1 sekolah hanya 1 admin
        if ($data['role'] === 'admin_sekolah') {
            $exists = User::where('role', 'admin_sekolah')
                ->where('sekolah_id', $data['sekolah_id'])
                ->where('id', '!=', $user->id)
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'sekolah_id' => 'Sekolah ini sudah memiliki Admin Sekolah'
                ]);
            }
        } else {
            $data['sekolah_id'] = null;
        }

        $updateData = [
            'name'       => $data['name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'cabang_id'  => $data['cabang_id'] ?? null,
            'sekolah_id' => $data['sekolah_id'],
        ];

        $user->update($updateData);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        return back()->with('success', 'User berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            if (auth()->id() === $user->id) {
                return redirect()->back()->with('success', 'Anda tidak dapat menghapus akun sendiri');
            }

            // Proteksi: superadmin terakhir tidak boleh dihapus
            if ($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1) {
                return redirect()->back()->with('success', 'Superadmin terakhir tidak boleh dihapus');
            }

            $user->delete();

            return redirect()->route('users.index')->with('success', 'User berhasil dihapus');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('users.index')->with('success', 'User tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('success', 'Terjadi kesalahan saat menghapus user');
        }
    }
}