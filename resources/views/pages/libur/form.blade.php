@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $libur ? 'Edit Hari Libur' : 'Tambah Hari Libur' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $libur ? 'Ubah data hari libur' : 'Tambahkan hari libur baru' }}
                </p>
            </div>
            <a href="{{ route('libur.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <x-common.component-card title="{{ $libur ? 'Form Edit Hari Libur' : 'Form Tambah Hari Libur' }}">
            <form action="{{ $libur ? route('libur.update', $libur->id) : route('libur.store') }}" method="POST" class="space-y-6">
                @csrf
                @if ($libur)
                    @method('PUT')
                @endif

                @if (session('error'))
                    <div class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-500/15 dark:text-red-500">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="2"
                            placeholder="Contoh: Libur Hari Raya Idul Fitri"
                            required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">{{ old('keterangan', $libur ? $libur->keterangan : '') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Jenis -->
                        <div>
                            <label for="jenis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Jenis Libur <span class="text-red-500">*</span>
                            </label>
                            <select id="jenis" name="jenis" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                                <option value="">Pilih Jenis</option>
                                <option value="nasional" {{ old('jenis', $libur?->jenis) == 'nasional' ? 'selected' : '' }}>Nasional</option>
                                <option value="pondok" {{ old('jenis', $libur?->jenis) == 'pondok' ? 'selected' : '' }}>Pondok</option>
                                <option value="khusus" {{ old('jenis', $libur?->jenis) == 'khusus' ? 'selected' : '' }}>Khusus</option>
                            </select>
                            @error('jenis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Untuk Jenis Santri -->
                        <div>
                            <label for="untuk_jenis_santri" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Untuk Santri <span class="text-red-500">*</span>
                            </label>
                            <select id="untuk_jenis_santri" name="untuk_jenis_santri" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                                <option value="semua" {{ old('untuk_jenis_santri', $libur?->untuk_jenis_santri) == 'semua' ? 'selected' : '' }}>Semua</option>
                                <option value="putra" {{ old('untuk_jenis_santri', $libur?->untuk_jenis_santri) == 'putra' ? 'selected' : '' }}>Putra</option>
                                <option value="putri" {{ old('untuk_jenis_santri', $libur?->untuk_jenis_santri) == 'putri' ? 'selected' : '' }}>Putri</option>
                            </select>
                            @error('untuk_jenis_santri')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Rutin Mingguan Toggle -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="rutin_mingguan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Libur Rutin Mingguan
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Aktifkan jika libur berlaku setiap minggu pada hari tertentu
                                </p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="rutin_mingguan" value="0">
                                <input type="checkbox" id="rutin_mingguan" name="rutin_mingguan" value="1"
                                    {{ old('rutin_mingguan', $libur?->rutin_mingguan) ? 'checked' : '' }}
                                    onchange="toggleRutinFields()"
                                    class="peer sr-only">
                                <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-blue-500/20 dark:bg-gray-700 dark:after:border-gray-600 dark:peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Hari Rutin (conditional) -->
                    <div id="hari_rutin_container" class="{{ old('rutin_mingguan', $libur?->rutin_mingguan) ? '' : 'hidden' }}">
                        <label for="hari_rutin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Hari Libur Rutin <span class="text-red-500">*</span>
                        </label>
                        <select id="hari_rutin" name="hari_rutin"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            <option value="">Pilih Hari</option>
                            @foreach(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'] as $hari)
                                <option value="{{ $hari }}" {{ old('hari_rutin', $libur?->hari_rutin) == $hari ? 'selected' : '' }}>{{ ucfirst($hari) }}</option>
                            @endforeach
                        </select>
                        @error('hari_rutin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal (conditional - hidden when rutin) -->
                    <div id="tanggal_container" class="{{ old('rutin_mingguan', $libur?->rutin_mingguan) ? 'hidden' : '' }}">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Tanggal Mulai -->
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                    value="{{ old('tanggal_mulai', $libur && $libur->tanggal_mulai ? $libur->tanggal_mulai->format('Y-m-d') : '') }}"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                                @error('tanggal_mulai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggal Selesai -->
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                    value="{{ old('tanggal_selesai', $libur && $libur->tanggal_selesai ? $libur->tanggal_selesai->format('Y-m-d') : '') }}"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                                @error('tanggal_selesai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <a href="{{ route('libur.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ $libur ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </x-common.component-card>
    </div>

    <script>
        function toggleRutinFields() {
            const isRutin = document.getElementById('rutin_mingguan').checked;
            document.getElementById('hari_rutin_container').classList.toggle('hidden', !isRutin);
            document.getElementById('tanggal_container').classList.toggle('hidden', isRutin);
            
            // Toggle required attributes
            document.getElementById('hari_rutin').required = isRutin;
            document.getElementById('tanggal_mulai').required = !isRutin;
            document.getElementById('tanggal_selesai').required = !isRutin;
        }
        
        // Initial state
        document.addEventListener('DOMContentLoaded', toggleRutinFields);
    </script>
@endsection
