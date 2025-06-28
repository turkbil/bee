<div>
    @auth
        {{-- AUTH USER MENU --}}
        @livewire('header-user-dropdown')
    @else
        {{-- GUEST MENU --}}
        <a href="{{ route('login') }}"
            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">
            Giriş Yap
        </a>
        
        @if (Route::has('register'))
        <a href="{{ route('register') }}"
            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">
            Kayıt Ol
        </a>
        @endif
    @endauth
</div>