<div class="space-y-6">
    {{-- File Upload Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Upload File Import</h3>
        
        @if(empty($importResults))
            {{-- Upload Form --}}
            <div class="space-y-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Upload file Excel yang telah diisi sesuai template. 
                    <a href="{{ route('santri.template') }}" class="text-blue-600 hover:underline dark:text-blue-400">Download Template</a> terlebih dahulu jika belum punya.
                </p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload File Excel (.xlsx, .xls)
                    </label>
                    <input type="file" wire:model="file" accept=".xlsx,.xls"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                    
                    <div wire:loading wire:target="file" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                        <svg class="animate-spin inline h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Membaca file...
                    </div>

                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if(!empty($parseErrors))
                    <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/30">
                        <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Error</h4>
                        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300">
                            @foreach($parseErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        {{-- Import Results --}}
        @if(!empty($importResults))
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-900/30">
                        <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $successCount }}</p>
                        <p class="text-sm text-green-600 dark:text-green-500">Berhasil</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/30">
                        <p class="text-2xl font-bold text-red-700 dark:text-red-400">{{ $failedCount }}</p>
                        <p class="text-sm text-red-600 dark:text-red-500">Gagal</p>
                    </div>
                </div>

                <div class="max-h-60 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-400">Baris</th>
                                <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-400">Status</th>
                                <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-400">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($importResults as $result)
                                <tr>
                                    <td class="px-4 py-2 text-gray-700 dark:text-gray-300">{{ $result['row'] }}</td>
                                    <td class="px-4 py-2">
                                        @if($result['success'])
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Sukses</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Gagal</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $result['message'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-3">
                    <button wire:click="resetForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Import Lagi
                    </button>
                    <a href="{{ route('santri.index') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Lihat Data Santri
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Preview Section --}}
    @if($showPreview && !empty($previewData))
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    Preview Data ({{ count($previewData) }} baris)
                </h3>
                <div class="flex items-center gap-2">
                    @php
                        $validRows = collect($previewData)->filter(fn($row) => empty($row['rowErrors']))->count();
                        $invalidRows = count($previewData) - $validRows;
                    @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                        {{ $validRows }} Valid
                    </span>
                    @if($invalidRows > 0)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            {{ $invalidRows }} Error
                        </span>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 mb-4">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Baris</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Nama</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Username</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">JK</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Kamar</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">TTL</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($previewData as $row)
                            <tr class="{{ !empty($row['rowErrors']) ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $row['row'] }}</td>
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $row['nama_lengkap'] }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400 font-mono text-xs">
                                    {{ $row['username'] ?: '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $row['jenis_kelamin'] === 'L' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400' }}">
                                        {{ $row['jenis_kelamin'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $row['nama_kamar'] }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $row['tempat_lahir'] }}, {{ $row['tanggal_lahir'] }}
                                </td>
                                <td class="px-3 py-2">
                                    @if(empty($row['rowErrors']))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Valid
                                        </span>
                                    @else
                                        <span class="text-xs text-red-600 dark:text-red-400" title="{{ implode(', ', $row['rowErrors']) }}">
                                            {{ implode(', ', $row['rowErrors']) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-3">
                <button wire:click="resetForm" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    Batal
                </button>
                @if($validRows > 0)
                    <button wire:click="processImport" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove wire:target="processImport">
                            Import {{ $validRows }} Data Valid
                        </span>
                        <span wire:loading wire:target="processImport" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengimpor...
                        </span>
                    </button>
                @endif
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                * Password default: <code class="px-1 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">password123</code> | QR Code akan digenerate otomatis
            </p>
        </div>
    @endif
</div>
