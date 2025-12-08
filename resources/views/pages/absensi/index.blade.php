@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Absensi</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($hariIni) }}, {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</p>
            </div>
            <form method="GET" action="{{ route('absensi.index') }}" class="flex items-center gap-2">
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </form>
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

        <!-- Kegiatan Cards -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Kegiatan Hari Ini</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pilih kegiatan untuk mengisi absensi</p>
            </div>

            @if($subKegiatans->isEmpty())
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p>Tidak ada kegiatan untuk hari {{ $hariIni }}</p>
                </div>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($subKegiatans as $sub)
                        @php
                            $waktu = '';
                            if ($sub->waktu_mulai && $sub->waktu_selesai) {
                                $waktu = substr($sub->waktu_mulai, 0, 5) . ' - ' . substr($sub->waktu_selesai, 0, 5);
                            } elseif ($sub->waktu_mulai) {
                                $waktu = substr($sub->waktu_mulai, 0, 5);
                            }
                            $isComplete = $sub->absensi_count >= $sub->peserta_count && $sub->peserta_count > 0;
                            $percentage = $sub->peserta_count > 0 ? round(($sub->absensi_count / $sub->peserta_count) * 100) : 0;
                        @endphp
                        <a href="{{ route('absensi.create', ['subKegiatan' => $sub->id, 'tanggal' => $tanggal]) }}" 
                           class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900 dark:text-white truncate">{{ $sub->nama_sub_kegiatan }}</span>
                                        @if($isComplete)
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                        @if($waktu)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">🕐 {{ $waktu }}</span>
                                        @endif
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">{{ $sub->kegiatan->nama_kegiatan ?? '-' }}</span>
                                        @if($sub->guruPenanggungJawab)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">📚 {{ $sub->guruPenanggungJawab->nama }}</span>
                                        @endif
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="mt-2">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-gray-500 dark:text-gray-400">{{ $sub->absensi_count }} / {{ $sub->peserta_count }} santri</span>
                                            <span class="{{ $isComplete ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                            <div class="{{ $isComplete ? 'bg-green-500' : 'bg-blue-500' }} h-1.5 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
