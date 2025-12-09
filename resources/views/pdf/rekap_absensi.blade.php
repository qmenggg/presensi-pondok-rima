@extends('pdf.layout')

@section('title', 'Laporan Absensi Harian')

@section('content')
    <div class="title">LAPORAN ABSENSI HARIAN</div>

    <table style="width: 100%; border: none; margin-bottom: 10px; font-size: 11pt; border-collapse: collapse;">
        <tr>
            <td style="border: none; width: 120px;">Hari, Tanggal</td>
            <td style="border: none; width: 10px;">:</td>
            <td style="border: none;">
                @php
                    $date = \Carbon\Carbon::parse($tanggal)->locale('id');
                    $dayName = $date->isoFormat('dddd');
                    if ($dayName == 'Minggu') {
                        $dayName = 'Ahad';
                    }
                    $formattedDate = $dayName . ', ' . $date->isoFormat('D MMMM Y');
                @endphp
                {{ $formattedDate }}
            </td>
        </tr>
        <tr>
            <td style="border: none;">Kegiatan</td>
            <td style="border: none;">:</td>
            <td style="border: none;">
                @if($subKegiatanList && $subKegiatanList->count() > 0)
                    {{ $subKegiatanList->pluck('nama_sub_kegiatan')->join(', ') }}
                @else
                    Semua Sub Kegiatan
                @endif
            </td>
        </tr>
        <tr>
            <td style="border: none;">Kamar</td>
            <td style="border: none;">:</td>
            <td style="border: none;">
                @if(is_array($kamar) && !empty($kamar))
                    {{ implode(', ', \App\Models\Kamar::whereIn('id', $kamar)->pluck('nama_kamar')->toArray()) }}
                @elseif($kamar)
                    {{ \App\Models\Kamar::find($kamar)?->nama_kamar }}
                @else
                    Semua Kamar
                @endif
            </td>
        </tr>
    </table>

    <style>
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        
        /* Footer Styling Baru */
        .footer-ttd {
            margin-top: 30px;
            width: 100%;
        }
        .ttd-box {
            float: right;
            width: 300px;
            text-align: center;
            font-family: 'Times New Roman', Times, serif;
        }
        .ttd-date {
            margin-bottom: 5px;
        }
        .ttd-mengetahui {
            margin-bottom: 60px; /* Spasi untuk tanda tangan */
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .ttd-jabatan {
            margin-top: 2px;
            font-weight: bold;
        }
    </style>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Santri</th>
                <th style="width: 20%;">Kamar</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 10%;">Jam</th>
                <th style="width: 15%;">Pencatat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $log->santri->user->nama ?? '-' }}</td>
                    <td>{{ $log->santri->kamar->nama_kamar ?? '-' }}</td>
                    <td class="text-center">
                        @if($log->status === 'hadir')
                            Hadir
                        @elseif($log->status === 'sakit')
                            Sakit
                        @elseif($log->status === 'izin')
                            Izin
                        @else
                            Alpa
                        @endif
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</td>
                    <td>{{ $log->pencatat->nama ?? 'Sistem' }}</td>
                </tr>
            @endforeach

            @if(count($logs) === 0)
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data absensi</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <div style="margin-top: 15px; font-size: 10pt;">
        <strong>Ringkasan:</strong>
        Hadir: {{ $logs->where('status', 'hadir')->count() }} | 
        Sakit: {{ $logs->where('status', 'sakit')->count() }} | 
        Izin: {{ $logs->where('status', 'izin')->count() }} | 
        Alpa: {{ $logs->where('status', 'alpa')->count() }}
    </div>

    {{-- Tanda Tangan Footer Baru --}}
    <div class="footer-ttd">
        <div class="ttd-box">
            <div class="ttd-date">
                @php
                    $now = \Carbon\Carbon::now()->locale('id');
                    $dayNow = $now->isoFormat('dddd');
                    if ($dayNow == 'Minggu') {
                        $dayNow = 'Ahad';
                    }
                    $dateNow = $dayNow . ', ' . $now->isoFormat('D MMMM Y');
                @endphp
                Kajen, {{ $dateNow }}
            </div>
            <div class="ttd-mengetahui">Mengetahui,</div>
            <div class="ttd-name">................................................</div>
            <div class="ttd-jabatan">Ketua Pondok</div>
        </div>
    </div>
@endsection
