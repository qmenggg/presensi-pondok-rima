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
                        <li class="font-medium text-gray-900 dark:text-white">{{ $subKegiatan ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </nav>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $subKegiatan ? 'Edit Sub Kegiatan' : 'Tambah Sub Kegiatan' }}
                </h2>
            </div>
            <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <x-common.component-card title="{{ $subKegiatan ? 'Form Edit Sub Kegiatan' : 'Form Tambah Sub Kegiatan' }}">
            <form action="{{ $subKegiatan ? route('sub-kegiatan.update', [$kegiatan->id, $subKegiatan->id]) : route('sub-kegiatan.store', $kegiatan->id) }}" method="POST" class="space-y-6">
                @csrf
                @if ($subKegiatan)
                    @method('PUT')
                @endif

                @if (session('error'))
                    <div class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-500/15 dark:text-red-500">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Nama Sub Kegiatan -->
                    <div>
                        <label for="nama_sub_kegiatan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Sub Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_sub_kegiatan" name="nama_sub_kegiatan"
                            value="{{ old('nama_sub_kegiatan', $subKegiatan ? $subKegiatan->nama_sub_kegiatan : '') }}"
                            placeholder="Contoh: Sholat Subuh Berjamaah"
                            required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                        @error('nama_sub_kegiatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hari (Multi-select Checkbox) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Hari <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" id="select-all-hari" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                                Pilih Semua
                            </button>
                            <button type="button" id="clear-all-hari" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                                Hapus Semua
                            </button>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mt-3">
                            @php
                                $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
                                $oldHaris = old('haris', $selectedHaris ?? []);
                            @endphp
                            @foreach ($hariList as $hari)
                                <label class="hari-checkbox flex items-center justify-center p-3 rounded-lg border cursor-pointer transition-colors
                                    {{ in_array($hari, $oldHaris) ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30' : 'border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                    <input type="checkbox" name="haris[]" value="{{ $hari }}"
                                        {{ in_array($hari, $oldHaris) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($hari) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('haris')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Waktu Mulai -->
                        <div>
                            <label for="waktu_mulai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Waktu Mulai <span class="text-gray-400 text-xs">(opsional)</span>
                            </label>
                            <input type="time" id="waktu_mulai" name="waktu_mulai"
                                value="{{ old('waktu_mulai', $subKegiatan ? substr($subKegiatan->waktu_mulai ?? '', 0, 5) : '') }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('waktu_mulai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Waktu Selesai -->
                        <div>
                            <label for="waktu_selesai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Waktu Selesai <span class="text-gray-400 text-xs">(opsional)</span>
                            </label>
                            <input type="time" id="waktu_selesai" name="waktu_selesai"
                                value="{{ old('waktu_selesai', $subKegiatan ? substr($subKegiatan->waktu_selesai ?? '', 0, 5) : '') }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('waktu_selesai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Untuk Jenis Santri -->
                        <div>
                            <label for="untuk_jenis_santri" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Untuk Santri <span class="text-red-500">*</span>
                            </label>
                            <select id="untuk_jenis_santri" name="untuk_jenis_santri" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                                <option value="">Pilih</option>
                                <option value="putra" {{ old('untuk_jenis_santri', $subKegiatan ? $subKegiatan->untuk_jenis_santri : '') === 'putra' ? 'selected' : '' }}>Putra</option>
                                <option value="putri" {{ old('untuk_jenis_santri', $subKegiatan ? $subKegiatan->untuk_jenis_santri : '') === 'putri' ? 'selected' : '' }}>Putri</option>
                                <option value="campur" {{ old('untuk_jenis_santri', $subKegiatan ? $subKegiatan->untuk_jenis_santri : '') === 'campur' ? 'selected' : '' }}>Campur</option>
                            </select>
                            @error('untuk_jenis_santri')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="lokasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Lokasi <span class="text-gray-400 text-xs">(opsional)</span>
                            </label>
                            <input type="text" id="lokasi" name="lokasi"
                                value="{{ old('lokasi', $subKegiatan ? $subKegiatan->lokasi : '') }}"
                                placeholder="Contoh: Masjid, Aula"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('lokasi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Guru Penanggung Jawab (Select2) -->
                    <div>
                        <label for="guru_penanggung_jawab" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Guru Penanggung Jawab <span class="text-gray-400 text-xs">(opsional)</span>
                        </label>
                        <select id="guru_penanggung_jawab" name="guru_penanggung_jawab"
                            class="guru-select w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            <option value="">Pilih Guru (ketik untuk mencari)</option>
                            @foreach ($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_penanggung_jawab', $subKegiatan ? $subKegiatan->guru_penanggung_jawab : '') == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_penanggung_jawab')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Keterangan <span class="text-gray-400 text-xs">(opsional)</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="2"
                            placeholder="Deskripsi singkat kegiatan"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">{{ old('keterangan', $subKegiatan ? $subKegiatan->keterangan : '') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <a href="{{ route('sub-kegiatan.index', $kegiatan->id) }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ $subKegiatan ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border-radius: 0.5rem !important;
            border-color: #d1d5db !important;
            padding: 6px 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #1f2937 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .dark .select2-container--default .select2-selection--single {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: rgba(255,255,255,0.9) !important;
        }
        .select2-dropdown {
            border-radius: 0.5rem !important;
        }
        .hari-checkbox:has(input:checked) {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .dark .hari-checkbox:has(input:checked) {
            background-color: rgba(59, 130, 246, 0.2);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for guru
            $('.guru-select').select2({
                placeholder: 'Pilih Guru (ketik untuk mencari)',
                allowClear: true,
                width: '100%',
            });

            // Select all hari
            $('#select-all-hari').click(function() {
                $('input[name="haris[]"]').prop('checked', true).trigger('change');
                updateHariStyles();
            });

            // Clear all hari
            $('#clear-all-hari').click(function() {
                $('input[name="haris[]"]').prop('checked', false).trigger('change');
                updateHariStyles();
            });

            // Update styles on checkbox change
            $('input[name="haris[]"]').change(function() {
                updateHariStyles();
            });

            function updateHariStyles() {
                $('.hari-checkbox').each(function() {
                    const checkbox = $(this).find('input[type="checkbox"]');
                    if (checkbox.is(':checked')) {
                        $(this).addClass('border-primary-500 bg-primary-50 dark:bg-primary-900/30')
                               .removeClass('border-gray-300 dark:border-gray-700');
                    } else {
                        $(this).removeClass('border-primary-500 bg-primary-50 dark:bg-primary-900/30')
                               .addClass('border-gray-300 dark:border-gray-700');
                    }
                });
            }
        });
    </script>
@endpush
