<div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="GET" action="{{ route($this->routeName) }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Date/Month/Tapel based on page type --}}
            @if($pageType === 'harian')
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                        class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                </div>
            @elseif($pageType === 'bulanan')
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Bulan</label>
                    <input type="month" name="bulan" value="{{ $bulan }}"
                        class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                </div>
            @elseif($pageType === 'tahunan')
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tahun Pelajaran</label>
                    <select name="tapel_id"
                        class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                        @foreach($tapels as $t)
                            <option value="{{ $t->id }}" {{ $tapelId == $t->id ? 'selected' : '' }}>{{ $t->nama_tapel }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Jenis Santri - with live update --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Jenis Santri</label>
                <select wire:model.live="jenisSantri" name="jenis_santri"
                    class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    <option value="">Semua</option>
                    <option value="putra">Putra</option>
                    <option value="putri">Putri</option>
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                <select wire:model="status" name="status"
                    class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                    <option value="">Semua</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alfa">Alfa</option>
                </select>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-end">
                <button type="submit" class="w-full h-10 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </div>

        {{-- Kamar Checkboxes - Reactive --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
            <div class="flex items-center gap-2 mb-2">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Kamar:</p>
                @if($jenisSantri)
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                        {{ $jenisSantri === 'putra' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400' }}">
                        {{ ucfirst($jenisSantri) }}
                    </span>
                @endif
                <span wire:loading wire:target="jenisSantri" class="text-xs text-gray-400">
                    <svg class="animate-spin h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </div>
            <div class="flex flex-wrap gap-2" wire:loading.class="opacity-50" wire:target="jenisSantri">
                @forelse($this->filteredKamars as $kamar)
                    <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 text-sm transition-colors">
                        <input type="checkbox" wire:model="selectedKamars" name="kamar_ids[]" value="{{ $kamar->id }}" class="rounded">
                        <span class="text-gray-700 dark:text-gray-300">{{ $kamar->nama_kamar }}</span>
                    </label>
                @empty
                    <p class="text-sm text-gray-400">Tidak ada kamar</p>
                @endforelse
            </div>
        </div>

        {{-- Sub Kegiatan Checkboxes - Reactive --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
            <div class="flex items-center gap-2 mb-2">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Sub Kegiatan:</p>
                @if($jenisSantri)
                    <span class="text-xs text-gray-400">(termasuk campur)</span>
                @endif
                <span wire:loading wire:target="jenisSantri" class="text-xs text-gray-400">
                    <svg class="animate-spin h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </div>
            <div class="flex flex-wrap gap-2" wire:loading.class="opacity-50" wire:target="jenisSantri">
                @forelse($this->filteredSubKegiatans as $subKegiatan)
                    <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 text-sm transition-colors">
                        <input type="checkbox" wire:model="selectedSubKegiatans" name="sub_kegiatan_ids[]" value="{{ $subKegiatan->id }}" class="rounded">
                        <span class="text-gray-700 dark:text-gray-300">{{ $subKegiatan->nama_sub_kegiatan }}</span>
                    </label>
                @empty
                    <p class="text-sm text-gray-400">Tidak ada sub kegiatan</p>
                @endforelse
            </div>
        </div>
    </form>
</div>
