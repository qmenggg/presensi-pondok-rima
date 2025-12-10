@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Laporan Bulanan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $startDate->locale('id')->isoFormat('MMMM Y') }}
                </p>
            </div>
        </div>

        <!-- Filters (Livewire Component) -->
        @livewire('laporan-filter', [
            'pageType' => 'bulanan',
            'bulan' => $bulan,
            'filters' => $filters,
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
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Rekap Per Santri</h3>
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($santriStats as $index => $stat)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $stat['santri']->nama ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $stat['santri']->kamar->nama_kamar ?? '-' }}</p>
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total: {{ $stat['total'] }}</span>
                        </div>
                        <div class="grid grid-cols-4 gap-2 mt-3">
                            <div class="text-center p-2 rounded-lg bg-green-50 dark:bg-green-900/20">
                                <p class="text-sm font-bold text-green-600 dark:text-green-400">{{ $stat['hadir'] }}</p>
                                <p class="text-xs text-green-600/80 dark:text-green-400/80">Hadir</p>
                            </div>
                            <div class="text-center p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $stat['izin'] }}</p>
                                <p class="text-xs text-blue-600/80 dark:text-blue-400/80">Izin</p>
                            </div>
                            <div class="text-center p-2 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                                <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">{{ $stat['sakit'] }}</p>
                                <p class="text-xs text-yellow-600/80 dark:text-yellow-400/80">Sakit</p>
                            </div>
                            <div class="text-center p-2 rounded-lg bg-red-50 dark:bg-red-900/20">
                                <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ $stat['alfa'] }}</p>
                                <p class="text-xs text-red-600/80 dark:text-red-400/80">Alfa</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>Tidak ada data</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Santri</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Kamar</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Hadir</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Izin</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Sakit</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Alfa</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Total</th>
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
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $stat['total'] }}</td>
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
    </div>
@endsection
