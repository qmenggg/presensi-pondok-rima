<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::whereNotIn('role', ['santri']);
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();
        
        return view('pages.user.index', [
            'title' => 'User Management',
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.user.form', [
            'title' => 'Tambah User',
            'user' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P,ALL',
            'role' => 'required|in:admin,pengasuh,pengurus,asatid',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $userData = [
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'role' => $validated['role'],
                'aktif' => true,
            ];

            // Handle foto upload
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoName = Str::slug($validated['nama']) . '-' . time() . '.' . $foto->extension();
                
                // Ensure directory exists
                $fotoFolder = storage_path('app/public/asset_user/foto');
                if (!file_exists($fotoFolder)) {
                    mkdir($fotoFolder, 0777, true);
                }
                
                $foto->storeAs('asset_user/foto', $fotoName, 'public');
                $userData['foto'] = $fotoName;
            }

            User::create($userData);

            return redirect()->route('user.index')
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Don't show santri users
        if ($user->role === 'santri') {
            abort(404);
        }
        
        return view('pages.user.show', [
            'title' => 'Detail User',
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Don't edit santri users
        if ($user->role === 'santri') {
            abort(404);
        }
        
        return view('pages.user.form', [
            'title' => 'Edit User',
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Don't update santri users
        if ($user->role === 'santri') {
            abort(404);
        }

        $rules = [
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P,ALL',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        // Only validate role if current role is not admin
        // Admin role cannot be changed
        if ($user->role !== 'admin') {
            $rules['role'] = 'required|in:admin,pengasuh,pengurus,asatid';
        }

        $validated = $request->validate($rules);

        try {
            $userData = [
                'username' => $validated['username'],
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ];

            // Only update role if not admin
            if ($user->role !== 'admin' && isset($validated['role'])) {
                $userData['role'] = $validated['role'];
            }

            // Update password if provided
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            // Handle foto
            if ($request->hasFile('foto')) {
                // Delete old foto
                if ($user->foto && Storage::disk('public')->exists('asset_user/foto/' . $user->foto)) {
                    Storage::disk('public')->delete('asset_user/foto/' . $user->foto);
                }

                $foto = $request->file('foto');
                $fotoName = Str::slug($validated['nama']) . '-' . time() . '.' . $foto->extension();
                
                $fotoFolder = storage_path('app/public/asset_user/foto');
                if (!file_exists($fotoFolder)) {
                    mkdir($fotoFolder, 0777, true);
                }
                
                $foto->storeAs('asset_user/foto', $fotoName, 'public');
                $userData['foto'] = $fotoName;
            }

            $user->update($userData);

            return redirect()->route('user.index')
                ->with('success', 'User berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        // Don't allow toggle for santri
        if ($user->role === 'santri') {
            return redirect()->route('user.index')
                ->with('error', 'Tidak dapat mengubah status user santri di sini.');
        }

        // Don't allow self-deactivation
        if ($user->id === auth()->id()) {
            return redirect()->route('user.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        try {
            $user->update(['aktif' => !$user->aktif]);
            $status = $user->aktif ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->route('user.index')
                ->with('success', "User berhasil {$status}.");
        } catch (\Exception $e) {
            return redirect()->route('user.index')
                ->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }
}
