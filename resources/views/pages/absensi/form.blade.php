@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400">
                        <li><a href="{{ route('absensi.index') }}" class="hover:text-primary-600">Absensi</a></li>
                        <li><span class="mx-1">/</span></li>
                        <li class="font-medium text-gray-700 dark:text-white truncate max-w-[150px]">{{ $subKegiatan->nama_sub_kegiatan }}</li>
                    </ol>
                </nav>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Scan QR Code</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Absensi Kehadiran</p>
            </div>
            <a href="{{ route('absensi.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Scanner Section -->
            <div class="space-y-4">
                <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-black dark:border-gray-800">
                    <div id="reader" class="w-full h-[300px] sm:h-[400px] bg-black"></div>
                    
                    <!-- Overlay text when camera is off/loading -->
                    <div id="camera-placeholder" class="absolute inset-0 flex items-center justify-center text-white/50 text-center p-4 pointer-events-none">
                        <p>Memuat kamera...</p>
                    </div>

                    <!-- Scan result feedback (Overlay) -->
                    <div id="scan-feedback" class="absolute inset-0 flex items-center justify-center bg-black/70 backdrop-blur-sm z-10 hidden transition-opacity duration-300">
                        <div class="text-center p-6 bg-white dark:bg-gray-900 rounded-2xl shadow-xl transform scale-95 transition-transform duration-300" id="scan-feedback-content">
                            <!-- Content injected via JS -->
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-2">
                    <button type="button" id="switch-camera-btn" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Switch Camera
                    </button>
                    <span class="text-xs text-gray-400" id="camera-status">Camera Active</span>
                </div>
            </div>

            <!-- Attendance List Section -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] flex flex-col h-full max-h-[600px]">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50 rounded-t-2xl">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Daftar Hadir</h3>
                    <div class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold" id="hadir-counter">
                        {{ collect($existingAbsensi)->where(fn($s) => $s === 'hadir')->count() }} / {{ $santris->count() }}
                    </div>
                </div>
                
                <div class="overflow-y-auto flex-1 p-0" id="attendance-list">
                    @if(empty($existingAbsensi))
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400" id="empty-state">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <p class="text-sm">Belum ada yang hadir</p>
                        </div>
                    @endif

                    <!-- List container -->
                    <div class="divide-y divide-gray-100 dark:divide-gray-800" id="attendee-items">
                         @foreach($santris as $santri)
                            @if(isset($existingAbsensi[$santri->id]) && $existingAbsensi[$santri->id] === 'hadir')
                                <div class="p-3 flex items-center gap-3 bg-green-50/30 dark:bg-green-900/10 animate-fade-in" id="santri-row-{{ $santri->id }}">
                                    @if($santri->foto)
                                        <img src="{{ asset('storage/asset_santri/foto/' . $santri->foto) }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">
                                            {{ substr($santri->nama, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $santri->nama }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $santri->kamar->nama_kamar ?? '-' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-medium">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Hadir
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Feedback -->
    <audio id="success-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>
    <audio id="error-sound" src="https://assets.mixkit.co/active_storage/sfx/2572/2572-preview.mp3" preload="auto"></audio>

    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const html5QrCode = new Html5Qrcode("reader");
            const feedbackOverlay = document.getElementById('scan-feedback');
            const feedbackContent = document.getElementById('scan-feedback-content');
            const successSound = document.getElementById('success-sound');
            const errorSound = document.getElementById('error-sound');
            let isProcessing = false;

            // Safely play audio (won't throw errors if audio fails)
            function playSound(audioElement) {
                if (audioElement && audioElement.play) {
                    audioElement.play().catch(() => {
                        // Silently ignore audio errors - audio is optional feedback
                    });
                }
            }

            // Start Scanner
            function startScanner(camerId = null) {
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                const cameraConfig = camerId ? { deviceId: camerId } : { facingMode: "environment" };

                html5QrCode.start(
                    cameraConfig, 
                    config, 
                    onScanSuccess, 
                    (errorMessage) => {
                        // ignore errors for better UX
                    }
                ).then(() => {
                    document.getElementById('camera-placeholder').style.display = 'none';
                }).catch(err => {
                    console.error("Error starting scanner", err);
                    document.getElementById('camera-placeholder').innerHTML = '<p class="text-red-500">Gagal akses kamera. Pastikan izin diberikan.</p>';
                });
            }

            // Start immediately
            startScanner();

            function onScanSuccess(decodedText, decodedResult) {
                if (isProcessing) return;
                isProcessing = true;
                
                // Pause scanning visually
                html5QrCode.pause();

                // Send to server
                fetch('{{ route("absensi.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        qr_code: decodedText,
                        sub_kegiatan_id: {{ $subKegiatan->id }}
                    })
                })
                .then(response => {
                    // Handle non-2xx responses properly
                    return response.json().then(data => {
                        if (!response.ok) {
                            // Server returned an error status code
                            throw { isServerError: true, data: data };
                        }
                        return data;
                    });
                })
                .then(data => {
                    if (data.status === 'success' && data.santri_nama) {
                        showSuccessFeedback(data.santri_nama);
                        updateAttendanceList(data.santri_nama, data.kamar_nama || '-');
                    } else {
                        showErrorFeedback(data.message || 'Terjadi kesalahan.');
                    }
                })
                .catch(error => {
                    if (error.isServerError && error.data) {
                        showErrorFeedback(error.data.message || 'Terjadi kesalahan pada server.');
                    } else {
                        showErrorFeedback('Terjadi kesalahan koneksi.');
                        console.error('Scan error:', error);
                    }
                })
                .finally(() => {
                    // Resume scanning after delay
                    setTimeout(() => {
                        feedbackOverlay.classList.add('hidden');
                        isProcessing = false;
                        html5QrCode.resume();
                    }, 2000);
                });
            }

            function showSuccessFeedback(name) {
                playSound(successSound);
                const displayName = name || 'Santri';
                feedbackContent.innerHTML = `
                    <div class="mb-3 mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Berhasil!</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">${displayName}</p>
                `;
                feedbackOverlay.classList.remove('hidden');
            }

            function showErrorFeedback(message) {
                playSound(errorSound);
                const errorMsg = message || 'Terjadi kesalahan.';
                feedbackContent.innerHTML = `
                    <div class="mb-3 mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Gagal</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-[200px]">${errorMsg}</p>
                `;
                feedbackOverlay.classList.remove('hidden');
            }

            function updateAttendanceList(name, kamar) {
                // Guard against null/undefined name
                if (!name) {
                    console.warn('updateAttendanceList called with empty name');
                    return;
                }

                const emptyState = document.getElementById('empty-state');
                if (emptyState) emptyState.remove();

                const list = document.getElementById('attendee-items');
                if (!list) {
                    console.warn('Attendance list element not found');
                    return;
                }

                const kamarName = kamar || '-';
                const newItem = document.createElement('div');
                newItem.className = 'p-3 flex items-center gap-3 bg-green-50/30 dark:bg-green-900/10 animate-fade-in border-b border-gray-100 dark:border-gray-800';
                newItem.innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold">
                        ${name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${kamarName}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-medium">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Hadir
                        </span>
                    </div>
                `;
                list.insertBefore(newItem, list.firstChild);

                // Update counter
                const counter = document.getElementById('hadir-counter');
                if (counter) {
                    let [current, total] = counter.innerText.split('/').map(s => parseInt(s.trim()));
                    counter.innerText = `${current + 1} / ${total}`;
                }
            }

            // Cleanup
            window.addEventListener('beforeunload', () => {
                if (html5QrCode.isScanning) {
                    html5QrCode.stop();
                }
            });
        });
    </script>

    <style>
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endsection
