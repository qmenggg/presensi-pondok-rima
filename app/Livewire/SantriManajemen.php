<?php

namespace App\Livewire;

use App\Models\Kamar;
use App\Models\Santri;
use Livewire\Component;
use Livewire\WithPagination;

class SantriManajemen extends Component
{
    use WithPagination;

    public $filterKamar = '';
    public $filterStatus = '';
    public $targetKamar = '';
    public $selectedSantris = [];
    public $selectAll = false;
    public $search = '';
    
    protected $queryString = ['filterKamar', 'filterStatus', 'search'];

    public function mount()
    {
        $this->filterKamar = request('kamar_id', '');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedSantris = $this->getSantrisQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedSantris = [];
        }
    }

    public function updatedFilterKamar()
    {
        $this->resetPage();
        $this->selectedSantris = [];
        $this->selectAll = false;
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
        $this->selectedSantris = [];
        $this->selectAll = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleAktif($santriId)
    {
        $santri = Santri::with('user')->find($santriId);
        if ($santri && $santri->user) {
            $santri->user->aktif = !$santri->user->aktif;
            $santri->user->save();
            
            $status = $santri->user->aktif ? 'diaktifkan' : 'dinonaktifkan';
            session()->flash('message', "Santri {$santri->user->nama} berhasil {$status}.");
        }
    }

    public function pindahKamarSingle($santriId, $kamarId)
    {
        if (empty($kamarId)) return;
        
        $santri = Santri::with('user')->find($santriId);
        $kamar = Kamar::find($kamarId);
        
        if ($santri && $kamar) {
            // Validate gender match
            $jenisKelamin = $santri->user->jenis_kelamin;
            $jenisKamar = $kamar->jenis;
            
            if (($jenisKelamin === 'L' && $jenisKamar !== 'putra') || 
                ($jenisKelamin === 'P' && $jenisKamar !== 'putri')) {
                session()->flash('error', "Tidak dapat memindahkan santri {$santri->user->nama} ke kamar {$kamar->nama_kamar}. Jenis kelamin tidak sesuai.");
                return;
            }
            
            $oldKamar = $santri->kamar->nama_kamar ?? 'Tidak ada';
            $santri->kamar_id = $kamarId;
            $santri->save();
            
            session()->flash('message', "Santri {$santri->user->nama} berhasil dipindahkan dari {$oldKamar} ke {$kamar->nama_kamar}.");
        }
    }

    public function pindahKamarBulk()
    {
        if (empty($this->targetKamar) || empty($this->selectedSantris)) {
            session()->flash('error', 'Pilih santri dan kamar tujuan terlebih dahulu.');
            return;
        }
        
        $kamar = Kamar::find($this->targetKamar);
        if (!$kamar) {
            session()->flash('error', 'Kamar tujuan tidak ditemukan.');
            return;
        }
        
        $santris = Santri::with('user')->whereIn('id', $this->selectedSantris)->get();
        $successCount = 0;
        $failedCount = 0;
        
        foreach ($santris as $santri) {
            $jenisKelamin = $santri->user->jenis_kelamin;
            $jenisKamar = $kamar->jenis;
            
            // Skip if gender doesn't match
            if (($jenisKelamin === 'L' && $jenisKamar !== 'putra') || 
                ($jenisKelamin === 'P' && $jenisKamar !== 'putri')) {
                $failedCount++;
                continue;
            }
            
            $santri->kamar_id = $this->targetKamar;
            $santri->save();
            $successCount++;
        }
        
        $this->selectedSantris = [];
        $this->selectAll = false;
        $this->targetKamar = '';
        
        if ($successCount > 0) {
            session()->flash('message', "{$successCount} santri berhasil dipindahkan ke {$kamar->nama_kamar}.");
        }
        if ($failedCount > 0) {
            session()->flash('warning', "{$failedCount} santri gagal dipindahkan karena jenis kelamin tidak sesuai.");
        }
    }

    private function getSantrisQuery()
    {
        $query = Santri::with(['user', 'kamar']);
        
        if ($this->filterKamar) {
            $query->where('kamar_id', $this->filterKamar);
        }
        
        if ($this->filterStatus !== '') {
            $query->whereHas('user', function($q) {
                $q->where('aktif', $this->filterStatus === 'aktif');
            });
        }
        
        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy('kamar_id')->orderBy('id');
    }

    public function render()
    {
        $kamars = Kamar::orderBy('jenis')->orderBy('nama_kamar')->get();
        $santris = $this->getSantrisQuery()->paginate(20);
        
        return view('livewire.santri-manajemen', [
            'kamars' => $kamars,
            'santris' => $santris,
        ]);
    }
}
