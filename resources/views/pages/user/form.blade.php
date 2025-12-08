@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    {{ $user ? 'Edit User' : 'Tambah User' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $user ? 'Ubah data user' : 'Tambahkan user baru ke sistem' }}
                </p>
            </div>
            <a href="{{ route('user.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <x-common.component-card title="{{ $user ? 'Form Edit User' : 'Form Tambah User' }}">
            <form action="{{ $user ? route('user.update', $user->id) : route('user.store') }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if ($user)
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
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Informasi User</h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" name="username"
                                value="{{ old('username', $user ? $user->username : '') }}"
                                required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }} 
                                @if (!$user)<span class="text-red-500">*</span>@endif
                            </label>
                            <input type="password" id="password" name="password" {{ $user ? '' : 'required' }}
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
                                value="{{ old('nama', $user ? $user->nama : '') }}" required
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
                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">(Pilih "Semua" untuk pengurus yang mengelola putra & putri)</span>
                            </label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L"
                                    {{ old('jenis_kelamin', $user ? $user->jenis_kelamin : '') === 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P"
                                    {{ old('jenis_kelamin', $user ? $user->jenis_kelamin : '') === 'P' ? 'selected' : '' }}>
                                    Perempuan</option>
                                <option value="ALL"
                                    {{ old('jenis_kelamin', $user ? $user->jenis_kelamin : '') === 'ALL' ? 'selected' : '' }}>
                                    Semua (Putra & Putri)</option>
                            </select>
                            @error('jenis_kelamin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Role <span class="text-red-500">*</span>
                            @if ($user && $user->role === 'admin')
                                <span class="ml-2 text-xs text-orange-600 dark:text-orange-400">(Role admin tidak dapat diubah)</span>
                            @endif
                        </label>
                        <select id="role" name="role" required
                            {{ $user && $user->role === 'admin' ? 'disabled' : '' }}
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-primary-500 {{ $user && $user->role === 'admin' ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : '' }}">
                            <option value="">Pilih Role</option>
                            <option value="admin"
                                {{ old('role', $user ? $user->role : '') === 'admin' ? 'selected' : '' }}>
                                Admin</option>
                            <option value="pengasuh"
                                {{ old('role', $user ? $user->role : '') === 'pengasuh' ? 'selected' : '' }}>
                                Pengasuh</option>
                            <option value="pengurus"
                                {{ old('role', $user ? $user->role : '') === 'pengurus' ? 'selected' : '' }}>
                                Pengurus</option>
                            <option value="asatid"
                                {{ old('role', $user ? $user->role : '') === 'asatid' ? 'selected' : '' }}>
                                Asatid</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto -->
                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Foto (Opsional)
                        </label>

                        <!-- Image Upload Area -->
                        <div x-data="{
                            imagePreview: null,
                            currentImage: '{{ $user && $user->foto ? asset('storage/asset_user/foto/' . $user->foto) : null }}',
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
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-800 pt-6">
                    <a href="{{ route('user.index') }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ $user ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
