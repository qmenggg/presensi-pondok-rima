{{-- Confirm Modal Component --}}
{{-- 
Usage: 
<x-confirm-modal 
    id="deleteModal" 
    title="Hapus Data" 
    message="Yakin ingin menghapus data ini?"
    confirmText="Hapus"
    type="danger"
/>

Then call: showConfirmModal('deleteModal', () => { document.getElementById('deleteForm').submit(); })
--}}

@props([
    'id' => 'confirmModal',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Ya',
    'cancelText' => 'Batal',
    'type' => 'danger' // danger, warning, info
])

@php
$colors = [
    'danger' => [
        'icon' => 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400',
        'button' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    ],
    'warning' => [
        'icon' => 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/30 dark:text-yellow-400',
        'button' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
    ],
    'info' => [
        'icon' => 'text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400',
        'button' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
    ]
];
$color = $colors[$type] ?? $colors['danger'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-[99999] hidden overflow-y-auto" aria-modal="true">
    <div class="flex min-h-screen items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="hideConfirmModal('{{ $id }}')"></div>
        
        {{-- Modal --}}
        <div class="relative w-full max-w-md transform rounded-2xl bg-white p-6 shadow-2xl transition-all dark:bg-gray-800">
            <div class="flex items-start gap-4">
                {{-- Icon --}}
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full {{ $color['icon'] }}">
                    @if($type === 'danger')
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    @elseif($type === 'warning')
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @else
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                </div>
                
                {{-- Content --}}
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $message }}</p>
                </div>
            </div>
            
            {{-- Actions --}}
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="hideConfirmModal('{{ $id }}')"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ $cancelText }}
                </button>
                <button type="button" id="{{ $id }}-confirm-btn"
                    class="rounded-lg px-4 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $color['button'] }}">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
let confirmCallbacks = {};

function showConfirmModal(modalId, onConfirm) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        const confirmBtn = document.getElementById(modalId + '-confirm-btn');
        if (confirmBtn && onConfirm) {
            confirmCallbacks[modalId] = onConfirm;
            confirmBtn.onclick = () => {
                onConfirm();
                hideConfirmModal(modalId);
            };
        }
    }
}

function hideConfirmModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        delete confirmCallbacks[modalId];
    }
}

// Close on ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]:not(.hidden)').forEach(modal => {
            hideConfirmModal(modal.id);
        });
    }
});
</script>
@endpush
@endonce
