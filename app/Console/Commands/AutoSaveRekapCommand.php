<?php

namespace App\Console\Commands;

use App\Models\AbsensiLog;
use App\Models\Absensi;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoSaveRekapCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rekap:auto-save {--date= : Date to process (YYYY-MM-DD), defaults to yesterday}';

    /**
     * The console command description.
     */
    protected $description = 'Auto-save pending rekap changes from previous day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') 
            ? Carbon::parse($this->option('date'))->format('Y-m-d')
            : Carbon::yesterday()->format('Y-m-d');
        
        $this->info("Processing pending changes for date: $date");
        
        $pendingLogs = AbsensiLog::where('tanggal', $date)
            ->pending()
            ->get();
        
        if ($pendingLogs->isEmpty()) {
            $this->info('No pending changes found.');
            return 0;
        }
        
        $count = 0;
        foreach ($pendingLogs as $log) {
            $absensi = Absensi::find($log->absensi_id);
            if ($absensi) {
                $absensi->update([
                    'status' => $log->status_baru,
                ]);
            }
            
            $log->update([
                'approval_status' => 'auto_saved',
                'disetujui_pada' => now(),
            ]);
            $count++;
        }
        
        $this->info("Auto-saved $count pending changes.");
        return 0;
    }
}
