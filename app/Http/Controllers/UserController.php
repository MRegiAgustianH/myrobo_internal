<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->get();
        $sekolahs = \App\Models\Sekolah::all();
        return view('admin.users.index', compact('users', 'sekolahs'));

}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required',
            'email'      => 'required|email|unique:users',
            'role'       => 'required',
            'password'   => 'nullable|min:6',
            'sekolah_id' => $request->role === 'admin_sekolah'
                ? 'required|exists:sekolahs,id'
                : 'nullable',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        User::create($data);

        return back()->with('success', 'User berhasil ditambahkan');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,instruktur,admin_sekolah',

            // sekolah wajib jika admin_sekolah
            'sekolah_id' => $request->role === 'admin_sekolah'
                ? 'required|exists:sekolahs,id'
                : 'nullable',
        ]);

        /**
         * JIKA ROLE = ADMIN SEKOLAH
         * pastikan 1 sekolah hanya punya 1 admin sekolah
         */
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
            // JIKA ROLE BUKAN admin_sekolah → sekolah_id harus null
            $data['sekolah_id'] = null;
        }

        // UPDATE DATA UTAMA
        $user->update([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'sekolah_id' => $data['sekolah_id'],
        ]);

        // UPDATE PASSWORD JIKA ADA
        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        return back()->with('success', 'User berhasil diperbarui');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
