@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="{{ route('santri.index') }}" class="hover:text-primary-600">Data Santri</a></li>
                        <li><span class="mx-2">/</span></li>
                        <li class="font-medium text-gray-900 dark:text-white">Import</li>
                    </ol>
                </nav>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Import Data Santri</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload file Excel untuk menambahkan santri secara bulk</p>
            </div>
            <a href="{{ route('santri.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Livewire Import Component -->
        @livewire('santri-import')
    </div>
@endsection
