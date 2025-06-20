<div class="profile-card bg-white dark:bg-gray-800 rounded-xl overflow-hidden" 
     x-data="{ avatarUrl: '{{ $user->getFirstMedia('avatar') ? $user->getFirstMedia('avatar')->getUrl() . '?v=' . time() : '' }}' }"
     @avatar-updated.window="avatarUrl = $event.detail.url">
    <!-- User Info Section -->
    <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
        <div class="w-20 h-20 rounded-full mx-auto mb-4 border-4 border-primary-200 dark:border-primary-700 overflow-hidden">
            <div x-show="avatarUrl" class="w-full h-full">
                <img :src="avatarUrl" 
                     alt="{{ $user->name }}"
                     class="w-full h-full object-cover">
            </div>
            <div x-show="!avatarUrl" class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
            {{ $user->name }}
        </h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-3">
            {{ $user->email }}
        </p>
        <div class="flex flex-wrap gap-2 justify-center">
            @forelse($user->roles as $role)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                    {{ $role->name }}
                </span>
            @empty
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                    Kullanıcı
                </span>
            @endforelse
        </div>
        
        <!-- Profile Stats -->
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="text-center">
                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    @php
                        $diff = $user->created_at->diff(now());
                        echo $diff->days;
                    @endphp
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Gün</div>
            </div>
            <div class="text-center">
                <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $user->email_verified_at ? '✓' : '○' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Doğrulama</div>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div>
        <a href="{{ route('profile.edit') }}" class="profile-sidebar-item {{ request()->routeIs('profile.edit') ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Profil Bilgileri
        </a>
        <a href="{{ route('profile.avatar') }}" class="profile-sidebar-item {{ request()->routeIs('profile.avatar') ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Avatar Yönetimi
        </a>
        <a href="{{ route('profile.password') }}" class="profile-sidebar-item {{ request()->routeIs('profile.password') ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Şifre Değiştir
        </a>
        <a href="{{ route('profile.delete') }}" class="profile-sidebar-item danger {{ request()->routeIs('profile.delete') ? 'bg-red-50 text-red-600 dark:bg-red-900/50 dark:text-red-400' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hesabı Sil
        </a>
    </div>
</div>