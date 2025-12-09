<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="mb-4 rounded-lg bg-yellow-50 p-4 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Filters & Bulk Actions -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Nama</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Ketik nama..."
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- Filter Kamar -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Kamar</label>
                <select wire:model.live="filterKamar"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Kamar</option>
                    @foreach ($kamars as $kamar)
                        <option value="{{ $kamar->id }}">{{ $kamar->nama_kamar }} ({{ ucfirst($kamar->jenis) }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Status</label>
                <select wire:model.live="filterStatus"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non-aktif</option>
                </select>
            </div>

            <!-- Target Kamar (Bulk) -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Kamar Tujuan</label>
                <select wire:model="targetKamar"
                    class="w-full h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">Pilih Kamar Tujuan</option>
                    @foreach ($kamars as $kamar)
                        <option value="{{ $kamar->id }}">{{ $kamar->nama_kamar }} ({{ ucfirst($kamar->jenis) }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Bulk Action Button -->
            <div class="flex items-end">
                <button wire:click="pindahKamarBulk" 
                    @if(empty($selectedSantris) || empty($targetKamar)) disabled @endif
                    class="w-full h-10 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Pindah ({{ count($selectedSantris) }})
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="mt-4 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll" 
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Kamar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Pindah Kamar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($santris as $santri)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]" wire:key="santri-{{ $santri->id }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:model.live="selectedSantris" value="{{ $santri->id }}"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @php $foto = $santri->foto ?? $santri->user->foto ?? null; @endphp
                                    @if ($foto)
                                        <img src="{{ asset('storage/asset_santri/foto/' . $foto) }}" alt="Foto" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-semibold">
                                            {{ strtoupper(substr($santri->user->nama ?? 'N', 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $santri->user->nama ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $santri->user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $santri->kamar ? ($santri->kamar->jenis === 'putra' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400') : 'bg-gray-100 text-gray-700' }}">
                                    {{ $santri->kamar->nama_kamar ?? 'Tidak ada' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="toggleAktif({{ $santri->id }})"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $santri->user->aktif ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $santri->user->aktif ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <p class="mt-1 text-xs {{ $santri->user->aktif ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $santri->user->aktif ? 'Aktif' : 'Nonaktif' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2" x-data="{ targetId: '' }">
                                    <select x-model="targetId"
                                        class="w-32 h-8 rounded border border-gray-300 bg-white px-2 text-xs focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="">Pilih</option>
                                        @foreach ($kamars as $kamar)
                                            @if ($kamar->id !== $santri->kamar_id)
                                                <option value="{{ $kamar->id }}">{{ $kamar->nama_kamar }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button @click="$wire.pindahKamarSingle({{ $santri->id }}, targetId)"
                                        :disabled="!targetId"
                                        class="px-2 py-1 rounded bg-blue-600 text-white text-xs hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Pindah
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data santri
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $santris->links() }}
        </div>
    </div>
</div>
