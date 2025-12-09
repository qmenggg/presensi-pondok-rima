<?php

namespace App\Livewire;

use App\Models\Kamar;
use App\Models\SubKegiatan;
use Livewire\Component;

class LaporanFilter extends Component
{
    // Filter values
    public string $jenisSantri = '';
    public array $selectedKamars = [];
    public array $selectedSubKegiatans = [];
    public string $status = '';
    
    // For different page types
    public string $pageType = 'harian'; // harian, bulanan, tahunan
    public string $tanggal = '';
    public string $bulan = '';
    public ?int $tapelId = null;
    
    // For tahunan page
    public $tapels = [];
    
    // Initial values from request
    public array $initialFilters = [];

    public function mount(
        string $pageType = 'harian',
        string $tanggal = '',
        string $bulan = '',
        ?int $tapelId = null,
        array $filters = [],
        $tapels = []
    ): void {
        $this->pageType = $pageType;
        $this->tanggal = $tanggal;
        $this->bulan = $bulan;
        $this->tapelId = $tapelId;
        $this->tapels = $tapels;
        $this->initialFilters = $filters;
        
        // Set initial values from filters
        $this->jenisSantri = $filters['jenis_santri'] ?? '';
        $this->selectedKamars = $filters['kamar_ids'] ?? [];
        $this->selectedSubKegiatans = $filters['sub_kegiatan_ids'] ?? [];
        $this->status = $filters['status'] ?? '';
    }

    /**
     * When jenis santri changes, reset kamar and sub kegiatan selections
     */
    public function updatedJenisSantri($value): void
    {
        $this->selectedKamars = [];
        $this->selectedSubKegiatans = [];
    }

    /**
     * Get filtered kamars based on jenis santri
     */
    public function getFilteredKamarsProperty()
    {
        $query = Kamar::orderBy('nama_kamar');
        
        if ($this->jenisSantri === 'putra') {
            $query->where('jenis', 'putra');
        } elseif ($this->jenisSantri === 'putri') {
            $query->where('jenis', 'putri');
        }
        
        return $query->get();
    }

    /**
     * Get filtered sub kegiatans based on jenis santri
     */
    public function getFilteredSubKegiatansProperty()
    {
        $query = SubKegiatan::with('kegiatan')->orderBy('nama_sub_kegiatan');
        
        if ($this->jenisSantri === 'putra') {
            $query->whereIn('untuk_jenis_santri', ['putra', 'campur']);
        } elseif ($this->jenisSantri === 'putri') {
            $query->whereIn('untuk_jenis_santri', ['putri', 'campur']);
        }
        
        return $query->get();
    }

    /**
     * Get the route name based on page type
     */
    public function getRouteNameProperty(): string
    {
        return match($this->pageType) {
            'bulanan' => 'laporan.bulanan',
            'tahunan' => 'laporan.tahunan',
            default => 'laporan.harian',
        };
    }

    public function render()
    {
        return view('livewire.laporan-filter');
    }
}
