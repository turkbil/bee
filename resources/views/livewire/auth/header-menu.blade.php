<div>
    @auth
        {{-- AUTH USER MENU --}}
        @livewire('header-user-dropdown')
    @else
        {{-- GUEST MENU - MODERN DROPDOWN --}}
        <div class="relative z-[300]" x-data="{ open: false }">
            <button @click="open = !open"
                    aria-label="Kullanıcı menüsünü aç"
                    class="flex items-center justify-center w-10 h-10 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors duration-300 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </button>
            
            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-gray-900 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-1 z-[300]">
                
                <a href="{{ route('login') }}"
                   class="w-full flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 rounded-lg mx-1">
                    <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="flex-1 font-medium">Giriş Yap</span>
                </a>

                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="w-full flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 dark:hover:from-green-900/20 dark:hover:to-emerald-900/20 hover:text-green-600 dark:hover:text-green-400 transition-all duration-300 rounded-lg mx-1">
                    <svg class="w-5 h-5 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span class="flex-1 font-medium">Kayıt Ol</span>
                </a>
                @endif
                
                <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                
                <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">
                    Hesabınız yok mu? Hemen ücretsiz kayıt olun!
                </div>
            </div>
        </div>
    @endauth
</div>