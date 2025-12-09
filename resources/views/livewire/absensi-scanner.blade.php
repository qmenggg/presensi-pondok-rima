<div class="min-h-screen pb-20"
     x-data="zxingQrScanner()"
     x-init="initScanner()"
     @auto-hide-feedback.window="setTimeout(() => $wire.hideFeedback(), 2500)">
    {{-- Header --}}
    <div class="flex items-start justify-between gap-2 mb-4">
        <div class="min-w-0 flex-1">
            <nav class="flex mb-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400">
                    <li><a href="{{ route('absensi.index') }}" class="hover:text-primary-600">Absensi</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="font-medium text-gray-700 dark:text-white truncate max-w-[150px]">{{ $subKegiatanNama }}</li>
                </ol>
            </nav>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Scan QR Code</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <a href="{{ route('absensi.index') }}"
           class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white p-3 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 active:scale-95 transition-transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>
    </div>

    {{-- Main Content - Mobile First --}}
    <div class="flex flex-col lg:grid lg:grid-cols-2 gap-4 lg:gap-6">

        {{-- Scanner Section --}}
        <div class="space-y-3">
            <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-black dark:border-gray-800 aspect-square lg:aspect-video">
                <video id="qr-reader" class="w-full h-full object-cover bg-black"></video>

                {{-- Camera Loading Placeholder --}}
                <div id="camera-placeholder" class="absolute inset-0 flex items-center justify-center text-white/50 text-center p-4 pointer-events-none">
                    <div class="space-y-2">
                        <svg class="w-10 h-10 mx-auto animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="text-sm" x-text="cameraStatus">Memuat kamera...</p>
                    </div>
                </div>
            </div>

            {{-- Camera Controls --}}
            <div class="flex items-center justify-between px-1">
                <button type="button" @click="switchCamera()"
                        class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2 -ml-2 rounded-lg active:bg-gray-100 dark:active:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Ganti Kamera
                </button>
                <span class="text-xs text-gray-400 px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded-full" x-text="cameraActive ? 'Kamera Aktif' : 'Kamera Off'">Kamera Aktif</span>
            </div>
        </div>

        {{-- Scan Feedback Badge (non-blocking) --}}
        @if($showFeedback)
        <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 animate-fade-in"
             wire:click="hideFeedback">
            @if($feedbackType === 'success')
                <div class="flex items-center gap-2 px-4 py-3 bg-green-500 text-white rounded-xl shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">{{ $lastScannedName ?? 'Berhasil' }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 px-4 py-3 bg-red-500 text-white rounded-xl shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="font-medium text-sm">{{ $feedbackMessage }}</span>
                </div>
            @endif
        </div>
        @endif

        {{-- Attendance List Section --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] flex flex-col min-h-[300px] max-h-[500px] lg:max-h-[600px]">
            {{-- List Header --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50 rounded-t-2xl">
                <h3 class="font-semibold text-gray-800 dark:text-white">Daftar Hadir</h3>
                <div class="px-3 py-1.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-sm font-bold">
                    {{ $hadirCount }} / {{ $totalPeserta }}
                </div>
            </div>

            {{-- List Content --}}
            <div class="overflow-y-auto flex-1" wire:poll.10s>
                @if($recentAbsensi->isEmpty())
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">Belum ada yang hadir</p>
                        <p class="text-xs mt-1">Scan QR code untuk memulai absensi</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($recentAbsensi as $absen)
                            <div class="p-3 sm:p-4 flex items-center gap-3 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors"
                                 wire:key="absen-{{ $absen->id }}">
                                {{-- Avatar --}}
                                @if($absen->santri->foto)
                                    <img src="{{ asset('storage/asset_santri/foto/' . $absen->santri->foto) }}"
                                         class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover flex-shrink-0 ring-2 ring-green-100 dark:ring-green-900/30">
                                @else
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-green-400 to-green-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($absen->santri->nama ?? '?', 0, 1)) }}
                                    </div>
                                @endif

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $absen->santri->nama ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $absen->santri->kamar->nama_kamar ?? '-' }}
                                    </p>
                                </div>

                                {{-- Time --}}
                                <div class="text-right flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-medium">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $absen->updated_at->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Load More --}}
                    @if($recentAbsensi->count() >= $perPage)
                        <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                            <button wire:click="loadMore"
                                    wire:loading.attr="disabled"
                                    class="w-full py-2.5 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-colors active:scale-[0.98]">
                                <span wire:loading.remove wire:target="loadMore">Muat Lebih Banyak</span>
                                <span wire:loading wire:target="loadMore">
                                    <svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Audio Feedback (Hidden) --}}
    <audio id="success-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
    <audio id="error-sound" src="https://assets.mixkit.co/active_storage/sfx/2572/2572-preview.mp3" preload="auto"></audio>

    {{-- ZXing QR Scanner Script (CDN Version - No Build Required) --}}
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script src="{{ asset('js/zxing-scanner.js') }}"></script>


    <style>
        .animate-fade-in {
            animation: fadeIn 0.2s ease-out;
        }
        .animate-scale-in {
            animation: scaleIn 0.2s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</div>
