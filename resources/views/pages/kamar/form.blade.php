@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $kamar ? 'Edit Kamar' : 'Tambah Kamar' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $kamar ? 'Ubah data kamar' : 'Tambahkan data kamar baru' }}
                </p>
            </div>
            <a href="{{ route('kamar.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <form action="{{ $kamar ? route('kamar.update', $kamar->id) : route('kamar.store') }}" method="POST" class="space-y-6">
                @csrf
                @if ($kamar)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Nama Kamar -->
                    <div>
                        <label for="nama_kamar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nama Kamar <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_kamar" name="nama_kamar"
                            value="{{ old('nama_kamar', $kamar ? $kamar->nama_kamar : '') }}" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                        @error('nama_kamar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis -->
                    <div>
                        <label for="jenis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis" name="jenis" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            <option value="">Pilih Jenis</option>
                            <option value="putra" {{ old('jenis', $kamar ? $kamar->jenis : '') == 'putra' ? 'selected' : '' }}>Putra</option>
                            <option value="putri" {{ old('jenis', $kamar ? $kamar->jenis : '') == 'putri' ? 'selected' : '' }}>Putri</option>
                        </select>
                        @error('jenis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <button type="reset"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Reset
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ $kamar ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
