<div class="profile-sidebar bg-white dark:bg-gray-800 rounded-xl p-6 sticky top-6">
    <!-- User Info -->
    <div class="text-center mb-6">
        @if($user && $user->getFirstMedia('avatar'))
            <img src="{{ $user->getFirstMedia('avatar')->getUrl() }}?v={{ time() }}" 
                 alt="Avatar" 
                 class="w-20 h-20 rounded-full mx-auto mb-4 object-cover ring-4 ring-gray-100 dark:ring-gray-700">
        @else
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 ring-4 ring-gray-100 dark:ring-gray-700">
                {{ $user ? strtoupper(substr($user->name, 0, 2)) : 'U' }}
            </div>
        @endif
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user ? $user->name . ($user->surname ? ' ' . $user->surname : '') : 'User' }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user ? $user->email : '' }}</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="space-y-2">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <i class="fa-solid fa-user-pen w-5 mr-3 text-center"></i>
            Profil Bilgileri
        </a>

        <a href="{{ route('profile.avatar') }}"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.avatar') ? 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <i class="fa-solid fa-camera w-5 mr-3 text-center"></i>
            Avatar Yönetimi
        </a>

        <a href="{{ route('profile.password') }}"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.password') ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <i class="fa-solid fa-key w-5 mr-3 text-center"></i>
            Şifre Değiştir
        </a>

        <a href="{{ route('profile.delete') }}"
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.delete') ? 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400' : 'text-gray-700 hover:bg-red-50 hover:text-red-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-400' }}">
            <i class="fa-solid fa-trash-can w-5 mr-3 text-center"></i>
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