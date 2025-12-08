<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kamar::withCount('santris');
        
        // Search
        if ($request->filled('search')) {
            $query->where('nama_kamar', 'like', '%' . $request->search . '%');
        }
        
        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        $kamars = $query->orderBy('nama_kamar', 'asc')->paginate(10)->withQueryString();
        
        return view('pages.kamar.index', [
            'title' => 'Data Kamar',
            'kamars' => $kamars,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.kamar.form', [
            'title' => 'Tambah Kamar',
            'kamar' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:50',
            'jenis' => 'required|in:putra,putri',
        ]);

        try {
            Kamar::create($validated);

            return redirect()->route('kamar.index')
                ->with('success', 'Kamar berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kamar: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kamar $kamar)
    {
        return view('pages.kamar.form', [
            'title' => 'Edit Kamar',
            'kamar' => $kamar,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kamar $kamar)
    {
        $validated = $request->validate([
            'nama_kamar' => 'required|string|max:50',
            'jenis' => 'required|in:putra,putri',
        ]);

        try {
            $kamar->update($validated);

            return redirect()->route('kamar.index')
                ->with('success', 'Kamar berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kamar: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kamar $kamar)
    {
        try {
            if ($kamar->santris()->count() > 0) {
                return redirect()->route('kamar.index')
                    ->with('error', 'Tidak dapat menghapus. Kamar memiliki ' . $kamar->santris()->count() . ' santri terkait.');
            }

            $kamar->delete();

            return redirect()->route('kamar.index')
                ->with('success', 'Kamar berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('kamar.index')
                ->with('error', 'Gagal menghapus kamar: ' . $e->getMessage());
        }
    }
}
