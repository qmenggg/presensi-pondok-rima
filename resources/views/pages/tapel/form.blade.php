@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $tapel ? 'Edit Tahun Pelajaran' : 'Tambah Tahun Pelajaran' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $tapel ? 'Ubah data tahun pelajaran' : 'Tambahkan tahun pelajaran baru' }}
                </p>
            </div>
            <a href="{{ route('tapel.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <x-common.component-card title="{{ $tapel ? 'Form Edit Tahun Pelajaran' : 'Form Tambah Tahun Pelajaran' }}">
            <form action="{{ $tapel ? route('tapel.update', $tapel->id) : route('tapel.store') }}" method="POST" class="space-y-6">
                @csrf
                @if ($tapel)
                    @method('PUT')
                @endif

                @if (session('error'))
                    <div class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-500/15 dark:text-red-500">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Nama Tapel -->
                    <div>
                        <label for="nama_tapel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Tahun Pelajaran <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_tapel" name="nama_tapel"
                            value="{{ old('nama_tapel', $tapel ? $tapel->nama_tapel : '') }}"
                            placeholder="Contoh: 2024/2025"
                            required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                        @error('nama_tapel')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', $tapel && $tapel->tanggal_mulai ? $tapel->tanggal_mulai->format('Y-m-d') : '') }}"
                                required
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
                                value="{{ old('tanggal_selesai', $tapel && $tapel->tanggal_selesai ? $tapel->tanggal_selesai->format('Y-m-d') : '') }}"
                                required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('tanggal_selesai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Aktif -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="aktif" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status Aktif
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Jika diaktifkan, tahun pelajaran lain akan otomatis menjadi non-aktif
                                </p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="hidden" name="aktif" value="0">
                                <input type="checkbox" id="aktif" name="aktif" value="1"
                                    {{ old('aktif', $tapel ? $tapel->aktif : false) ? 'checked' : '' }}
                                    class="peer sr-only">
                                <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-500/20 dark:bg-gray-700 dark:after:border-gray-600 dark:peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <a href="{{ route('tapel.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ $tapel ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
