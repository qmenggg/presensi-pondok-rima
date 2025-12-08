@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Detail User</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi lengkap user</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('user.edit', $user->id) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('user.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                        </path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- User Card -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-6">
                <!-- Profile Section -->
                <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if ($user->foto)
                            <img src="{{ asset('storage/asset_user/foto/' . $user->foto) }}" alt="{{ $user->nama }}"
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700 shadow-lg">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar"
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700 shadow-lg">
                        @endif
                    </div>
                    
                    <!-- Basic Info -->
                    <div class="text-center sm:text-left flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->nama }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ '@' . $user->username }}</p>
                        
                        <div class="flex flex-wrap gap-2 mt-4 justify-center sm:justify-start">
                            <!-- Role Badge -->
                            @php
                                $roleColors = [
                                    'admin' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'pengasuh' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'pengurus' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'asatid' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                ];
                                $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full {{ $roleColor }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                {{ ucfirst($user->role) }}
                            </span>
                            
                            <!-- Status Badge -->
                            @if ($user->aktif)
                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    <span class="w-2 h-2 rounded-full bg-green-500 mr-1.5"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    <span class="w-2 h-2 rounded-full bg-red-500 mr-1.5"></span>
                                    Nonaktif
                                </span>
                            @endif
                            
                            <!-- Gender Badge -->
                            @if ($user->jenis_kelamin === 'L')
                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                    </svg>
                                    Laki-laki
                                </span>
                            @elseif ($user->jenis_kelamin === 'P')
                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full bg-pink-50 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                    </svg>
                                    Perempuan
                                </span>
                            @elseif ($user->jenis_kelamin === 'ALL')
                                <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                    Semua (Putra & Putri)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Detail Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6">
                    <!-- Username -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Username</h4>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user->username }}</p>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Nama Lengkap</h4>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ $user->nama }}</p>
                    </div>

                    <!-- Role -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Role</h4>
                        <p class="text-base font-medium text-gray-900 dark:text-white">{{ ucfirst($user->role) }}</p>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Jenis Kelamin</h4>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            @if ($user->jenis_kelamin === 'L')
                                Laki-laki
                            @elseif ($user->jenis_kelamin === 'P')
                                Perempuan
                            @elseif ($user->jenis_kelamin === 'ALL')
                                Semua (Putra & Putri)
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h4>
                        <p class="text-base font-medium {{ $user->aktif ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $user->aktif ? 'Aktif' : 'Nonaktif' }}
                        </p>
                    </div>

                    <!-- Created At -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Dibuat Pada</h4>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>

                    <!-- Updated At -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Terakhir Diupdate</h4>
                        <p class="text-base font-medium text-gray-900 dark:text-white">
                            {{ $user->updated_at ? $user->updated_at->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
