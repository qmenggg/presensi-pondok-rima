<?php

namespace App\Http\Controllers;

use App\Models\Tapel;
use Illuminate\Http\Request;

class TapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tapel::withCount('kegiatans');
        
        // Search
        if ($request->filled('search')) {
            $query->where('nama_tapel', 'like', '%' . $request->search . '%');
        }
        
        $tapels = $query->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();
        
        return view('pages.tapel.index', [
            'title' => 'Tahun Pelajaran',
            'tapels' => $tapels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.tapel.form', [
            'title' => 'Tambah Tahun Pelajaran',
            'tapel' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tapel' => 'required|string|max:50|unique:tapels,nama_tapel',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'aktif' => 'nullable|boolean',
        ]);

        // Convert checkbox value to boolean
        $validated['aktif'] = $request->boolean('aktif');

        try {
            Tapel::create($validated);
            return redirect()->route('tapel.index')
                ->with('success', 'Tahun Pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan Tahun Pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tapel $tapel)
    {
        return view('pages.tapel.form', [
            'title' => 'Edit Tahun Pelajaran',
            'tapel' => $tapel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tapel $tapel)
    {
        $validated = $request->validate([
            'nama_tapel' => 'required|string|max:50|unique:tapels,nama_tapel,' . $tapel->id,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'aktif' => 'nullable|boolean',
        ]);

        // Convert checkbox value to boolean
        $validated['aktif'] = $request->boolean('aktif');

        try {
            $tapel->update($validated);
            return redirect()->route('tapel.index')
                ->with('success', 'Tahun Pelajaran berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate Tahun Pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tapel $tapel)
    {
        try {
            if ($tapel->kegiatans()->count() > 0) {
                return redirect()->route('tapel.index')
                    ->with('error', 'Tidak dapat menghapus. Tahun pelajaran memiliki ' . $tapel->kegiatans()->count() . ' kegiatan terkait.');
            }

            $tapel->delete();
            return redirect()->route('tapel.index')
                ->with('success', 'Tahun Pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('tapel.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
