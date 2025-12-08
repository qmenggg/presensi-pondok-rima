<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\SubKegiatan;
use App\Models\Santri;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Display the attendance page with kegiatan list for today.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $hariIni = Carbon::parse($tanggal)->locale('id')->isoFormat('dddd');
        
        // Get sub kegiatan based on role
        $query = SubKegiatan::with(['kegiatan', 'guruPenanggungJawab', 'subKegiatanHaris'])
            ->whereHas('subKegiatanHaris', function($q) use ($hariIni) {
                $q->where('hari', strtolower($hariIni));
            });

        // Role-based filtering
        if ($user->role === 'asatid') {
            // Asatid: hanya kegiatan dimana dia jadi guru penanggung jawab
            $query->where('guru_penanggung_jawab_id', $user->id);
        } elseif ($user->role === 'pengurus') {
            // Pengurus: filter berdasarkan jenis kelamin
            if ($user->jenis_kelamin === 'L') {
                $query->whereIn('untuk_jenis_santri', ['putra', 'campur']);
            } elseif ($user->jenis_kelamin === 'P') {
                $query->whereIn('untuk_jenis_santri', ['putri', 'campur']);
            }
            // Jika ALL, tampilkan semua
        }
        // Admin & Pengasuh: lihat semua

        $subKegiatans = $query->orderBy('waktu_mulai', 'asc')->get();

        // Count absensis for each sub kegiatan
        foreach ($subKegiatans as $sub) {
            $sub->absensi_count = Absensi::where('sub_kegiatan_id', $sub->id)
                ->where('tanggal', $tanggal)
                ->count();
            $sub->peserta_count = $this->getPesertaCount($sub);
        }

        return view('pages.absensi.index', [
            'title' => 'Absensi',
            'subKegiatans' => $subKegiatans,
            'tanggal' => $tanggal,
            'hariIni' => $hariIni,
        ]);
    }

    /**
     * Show the form to take attendance for a specific sub kegiatan.
     */
    public function create(SubKegiatan $subKegiatan, Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        
        // Check permission
        if (!$this->canAccessSubKegiatan($subKegiatan)) {
            abort(403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        // Get all santri who are participants of this sub kegiatan
        $santris = $this->getPesertaSantri($subKegiatan);

        // Get existing absensi for today
        $existingAbsensi = Absensi::where('sub_kegiatan_id', $subKegiatan->id)
            ->where('tanggal', $tanggal)
            ->pluck('status', 'santri_id')
            ->toArray();

        $existingKeterangan = Absensi::where('sub_kegiatan_id', $subKegiatan->id)
            ->where('tanggal', $tanggal)
            ->pluck('keterangan', 'santri_id')
            ->toArray();

        return view('pages.absensi.form', [
            'title' => 'Absensi - ' . $subKegiatan->nama_sub_kegiatan,
            'subKegiatan' => $subKegiatan,
            'santris' => $santris,
            'tanggal' => $tanggal,
            'existingAbsensi' => $existingAbsensi,
            'existingKeterangan' => $existingKeterangan,
        ]);
    }

    /**
     * Handle QR Code Scan (AJAX).
     */
    public function scan(Request $request) 
    {
        $request->validate([
            'qr_code' => 'required|string',
            'sub_kegiatan_id' => 'required|exists:sub_kegiatans,id',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $subKegiatan = SubKegiatan::findOrFail($request->sub_kegiatan_id);

        // Check permission
        if (!$this->canAccessSubKegiatan($subKegiatan)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke kegiatan ini.'
            ], 403);
        }

        // Find santri by QR Code (with kamar relation)
        $santri = Santri::with('kamar')->where('qr_code', $request->qr_code)->first();

        if (!$santri) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid atau santri tidak ditemukan.'
            ], 404);
        }

        // Check if santri is a participant
        $isParticipant = $this->isSantriParticipant($subKegiatan, $santri);
        if (!$isParticipant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Santri ini bukan peserta kegiatan ini.'
            ], 400);
        }

        // Check if already present
        $existing = Absensi::where('sub_kegiatan_id', $subKegiatan->id)
            ->where('santri_id', $santri->id)
            ->where('tanggal', Carbon::today()->format('Y-m-d'))
            ->first();

        if ($existing && $existing->status === 'hadir') {
            return response()->json([
                'status' => 'error',
                'message' => 'Santri ini sudah absen hadir sebelumnya.'
            ], 400);
        }

        // Record attendance
        Absensi::updateOrCreate(
            [
                'sub_kegiatan_id' => $subKegiatan->id,
                'santri_id' => $santri->id,
                'tanggal' => Carbon::today()->format('Y-m-d'),
            ],
            [
                'status' => 'hadir',
                'pencatat_id' => auth()->id(),
                // 'latitude' => $request->latitude, // Uncomment if latitude column exists
                // 'longitude' => $request->longitude, // Uncomment if longitude column exists
            ] // Note: keterangan is null for regular presence
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil absen: ' . $santri->nama,
            'santri_nama' => $santri->nama,
            'kamar_nama' => $santri->kamar ? $santri->kamar->nama_kamar : '-',
            'jam' => Carbon::now()->format('H:i:s')
        ]);
    }

    /**
     * Check if santri is a participant.
     */
    private function isSantriParticipant(SubKegiatan $subKegiatan, Santri $santri)
    {
        // Check individual assignment
        $isIndividual = $subKegiatan->subKegiatanSantris()->where('santri_id', $santri->id)->exists();
        if ($isIndividual) return true;

        // Check kamar assignment
        if ($santri->kamar_id) {
            $isKamar = $subKegiatan->subKegiatanKamars()->where('kamar_id', $santri->kamar_id)->exists();
            if ($isKamar) return true;
        }

        return false;
    }

    /**
     * Store or update attendance (Legacy/Manual Fallback).
     */
    public function store(SubKegiatan $subKegiatan, Request $request)
    {
        // ... (existing store method code if needed, but primary is scan now)
        // For now we keep it compatible or redirect back
        return redirect()->route('absensi.index');
    }

    /**
     * Check if current user can access the sub kegiatan.
     */
    private function canAccessSubKegiatan(SubKegiatan $subKegiatan)
    {
        $user = auth()->user();

        // Admin & Pengasuh can access all
        if (in_array($user->role, ['admin', 'pengasuh'])) {
            return true;
        }

        // Asatid: only their own kegiatan
        if ($user->role === 'asatid') {
            return $subKegiatan->guru_penanggung_jawab_id === $user->id;
        }

        // Pengurus: based on jenis kelamin
        if ($user->role === 'pengurus') {
            if ($user->jenis_kelamin === 'L') {
                return in_array($subKegiatan->untuk_jenis_santri, ['putra', 'campur']);
            } elseif ($user->jenis_kelamin === 'P') {
                return in_array($subKegiatan->untuk_jenis_santri, ['putri', 'campur']);
            } elseif ($user->jenis_kelamin === 'ALL') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get peserta santri for a sub kegiatan.
     */
    private function getPesertaSantri(SubKegiatan $subKegiatan)
    {
        // Get santri from assigned kamars (with kamar relation)
        $kamarIds = $subKegiatan->subKegiatanKamars->pluck('kamar_id');
        $santriFromKamar = Santri::with('kamar')
            ->whereIn('kamar_id', $kamarIds)
            ->get();

        // Get individually assigned santri (excluding those already in kamars)
        $santriFromIndividual = Santri::with('kamar')
            ->whereIn('id', $subKegiatan->subKegiatanSantris->pluck('santri_id'))
            ->whereNotIn('kamar_id', $kamarIds)
            ->get();

        // Merge and sort by nama
        return $santriFromKamar->merge($santriFromIndividual)->sortBy('nama');
    }

    /**
     * Get total peserta count.
     */
    private function getPesertaCount(SubKegiatan $subKegiatan)
    {
        $kamarIds = $subKegiatan->subKegiatanKamars->pluck('kamar_id');
        $santriFromKamarCount = Santri::whereIn('kamar_id', $kamarIds)
            ->count();

        $santriFromIndividualCount = Santri::whereIn('id', $subKegiatan->subKegiatanSantris->pluck('santri_id'))
            ->whereNotIn('kamar_id', $kamarIds)
            ->count();

        return $santriFromKamarCount + $santriFromIndividualCount;
    }
}
