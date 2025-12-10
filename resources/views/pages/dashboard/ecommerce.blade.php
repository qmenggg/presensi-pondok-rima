@extends('layouts.app')

@php
    // Data dari controller
    $totalHadir = $stats['absensi']['hadir'] ?? 0;
    $totalIzin = $stats['absensi']['izin'] ?? 0;
    $totalSakit = $stats['absensi']['sakit'] ?? 0;
    $totalAlfa = $stats['absensi']['alpha'] ?? 0;
    $totalAbsensi = $totalHadir + $totalIzin + $totalSakit + $totalAlfa;
    $persentaseKehadiran = $stats['absensi']['persentase_hadir'] ?? 0;
    
    // Placeholder untuk santri bermasalah (akan diimplementasikan nanti)
    $santriBermasalah = [];
    
    // Pagination untuk jadwal
    $perPage = 5;
    $currentPage = request()->get('page', 1);
    $totalJadwal = $jadwalHariIni->count();
    $totalPages = ceil($totalJadwal / $perPage);
    $jadwalPaginated = $jadwalHariIni->slice(($currentPage - 1) * $perPage, $perPage);
@endphp

@section('content')
    <div class="space-y-6 md:space-y-8">
        <!-- Header Dashboard -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Dashboard Ringkasan</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $today }}</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <!-- Container Utama -->
        <div class="flex flex-col gap-4 p-4">

            <!-- Kartu Persentase Kehadiran (Memenuhi Lebar) -->
            <div
                class="group w-full bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6 text-white">
                <div class="flex flex-col items-center text-center sm:flex-row sm:items-center sm:text-left">
                    <div
                        class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-white/20 backdrop-blur-sm mb-4 sm:mb-0 sm:mr-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-purple-100">Persentase Kehadiran</p>
                        <h4 class="text-3xl font-bold mt-1">
                            {{ $persentaseKehadiran }}%
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Grid untuk 4 Kartu Lainnya (2 per baris) -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Total Hadir -->
                <div
                    class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-green-100 dark:bg-green-900 mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-7 w-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Hadir</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $totalHadir }}
                        </h4>
                    </div>
                </div>

                <!-- Total Izin -->
                <div
                    class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900 mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-7 w-7 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Izin</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $totalIzin }}
                        </h4>
                    </div>
                </div>

                <!-- Total Sakit -->
                <div
                    class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900 mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Sakit</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $totalSakit }}
                        </h4>
                    </div>
                </div>

                <!-- Total Alfa -->
                <div
                    class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out p-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900 mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="h-7 w-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Alfa</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $totalAlfa }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>


        <!-- Jadwal Kegiatan -->
        <x-common.component-card title="Jadwal / Kegiatan Hari Ini">
            <div class="overflow-hidden">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-4 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Nama Kegiatan</p>
                                </th>
                                <th class="px-4 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Jam</p>
                                </th>
                                <th class="px-4 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Lokasi</p>
                                </th>
                                <th class="px-4 py-3 text-left sm:px-6">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwalPaginated as $jadwal)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-4 py-4 sm:px-6">
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            {{ $jadwal->nama_sub_kegiatan }}</p>
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $jadwal->kegiatan->nama_kegiatan ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        @php
                                            $statusClasses = [
                                                'belum_mulai' => 'bg-gray-50 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400',
                                                'berlangsung' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-500',
                                                'selesai' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
                                            ];
                                            $statusLabels = [
                                                'belum_mulai' => 'Belum Mulai',
                                                'berlangsung' => 'Berlangsung',
                                                'selesai' => 'Selesai',
                                            ];
                                            $statusKey = $jadwal->status_jadwal ?? 'belum_mulai';
                                        @endphp
                                        <span class="inline-block rounded-full px-2 py-0.5 text-theme-xs font-medium {{ $statusClasses[$statusKey] }}">{{ $statusLabels[$statusKey] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada jadwal kegiatan hari ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($totalPages > 1)
                    <div class="flex items-center justify-between border-t border-gray-100 dark:border-gray-800 px-4 py-3 sm:px-6">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Menampilkan {{ (($currentPage - 1) * $perPage) + 1 }} - {{ min($currentPage * $perPage, $totalJadwal) }} dari {{ $totalJadwal }} kegiatan
                        </div>
                        <div class="flex gap-2">
                            @if($currentPage > 1)
                                <a href="?page={{ $currentPage - 1 }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700">
                                    Sebelumnya
                                </a>
                            @endif
                            @if($currentPage < $totalPages)
                                <a href="?page={{ $currentPage + 1 }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700">
                                    Selanjutnya
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </x-common.component-card>

        <!-- Grafik & Santri Bermasalah -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 md:gap-8">
            <!-- Grafik Absensi -->
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 sm:px-6 sm:pt-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                            Statistik Absensi
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if ($role === 'admin' || $role === 'pengasuh')
                                Data kehadiran seluruh pondok
                            @elseif($role === 'pengurus')
                                Data santri (Putra/Putri sesuai gender)
                            @elseif($role === 'asatid')
                                Data kelas yang diajar
                            @endif
                        </p>
    </div>
    </div>


                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <div class="-ml-5 min-w-[690px] pl-2 xl:min-w-full">
                        <div id="chartOne"></div>
                    </div>
                </div>
    </div>

            <!-- Daftar Santri Bermasalah -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <!-- Card Header -->
                <div class="px-6 py-5 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                            Daftar Santri Bermasalah
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            List santri yang perlu perhatian khusus
                        </p>
                    </div>
                    <div class="shrink-0">
                        @include('components.common.santri-filter-dropdown')
                    </div>
                </div>
                <!-- Card Body -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <div class="space-y-6">
                        <div id="santri-list-container" class="space-y-3">
                            @if(count($santriBermasalah) > 0)
                                @foreach($santriBermasalah as $santri)
                                    <div class="santri-item group flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-4 transition-all duration-300 hover:border-red-300 hover:bg-red-50/50 hover:shadow-md dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-red-800 dark:hover:bg-red-500/10"
                                         data-gender="putra">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $santri['foto'] }}" alt="{{ $santri['nama'] }}"
                                                 class="h-12 w-12 rounded-full border-2 border-white shadow-sm dark:border-gray-800">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90 truncate">
                                                {{ $santri['nama'] }}
                                            </p>
                                            <p class="text-gray-500 text-theme-xs dark:text-gray-400 mt-0.5">
                                                {{ $santri['kamar'] }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="inline-block rounded-full px-3 py-1 text-theme-xs font-medium bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500">
                                                {{ $santri['status'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="py-8 text-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">Tidak ada santri bermasalah</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>




    </div>

        <!-- Quick Action -->
        @if ($role === 'admin')
            <x-common.component-card title="Quick Action">
                <!-- Grid Container: 2 kolom di mobile, 4 kolom di web -->
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <!-- Card Kelola Santri -->
                    <a href="{{ route('santri.index') }}"
                        class="group flex flex-col items-center justify-center gap-3 rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-lg transition-all duration-300 ease-in-out hover:-translate-y-1 hover:border-primary-500 hover:bg-primary-50 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-primary-500/10">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 transition-transform duration-300 group-hover:scale-110 dark:bg-primary-500/20">
                            <svg class="fill-primary-600 dark:fill-primary-400" width="24" height="24"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 5C13.66 5 15 6.34 15 8C15 9.66 13.66 11 12 11C10.34 11 9 9.66 9 8C9 6.34 10.34 5 12 5ZM12 19.2C9.5 19.2 7.29 17.92 6 15.98C6.03 13.99 10 12.9 12 12.9C13.99 12.9 17.97 13.99 18 15.98C16.71 17.92 14.5 19.2 12 19.2Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-theme-sm dark:text-white/90">Kelola Santri</h4>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tambah, edit, hapus data santri</p>
                        </div>
                    </a>

                    <!-- Card Kelola Kegiatan -->
                    <a href="{{ route('kegiatan.index') }}"
                        class="group flex flex-col items-center justify-center gap-3 rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-lg transition-all duration-300 ease-in-out hover:-translate-y-1 hover:border-primary-500 hover:bg-primary-50 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-primary-500/10">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 transition-transform duration-300 group-hover:scale-110 dark:bg-primary-500/20">
                            <svg class="fill-primary-600 dark:fill-primary-400" width="24" height="24"
                                viewBox="0 0 24 24">
                                <path
                                    d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V8H19V19ZM7 10H9V12H7V10ZM11 10H13V12H11V10ZM15 10H17V12H15V10ZM7 14H9V16H7V14ZM11 14H13V16H11V14ZM15 14H17V16H15V14Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-theme-sm dark:text-white/90">Kelola Kegiatan</h4>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Atur jadwal kegiatan pondok</p>
                        </div>
                    </a>

                    <!-- Card Kelola User -->
                    <a href="{{ route('user.index') }}"
                        class="group flex flex-col items-center justify-center gap-3 rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-lg transition-all duration-300 ease-in-out hover:-translate-y-1 hover:border-primary-500 hover:bg-primary-50 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-primary-500/10">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 transition-transform duration-300 group-hover:scale-110 dark:bg-primary-500/20">
                            <svg class="fill-primary-600 dark:fill-primary-400" width="24" height="24"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-theme-sm dark:text-white/90">Kelola User</h4>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Manajemen pengguna sistem</p>
                        </div>
                    </a>

                    <!-- Card Kelola Kelas/Kamar -->
                    <a href="{{ route('kamar.index') }}"
                        class="group flex flex-col items-center justify-center gap-3 rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-lg transition-all duration-300 ease-in-out hover:-translate-y-1 hover:border-primary-500 hover:bg-primary-50 hover:shadow-xl dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-primary-500/10">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-100 transition-transform duration-300 group-hover:scale-110 dark:bg-primary-500/20">
                            <svg class="fill-primary-600 dark:fill-primary-400" width="24" height="24"
                                viewBox="0 0 24 24">
                                <path
                                    d="M4 6H2V20C2 21.1 2.9 22 4 22H18V20H4V6ZM20 2H8C6.9 2 6 2.9 6 4V16C6 17.1 6.9 18 8 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H8V4H20V16ZM12 5.5V14.5L17 10L12 5.5Z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 text-theme-sm dark:text-white/90">Kelola Kelas/Kamar
                            </h4>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Atur kelas dan kamar santri</p>
                        </div>
                    </a>
                </div>
            </x-common.component-card>
        @endif
  </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Filter santri bermasalah
            const dropdown = document.getElementById('filter-gender');
            const items = document.querySelectorAll('.santri-item');

            if (dropdown) {
                dropdown.addEventListener('change', () => {
                    const filter = dropdown.value;

                    items.forEach(item => {
                        const gender = item.getAttribute('data-gender');

                    if (filter === 'all' || filter === gender) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });
        });
    </script>
@endpush
