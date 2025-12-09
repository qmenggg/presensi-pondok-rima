<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use App\Models\Santri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IzinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Izin::with(['santri.user', 'disetujuiOleh']);
        
        // Search by nama santri
        if ($request->filled('search')) {
            $query->whereHas('santri.user', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by status approval
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->whereNull('disetujui_oleh');
            } elseif ($request->status === 'approved') {
                $query->whereNotNull('disetujui_oleh')->whereNull('alasan_reject');
            } elseif ($request->status === 'rejected') {
                $query->whereNotNull('alasan_reject');
            }
        }
        
        // Filter by jenis (sakit/izin)
        if ($request->filled('jenis')) {
            $query->where('status', $request->jenis);
        }
        
        $izins = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('pages.izin.index', [
            'title' => 'Izin Santri',
            'izins' => $izins,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $santris = Santri::with('user')
            ->whereHas('user', function($q) {
                $q->where('aktif', true);
            })
            ->get()
            ->sortBy('nama');
        
        return view('pages.izin.form', [
            'title' => 'Tambah Izin',
            'izin' => null,
            'santris' => $santris,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:sakit,izin',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            Izin::create($validated);
            return redirect()->route('izin.index')
                ->with('success', 'Izin berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan izin: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Izin $izin)
    {
        $izin->load(['santri.user', 'santri.kamar', 'disetujuiOleh']);
        
        return view('pages.izin.show', [
            'title' => 'Detail Izin',
            'izin' => $izin,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Izin $izin)
    {
        // Only allow editing pending izin
        if ($izin->disetujui_oleh) {
            return redirect()->route('izin.index')
                ->with('error', 'Izin yang sudah diproses tidak dapat diedit.');
        }
        
        $santris = Santri::with('user')
            ->whereHas('user', function($q) {
                $q->where('aktif', true);
            })
            ->get()
            ->sortBy('nama');
        
        return view('pages.izin.form', [
            'title' => 'Edit Izin',
            'izin' => $izin,
            'santris' => $santris,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Izin $izin)
    {
        // Only allow updating pending izin
        if ($izin->disetujui_oleh) {
            return redirect()->route('izin.index')
                ->with('error', 'Izin yang sudah diproses tidak dapat diedit.');
        }
        
        $validated = $request->validate([
            'santri_id' => 'required|exists:santris,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:sakit,izin',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $izin->update($validated);
            return redirect()->route('izin.index')
                ->with('success', 'Izin berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate izin: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Izin $izin)
    {
        // Only allow deleting pending izin
        if ($izin->disetujui_oleh) {
            return redirect()->route('izin.index')
                ->with('error', 'Izin yang sudah diproses tidak dapat dihapus.');
        }
        
        try {
            $izin->delete();
            return redirect()->route('izin.index')
                ->with('success', 'Izin berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('izin.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Approve the izin.
     */
    public function approve(Izin $izin)
    {
        if ($izin->disetujui_oleh) {
            return redirect()->route('izin.index')
                ->with('error', 'Izin sudah diproses sebelumnya.');
        }
        
        try {
            $izin->update([
                'disetujui_oleh' => auth()->id(),
                'disetujui_pada' => Carbon::now(),
                'alasan_reject' => null,
            ]);
            
            return redirect()->route('izin.index')
                ->with('success', 'Izin berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->route('izin.index')
                ->with('error', 'Gagal menyetujui izin: ' . $e->getMessage());
        }
    }

    /**
     * Reject the izin.
     */
    public function reject(Request $request, Izin $izin)
    {
        if ($izin->disetujui_oleh && !$izin->alasan_reject) {
            return redirect()->route('izin.index')
                ->with('error', 'Izin yang sudah disetujui tidak dapat ditolak.');
        }
        
        $validated = $request->validate([
            'alasan_reject' => 'required|string|max:500',
        ]);
        
        try {
            $izin->update([
                'disetujui_oleh' => auth()->id(),
                'disetujui_pada' => Carbon::now(),
                'alasan_reject' => $validated['alasan_reject'],
            ]);
            
            return redirect()->route('izin.index')
                ->with('success', 'Izin berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->route('izin.index')
                ->with('error', 'Gagal menolak izin: ' . $e->getMessage());
        }
    }

    /**
     * Get active izin for a santri on a specific date.
     */
    public static function getActiveForSantri($santriId, $tanggal)
    {
        return Izin::where('santri_id', $santriId)
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->whereNotNull('disetujui_oleh')
            ->whereNull('alasan_reject')
            ->first();
    }
}
