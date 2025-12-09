{{-- Global Toast Notifications --}}
{{-- Position: Top-right on desktop, top-center on mobile --}}
<div x-data="{ 
    show: {{ session('success') || session('error') || session('warning') || session('info') ? 'true' : 'false' }},
    type: '{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info')) }}',
    message: '{{ session('success') ?? session('error') ?? session('warning') ?? session('info') ?? '' }}',
    init() {
        if (this.show) {
            setTimeout(() => this.show = false, 4000);
        }
    }
}"
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="show = false"
    class="fixed z-50 cursor-pointer
           top-20 left-1/2 -translate-x-1/2
           sm:top-6 sm:right-6 sm:left-auto sm:translate-x-0"
    x-cloak>
    
    {{-- Success Toast --}}
    <template x-if="type === 'success'">
        <div class="flex items-center gap-3 px-4 py-3 bg-green-500 text-white rounded-xl shadow-lg min-w-[280px] max-w-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="text-sm font-medium" x-text="message"></span>
        </div>
    </template>
    
    {{-- Error Toast --}}
    <template x-if="type === 'error'">
        <div class="flex items-center gap-3 px-4 py-3 bg-red-500 text-white rounded-xl shadow-lg min-w-[280px] max-w-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span class="text-sm font-medium" x-text="message"></span>
        </div>
    </template>
    
    {{-- Warning Toast --}}
    <template x-if="type === 'warning'">
        <div class="flex items-center gap-3 px-4 py-3 bg-yellow-500 text-white rounded-xl shadow-lg min-w-[280px] max-w-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="text-sm font-medium" x-text="message"></span>
        </div>
    </template>
    
    {{-- Info Toast --}}
    <template x-if="type === 'info'">
        <div class="flex items-center gap-3 px-4 py-3 bg-blue-500 text-white rounded-xl shadow-lg min-w-[280px] max-w-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium" x-text="message"></span>
        </div>
    </template>
</div>
