<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kamar;
use App\Models\SubKegiatan;
use App\Models\Santri;
use App\Models\Tapel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Laporan Harian
     */
    public function harian(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $tanggalCarbon = Carbon::parse($tanggal);
        
        // Get filters
        $filters = $this->getFilters($request);
        
        // Build query
        $query = Absensi::with(['santri.user', 'santri.kamar', 'subKegiatan.kegiatan'])
            ->where('tanggal', $tanggal);
        
        $this->applyFilters($query, $filters);
        
        $absensis = $query->get();
        
        // Group by status for stats
        $stats = [
            'hadir' => $absensis->where('status', 'hadir')->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alfa' => $absensis->where('status', 'alfa')->count(),
            'total' => $absensis->count(),
        ];
        
        // Get filter options
        $filterOptions = $this->getFilterOptions();
        
        return view('pages.laporan.harian', [
            'title' => 'Laporan Harian',
            'tanggal' => $tanggal,
            'tanggalCarbon' => $tanggalCarbon,
            'absensis' => $absensis,
            'stats' => $stats,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }

    /**
     * Laporan Bulanan
     */
    public function bulanan(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::today()->format('Y-m'));
        $parts = explode('-', $bulan);
        $year = $parts[0];
        $month = $parts[1];
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // Get filters
        $filters = $this->getFilters($request);
        
        // Build query for monthly summary
        $query = Absensi::with(['santri.user', 'santri.kamar'])
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        $this->applyFilters($query, $filters);
        
        $absensis = $query->get();
        
        // Group by santri
        $santriStats = [];
        foreach ($absensis->groupBy('santri_id') as $santriId => $records) {
            $santri = $records->first()->santri;
            $santriStats[] = [
                'santri' => $santri,
                'hadir' => $records->where('status', 'hadir')->count(),
                'izin' => $records->where('status', 'izin')->count(),
                'sakit' => $records->where('status', 'sakit')->count(),
                'alfa' => $records->where('status', 'alfa')->count(),
                'total' => $records->count(),
            ];
        }
        
        // Overall stats
        $stats = [
            'hadir' => $absensis->where('status', 'hadir')->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alfa' => $absensis->where('status', 'alfa')->count(),
            'total' => $absensis->count(),
        ];
        
        $filterOptions = $this->getFilterOptions();
        
        return view('pages.laporan.bulanan', [
            'title' => 'Laporan Bulanan',
            'bulan' => $bulan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'santriStats' => $santriStats,
            'stats' => $stats,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }

    /**
     * Laporan Tahunan
     */
    public function tahunan(Request $request)
    {
        $tapelId = $request->get('tapel_id');
        
        // Get active tapel if not specified
        $tapel = $tapelId 
            ? Tapel::find($tapelId) 
            : Tapel::where('aktif', true)->first();
        
        if (!$tapel) {
            return view('pages.laporan.tahunan', [
                'title' => 'Laporan Tahunan',
                'error' => 'Tidak ada Tahun Pelajaran aktif',
                'tapels' => Tapel::orderBy('tanggal_mulai', 'desc')->get(),
            ]);
        }
        
        $startDate = $tapel->tanggal_mulai;
        $endDate = $tapel->tanggal_selesai;
        
        // Get filters
        $filters = $this->getFilters($request);
        
        // Build query for yearly summary
        $query = Absensi::with(['santri.user', 'santri.kamar'])
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        $this->applyFilters($query, $filters);
        
        $absensis = $query->get();
        
        // Group by santri
        $santriStats = [];
        foreach ($absensis->groupBy('santri_id') as $santriId => $records) {
            $santri = $records->first()->santri;
            $total = $records->count();
            $hadir = $records->where('status', 'hadir')->count();
            $santriStats[] = [
                'santri' => $santri,
                'hadir' => $hadir,
                'izin' => $records->where('status', 'izin')->count(),
                'sakit' => $records->where('status', 'sakit')->count(),
                'alfa' => $records->where('status', 'alfa')->count(),
                'total' => $total,
                'persentase' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
            ];
        }
        
        // Sort by persentase descending
        usort($santriStats, fn($a, $b) => $b['persentase'] <=> $a['persentase']);
        
        $stats = [
            'hadir' => $absensis->where('status', 'hadir')->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alfa' => $absensis->where('status', 'alfa')->count(),
            'total' => $absensis->count(),
        ];
        
        $filterOptions = $this->getFilterOptions();
        $filterOptions['tapels'] = Tapel::orderBy('tanggal_mulai', 'desc')->get();
        
        return view('pages.laporan.tahunan', [
            'title' => 'Laporan Tahunan',
            'tapel' => $tapel,
            'santriStats' => $santriStats,
            'stats' => $stats,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }

    /**
     * Get filters from request
     */
    private function getFilters(Request $request)
    {
        return [
            'status' => $request->get('status'),
            'kamar_ids' => $request->get('kamar_ids', []),
            'jenis_santri' => $request->get('jenis_santri'),
            'sub_kegiatan_ids' => $request->get('sub_kegiatan_ids', []),
        ];
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, $filters)
    {
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by kamar (array)
        if (!empty($filters['kamar_ids'])) {
            $query->whereHas('santri', function($q) use ($filters) {
                $q->whereIn('kamar_id', $filters['kamar_ids']);
            });
        }
        
        // Filter by jenis santri
        if (!empty($filters['jenis_santri']) && $filters['jenis_santri'] !== 'semua') {
            $query->whereHas('santri.user', function($q) use ($filters) {
                $jenisKelamin = $filters['jenis_santri'] === 'putra' ? 'L' : 'P';
                $q->where('jenis_kelamin', $jenisKelamin);
            });
        }
        
        // Filter by sub kegiatan (array)
        if (!empty($filters['sub_kegiatan_ids'])) {
            $query->whereIn('sub_kegiatan_id', $filters['sub_kegiatan_ids']);
        }
    }

    /**
     * Get filter options for dropdowns
     */
    private function getFilterOptions()
    {
        return [
            'kamars' => Kamar::orderBy('nama_kamar')->get(),
            'subKegiatans' => SubKegiatan::with('kegiatan')
                ->orderBy('nama_sub_kegiatan')
                ->get(),
        ];
    }
}
