@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400">
                        <li><a href="{{ route('kegiatan.index') }}" class="hover:text-primary-600">Kegiatan</a></li>
                        <li><span class="mx-1">/</span></li>
                        <li class="font-medium text-gray-700 dark:text-white truncate max-w-[100px] sm:max-w-none">{{ $kegiatan->nama_kegiatan }}</li>
                    </ol>
                </nav>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Sub Kegiatan</h2>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('sub-kegiatan.create', $kegiatan->id) }}"
                   class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="hidden sm:inline">Tambah</span>
                </a>
                <a href="{{ route('kegiatan.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-lg bg-green-50 p-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table Card -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Search -->
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar Sub Kegiatan</h3>
                <form method="GET" action="{{ route('sub-kegiatan.index', $kegiatan->id) }}" class="relative w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." 
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-4 pr-10 text-sm placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-[200px]"/>
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($subKegiatans as $sub)
                    @php
                        $haris = $sub->subKegiatanHaris->pluck('hari')->map(fn($h) => ucfirst($h))->toArray();
                        $hariText = count($haris) === 7 ? 'Setiap Hari' : implode(', ', $haris);
                        $waktu = '';
                        if ($sub->waktu_mulai && $sub->waktu_selesai) {
                            $waktu = substr($sub->waktu_mulai, 0, 5) . '-' . substr($sub->waktu_selesai, 0, 5);
                        }
                        $kamarCount = $sub->subKegiatanKamars->count();
                        $santriCount = \App\Http\Controllers\SubKegiatanController::getUniqueSantriCount($sub);
                        $untukColors = [
                            'putra' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'putri' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
                            'campur' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                        ];
                    @endphp
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $sub->nama_sub_kegiatan }}</p>
                                @if($sub->lokasi)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $sub->lokasi }}</p>
                                @endif
                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ Str::limit($hariText, 15) }}</span>
                                    @if($waktu)
                                        <span class="text-[10px] text-gray-500">{{ $waktu }}</span>
                                    @endif
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded-full {{ $untukColors[$sub->untuk_jenis_santri] ?? 'bg-gray-100' }}">{{ ucfirst($sub->untuk_jenis_santri) }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ $kamarCount }} kamar</span>
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">{{ $santriCount }} santri</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <a href="{{ route('sub-kegiatan.assign', [$kegiatan->id, $sub->id]) }}" class="p-1.5 text-green-600 bg-green-50 rounded-lg dark:bg-green-900/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('sub-kegiatan.edit', [$kegiatan->id, $sub->id]) }}" class="p-1.5 text-blue-600 bg-blue-50 rounded-lg dark:bg-blue-900/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('sub-kegiatan.destroy', [$kegiatan->id, $sub->id]) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 bg-red-50 rounded-lg dark:bg-red-900/20">
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
                        <p>Tidak ada sub kegiatan</p>
                        <a href="{{ route('sub-kegiatan.create', $kegiatan->id) }}" class="text-primary-600 text-sm mt-2 inline-block">+ Tambah Baru</a>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 w-10">No</th>
                            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Nama</th>
                            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Jadwal</th>
                            <th class="hidden lg:table-cell px-3 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Untuk</th>
                            <th class="hidden xl:table-cell px-3 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Guru</th>
                            <th class="px-3 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Peserta</th>
                            <th class="px-3 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300 w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($subKegiatans as $index => $sub)
                            @php
                                $haris = $sub->subKegiatanHaris->pluck('hari')->map(fn($h) => ucfirst($h))->toArray();
                                $hariText = count($haris) === 7 ? 'Setiap Hari' : implode(', ', $haris);
                                $waktu = '';
                                if ($sub->waktu_mulai && $sub->waktu_selesai) {
                                    $waktu = substr($sub->waktu_mulai, 0, 5) . ' - ' . substr($sub->waktu_selesai, 0, 5);
                                }
                                $kamarCount = $sub->subKegiatanKamars->count();
                                $santriCount = \App\Http\Controllers\SubKegiatanController::getUniqueSantriCount($sub);
                                $untukColors = [
                                    'putra' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'putri' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
                                    'campur' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $subKegiatans->firstItem() + $index }}</td>
                                <td class="px-3 py-3">
                                    <span class="font-medium text-gray-900 dark:text-white/90">{{ $sub->nama_sub_kegiatan }}</span>
                                    @if($sub->lokasi)
                                        <p class="text-xs text-gray-500 mt-0.5"><svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> {{ $sub->lokasi }}</p>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ $hariText }}</span>
                                    @if($waktu)
                                        <span class="text-xs text-gray-500 ml-1">{{ $waktu }}</span>
                                    @endif
                                </td>
                                <td class="hidden lg:table-cell px-3 py-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $untukColors[$sub->untuk_jenis_santri] ?? 'bg-gray-100' }}">{{ ucfirst($sub->untuk_jenis_santri) }}</span>
                                </td>
                                <td class="hidden xl:table-cell px-3 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $sub->guruPenanggungJawab->nama ?? '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex gap-1">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ $kamarCount }}</span>
                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">{{ $santriCount }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('sub-kegiatan.assign', [$kegiatan->id, $sub->id]) }}" class="p-1.5 text-green-600 hover:bg-green-50 dark:text-green-400 rounded-lg" title="Assign">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('sub-kegiatan.edit', [$kegiatan->id, $sub->id]) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:text-blue-400 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('sub-kegiatan.destroy', [$kegiatan->id, $sub->id]) }}" method="POST" onsubmit="return confirm('Hapus?')">
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
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subKegiatans->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $subKegiatans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
