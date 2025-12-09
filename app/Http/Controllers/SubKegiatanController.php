<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\SubKegiatanHari;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Santri;
use Illuminate\Http\Request;

class SubKegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Kegiatan $kegiatan)
    {
        $query = SubKegiatan::with(['guruPenanggungJawab', 'subKegiatanHaris', 'subKegiatanKamars.kamar.santris', 'subKegiatanSantris'])
            ->where('kegiatan_id', $kegiatan->id);
        
        // Search
        if ($request->filled('search')) {
            $query->where('nama_sub_kegiatan', 'like', '%' . $request->search . '%');
        }
        
        $subKegiatans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('pages.kegiatan.sub.index', [
            'title' => 'Sub Kegiatan - ' . $kegiatan->nama_kegiatan,
            'kegiatan' => $kegiatan,
            'subKegiatans' => $subKegiatans,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kegiatan $kegiatan)
    {
        $gurus = User::where('role', 'asatid')->where('aktif', true)->orderBy('nama')->get();
        return view('pages.kegiatan.sub.form', [
            'title' => 'Tambah Sub Kegiatan',
            'kegiatan' => $kegiatan,
            'subKegiatan' => null,
            'gurus' => $gurus,
            'selectedHaris' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'nama_sub_kegiatan' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'haris' => 'required|array|min:1',
            'haris.*' => 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'untuk_jenis_santri' => 'required|in:putra,putri,campur',
            'lokasi' => 'nullable|string|max:100',
            'guru_penanggung_jawab' => 'nullable|exists:users,id',
        ]);

        try {
            $subKegiatan = SubKegiatan::create([
                'kegiatan_id' => $kegiatan->id,
                'nama_sub_kegiatan' => $validated['nama_sub_kegiatan'],
                'keterangan' => $validated['keterangan'] ?? null,
                'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                'untuk_jenis_santri' => $validated['untuk_jenis_santri'],
                'lokasi' => $validated['lokasi'] ?? null,
                'guru_penanggung_jawab' => $validated['guru_penanggung_jawab'] ?? null,
            ]);

            // Save haris
            foreach ($validated['haris'] as $hari) {
                $subKegiatan->subKegiatanHaris()->create(['hari' => $hari]);
            }

            return redirect()->route('sub-kegiatan.index', $kegiatan->id)
                ->with('success', 'Sub Kegiatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan sub kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kegiatan $kegiatan, SubKegiatan $subKegiatan)
    {
        $gurus = User::where('role', 'asatid')->where('aktif', true)->orderBy('nama')->get();
        $selectedHaris = $subKegiatan->subKegiatanHaris->pluck('hari')->toArray();
        
        return view('pages.kegiatan.sub.form', [
            'title' => 'Edit Sub Kegiatan',
            'kegiatan' => $kegiatan,
            'subKegiatan' => $subKegiatan,
            'gurus' => $gurus,
            'selectedHaris' => $selectedHaris,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kegiatan $kegiatan, SubKegiatan $subKegiatan)
    {
        $validated = $request->validate([
            'nama_sub_kegiatan' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
            'haris' => 'required|array|min:1',
            'haris.*' => 'in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'untuk_jenis_santri' => 'required|in:putra,putri,campur',
            'lokasi' => 'nullable|string|max:100',
            'guru_penanggung_jawab' => 'nullable|exists:users,id',
        ]);

        try {
            $subKegiatan->update([
                'nama_sub_kegiatan' => $validated['nama_sub_kegiatan'],
                'keterangan' => $validated['keterangan'] ?? null,
                'waktu_mulai' => $validated['waktu_mulai'] ?? null,
                'waktu_selesai' => $validated['waktu_selesai'] ?? null,
                'untuk_jenis_santri' => $validated['untuk_jenis_santri'],
                'lokasi' => $validated['lokasi'] ?? null,
                'guru_penanggung_jawab' => $validated['guru_penanggung_jawab'] ?? null,
            ]);

            // Update haris
            $subKegiatan->subKegiatanHaris()->delete();
            foreach ($validated['haris'] as $hari) {
                $subKegiatan->subKegiatanHaris()->create(['hari' => $hari]);
            }

            return redirect()->route('sub-kegiatan.index', $kegiatan->id)
                ->with('success', 'Sub Kegiatan berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate sub kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan, SubKegiatan $subKegiatan)
    {
        try {
            // Delete related data
            $subKegiatan->subKegiatanHaris()->delete();
            $subKegiatan->subKegiatanKamars()->delete();
            $subKegiatan->subKegiatanSantris()->delete();
            $subKegiatan->delete();
            
            return redirect()->route('sub-kegiatan.index', $kegiatan->id)
                ->with('success', 'Sub Kegiatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('sub-kegiatan.index', $kegiatan->id)
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Show assign peserta form.
     */
    public function assign(Kegiatan $kegiatan, SubKegiatan $subKegiatan)
    {
        $jenisFilter = $subKegiatan->untuk_jenis_santri;
        
        // Filter kamar berdasarkan jenis santri
        $kamarsQuery = Kamar::orderBy('nama_kamar');
        if ($jenisFilter === 'putra') {
            $kamarsQuery->where('jenis', 'putra');
        } elseif ($jenisFilter === 'putri') {
            $kamarsQuery->where('jenis', 'putri');
        }
        // jika campur, tidak perlu filter
        $kamars = $kamarsQuery->get();
        
        // Filter santri berdasarkan jenis kelamin
        $santrisQuery = Santri::with('user', 'kamar')
            ->whereHas('user', function($q) use ($jenisFilter) {
                $q->where('aktif', true);
                if ($jenisFilter === 'putra') {
                    $q->where('jenis_kelamin', 'L');
                } elseif ($jenisFilter === 'putri') {
                    $q->where('jenis_kelamin', 'P');
                }
            });
        $santris = $santrisQuery->get();
        
        $assignedKamars = $subKegiatan->subKegiatanKamars()->pluck('kamar_id')->toArray();
        $assignedSantris = $subKegiatan->subKegiatanSantris()->pluck('santri_id')->toArray();
        
        return view('pages.kegiatan.sub.assign', [
            'title' => 'Assign Peserta - ' . $subKegiatan->nama_sub_kegiatan,
            'kegiatan' => $kegiatan,
            'subKegiatan' => $subKegiatan,
            'kamars' => $kamars,
            'santris' => $santris,
            'assignedKamars' => $assignedKamars,
            'assignedSantris' => $assignedSantris,
        ]);
    }


    /**
     * Store assign peserta.
     */
    public function storeAssign(Request $request, Kegiatan $kegiatan, SubKegiatan $subKegiatan)
    {
        $validated = $request->validate([
            'kamars' => 'nullable|array',
            'kamars.*' => 'exists:kamars,id',
            'santris' => 'nullable|array',
            'santris.*' => 'exists:santris,id',
        ]);

        try {
            // Sync kamars
            $subKegiatan->subKegiatanKamars()->delete();
            if (!empty($validated['kamars'])) {
                foreach ($validated['kamars'] as $kamarId) {
                    $subKegiatan->subKegiatanKamars()->create(['kamar_id' => $kamarId]);
                }
            }

            // Sync santris
            $subKegiatan->subKegiatanSantris()->delete();
            if (!empty($validated['santris'])) {
                foreach ($validated['santris'] as $santriId) {
                    $subKegiatan->subKegiatanSantris()->create(['santri_id' => $santriId]);
                }
            }

            return redirect()->route('sub-kegiatan.index', $kegiatan->id)
                ->with('success', 'Peserta berhasil diassign.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal assign peserta: ' . $e->getMessage());
        }
    }

    /**
     * Helper to calculate unique santri count
     */
    public static function getUniqueSantriCount(SubKegiatan $sub): int
    {
        $santriIdsFromKamars = [];
        foreach ($sub->subKegiatanKamars as $sk) {
            $kamarSantriIds = $sk->kamar->santris->pluck('id')->toArray();
            $santriIdsFromKamars = array_merge($santriIdsFromKamars, $kamarSantriIds);
        }
        
        $individualSantriIds = $sub->subKegiatanSantris->pluck('santri_id')->toArray();
        
        return count(array_unique(array_merge($santriIdsFromKamars, $individualSantriIds)));
    }
}
