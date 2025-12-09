@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Laporan Harian</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $tanggalCarbon->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <form method="GET" action="{{ route('laporan.harian') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                            class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                        <select name="status" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            <option value="">Semua</option>
                            <option value="hadir" {{ $filters['status'] == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ $filters['status'] == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ $filters['status'] == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alfa" {{ $filters['status'] == 'alfa' ? 'selected' : '' }}>Alfa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jenis Santri</label>
                        <select name="jenis_santri" class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            <option value="">Semua</option>
                            <option value="putra" {{ $filters['jenis_santri'] == 'putra' ? 'selected' : '' }}>Putra</option>
                            <option value="putri" {{ $filters['jenis_santri'] == 'putri' ? 'selected' : '' }}>Putri</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full h-10 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                            Filter
                        </button>
                    </div>
                </div>

                <!-- Kamar Checkboxes -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Kamar:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($filterOptions['kamars'] as $kamar)
                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">
                                <input type="checkbox" name="kamar_ids[]" value="{{ $kamar->id }}" 
                                    {{ in_array($kamar->id, $filters['kamar_ids'] ?? []) ? 'checked' : '' }} class="rounded">
                                <span class="text-gray-700 dark:text-gray-300">{{ $kamar->nama_kamar }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Sub Kegiatan Checkboxes -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Sub Kegiatan:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($filterOptions['subKegiatans'] as $subKegiatan)
                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 text-sm">
                                <input type="checkbox" name="sub_kegiatan_ids[]" value="{{ $subKegiatan->id }}" 
                                    {{ in_array($subKegiatan->id, $filters['sub_kegiatan_ids'] ?? []) ? 'checked' : '' }} class="rounded">
                                <span class="text-gray-700 dark:text-gray-300">{{ $subKegiatan->nama_sub_kegiatan }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
            </div>
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $stats['hadir'] }}</p>
                <p class="text-xs text-green-600 dark:text-green-500">Hadir</p>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $stats['izin'] }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-500">Izin</p>
            </div>
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ $stats['sakit'] }}</p>
                <p class="text-xs text-yellow-600 dark:text-yellow-500">Sakit</p>
            </div>
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $stats['alfa'] }}</p>
                <p class="text-xs text-red-600 dark:text-red-500">Alfa</p>
            </div>
        </div>

        <!-- Data Table -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Detail Absensi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Santri</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Kamar</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Kegiatan</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($absensis as $index => $absensi)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $absensi->santri->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $absensi->santri->kamar->nama_kamar ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $absensi->subKegiatan->nama_sub_kegiatan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full 
                                        {{ $absensi->status == 'hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $absensi->status == 'izin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $absensi->status == 'sakit' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                        {{ $absensi->status == 'alfa' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                        {{ ucfirst($absensi->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
