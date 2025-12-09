@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $izin ? 'Edit Izin' : 'Tambah Izin' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $izin ? 'Ubah data izin santri' : 'Tambahkan izin baru untuk santri' }}
                </p>
            </div>
            <a href="{{ route('izin.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <x-common.component-card title="{{ $izin ? 'Form Edit Izin' : 'Form Tambah Izin' }}">
            <form action="{{ $izin ? route('izin.update', $izin->id) : route('izin.store') }}" method="POST" class="space-y-6">
                @csrf
                @if ($izin)
                    @method('PUT')
                @endif

                @if (session('error'))
                    <div class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-500/15 dark:text-red-500">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <!-- Santri -->
                    <div>
                        <label for="santri_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Santri <span class="text-red-500">*</span>
                        </label>
                        <select id="santri_id" name="santri_id" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            <option value="">Pilih Santri</option>
                            @foreach($santris as $santri)
                                <option value="{{ $santri->id }}" {{ old('santri_id', $izin?->santri_id) == $santri->id ? 'selected' : '' }}>
                                    {{ $santri->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('santri_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                            <option value="">Pilih Jenis</option>
                            <option value="sakit" {{ old('status', $izin?->status) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="izin" {{ old('status', $izin?->status) == 'izin' ? 'selected' : '' }}>Izin</option>
                        </select>
                        @error('status')
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
                                value="{{ old('tanggal_mulai', $izin && $izin->tanggal_mulai ? $izin->tanggal_mulai->format('Y-m-d') : '') }}"
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
                                value="{{ old('tanggal_selesai', $izin && $izin->tanggal_selesai ? $izin->tanggal_selesai->format('Y-m-d') : '') }}"
                                required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('tanggal_selesai')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Keterangan
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                            placeholder="Keterangan tambahan (opsional)"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">{{ old('keterangan', $izin?->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <a href="{{ route('izin.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ $izin ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
