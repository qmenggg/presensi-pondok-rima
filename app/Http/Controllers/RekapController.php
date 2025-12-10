<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiLog;
use App\Models\Izin;
use App\Models\Libur;
use App\Models\SubKegiatan;
use App\Models\SubKegiatanLibur;
use App\Models\Santri;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    /**
     * Display the rekap presensi for a sub kegiatan.
     */
    public function index(SubKegiatan $subKegiatan, $tanggal)
    {
        if (!$this->canAccessSubKegiatan($subKegiatan)) {
            abort(403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $user = auth()->user();
        $tanggalCarbon = Carbon::parse($tanggal);
        
        // Check if today is holiday (global)
        $libur = Libur::isLibur($tanggal);
        
        // Check if this sub_kegiatan is marked as holiday
        $liburKegiatan = SubKegiatanLibur::isLibur($subKegiatan->id, $tanggal);
        
        // Determine if user can edit (disabled if on holiday)
        $canEdit = $this->canEditRekap() && !$libur && !$liburKegiatan;
        
        // Get all peserta santri
        $peserta = $this->getPesertaSantri($subKegiatan);
        
        // Calculate status for each santri
        $rekapData = [];
        foreach ($peserta as $santri) {
            $absensi = Absensi::where('sub_kegiatan_id', $subKegiatan->id)
                ->where('santri_id', $santri->id)
                ->where('tanggal', $tanggal)
                ->first();
            
            if ($absensi && $absensi->status === 'hadir') {
                $status = 'hadir';
                $keterangan = $absensi->keterangan;
            } else {
                $izin = Izin::getActiveForSantri($santri->id, $tanggal);
                if ($izin) {
                    $status = $izin->status;
                    $keterangan = $izin->keterangan;
                } else {
                    $status = 'alfa';
                    $keterangan = null;
                }
            }
            
            // Check if there's pending change
            $pendingLog = AbsensiLog::where('santri_id', $santri->id)
                ->where('sub_kegiatan_id', $subKegiatan->id)
                ->where('tanggal', $tanggal)
                ->pending()
                ->first();
            
            $rekapData[] = [
                'santri' => $santri,
                'status' => $status,
                'keterangan' => $keterangan,
                'absensi' => $absensi,
                'pending' => $pendingLog,
            ];
        }
        
        $stats = [
            'hadir' => collect($rekapData)->where('status', 'hadir')->count(),
            'izin' => collect($rekapData)->where('status', 'izin')->count(),
            'sakit' => collect($rekapData)->where('status', 'sakit')->count(),
            'alfa' => collect($rekapData)->where('status', 'alfa')->count(),
            'total' => count($rekapData),
        ];
        
        return view('pages.rekap.index', [
            'title' => 'Rekap - ' . $subKegiatan->nama_sub_kegiatan,
            'subKegiatan' => $subKegiatan,
            'tanggal' => $tanggal,
            'tanggalCarbon' => $tanggalCarbon,
            'rekapData' => $rekapData,
            'stats' => $stats,
            'libur' => $libur,
            'liburKegiatan' => $liburKegiatan,
            'canEdit' => $canEdit,
        ]);
    }

    /**
     * Finalize and save the rekap.
     */
    public function finalize(Request $request, SubKegiatan $subKegiatan, $tanggal)
    {
        if (!$this->canAccessSubKegiatan($subKegiatan)) {
            abort(403);
        }

        // Pengasuh cannot edit
        if (!$this->canEditRekap()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengedit rekap.');
        }
        
        // Check if global holiday
        $libur = Libur::isLibur($tanggal);
        if ($libur) {
            return redirect()->back()->with('error', 'Tidak dapat mengedit rekap pada hari libur.');
        }
        
        // Check if sub_kegiatan is on holiday
        $liburKegiatan = SubKegiatanLibur::isLibur($subKegiatan->id, $tanggal);
        if ($liburKegiatan) {
            return redirect()->back()->with('error', 'Kegiatan ini sedang diliburkan.');
        }

        $validated = $request->validate([
            'santri_status' => 'required|array',
            'santri_status.*' => 'required|in:hadir,izin,sakit,alfa',
        ]);

        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        try {
            foreach ($validated['santri_status'] as $santriId => $newStatus) {
                // Get current absensi
                $absensi = Absensi::where('sub_kegiatan_id', $subKegiatan->id)
                    ->where('santri_id', $santriId)
                    ->where('tanggal', $tanggal)
                    ->first();
                
                $oldStatus = $absensi ? $absensi->status : null;
                
                // If status changed
                if ($oldStatus !== $newStatus) {
                    if ($isAdmin) {
                        // Admin: directly save
                        $absensi = Absensi::updateOrCreate(
                            [
                                'sub_kegiatan_id' => $subKegiatan->id,
                                'santri_id' => $santriId,
                                'tanggal' => $tanggal,
                            ],
                            [
                                'status' => $newStatus,
                                'pencatat_id' => auth()->id(),
                            ]
                        );
                        
                        // Log the change
                        AbsensiLog::create([
                            'absensi_id' => $absensi->id,
                            'santri_id' => $santriId,
                            'sub_kegiatan_id' => $subKegiatan->id,
                            'tanggal' => $tanggal,
                            'status_lama' => $oldStatus,
                            'status_baru' => $newStatus,
                            'diubah_oleh' => auth()->id(),
                            'disetujui_oleh' => auth()->id(),
                            'approval_status' => 'approved',
                            'disetujui_pada' => now(),
                        ]);
                    } else {
                        // Non-admin: create pending approval
                        // First create/update absensi if not exists
                        if (!$absensi) {
                            $absensi = Absensi::create([
                                'sub_kegiatan_id' => $subKegiatan->id,
                                'santri_id' => $santriId,
                                'tanggal' => $tanggal,
                                'status' => $oldStatus ?? 'alfa',
                                'pencatat_id' => auth()->id(),
                            ]);
                        }
                        
                        // Check if there's existing pending
                        $existingPending = AbsensiLog::where('santri_id', $santriId)
                            ->where('sub_kegiatan_id', $subKegiatan->id)
                            ->where('tanggal', $tanggal)
                            ->pending()
                            ->first();
                        
                        if ($existingPending) {
                            $existingPending->update([
                                'status_baru' => $newStatus,
                                'diubah_oleh' => auth()->id(),
                            ]);
                        } else {
                            AbsensiLog::create([
                                'absensi_id' => $absensi->id,
                                'santri_id' => $santriId,
                                'sub_kegiatan_id' => $subKegiatan->id,
                                'tanggal' => $tanggal,
                                'status_lama' => $oldStatus,
                                'status_baru' => $newStatus,
                                'diubah_oleh' => auth()->id(),
                                'approval_status' => 'pending',
                            ]);
                        }
                    }
                }
            }
            
            if ($isAdmin) {
                return redirect()->back()->with('success', 'Rekap berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Perubahan diajukan, menunggu persetujuan admin.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan rekap: ' . $e->getMessage());
        }
    }

    /**
     * Check if current user can edit rekap.
     */
    private function canEditRekap()
    {
        $user = auth()->user();
        // Pengasuh = read only
        if ($user->role === 'pengasuh') {
            return false;
        }
        return in_array($user->role, ['admin', 'asatid', 'pengurus']);
    }

    /**
     * Check if current user can access the sub kegiatan.
     */
    private function canAccessSubKegiatan(SubKegiatan $subKegiatan)
    {
        $user = auth()->user();

        if (in_array($user->role, ['admin', 'pengasuh'])) {
            return true;
        }

        if ($user->role === 'asatid') {
            return $subKegiatan->guru_penanggung_jawab_id === $user->id;
        }

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
        $kamarIds = $subKegiatan->subKegiatanKamars->pluck('kamar_id');
        $santriFromKamar = Santri::with(['user', 'kamar'])
            ->whereIn('kamar_id', $kamarIds)
            ->get();

        $santriFromIndividual = Santri::with(['user', 'kamar'])
            ->whereIn('id', $subKegiatan->subKegiatanSantris->pluck('santri_id'))
            ->whereNotIn('kamar_id', $kamarIds)
            ->get();

        return $santriFromKamar->merge($santriFromIndividual)->sortBy('nama');
    }
}
