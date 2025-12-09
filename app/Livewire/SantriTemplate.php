<?php

namespace App\Livewire;

use App\Models\Kamar;
use App\Exports\SantriTemplateExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class SantriTemplate extends Component
{
    public string $jenisKelamin = '';
    public array $allKamars = [];
    public array $selectedKamars = [];

    public function mount(): void
    {
        $this->loadKamars();
    }

    public function updatedJenisKelamin(): void
    {
        $this->loadKamars();
        // Reset selected kamars when filter changes
        $this->selectedKamars = [];
    }

    public function loadKamars(): void
    {
        $query = Kamar::orderBy('nama_kamar');
        
        if ($this->jenisKelamin === 'L') {
            $query->where('jenis', 'putra');
        } elseif ($this->jenisKelamin === 'P') {
            $query->where('jenis', 'putri');
        }
        
        $this->allKamars = $query->get()->toArray();
    }

    public function selectAll(): void
    {
        $this->selectedKamars = collect($this->allKamars)->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedKamars = [];
    }

    public function downloadTemplate()
    {
        // Get selected kamar names
        $selectedKamarNames = [];
        if (!empty($this->selectedKamars)) {
            $selectedKamarNames = Kamar::whereIn('id', $this->selectedKamars)
                ->pluck('nama_kamar')
                ->toArray();
        }

        $jenis = $this->jenisKelamin ?: null;
        $filename = 'template-import-santri';
        
        if ($jenis === 'L') {
            $filename .= '-putra';
        } elseif ($jenis === 'P') {
            $filename .= '-putri';
        }
        
        $filename .= '.xlsx';

        return Excel::download(new SantriTemplateExport($jenis, $selectedKamarNames), $filename);
    }

    public function render()
    {
        return view('livewire.santri-template');
    }
}
