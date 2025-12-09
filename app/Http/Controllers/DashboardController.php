<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Santri;
use App\Models\Kamar;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Tapel;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $hariIni = $today->locale('id')->isoFormat('dddd');
        
        // Get active Tapel
        $tapelAktif = Tapel::getAktif();
        
        // ============================================
        // STATISTIK UMUM
        // ============================================
        $stats = [
            'total_user' => User::count(),
            'total_santri' => Santri::count(),
            'total_santri_aktif' => Santri::whereHas('user', fn($q) => $q->where('aktif', true))->count(),
            'total_kamar' => Kamar::count(),
            'kamar_putra' => Kamar::where('jenis', 'putra')->count(),
            'kamar_putri' => Kamar::where('jenis', 'putri')->count(),
            'total_kegiatan' => Kegiatan::count(),
            'total_sub_kegiatan' => SubKegiatan::count(),
            'tapel_aktif' => $tapelAktif?->nama_tapel ?? '-',
        ];
        
        // ============================================
        // STATISTIK ABSENSI HARI INI
        // ============================================
        $absensiHariIni = Absensi::where('tanggal', $today->format('Y-m-d'))->get();
        
        $stats['absensi'] = [
            'hadir' => $absensiHariIni->where('status', 'hadir')->count(),
            'izin' => $absensiHariIni->where('status', 'izin')->count(),
            'sakit' => $absensiHariIni->where('status', 'sakit')->count(),
            'alfa' => $absensiHariIni->where('status', 'alfa')->count(),
        ];
        
        $totalAbsensi = array_sum($stats['absensi']);
        $stats['absensi']['persentase_hadir'] = $totalAbsensi > 0 
            ? round(($stats['absensi']['hadir'] / $totalAbsensi) * 100, 1) 
            : 0;

        // ============================================
        // JADWAL KEGIATAN HARI INI
        // ============================================
        $jadwalQuery = SubKegiatan::with(['kegiatan'])
            ->whereHas('subKegiatanHaris', function($q) use ($hariIni) {
                $q->where('hari', strtolower($hariIni));
            });
        
        // Filter by active tapel if exists
        if ($tapelAktif) {
            $jadwalQuery->whereHas('kegiatan', function($q) use ($tapelAktif) {
                $q->where('tapel_id', $tapelAktif->id);
            });
        }
        
        $jadwalHariIni = $jadwalQuery->orderBy('waktu_mulai', 'asc')->get();
        
        // Add status to each jadwal
        $now = Carbon::now();
        $jadwalHariIni = $jadwalHariIni->map(function($jadwal) use ($now) {
            $waktuMulai = Carbon::parse($jadwal->waktu_mulai);
            $waktuSelesai = Carbon::parse($jadwal->waktu_selesai);
            
            if ($now->lt($waktuMulai)) {
                $jadwal->status_jadwal = 'belum_mulai';
            } elseif ($now->between($waktuMulai, $waktuSelesai)) {
                $jadwal->status_jadwal = 'berlangsung';
            } else {
                $jadwal->status_jadwal = 'selesai';
            }
            
            return $jadwal;
        });

        return view('pages.dashboard.ecommerce', [
            'title' => 'Dashboard',
            'role' => $user->role,
            'stats' => $stats,
            'jadwalHariIni' => $jadwalHariIni,
            'tapelAktif' => $tapelAktif,
            'today' => $today->format('d F Y'),
        ]);
    }
}
