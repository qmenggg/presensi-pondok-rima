{{-- Submit Button Component with Loading State --}}
{{--
Usage:
<x-submit-button text="Simpan" loadingText="Menyimpan..." />
<x-submit-button text="Hapus" loadingText="Menghapus..." type="danger" />
--}}

@props([
    'text' => 'Simpan',
    'loadingText' => 'Memproses...',
    'type' => 'primary', // primary, danger, secondary
    'size' => 'md'
])

@php
$types = [
    'primary' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white',
    'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
    'secondary' => 'bg-gray-200 hover:bg-gray-300 focus:ring-gray-400 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
];
$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-base'
];
$typeClass = $types[$type] ?? $types['primary'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<button 
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => "inline-flex items-center justify-center gap-2 rounded-lg font-medium transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed $typeClass $sizeClass"
    ]) }}
    onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin w-4 h-4\' xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> {{ $loadingText }}'; this.form.submit();"
>
    {{ $text }}
</button>
