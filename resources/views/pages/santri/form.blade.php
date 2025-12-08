@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $santri ? 'Edit Santri' : 'Tambah Santri' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $santri ? 'Ubah data santri' : 'Tambahkan data santri baru' }}
                </p>
            </div>
            <a href="{{ route('santri.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <x-common.component-card title="{{ $santri ? 'Form Edit Santri' : 'Form Tambah Santri' }}">
            <form action="{{ $santri ? route('santri.update', $santri->id) : route('santri.store') }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if ($santri)
                    @method('PUT')
                @endif

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-500/15 dark:text-green-500">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-500/15 dark:text-red-500">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Data User -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Data User</h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" name="username"
                                value="{{ old('username', $santri && $santri->user ? $santri->user->username : '') }}"
                                required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password {{ $santri ? '(kosongkan jika tidak diubah)' : '' }} <span
                                    class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" {{ $santri ? '' : 'required' }}
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Nama -->
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama" name="nama"
                                value="{{ old('nama', $santri && $santri->user ? $santri->user->nama : '') }}" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="jenis_kelamin"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L"
                                    {{ old('jenis_kelamin', $santri && $santri->user ? $santri->user->jenis_kelamin : '') === 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P"
                                    {{ old('jenis_kelamin', $santri && $santri->user ? $santri->user->jenis_kelamin : '') === 'P' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Santri -->
                <div class="space-y-4 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Data Santri</h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Tempat Lahir -->
                        <div>
                            <label for="tempat_lahir"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir"
                                value="{{ old('tempat_lahir', $santri ? $santri->tempat_lahir : '') }}" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('tempat_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tanggal_lahir"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $santri && $santri->tanggal_lahir ? $santri->tanggal_lahir->format('Y-m-d') : '') }}"
                                required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('tanggal_lahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <textarea id="alamat" name="alamat" rows="3" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">{{ old('alamat', $santri ? $santri->alamat : '') }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Nama Wali -->
                        <div>
                            <label for="nama_wali" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nama Wali <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nama_wali" name="nama_wali"
                                value="{{ old('nama_wali', $santri ? $santri->nama_wali : '') }}" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('nama_wali')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kamar -->
                        <div>
                            <label for="kamar_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kamar
                            </label>
                            <select id="kamar_id" name="kamar_id"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                                <option value="">Pilih Kamar</option>
                                @foreach ($kamars as $kamar)
                                    <option value="{{ $kamar->id }}"
                                        {{ old('kamar_id', $santri ? $santri->kamar_id : '') == $kamar->id ? 'selected' : '' }}>
                                        {{ $kamar->nama_kamar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kamar_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Foto -->
                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Foto
                        </label>

                        <!-- Image Upload Area -->
                        <div x-data="{
                            imagePreview: null,
                            currentImage: '{{ $santri && ($santri->foto || ($santri->user && $santri->user->foto)) ? asset('storage/asset_santri/foto/' . ($santri->foto ?? ($santri->user ? $santri->user->foto : ''))) : null }}',
                            isDragging: false,
                            handleFileSelect(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    if (file.type.startsWith('image/')) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            this.imagePreview = e.target.result;
                                            this.currentImage = null;
                                        };
                                        reader.readAsDataURL(file);
                                    } else {
                                        alert('File harus berupa gambar!');
                                        event.target.value = '';
                                    }
                                }
                            },
                            handleDrop(event) {
                                event.preventDefault();
                                this.isDragging = false;
                                const file = event.dataTransfer.files[0];
                                if (file && file.type.startsWith('image/')) {
                                    document.getElementById('foto').files = event.dataTransfer.files;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.imagePreview = e.target.result;
                                        this.currentImage = null;
                                    };
                                    reader.readAsDataURL(file);
                                }
                            },
                            removeImage() {
                                this.imagePreview = null;
                                this.currentImage = null;
                                document.getElementById('foto').value = '';
                            }
                        }" class="space-y-3">
                            <!-- Upload Area -->
                            <div @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop($event)" @click="$refs.fileInput.click()"
                                :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-500/10' :
                                    'border-gray-300 dark:border-gray-700'"
                                class="relative border-2 border-dashed rounded-lg p-6 cursor-pointer transition-colors hover:border-primary-400 dark:hover:border-primary-600">
                                <input x-ref="fileInput" type="file" id="foto" name="foto" accept="image/*"
                                    @change="handleFileSelect($event)" class="hidden">

                                <!-- Preview Area -->
                                <div x-show="imagePreview || currentImage" class="text-center">
                                    <div class="relative inline-block">
                                        <img :src="imagePreview || currentImage" alt="Preview Foto"
                                            class="max-w-full h-48 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                        <button type="button" @click.stop="removeImage()"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400" x-show="imagePreview">
                                        Klik untuk mengganti gambar
                                    </p>
                                </div>

                                <!-- Upload Placeholder -->
                                <div x-show="!imagePreview && !currentImage" class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <span class="text-primary-600 dark:text-primary-400">Klik untuk upload</span>
                                            atau drag & drop
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            PNG, JPG, JPEG (Max. 2MB)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('foto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($santri && $santri->qr_code)
                        <!-- QR Code Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                QR Code
                            </label>
                            <div class="flex items-center gap-4">
                                <div
                                    class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-800">
                                    <img src="{{ asset('storage/asset_santri/qrcode/' . $santri->qr_code_file) }}"
                                        alt="QR Code" class="h-32 w-32">
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">Kode QR:</p>
                                    <p class="mt-1 font-mono text-sm text-gray-600 dark:text-gray-400">
                                        {{ $santri->qr_code }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <a href="{{ route('santri.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ $santri ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
