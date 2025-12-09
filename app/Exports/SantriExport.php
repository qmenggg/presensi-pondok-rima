<?php

namespace App\Exports;

use App\Models\Santri;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SantriExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Santri::with(['user', 'kamar'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Username',
            'Jenis Kelamin',
            'Nama Kamar',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'Nama Wali',
            'QR Code',
            'Status',
        ];
    }

    /**
     * @param Santri $santri
     * @return array
     */
    public function map($santri): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $santri->user->nama ?? '-',
            $santri->user->username ?? '-',
            $santri->user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $santri->kamar->nama_kamar ?? '-',
            $santri->tempat_lahir,
            $santri->tanggal_lahir?->format('Y-m-d'),
            $santri->alamat,
            $santri->nama_wali,
            $santri->qr_code ?? '-',
            $santri->user->aktif ? 'Aktif' : 'Tidak Aktif',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }
}
