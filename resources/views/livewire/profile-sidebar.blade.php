<div class="profile-sidebar bg-white dark:bg-gray-800 rounded-xl p-6 sticky top-6">
    <!-- User Info -->
    <div class="text-center mb-6">
        @if($user && $user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" 
                 alt="Avatar" 
                 class="w-20 h-20 rounded-full mx-auto mb-4 object-cover ring-4 ring-gray-100 dark:ring-gray-700">
        @else
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 ring-4 ring-gray-100 dark:ring-gray-700">
                {{ $user ? strtoupper(substr($user->name, 0, 2)) : 'U' }}
            </div>
        @endif
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user ? $user->name : 'User' }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user ? $user->email : '' }}</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="space-y-2">
        <a href="{{ route('profile.edit') }}" 
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profil Bilgileri
        </a>

        <a href="{{ route('profile.avatar') }}" 
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.avatar') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Avatar Yönetimi
        </a>

        <a href="{{ route('profile.password') }}" 
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.password') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Şifre Değiştir
        </a>

        <a href="{{ route('profile.delete') }}" 
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.delete') ? 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400' : 'text-gray-700 hover:bg-red-50 hover:text-red-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-400' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hesap Sil
        </a>
    </nav>

    <!-- Account Stats -->
    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="mb-3">
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Üyelik</div>
                <div class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                    {{ $user && $user->created_at ? \Carbon\Carbon::parse($user->created_at)->diffForHumans() : 'Bilinmiyor' }}
                </div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Son Giriş</div>
                <div class="text-lg font-semibold text-green-600 dark:text-green-400">
                    {{ $user && $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'İlk kez' }}
                </div>
            </div>
        </div>
    </div>
</div>