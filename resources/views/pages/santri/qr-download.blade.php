@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Download QR Code Santri</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih kamar untuk didownload dalam format ZIP</p>
            </div>
            <a href="{{ route('santri.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('santri.qr-download.process') }}" method="POST" id="qrDownloadForm">
            @csrf
            
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <!-- Quick Actions -->
                <div class="flex flex-wrap gap-3 mb-6">
                    <button type="button" onclick="selectAll()" 
                            class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50">
                        Pilih Semua
                    </button>
                    <button type="button" onclick="deselectAll()" 
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Hapus Semua
                    </button>
                    <div class="ml-auto flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>Total santri terpilih:</span>
                        <span id="selectedCount" class="font-bold text-blue-600 dark:text-blue-400">0</span>
                    </div>
                </div>

                <!-- Kamar Putra -->
                @if($kamarPutra->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                            </svg>
                        </span>
                        Kamar Putra
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($kamarPutra as $kamar)
                        <label class="kamar-option flex items-center gap-3 p-4 rounded-xl border border-gray-200 cursor-pointer transition-all hover:border-blue-300 hover:bg-blue-50/50 dark:border-gray-700 dark:hover:border-blue-700 dark:hover:bg-blue-900/20"
                               data-santri-count="{{ $kamar->santris_count }}">
                            <input type="checkbox" name="kamar_ids[]" value="{{ $kamar->id }}" 
                                   class="kamar-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800"
                                   onchange="updateCount()">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800 dark:text-white">{{ $kamar->nama_kamar }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $kamar->santris_count }} santri</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Kamar Putri -->
                @if($kamarPutri->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-pink-100 dark:bg-pink-900/30">
                            <svg class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                            </svg>
                        </span>
                        Kamar Putri
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($kamarPutri as $kamar)
                        <label class="kamar-option flex items-center gap-3 p-4 rounded-xl border border-gray-200 cursor-pointer transition-all hover:border-pink-300 hover:bg-pink-50/50 dark:border-gray-700 dark:hover:border-pink-700 dark:hover:bg-pink-900/20"
                               data-santri-count="{{ $kamar->santris_count }}">
                            <input type="checkbox" name="kamar_ids[]" value="{{ $kamar->id }}" 
                                   class="kamar-checkbox w-5 h-5 text-pink-600 border-gray-300 rounded focus:ring-pink-500 dark:border-gray-600 dark:bg-gray-800"
                                   onchange="updateCount()">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800 dark:text-white">{{ $kamar->nama_kamar }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $kamar->santris_count }} santri</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Download Button -->
                <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" id="downloadBtn" disabled
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download ZIP
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function selectAll() {
        document.querySelectorAll('.kamar-checkbox').forEach(cb => cb.checked = true);
        updateCount();
    }
    
    function deselectAll() {
        document.querySelectorAll('.kamar-checkbox').forEach(cb => cb.checked = false);
        updateCount();
    }
    
    function updateCount() {
        let total = 0;
        document.querySelectorAll('.kamar-checkbox:checked').forEach(cb => {
            const option = cb.closest('.kamar-option');
            total += parseInt(option.dataset.santriCount) || 0;
        });
        
        document.getElementById('selectedCount').textContent = total;
        document.getElementById('downloadBtn').disabled = total === 0;
        
        // Update visual state
        document.querySelectorAll('.kamar-option').forEach(option => {
            const cb = option.querySelector('.kamar-checkbox');
            if (cb.checked) {
                option.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/30');
            } else {
                option.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/30');
            }
        });
    }
</script>
@endpush
