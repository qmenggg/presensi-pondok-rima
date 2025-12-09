<?php

namespace App\Http\Controllers;

use App\Models\Libur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LiburController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Libur::query();
        
        // Search
        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }
        
        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        $liburs = $query->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();
        
        return view('pages.libur.index', [
            'title' => 'Hari Libur',
            'liburs' => $liburs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.libur.form', [
            'title' => 'Tambah Hari Libur',
            'libur' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:500',
            'jenis' => 'required|in:nasional,pondok,khusus',
            'untuk_jenis_santri' => 'required|in:putra,putri,semua',
            'rutin_mingguan' => 'nullable|boolean',
            'hari_rutin' => 'nullable|required_if:rutin_mingguan,1|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        ]);

        // Convert checkbox value
        $validated['rutin_mingguan'] = $request->boolean('rutin_mingguan');
        
        // Clear hari_rutin if not rutin
        if (!$validated['rutin_mingguan']) {
            $validated['hari_rutin'] = null;
        }

        try {
            Libur::create($validated);
            return redirect()->route('libur.index')
                ->with('success', 'Hari libur berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan hari libur: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Libur $libur)
    {
        return view('pages.libur.form', [
            'title' => 'Edit Hari Libur',
            'libur' => $libur,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Libur $libur)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:500',
            'jenis' => 'required|in:nasional,pondok,khusus',
            'untuk_jenis_santri' => 'required|in:putra,putri,semua',
            'rutin_mingguan' => 'nullable|boolean',
            'hari_rutin' => 'nullable|required_if:rutin_mingguan,1|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        ]);

        $validated['rutin_mingguan'] = $request->boolean('rutin_mingguan');
        
        if (!$validated['rutin_mingguan']) {
            $validated['hari_rutin'] = null;
        }

        try {
            $libur->update($validated);
            return redirect()->route('libur.index')
                ->with('success', 'Hari libur berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate hari libur: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Libur $libur)
    {
        try {
            $libur->delete();
            return redirect()->route('libur.index')
                ->with('success', 'Hari libur berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('libur.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Check if a date is a holiday.
     */
    public static function isLibur($tanggal, $jenisSantri = 'semua')
    {
        $hari = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd');
        
        // Check date range libur
        $libur = Libur::where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->where(function($q) use ($jenisSantri) {
                $q->where('untuk_jenis_santri', $jenisSantri)
                  ->orWhere('untuk_jenis_santri', 'semua');
            })
            ->first();
            
        if ($libur) return $libur;
        
        // Check rutin mingguan
        return Libur::where('rutin_mingguan', true)
            ->where('hari_rutin', strtolower($hari))
            ->where(function($q) use ($jenisSantri) {
                $q->where('untuk_jenis_santri', $jenisSantri)
                  ->orWhere('untuk_jenis_santri', 'semua');
            })
            ->first();
    }
}
