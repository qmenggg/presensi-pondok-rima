<?php

namespace App\Exports;

use App\Models\Kamar;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class SantriTemplateExport implements WithMultipleSheets
{
    protected ?string $jenisKelamin;
    protected array $selectedKamarNames;

    public function __construct(?string $jenisKelamin = null, array $selectedKamarNames = [])
    {
        $this->jenisKelamin = $jenisKelamin;
        $this->selectedKamarNames = $selectedKamarNames;
    }

    public function sheets(): array
    {
        return [
            'Data Santri' => new SantriTemplateDataSheet($this->jenisKelamin, $this->selectedKamarNames),
            'Petunjuk' => new SantriTemplatePetunjukSheet(),
            'Referensi Kamar' => new SantriTemplateKamarSheet($this->selectedKamarNames),
        ];
    }
}

class SantriTemplateDataSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected ?string $jenisKelamin;
    protected array $kamarList;

    public function __construct(?string $jenisKelamin = null, array $selectedKamarNames = [])
    {
        $this->jenisKelamin = $jenisKelamin;
        $this->kamarList = $selectedKamarNames;
    }

    public function headings(): array
    {
        return [
            'nama_lengkap',
            'jenis_kelamin',
            'nama_kamar',
            'tempat_lahir',
            'tanggal_lahir',
            'alamat',
            'nama_wali',
        ];
    }

    public function array(): array
    {
        // Return empty array - user will fill in the data
        return [];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Number of rows to apply validation
                $maxRows = 100;

                // === JENIS KELAMIN DROPDOWN (Column B) ===
                $jkOptions = $this->jenisKelamin 
                    ? ($this->jenisKelamin === 'L' ? 'L' : 'P')
                    : 'L,P';
                
                for ($row = 2; $row <= $maxRows + 1; $row++) {
                    $validation = $sheet->getCell("B{$row}")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Error');
                    $validation->setError('Pilih dari daftar!');
                    $validation->setPromptTitle('Jenis Kelamin');
                    $validation->setPrompt('Pilih L (Laki-laki) atau P (Perempuan)');
                    $validation->setFormula1('"' . $jkOptions . '"');
                }

                // === NAMA KAMAR DROPDOWN (Column C) ===
                if (!empty($this->kamarList)) {
                    $kamarOptions = implode(',', $this->kamarList);
                    
                    for ($row = 2; $row <= $maxRows + 1; $row++) {
                        $validation = $sheet->getCell("C{$row}")->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_STOP);
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Error');
                        $validation->setError('Pilih kamar dari daftar!');
                        $validation->setPromptTitle('Nama Kamar');
                        $validation->setPrompt('Pilih kamar yang tersedia');
                        $validation->setFormula1('"' . $kamarOptions . '"');
                    }
                }

                // === TANGGAL LAHIR FORMAT (Column E) ===
                for ($row = 2; $row <= $maxRows + 1; $row++) {
                    $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('YYYY-MM-DD');
                }

                // Info
                $kamarCount = count($this->kamarList);
                $sheet->getCell('I1')->setValue("📝 Dropdown: {$kamarCount} kamar tersedia");
                $sheet->getStyle('I1')->getFont()->setBold(true)->getColor()->setRGB('059669');
                $sheet->getColumnDimension('I')->setWidth(35);
            },
        ];
    }
}

class SantriTemplatePetunjukSheet implements FromArray, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT SANTRI'],
            [''],
            ['Kolom', 'Keterangan', 'Contoh', 'Aturan'],
            ['nama_lengkap', 'Nama lengkap santri', 'Ahmad Fauzi', 'Wajib diisi, ketik manual'],
            ['jenis_kelamin', 'L = Laki-laki, P = Perempuan', 'L', 'Wajib, PILIH DARI DROPDOWN'],
            ['nama_kamar', 'Nama kamar', 'Kamar Putra 1', 'Wajib, PILIH DARI DROPDOWN'],
            ['tempat_lahir', 'Kota/kabupaten tempat lahir', 'Jakarta', 'Wajib diisi'],
            ['tanggal_lahir', 'Format YYYY-MM-DD', '2005-03-15', 'Wajib diisi'],
            ['alamat', 'Alamat lengkap', 'Jl. Raya No. 10', 'Wajib diisi'],
            ['nama_wali', 'Nama orang tua/wali', 'H. Abdul Rahman', 'Wajib diisi'],
            [''],
            ['CATATAN PENTING:'],
            ['1. Kolom jenis_kelamin dan nama_kamar adalah DROPDOWN - pilih dari daftar'],
            ['2. Username akan digenerate otomatis dari nama depan + nama kamar'],
            ['3. Password default: password123'],
            ['4. QR Code akan digenerate otomatis'],
            ['5. Pastikan nama kamar sesuai dengan jenis kelamin santri'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
            ],
        ]);

        $sheet->getStyle('A5:D5')->getFont()->setBold(true)->getColor()->setRGB('059669');
        $sheet->getStyle('A6:D6')->getFont()->setBold(true)->getColor()->setRGB('059669');
        $sheet->getStyle('A12')->getFont()->setBold(true)->getColor()->setRGB('DC2626');
        $sheet->getStyle('A13')->getFont()->setBold(true)->getColor()->setRGB('059669');

        return [];
    }
}

class SantriTemplateKamarSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected array $selectedKamarNames;

    public function __construct(array $selectedKamarNames = [])
    {
        $this->selectedKamarNames = $selectedKamarNames;
    }

    public function headings(): array
    {
        return ['Nama Kamar', 'Jenis'];
    }

    public function array(): array
    {
        if (empty($this->selectedKamarNames)) {
            return [];
        }

        return Kamar::whereIn('nama_kamar', $this->selectedKamarNames)
            ->orderBy('jenis')
            ->orderBy('nama_kamar')
            ->get()
            ->map(function ($kamar) {
                return [
                    $kamar->nama_kamar,
                    $kamar->jenis === 'putra' ? 'Putra (L)' : 'Putri (P)',
                ];
            })->toArray();
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981'],
            ],
        ]);

        return [];
    }
}
