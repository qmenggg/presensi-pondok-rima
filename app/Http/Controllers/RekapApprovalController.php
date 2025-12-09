<?php

namespace App\Http\Controllers;

use App\Models\AbsensiLog;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapApprovalController extends Controller
{
    /**
     * Display pending approvals.
     */
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal');
        
        $query = AbsensiLog::with(['santri.user', 'subKegiatan', 'diubahOleh'])
            ->pending();
        
        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }
        
        $pendingLogs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        return view('pages.rekap.approval', [
            'title' => 'Approval Perubahan Rekap',
            'pendingLogs' => $pendingLogs,
            'tanggal' => $tanggal,
        ]);
    }

    /**
     * Approve a change.
     */
    public function approve(AbsensiLog $log)
    {
        if ($log->approval_status !== 'pending') {
            return redirect()->back()->with('error', 'Log ini sudah diproses.');
        }

        try {
            // Update the actual absensi
            $absensi = Absensi::find($log->absensi_id);
            if ($absensi) {
                $absensi->update([
                    'status' => $log->status_baru,
                    'pencatat_id' => auth()->id(),
                ]);
            }
            
            // Update the log
            $log->update([
                'approval_status' => 'approved',
                'disetujui_oleh' => auth()->id(),
                'disetujui_pada' => now(),
            ]);
            
            return redirect()->back()->with('success', 'Perubahan disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * Reject a change.
     */
    public function reject(AbsensiLog $log)
    {
        if ($log->approval_status !== 'pending') {
            return redirect()->back()->with('error', 'Log ini sudah diproses.');
        }

        try {
            $log->update([
                'approval_status' => 'rejected',
                'disetujui_oleh' => auth()->id(),
                'disetujui_pada' => now(),
            ]);
            
            return redirect()->back()->with('success', 'Perubahan ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }

    /**
     * Approve all pending changes.
     */
    public function approveAll(Request $request)
    {
        $tanggal = $request->get('tanggal');
        
        $query = AbsensiLog::pending();
        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }
        
        $pendingLogs = $query->get();
        $count = 0;
        
        foreach ($pendingLogs as $log) {
            $absensi = Absensi::find($log->absensi_id);
            if ($absensi) {
                $absensi->update([
                    'status' => $log->status_baru,
                    'pencatat_id' => auth()->id(),
                ]);
            }
            
            $log->update([
                'approval_status' => 'approved',
                'disetujui_oleh' => auth()->id(),
                'disetujui_pada' => now(),
            ]);
            $count++;
        }
        
        return redirect()->back()->with('success', "$count perubahan disetujui.");
    }
}
