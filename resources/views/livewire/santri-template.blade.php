<div class="space-y-6">
    {{-- Filter Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Download Template Import Santri</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Pilih jenis kelamin dan kamar yang akan ditampilkan sebagai pilihan di template Excel.
        </p>
        
        {{-- Jenis Kelamin Filter --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Kelamin</label>
            <select wire:model.live="jenisKelamin"
                class="w-full md:w-64 h-12 rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                <option value="">Semua (Putra & Putri)</option>
                <option value="L">Laki-laki (Putra)</option>
                <option value="P">Perempuan (Putri)</option>
            </select>
        </div>
    </div>

    {{-- Kamar Selection Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Pilih Kamar</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Centang kamar yang akan muncul di dropdown template
                    <span wire:loading wire:target="jenisKelamin" class="ml-2">
                        <svg class="animate-spin inline h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="selectAll" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400">
                    Pilih Semua
                </button>
                <button wire:click="deselectAll" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                    Hapus Semua
                </button>
            </div>
        </div>

        {{-- Selected Count Badge --}}
        <div class="mb-4">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium {{ count($selectedKamars) > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ count($selectedKamars) }} dari {{ count($allKamars) }} kamar dipilih
            </span>
        </div>
        
        {{-- Kamar Checkboxes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" wire:loading.class="opacity-50" wire:target="jenisKelamin">
            @forelse($allKamars as $kamar)
                <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all
                    {{ in_array((string)$kamar['id'], $selectedKamars) 
                        ? ($kamar['jenis'] === 'putra' 
                            ? 'border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/30' 
                            : 'border-pink-300 bg-pink-50 dark:border-pink-700 dark:bg-pink-900/30')
                        : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-700 dark:bg-gray-800/50 dark:hover:border-gray-600' 
                    }}">
                    <input type="checkbox" wire:model.live="selectedKamars" value="{{ $kamar['id'] }}"
                        class="w-4 h-4 rounded border-gray-300 {{ $kamar['jenis'] === 'putra' ? 'text-blue-600 focus:ring-blue-500' : 'text-pink-600 focus:ring-pink-500' }} dark:border-gray-600 dark:bg-gray-700">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $kamar['nama_kamar'] }}</p>
                        <p class="text-xs {{ $kamar['jenis'] === 'putra' ? 'text-blue-600 dark:text-blue-400' : 'text-pink-600 dark:text-pink-400' }}">
                            {{ $kamar['jenis'] === 'putra' ? 'Putra' : 'Putri' }}
                        </p>
                    </div>
                </label>
            @empty
                <div class="col-span-full text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p>Tidak ada kamar tersedia</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Download Button Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Siap Download</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if(count($selectedKamars) > 0)
                        Template akan berisi dropdown dengan {{ count($selectedKamars) }} kamar yang dipilih
                    @else
                        <svg class="w-5 h-5 inline-block text-yellow-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Pilih minimal 1 kamar untuk download template
                    @endif
                </p>
            </div>
            
            <button wire:click="downloadTemplate" wire:loading.attr="disabled"
                @if(count($selectedKamars) === 0) disabled @endif
                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium rounded-lg transition-colors
                    {{ count($selectedKamars) > 0 
                        ? 'text-white bg-green-600 hover:bg-green-700' 
                        : 'text-gray-400 bg-gray-100 cursor-not-allowed dark:bg-gray-800 dark:text-gray-500' 
                    }}">
                <span wire:loading.remove wire:target="downloadTemplate">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </span>
                <span wire:loading wire:target="downloadTemplate">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="downloadTemplate">Download Template</span>
                <span wire:loading wire:target="downloadTemplate">Downloading...</span>
            </button>
        </div>
    </div>
</div>
