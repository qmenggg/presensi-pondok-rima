@extends('pdf.layout')

@section('title', 'Laporan Bulanan')

@section('content')
    <style>
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header-subtitle {
            text-align: left;
            font-size: 12pt;
            margin-bottom: 15px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* Footer TTD */
        .ttd-wrapper {
            margin-top: 30px;
            width: 100%;
            overflow: hidden;
        }
        .ttd-box {
            float: right;
            width: 300px;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }
        .ttd-space {
            margin-top: 60px;
        }
    </style>

    <div class="header-title">LAPORAN REKAPITULASI PRESENSI BULANAN</div>
    <div class="header-subtitle">
        Bulan: {{ \Carbon\Carbon::parse($bulan)->locale('id')->isoFormat('MMMM Y') }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 30%">Nama Santri</th>
                <th style="width: 15%">Kamar</th>
                <th style="width: 8%">H</th>
                <th style="width: 8%">I</th>
                <th style="width: 8%">S</th>
                <th style="width: 8%">A</th>
                <th style="width: 8%">Total</th>
                <th style="width: 10%">Hadir %</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($santriStats as $index => $stat)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $stat['santri']->user->nama ?? '-' }}</td>
                    <td>{{ $stat['santri']->kamar->nama_kamar ?? '-' }}</td>
                    <td class="text-center">{{ $stat['hadir'] }}</td>
                    <td class="text-center">{{ $stat['izin'] }}</td>
                    <td class="text-center">{{ $stat['sakit'] }}</td>
                    <td class="text-center">{{ $stat['alfa'] }}</td>
                    <td class="text-center">{{ $stat['total'] }}</td>
                    <td class="text-center">{{ $stat['persentase'] }}%</td>
                </tr>
            @endforeach
            @if(count($santriStats) === 0)
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">
                        Tidak ada data presensi pada bulan ini.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd-wrapper">
        <div class="ttd-box">
            @php
                $now = \Carbon\Carbon::now()->locale('id');
                $dayName = $now->isoFormat('dddd');
                if ($dayName == 'Minggu') $dayName = 'Ahad';
                $dateString = $dayName . ', ' . $now->isoFormat('D MMMM Y');
            @endphp
            <p>Kajen, {{ $dateString }}</p>
            <p class="jabatan">Pengasuh</p>
            <div class="ttd-space"></div>
            <p class="nama">(_______________________)</p>
        </div>
    </div>
@endsection
