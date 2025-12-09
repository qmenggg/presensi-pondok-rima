@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Approval Perubahan Rekap</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Setujui atau tolak perubahan yang diajukan</p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <form method="GET" action="{{ route('rekap.approval') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                        class="h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                </div>
                <button type="submit" class="h-10 rounded-lg bg-blue-600 px-4 text-white text-sm font-medium hover:bg-blue-700">
                    Filter
                </button>
                @if($pendingLogs->count() > 0)
                    <form action="{{ route('rekap.approval.approve-all') }}" method="POST" class="inline">
                        @csrf
                        @if($tanggal)
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                        @endif
                        <button type="submit" class="h-10 rounded-lg bg-green-600 px-4 text-white text-sm font-medium hover:bg-green-700"
                            onclick="return confirm('Setujui semua perubahan?')">
                            Setujui Semua
                        </button>
                    </form>
                @endif
            </form>
        </div>

        <!-- Pending List -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                    Menunggu Persetujuan ({{ $pendingLogs->total() }})
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Santri</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Kegiatan</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Lama</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Baru</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Diajukan Oleh</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($pendingLogs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $log->tanggal->format('d M Y') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $log->santri->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $log->subKegiatan->nama_sub_kegiatan ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        {{ $log->status_lama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full 
                                        {{ $log->status_baru == 'hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $log->status_baru == 'izin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $log->status_baru == 'sakit' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                        {{ $log->status_baru == 'alfa' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                        {{ $log->status_baru }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $log->diubahOleh->nama ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('rekap.approval.approve', $log->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="Setujui">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('rekap.approval.reject', $log->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Tolak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada perubahan yang menunggu persetujuan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pendingLogs->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $pendingLogs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
