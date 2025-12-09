@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Hari Libur</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola hari libur pondok</p>
            </div>
            <a href="{{ route('libur.create') }}"
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
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar Libur</h3>
                <form method="GET" action="{{ route('libur.index') }}" class="flex gap-2">
                    <select name="jenis" onchange="this.form.submit()" class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Semua Jenis</option>
                        <option value="nasional" {{ request('jenis') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                        <option value="pondok" {{ request('jenis') == 'pondok' ? 'selected' : '' }}>Pondok</option>
                        <option value="khusus" {{ request('jenis') == 'khusus' ? 'selected' : '' }}>Khusus</option>
                    </select>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." 
                            class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-4 pr-10 text-sm text-gray-800 placeholder:text-gray-400 focus:border-blue-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-[200px]"/>
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
                @forelse ($liburs as $libur)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $libur->keterangan }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    @if($libur->rutin_mingguan)
                                        Setiap hari {{ ucfirst($libur->hari_rutin) }}
                                    @else
                                        {{ $libur->tanggal_mulai->format('d M Y') }} - {{ $libur->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full 
                                        {{ $libur->jenis == 'nasional' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 
                                           ($libur->jenis == 'pondok' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 
                                           'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400') }}">
                                        {{ ucfirst($libur->jenis) }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        {{ ucfirst($libur->untuk_jenis_santri) }}
                                    </span>
                                    @if($libur->rutin_mingguan)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            🔄 Rutin
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-1 ml-2">
                                <a href="{{ route('libur.edit', $libur->id) }}" class="p-2 text-blue-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('libur.destroy', $libur->id) }}" method="POST" onsubmit="return confirm('Hapus libur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <p>Tidak ada data</p>
                        <a href="{{ route('libur.create') }}" class="text-primary-600 text-sm mt-2 inline-block">+ Tambah Baru</a>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 w-12">No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Keterangan</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Periode</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Jenis</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Untuk</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300 w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($liburs as $index => $libur)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $liburs->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white/90">{{ $libur->keterangan }}</p>
                                    @if($libur->rutin_mingguan)
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400">🔄 Rutin Mingguan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    @if($libur->rutin_mingguan)
                                        Setiap {{ ucfirst($libur->hari_rutin) }}
                                    @else
                                        {{ $libur->tanggal_mulai->format('d M Y') }} - {{ $libur->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full 
                                        {{ $libur->jenis == 'nasional' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 
                                           ($libur->jenis == 'pondok' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 
                                           'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400') }}">
                                        {{ ucfirst($libur->jenis) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        {{ ucfirst($libur->untuk_jenis_santri) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('libur.edit', $libur->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:text-blue-400 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('libur.destroy', $libur->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
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

            @if($liburs->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $liburs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
