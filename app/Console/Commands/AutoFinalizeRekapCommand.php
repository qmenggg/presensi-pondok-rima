<?php

namespace App\Console\Commands;

use App\Models\Absensi;
use App\Models\Izin;
use App\Models\SubKegiatan;
use App\Models\Santri;
use App\Models\Tapel;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoFinalizeRekapCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rekap:auto-finalize 
                            {--date= : Date to process (YYYY-MM-DD), defaults to today}
                            {--mode=end-of-day : Mode: "end-of-day" or "after-activity"}';

    /**
     * The console command description.
     */
    protected $description = 'Auto-finalize rekap: save Alfa status for santri who did not attend';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') 
            ? Carbon::parse($this->option('date'))->format('Y-m-d')
            : Carbon::today()->format('Y-m-d');
        
        $mode = $this->option('mode');
        $now = Carbon::now();
        $hariIni = Carbon::parse($date)->locale('id')->isoFormat('dddd');
        
        $this->info("Auto-finalize rekap for date: $date (Mode: $mode)");
        
        // Get system user (first admin) for pencatat_id
        $systemUser = \App\Models\User::where('role', 'admin')->first();
        if (!$systemUser) {
            $this->error('No admin user found for system recorder.');
            return 1;
        }
        $systemUserId = $systemUser->id;
        
        // Get active Tapel
        $tapelAktif = Tapel::getAktif();
        if (!$tapelAktif) {
            $this->warn('No active Tapel found.');
            return 0;
        }
        
        // Get sub kegiatan for the day
        $query = SubKegiatan::with(['kegiatan', 'subKegiatanKamars', 'subKegiatanSantris'])
            ->whereHas('kegiatan', function($q) use ($tapelAktif) {
                $q->where('tapel_id', $tapelAktif->id);
            })
            ->whereHas('subKegiatanHaris', function($q) use ($hariIni) {
                $q->where('hari', strtolower($hariIni));
            });
        
        // Filter based on mode
        if ($mode === 'after-activity') {
            // Only process activities that have ended (with 30 min tolerance)
            $query->whereNotNull('waktu_selesai')
                  ->whereTime('waktu_selesai', '<=', $now->copy()->subMinutes(30)->format('H:i:s'));
        }
        // end-of-day mode processes all activities
        
        $subKegiatans = $query->get();
        
        if ($subKegiatans->isEmpty()) {
            $this->info('No activities to process.');
            return 0;
        }
        
        $totalProcessed = 0;
        $totalAlfa = 0;
        
        foreach ($subKegiatans as $subKegiatan) {
            $this->line("Processing: {$subKegiatan->nama_sub_kegiatan}");
            
            // Get all peserta santri
            $pesertaIds = $this->getPesertaSantriIds($subKegiatan);
            
            foreach ($pesertaIds as $santriId) {
                // Check if already has absensi record
                $existingAbsensi = Absensi::where('sub_kegiatan_id', $subKegiatan->id)
                    ->where('santri_id', $santriId)
                    ->where('tanggal', $date)
                    ->first();
                
                if ($existingAbsensi) {
                    // Already has record, skip
                    continue;
                }
                
                // Check for approved izin
                $izin = Izin::getActiveForSantri($santriId, $date);
                
                if ($izin) {
                    // Has izin, create record with izin/sakit status
                    Absensi::create([
                        'sub_kegiatan_id' => $subKegiatan->id,
                        'santri_id' => $santriId,
                        'tanggal' => $date,
                        'status' => $izin->status, // 'izin' or 'sakit'
                        'keterangan' => 'Auto-finalized: ' . $izin->keterangan,
                        'pencatat_id' => $systemUserId,
                    ]);
                } else {
                    // No izin, mark as Alfa
                    Absensi::create([
                        'sub_kegiatan_id' => $subKegiatan->id,
                        'santri_id' => $santriId,
                        'tanggal' => $date,
                        'status' => 'alfa',
                        'keterangan' => 'Auto-finalized by system',
                        'pencatat_id' => $systemUserId,
                    ]);
                    $totalAlfa++;
                }
                $totalProcessed++;
            }
        }
        
        $this->info("Processed {$subKegiatans->count()} activities.");
        $this->info("Created $totalProcessed records ($totalAlfa Alfa).");
        
        return 0;
    }
    
    /**
     * Get all peserta santri IDs for a sub kegiatan.
     */
    private function getPesertaSantriIds(SubKegiatan $subKegiatan): array
    {
        // Get santri from assigned kamars
        $kamarIds = $subKegiatan->subKegiatanKamars->pluck('kamar_id');
        $santriFromKamar = Santri::whereIn('kamar_id', $kamarIds)
            ->whereHas('user', fn($q) => $q->where('aktif', true))
            ->pluck('id')
            ->toArray();

        // Get individually assigned santri
        $santriFromIndividual = Santri::whereIn('id', $subKegiatan->subKegiatanSantris->pluck('santri_id'))
            ->whereNotIn('kamar_id', $kamarIds)
            ->whereHas('user', fn($q) => $q->where('aktif', true))
            ->pluck('id')
            ->toArray();

        return array_merge($santriFromKamar, $santriFromIndividual);
    }
}
