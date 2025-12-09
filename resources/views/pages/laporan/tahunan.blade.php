@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Laporan Tahunan</h2>
                @if(isset($tapel))
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $tapel->nama_tapel }} ({{ $tapel->tanggal_mulai->format('d M Y') }} - {{ $tapel->tanggal_selesai->format('d M Y') }})
                    </p>
                @endif
            </div>
        </div>

        @if(isset($error))
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
                {{ $error }}
            </div>
        @else
            <!-- Filters (Livewire Component) -->
            @livewire('laporan-filter', [
                'pageType' => 'tahunan',
                'tapelId' => $tapel->id,
                'filters' => $filters,
                'tapels' => $filterOptions['tapels'] ?? [],
            ])

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
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Ranking Kehadiran</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Rank</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Santri</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Kamar</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Hadir</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Izin</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Sakit</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Alfa</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($santriStats as $index => $stat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $stat['santri']->nama ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $stat['santri']->kamar->nama_kamar ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center text-green-600 dark:text-green-400 font-medium">{{ $stat['hadir'] }}</td>
                                    <td class="px-4 py-3 text-center text-blue-600 dark:text-blue-400">{{ $stat['izin'] }}</td>
                                    <td class="px-4 py-3 text-center text-yellow-600 dark:text-yellow-400">{{ $stat['sakit'] }}</td>
                                    <td class="px-4 py-3 text-center text-red-600 dark:text-red-400">{{ $stat['alfa'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full 
                                            {{ $stat['persentase'] >= 90 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                            {{ $stat['persentase'] >= 75 && $stat['persentase'] < 90 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                            {{ $stat['persentase'] < 75 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                            {{ $stat['persentase'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
