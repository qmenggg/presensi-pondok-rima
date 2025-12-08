@extends('layouts.app')

@section('content')
    <div class="space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">User Management</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola pengguna sistem</p>
            </div>
            <a href="{{ route('user.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah</span>
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <!-- Table Card -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Search -->
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Daftar User</h3>
                <form method="GET" action="{{ route('user.index') }}" class="relative w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/username..." 
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-4 pr-10 text-sm placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-[220px]"/>
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($users as $user)
                    @php
                        $roleColors = [
                            'admin' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            'pengasuh' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                            'pengurus' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                            'asatid' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                        ];
                        $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <div class="flex items-center gap-3">
                            <!-- Foto -->
                            <div class="flex-shrink-0">
                                @if($user->foto)
                                    <img src="{{ asset('storage/asset_user/foto/' . $user->foto) }}" alt="Foto" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $user->nama }}</p>
                                    @if($user->aktif)
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->username }}</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="px-2 py-0.5 text-[10px] font-medium rounded-full {{ $roleColor }}">{{ ucfirst($user->role) }}</span>
                                    @if($user->jenis_kelamin === 'L')
                                        <span class="text-[10px] text-blue-600 dark:text-blue-400">♂ L</span>
                                    @elseif($user->jenis_kelamin === 'P')
                                        <span class="text-[10px] text-pink-600 dark:text-pink-400">♀ P</span>
                                    @elseif($user->jenis_kelamin === 'ALL')
                                        <span class="text-[10px] text-purple-600 dark:text-purple-400">All</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col gap-1">
                                <a href="{{ route('user.show', $user->id) }}" class="p-1.5 text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('user.edit', $user->id) }}" class="p-1.5 text-blue-600 bg-blue-50 rounded-lg dark:bg-blue-900/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('user.toggle-status', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded-lg {{ $user->aktif ? 'text-orange-600 bg-orange-50 dark:bg-orange-900/20' : 'text-green-600 bg-green-50 dark:bg-green-900/20' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p>Tidak ada data user</p>
                        <a href="{{ route('user.create') }}" class="text-primary-600 text-sm mt-2 inline-block">+ Tambah User Baru</a>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300 w-12">No</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">User</th>
                            <th class="hidden lg:table-cell px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Username</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Role</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-600 dark:text-gray-300 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $index => $user)
                            @php
                                $roleColors = [
                                    'admin' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'pengasuh' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    'pengurus' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'asatid' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                ];
                                $roleColor = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $users->firstItem() + $index }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($user->foto)
                                            <img src="{{ asset('storage/asset_user/foto/' . $user->foto) }}" alt="" class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-900 dark:text-white/90">{{ $user->nama }}</span>
                                    </div>
                                </td>
                                <td class="hidden lg:table-cell px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $user->username }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $roleColor }}">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="hidden md:table-cell px-4 py-3">
                                    @if($user->aktif)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('user.show', $user->id) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 dark:text-gray-400 rounded-lg" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('user.edit', $user->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 dark:text-blue-400 rounded-lg" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('user.toggle-status', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="p-1.5 rounded-lg {{ $user->aktif ? 'text-orange-600 hover:bg-orange-50 dark:text-orange-400' : 'text-green-600 hover:bg-green-50 dark:text-green-400' }}" title="{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
