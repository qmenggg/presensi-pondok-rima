@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="{{ route('kegiatan.index') }}" class="hover:text-primary-600">Kegiatan</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li><a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}" class="hover:text-primary-600">{{ $kegiatan->nama_kegiatan }}</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li class="font-medium text-gray-900 dark:text-white">Assign Peserta</li>
                    </ol>
                </nav>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Assign Peserta</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subKegiatan->nama_sub_kegiatan }}</p>
            </div>
            <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        @if (session('error'))
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('sub-kegiatan.store-assign', [$kegiatan->id, $subKegiatan->id]) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Assign Kamar -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pilih Kamar</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Semua santri di kamar akan mengikuti kegiatan ini</p>
                        </div>
                    </div>

                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @foreach ($kamars as $kamar)
                            <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 cursor-pointer transition-colors">
                                <input type="checkbox" name="kamars[]" value="{{ $kamar->id }}"
                                    {{ in_array($kamar->id, $assignedKamars) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 dark:border-gray-700 dark:bg-gray-800">
                                <div class="ml-3">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $kamar->nama_kamar }}</span>
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">({{ $kamar->santris()->count() }} santri)</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Assign Santri Individual -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pilih Santri</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih santri individual (opsional)</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="text" id="search-santri" placeholder="Cari santri..." 
                            class="w-full h-[40px] rounded-lg border border-gray-300 bg-transparent py-2 px-3 text-sm text-gray-800 focus:border-primary-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"/>
                    </div>

                    <div class="space-y-2 max-h-64 overflow-y-auto" id="santri-list">
                        @foreach ($santris as $santri)
                            <label class="santri-item flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 cursor-pointer transition-colors" data-name="{{ strtolower($santri->user->nama) }}">
                                <input type="checkbox" name="santris[]" value="{{ $santri->id }}"
                                    {{ in_array($santri->id, $assignedSantris) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500 dark:border-gray-700 dark:bg-gray-800">
                                <div class="ml-3">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $santri->user->nama }}</span>
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $santri->kamar->nama_kamar ?? 'Belum ada kamar' }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Batal
                </a>
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    Simpan Assignment
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('search-santri').addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.santri-item').forEach(function(item) {
                const name = item.getAttribute('data-name');
                if (name.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
@endpush
