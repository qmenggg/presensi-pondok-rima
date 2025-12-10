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

        {{-- Global Holiday Banner --}}
        @if(isset($liburGlobal) && $liburGlobal)
            <div class="rounded-lg bg-yellow-50 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900/20">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-800 dark:text-yellow-300">Hari Libur: {{ $liburGlobal->keterangan ?? 'Libur' }}</p>
                        <p class="text-sm text-yellow-700 dark:text-yellow-400 mt-0.5">Jenis: {{ ucfirst($liburGlobal->jenis) }} | Untuk: {{ ucfirst($liburGlobal->untuk_jenis_santri) }}</p>
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
                            $isLibur = $sub->is_libur ?? false;
                        @endphp
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ $isLibur ? 'opacity-60' : '' }}" id="kegiatan-{{ $sub->id }}">
                            <div class="flex items-start justify-between gap-3">
                                <a href="{{ $isLibur ? '#' : route('absensi.create', ['subKegiatan' => $sub->id, 'tanggal' => $tanggal]) }}" 
                                   class="flex-1 min-w-0 {{ $isLibur ? 'pointer-events-none' : '' }}">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-gray-900 dark:text-white truncate">{{ $sub->nama_sub_kegiatan }}</span>
                                        @if($isLibur)
                                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
                                                LIBUR
                                            </span>
                                        @elseif($isComplete)
                                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                        @if($waktu)
                                            <span class="text-xs text-gray-500 dark:text-gray-400"><svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> {{ $waktu }}</span>
                                        @endif
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">{{ $sub->kegiatan->nama_kegiatan ?? '-' }}</span>
                                        @if($sub->guruPenanggungJawab)
                                            <span class="text-xs text-gray-500 dark:text-gray-400"><svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> {{ $sub->guruPenanggungJawab->nama }}</span>
                                        @endif
                                    </div>
                                    <!-- Progress Bar -->
                                    @if(!$isLibur)
                                    <div class="mt-2">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-gray-500 dark:text-gray-400">{{ $sub->absensi_count }} / {{ $sub->peserta_count }} santri</span>
                                            <span class="{{ $isComplete ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $percentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                            <div class="{{ $isComplete ? 'bg-green-500' : 'bg-blue-500' }} h-1.5 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                    @endif
                                </a>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    {{-- Admin: Toggle Libur Button --}}
                                    @if(auth()->user()->role === 'admin')
                                        <button type="button" 
                                                onclick="toggleLibur({{ $sub->id }}, '{{ $tanggal }}')"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                                    {{ $isLibur 
                                                        ? 'text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400' 
                                                        : 'text-orange-700 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:text-orange-400' }}"
                                                title="{{ $isLibur ? 'Aktifkan Kembali' : 'Tandai Libur' }}"
                                                id="libur-btn-{{ $sub->id }}">
                                            @if($isLibur)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Aktifkan
                                            @else
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                Libur
                                            @endif
                                        </button>
                                    @endif
                                    <a href="{{ route('rekap.index', [$sub->id, $tanggal]) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors"
                                       title="Rekap Presensi">
                                        <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg> Rekap
                                    </a>
                                    @if(!$isLibur)
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleLibur(subKegiatanId, tanggal) {
    const btn = document.getElementById('libur-btn-' + subKegiatanId);
    const card = document.getElementById('kegiatan-' + subKegiatanId);
    
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    fetch(`/absensi/${subKegiatanId}/toggle-libur`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tanggal: tanggal })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Reload page to reflect changes
            window.location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan jaringan');
        btn.disabled = false;
    });
}
</script>
@endpush
