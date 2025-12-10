<div class="relative" x-data="{ logoutModalOpen: false }">
    <!-- Logout Trigger Button -->
    <button
        type="button"
        @click="logoutModalOpen = true"
        class="flex items-center w-full gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
    >
        <span class="text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
        </span>
        Sign out
    </button>
    
    <!-- Logout Confirmation Modal (Inline) -->
    <div
        x-show="logoutModalOpen"
        style="display: none;"
        class="fixed inset-0 z-99999 flex items-start justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 backdrop-blur-sm p-4 sm:items-center"
    >
        <div 
            x-show="logoutModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800"
            @click.outside="logoutModalOpen = false"
        >
             <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 mb-5">
                <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
             </div>
             
             <div class="text-center">
                 <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-white">Konfirmasi Logout</h3>
                 <p class="text-gray-500 dark:text-gray-400 mb-6 text-theme-sm">Apakah Anda yakin ingin mengakhiri sesi ini? Anda harus login kembali untuk mengakses aplikasi.</p>
                 
                 <div class="flex justify-center gap-3">
                     <button @click="logoutModalOpen = false" class="px-5 py-2.5 font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition shadow-sm w-full">
                        Batal
                     </button>
                     <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full px-5 py-2.5 font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-200 dark:focus:ring-red-900 transition shadow-sm">
                            Ya, Logout
                        </button>
                     </form>
                 </div>
             </div>
        </div>
    </div>
</div>

