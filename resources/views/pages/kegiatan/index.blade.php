@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Kegiatan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola kegiatan pondok</p>
            </div>
            <a href="{{ route('kegiatan.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 transition-colors">
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
            <div class="flex flex-col gap-3 p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar Kegiatan</h3>
                </div>
                <form method="GET" action="{{ route('kegiatan.index') }}" class="flex flex-col sm:flex-row gap-2">
                    <select name="tapel_id" onchange="this.form.submit()" class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Semua Tapel</option>
                        @foreach ($tapels as $tapel)
                            <option value="{{ $tapel->id }}" {{ request('tapel_id') == $tapel->id ? 'selected' : '' }}>{{ $tapel->nama_tapel }}</option>
                        @endforeach
                    </select>
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan..." 
                            class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-4 pr-10 text-sm placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"/>
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
                @forelse ($kegiatans as $kegiatan)
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $kegiatan->nama_kegiatan }}</p>
                                @if($kegiatan->keterangan)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ $kegiatan->keterangan }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">{{ $kegiatan->tapel->nama_tapel ?? '-' }}</span>
                                    <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}" class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200">
                                        {{ $kegiatan->subKegiatans->count() }} sub
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 ml-2">
                                <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}" class="p-2 text-gray-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('kegiatan.edit', $kegiatan->id) }}" class="p-2 text-blue-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Hapus kegiatan?')">
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
                        <a href="{{ route('kegiatan.create') }}" class="text-primary-600 text-sm mt-2 inline-block">+ Tambah Baru</a>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 w-12">No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Kegiatan</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Tapel</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Sub</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300 w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($kegiatans as $index => $kegiatan)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $kegiatans->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-900 dark:text-white/90">{{ $kegiatan->nama_kegiatan }}</span>
                                    @if($kegiatan->keterangan)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($kegiatan->keterangan, 40) }}</p>
                                    @endif
                                </td>
                                <td class="hidden md:table-cell px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">{{ $kegiatan->tapel->nama_tapel ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}" class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200">
                                        {{ $kegiatan->subKegiatans->count() }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 dark:text-gray-400 rounded-lg" title="Sub Kegiatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('kegiatan.edit', $kegiatan->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:text-blue-400 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
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
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kegiatans->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $kegiatans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
