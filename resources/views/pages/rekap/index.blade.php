@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Rekap Presensi</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $subKegiatan->nama_sub_kegiatan }} - {{ $tanggalCarbon->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
            <a href="{{ route('absensi.index', ['tanggal' => $tanggal]) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        {{-- Libur Banner (Global) --}}
        @if($libur)
            <div class="rounded-lg bg-yellow-50 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">

                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Hari Libur: {{ $libur->keterangan }}</p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">Jenis: {{ ucfirst($libur->jenis) }} | Untuk: {{ ucfirst($libur->untuk_jenis_santri) }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        {{-- Libur Banner (Per Sub Kegiatan) --}}
        @if(isset($liburKegiatan) && $liburKegiatan)
            <div class="rounded-lg bg-orange-50 border-l-4 border-orange-400 p-4 dark:bg-orange-900/20">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-orange-800 dark:text-orange-300">Kegiatan Diliburkan: {{ $liburKegiatan->keterangan ?? 'Libur' }}</p>
                        <p class="text-xs text-orange-700 dark:text-orange-400 mt-0.5">Edit rekap tidak tersedia saat kegiatan libur.</p>
                    </div>
                </div>
            </div>
        @endif

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

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Peserta</p>
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

        <!-- Rekap Table -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-3">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar Peserta</h3>
                    <button type="button" onclick="toggleFilterPanel()" 
                        class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                        Filter Kamar
                    </button>
                </div>
                <!-- Filter Panel -->
                <div id="filterPanel" class="hidden border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Pilih kamar:</p>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $kamars = collect($rekapData)->pluck('santri.kamar')->unique('id')->filter()->sortBy('nama_kamar');
                        @endphp
                        @foreach($kamars as $kamar)
                            <label class="inline-flex items-center gap-1.5 px-2 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 text-xs sm:text-sm">
                                <input type="checkbox" class="kamar-checkbox rounded" value="{{ $kamar->nama_kamar }}" checked onchange="filterByKamar()">
                                <span class="text-gray-700 dark:text-gray-300">{{ $kamar->nama_kamar }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-2 flex gap-2">
                        <button type="button" onclick="selectAllKamar()" class="text-xs text-blue-600 hover:underline">Pilih Semua</button>
                        <button type="button" onclick="deselectAllKamar()" class="text-xs text-red-600 hover:underline">Hapus Semua</button>
                    </div>
                </div>
            </div>

            <form action="{{ route('rekap.finalize', [$subKegiatan->id, $tanggal]) }}" method="POST">
                @csrf
                
                <!-- Mobile Card View -->
                <div class="block sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($rekapData as $index => $data)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50" data-kamar="{{ $data['santri']->kamar->nama_kamar ?? '' }}">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">
                                        {{ $data['santri']->nama }}
                                        @if(isset($data['pending']) && $data['pending'])
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                Pending
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $data['santri']->kamar->nama_kamar ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($canEdit)
                                    <select name="santri_status[{{ $data['santri']->id }}]" 
                                        class="flex-1 rounded-lg border text-sm px-3 py-2
                                            {{ $data['status'] == 'hadir' ? 'border-green-300 bg-green-50 text-green-700 dark:border-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                            {{ $data['status'] == 'izin' ? 'border-blue-300 bg-blue-50 text-blue-700 dark:border-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                            {{ $data['status'] == 'sakit' ? 'border-yellow-300 bg-yellow-50 text-yellow-700 dark:border-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                            {{ $data['status'] == 'alfa' ? 'border-red-300 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                        ">
                                        <option value="hadir" {{ $data['status'] == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                        <option value="izin" {{ $data['status'] == 'izin' ? 'selected' : '' }}>Izin</option>
                                        <option value="sakit" {{ $data['status'] == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                        <option value="alfa" {{ $data['status'] == 'alfa' ? 'selected' : '' }}>Alfa</option>
                                    </select>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full 
                                        {{ $data['status'] == 'hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $data['status'] == 'izin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $data['status'] == 'sakit' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                        {{ $data['status'] == 'alfa' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    ">
                                        {{ ucfirst($data['status']) }}
                                    </span>
                                @endif
                                @if($data['keterangan'])
                                    <span class="text-xs text-gray-500">{{ $data['keterangan'] }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">Tidak ada peserta</div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 w-12">No</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Nama</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 hidden md:table-cell">Kamar</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300 w-40">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 hidden lg:table-cell">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($rekapData as $index => $data)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50" data-kamar="{{ $data['santri']->kamar->nama_kamar ?? '' }}">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">
                                        {{ $data['santri']->nama }}
                                        @if(isset($data['pending']) && $data['pending'])
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">{{ $data['santri']->kamar->nama_kamar ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($canEdit)
                                            <select name="santri_status[{{ $data['santri']->id }}]" 
                                                class="w-full rounded-lg border text-sm px-3 py-2
                                                    {{ $data['status'] == 'hadir' ? 'border-green-300 bg-green-50 text-green-700 dark:border-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                                    {{ $data['status'] == 'izin' ? 'border-blue-300 bg-blue-50 text-blue-700 dark:border-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                                    {{ $data['status'] == 'sakit' ? 'border-yellow-300 bg-yellow-50 text-yellow-700 dark:border-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                    {{ $data['status'] == 'alfa' ? 'border-red-300 bg-red-50 text-red-700 dark:border-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                ">
                                                <option value="hadir" {{ $data['status'] == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="izin" {{ $data['status'] == 'izin' ? 'selected' : '' }}>Izin</option>
                                                <option value="sakit" {{ $data['status'] == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="alfa" {{ $data['status'] == 'alfa' ? 'selected' : '' }}>Alfa</option>
                                            </select>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full 
                                                {{ $data['status'] == 'hadir' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                                {{ $data['status'] == 'izin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                                {{ $data['status'] == 'sakit' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                {{ $data['status'] == 'alfa' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                            ">
                                                {{ ucfirst($data['status']) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 hidden lg:table-cell">
                                        {{ $data['keterangan'] ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada peserta</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($canEdit)
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            @if(auth()->user()->role === 'admin')
                                Simpan Rekap
                            @else
                                Ajukan Perubahan
                            @endif
                        </button>
                    </div>
                @else
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                            Mode Lihat Saja - Anda tidak memiliki akses untuk mengedit rekap
                        </p>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function toggleFilterPanel() {
            const panel = document.getElementById('filterPanel');
            panel.classList.toggle('hidden');
        }

        function filterByKamar() {
            const checkboxes = document.querySelectorAll('.kamar-checkbox:checked');
            const selectedKamars = Array.from(checkboxes).map(cb => cb.value.toLowerCase());
            
            // Filter table rows (desktop)
            const rows = document.querySelectorAll('tbody tr[data-kamar]');
            rows.forEach(row => {
                const kamar = row.getAttribute('data-kamar').toLowerCase();
                if (selectedKamars.length === 0 || selectedKamars.includes(kamar)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter mobile cards
            const cards = document.querySelectorAll('.block.sm\\:hidden > div[data-kamar]');
            cards.forEach(card => {
                const kamar = card.getAttribute('data-kamar').toLowerCase();
                if (selectedKamars.length === 0 || selectedKamars.includes(kamar)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function selectAllKamar() {
            document.querySelectorAll('.kamar-checkbox').forEach(cb => cb.checked = true);
            filterByKamar();
        }

        function deselectAllKamar() {
            document.querySelectorAll('.kamar-checkbox').forEach(cb => cb.checked = false);
            filterByKamar();
        }
    </script>
@endsection
