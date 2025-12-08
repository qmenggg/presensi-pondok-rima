<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\Tapel;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kegiatan::with(['tapel', 'subKegiatans']);
        
        // Filter by tapel
        if ($request->filled('tapel_id')) {
            $query->where('tapel_id', $request->tapel_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where('nama_kegiatan', 'like', '%' . $request->search . '%');
        }
        
        $kegiatans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $tapels = Tapel::orderBy('nama_tapel', 'desc')->get();
        
        return view('pages.kegiatan.index', [
            'title' => 'Kegiatan',
            'kegiatans' => $kegiatans,
            'tapels' => $tapels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tapels = Tapel::orderBy('nama_tapel', 'desc')->get();
        return view('pages.kegiatan.form', [
            'title' => 'Tambah Kegiatan',
            'kegiatan' => null,
            'tapels' => $tapels,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tapel_id' => 'required|exists:tapels,id',
        ]);

        try {
            Kegiatan::create($validated);
            return redirect()->route('kegiatan.index')
                ->with('success', 'Kegiatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (Sub Kegiatan list).
     */
    public function show(Kegiatan $kegiatan)
    {
        return redirect()->route('sub-kegiatan.index', ['kegiatan' => $kegiatan->id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kegiatan $kegiatan)
    {
        $tapels = Tapel::orderBy('nama_tapel', 'desc')->get();
        return view('pages.kegiatan.form', [
            'title' => 'Edit Kegiatan',
            'kegiatan' => $kegiatan,
            'tapels' => $tapels,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tapel_id' => 'required|exists:tapels,id',
        ]);

        try {
            $kegiatan->update($validated);
            return redirect()->route('kegiatan.index')
                ->with('success', 'Kegiatan berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        try {
            if ($kegiatan->subKegiatans()->count() > 0) {
                return redirect()->route('kegiatan.index')
                    ->with('error', 'Tidak dapat menghapus. Kegiatan memiliki ' . $kegiatan->subKegiatans()->count() . ' sub kegiatan terkait.');
            }

            $kegiatan->delete();
            return redirect()->route('kegiatan.index')
                ->with('success', 'Kegiatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('kegiatan.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
