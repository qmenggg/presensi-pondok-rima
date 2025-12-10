<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Libur;
use App\Models\Santri;
use App\Models\SubKegiatan;
use App\Models\SubKegiatanLibur;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AbsensiScanner extends Component
{
    use WithPagination;

    public int $subKegiatanId;
    public string $tanggal;
    public ?string $subKegiatanNama = null;
    
    // Feedback state
    public bool $showFeedback = false;
    public string $feedbackType = 'success'; // 'success' or 'error'
    public string $feedbackMessage = '';
    public ?string $lastScannedName = null;

    // Counters
    public int $hadirCount = 0;
    public int $totalPeserta = 0;

    // Pagination
    public int $perPage = 15;

    protected $listeners = ['qrScanned' => 'scan'];

    public function mount(SubKegiatan $subKegiatan, ?string $tanggal = null): void
    {
        $this->subKegiatanId = $subKegiatan->id;
        $this->tanggal = $tanggal ?? Carbon::today()->format('Y-m-d');
        $this->subKegiatanNama = $subKegiatan->nama_sub_kegiatan;

        $this->updateCounters();
    }

    public function scan(string $qrCode): array
    {
        $subKegiatan = SubKegiatan::with(['kegiatan.tapel', 'subKegiatanHaris'])->find($this->subKegiatanId);
        
        if (!$subKegiatan) {
            return $this->setFeedback('error', 'Sub kegiatan tidak ditemukan.');
        }

        // Check permission
        if (!$this->canAccessSubKegiatan($subKegiatan)) {
            return $this->setFeedback('error', 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        // ============================================
        // VALIDASI 1: Cek Tahun Pelajaran (Tapel) Aktif
        // ============================================
        $tapel = $subKegiatan->kegiatan->tapel ?? null;
        if (!$tapel || !$tapel->aktif) {
            return $this->setFeedback('error', 'Tahun pelajaran tidak aktif. Absensi tidak dapat dilakukan.');
        }

        // ============================================
        // VALIDASI 2: Cek Hari Kegiatan
        // ============================================
        $hariIni = Carbon::today()->locale('id')->isoFormat('dddd');
        $hariKegiatan = $subKegiatan->subKegiatanHaris->pluck('hari')->map(fn($h) => strtolower($h))->toArray();
        
        if (!in_array(strtolower($hariIni), $hariKegiatan)) {
            return $this->setFeedback('error', 'Kegiatan ini tidak dijadwalkan hari ini (' . $hariIni . ').');
        }

        // ============================================
        // VALIDASI 3: Cek Libur Global
        // ============================================
        $jenisSantri = $subKegiatan->untuk_jenis_santri === 'campur' ? 'semua' : $subKegiatan->untuk_jenis_santri;
        $liburGlobal = Libur::isLibur(Carbon::today(), $jenisSantri);
        if ($liburGlobal) {
            return $this->setFeedback('error', 'Hari ini libur: ' . ($liburGlobal->keterangan ?? 'Libur'));
        }

        // ============================================
        // VALIDASI 4: Cek Libur Sub Kegiatan
        // ============================================
        $liburKegiatan = SubKegiatanLibur::isLibur($subKegiatan->id, Carbon::today());
        if ($liburKegiatan) {
            return $this->setFeedback('error', 'Kegiatan ini diliburkan: ' . ($liburKegiatan->keterangan ?? 'Libur'));
        }

        // ============================================
        // VALIDASI 5: Cek Waktu Kegiatan (dengan toleransi)
        // ============================================
        $now = Carbon::now();
        $waktuMulai = Carbon::parse($subKegiatan->waktu_mulai);
        $waktuSelesai = Carbon::parse($subKegiatan->waktu_selesai);
        
        // Toleransi: 15 menit sebelum mulai, 30 menit setelah selesai
        $waktuMulaiBatas = $waktuMulai->copy()->subMinutes(15);
        $waktuSelesaiBatas = $waktuSelesai->copy()->addMinutes(30);
        
        if (!$now->between($waktuMulaiBatas, $waktuSelesaiBatas)) {
            $jamMulai = Carbon::parse($subKegiatan->waktu_mulai)->format('H:i');
            $jamSelesai = Carbon::parse($subKegiatan->waktu_selesai)->format('H:i');
            return $this->setFeedback('error', "Absensi hanya bisa dilakukan pada jam {$jamMulai} - {$jamSelesai}.");
        }

        // Find santri by QR Code
        $santri = Santri::with(['user', 'kamar'])->where('qr_code', $qrCode)->first();

        if (!$santri) {
            return $this->setFeedback('error', 'QR Code tidak valid atau santri tidak ditemukan.');
        }

        // ============================================
        // VALIDASI 4: Cek Status Santri Aktif
        // ============================================
        if (!$santri->user || !$santri->user->aktif) {
            return $this->setFeedback('error', 'Santri tidak dalam status aktif.');
        }

        // Check if santri is participant
        if (!$this->isSantriParticipant($subKegiatan, $santri)) {
            return $this->setFeedback('error', 'Santri ini bukan peserta kegiatan ini.');
        }

        // Check if already present
        $existing = Absensi::where('sub_kegiatan_id', $subKegiatan->id)
            ->where('santri_id', $santri->id)
            ->where('tanggal', $this->tanggal)
            ->first();

        if ($existing && $existing->status === 'hadir') {
            return $this->setFeedback('error', 'Santri ini sudah absen hadir sebelumnya.');
        }

        // Record attendance
        Absensi::updateOrCreate(
            [
                'sub_kegiatan_id' => $subKegiatan->id,
                'santri_id' => $santri->id,
                'tanggal' => $this->tanggal,
            ],
            [
                'status' => 'hadir',
                'pencatat_id' => auth()->id(),
            ]
        );

        $this->updateCounters();
        $this->resetPage();

        return $this->setFeedback('success', 'Berhasil absen: ' . $santri->nama, $santri->nama);
    }

    public function loadMore(): void
    {
        $this->perPage += 15;
    }

    public function hideFeedback(): void
    {
        $this->showFeedback = false;
    }

    public function render()
    {
        $recentAbsensi = Absensi::with(['santri.user', 'santri.kamar'])
            ->where('sub_kegiatan_id', $this->subKegiatanId)
            ->where('tanggal', $this->tanggal)
            ->where('status', 'hadir')
            ->orderByDesc('updated_at')
            ->take($this->perPage)
            ->get();

        return view('livewire.absensi-scanner', [
            'recentAbsensi' => $recentAbsensi,
        ])->layout('layouts.app', ['title' => 'Scan QR - ' . $this->subKegiatanNama]);
    }

    private function setFeedback(string $type, string $message, ?string $name = null): array
    {
        $this->feedbackType = $type;
        $this->feedbackMessage = $message;
        $this->lastScannedName = $name;
        $this->showFeedback = true;

        // Auto-hide feedback after 2.5 seconds
        $this->dispatch('autoHideFeedback');
        $this->dispatch('feedbackShown', type: $type);

        return [
            'status' => $type,
            'message' => $message,
            'santri_nama' => $name,
        ];
    }

    private function updateCounters(): void
    {
        $this->hadirCount = Absensi::where('sub_kegiatan_id', $this->subKegiatanId)
            ->where('tanggal', $this->tanggal)
            ->where('status', 'hadir')
            ->count();

        $subKegiatan = SubKegiatan::with(['subKegiatanKamars', 'subKegiatanSantris'])->find($this->subKegiatanId);
        if ($subKegiatan) {
            $kamarIds = $subKegiatan->subKegiatanKamars->pluck('kamar_id');
            $santriFromKamarCount = Santri::whereIn('kamar_id', $kamarIds)->count();
            $santriFromIndividualCount = Santri::whereIn('id', $subKegiatan->subKegiatanSantris->pluck('santri_id'))
                ->whereNotIn('kamar_id', $kamarIds)
                ->count();
            $this->totalPeserta = $santriFromKamarCount + $santriFromIndividualCount;
        }
    }

    private function canAccessSubKegiatan(SubKegiatan $subKegiatan): bool
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

    private function isSantriParticipant(SubKegiatan $subKegiatan, Santri $santri): bool
    {
        $isIndividual = $subKegiatan->subKegiatanSantris()->where('santri_id', $santri->id)->exists();
        if ($isIndividual) return true;

        if ($santri->kamar_id) {
            $isKamar = $subKegiatan->subKegiatanKamars()->where('kamar_id', $santri->kamar_id)->exists();
            if ($isKamar) return true;
        }

        return false;
    }
}
