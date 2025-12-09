@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Izin Santri</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola izin dan sakit santri</p>
            </div>
            <a href="{{ route('izin.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah</span>
            </a>
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

        <!-- Table Card -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Search & Filter -->
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar Izin</h3>
                <form method="GET" action="{{ route('izin.index') }}" class="flex flex-wrap gap-2">
                    <select name="status" onchange="this.form.submit()" class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <select name="jenis" onchange="this.form.submit()" class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Semua Jenis</option>
                        <option value="sakit" {{ request('jenis') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ request('jenis') == 'izin' ? 'selected' : '' }}>Izin</option>
                    </select>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." 
                            class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-4 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:border-blue-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-[180px]"/>
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($izins as $izin)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $izin->santri->nama ?? '-' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $izin->tanggal_mulai->format('d M Y') }} - {{ $izin->tanggal_selesai->format('d M Y') }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full 
                                        {{ $izin->status == 'sakit' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ ucfirst($izin->status) }}
                                    </span>
                                    @if($izin->alasan_reject)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            Ditolak
                                        </span>
                                    @elseif($izin->disetujui_oleh)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            Pending
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1 ml-2">
                                @if(!$izin->disetujui_oleh)
                                    <form action="{{ route('izin.approve', $izin->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-green-600 rounded-lg" title="Setujui">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('izin.edit', $izin->id) }}" class="p-2 text-blue-600 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('izin.destroy', $izin->id) }}" method="POST" onsubmit="return confirm('Hapus izin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>Tidak ada data</p>
                        <a href="{{ route('izin.create') }}" class="text-primary-600 text-sm mt-2 inline-block">+ Tambah Baru</a>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 w-12">No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Santri</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Jenis</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Periode</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300 w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($izins as $index => $izin)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $izins->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white/90">{{ $izin->santri->nama ?? '-' }}</p>
                                    @if($izin->keterangan)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $izin->keterangan }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full 
                                        {{ $izin->status == 'sakit' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                        {{ ucfirst($izin->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $izin->tanggal_mulai->format('d M Y') }} - {{ $izin->tanggal_selesai->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($izin->alasan_reject)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            Ditolak
                                        </span>
                                    @elseif($izin->disetujui_oleh)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        @if(!$izin->disetujui_oleh)
                                            <form action="{{ route('izin.approve', $izin->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-green-600 hover:bg-green-50 dark:text-green-400 rounded-lg" title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" onclick="showRejectModal({{ $izin->id }})" class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 rounded-lg" title="Tolak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('izin.edit', $izin->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:text-blue-400 rounded-lg" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('izin.destroy', $izin->id) }}" method="POST" onsubmit="return confirm('Hapus?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 rounded-lg" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($izins->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $izins->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/50" onclick="hideRejectModal()"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tolak Izin</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="alasan_reject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="alasan_reject" name="alasan_reject" rows="3" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"
                            placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="hideRejectModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showRejectModal(izinId) {
            document.getElementById('rejectForm').action = '/izin/' + izinId + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        
        function hideRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection
