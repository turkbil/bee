<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            Avatar Yönetimi
        </h1>
    </x-slot>

    @push('styles')
    <style>
        .profile-card {
            border: 1px solid rgb(229 231 235);
        }
        .dark .profile-card {
            border-color: rgb(55 65 81);
        }
        .profile-sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid rgb(229 231 235);
            transition: background-color 0.2s ease, color 0.2s ease;
            color: rgb(75 85 99);
            text-decoration: none;
        }
        .profile-sidebar-item:hover {
            background-color: rgb(249 250 251);
            color: rgb(59 130 246);
        }
        .dark .profile-sidebar-item {
            border-bottom-color: rgb(55 65 81);
            color: rgb(156 163 175);
        }
        .dark .profile-sidebar-item:hover {
            background-color: rgb(31 41 55);
            color: rgb(96 165 250);
        }
        .profile-sidebar-item.danger:hover {
            background-color: rgb(254 242 242);
            color: rgb(239 68 68);
        }
        .dark .profile-sidebar-item.danger:hover {
            background-color: rgb(69 10 10);
            color: rgb(248 113 113);
        }
        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgb(229 231 235);
        }
        .dark .avatar-preview {
            border-color: rgb(55 65 81);
        }
    </style>
    @endpush

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            <div class="profile-card bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                <!-- User Info Section -->
                <div class="p-6 text-center border-b border-gray-200 dark:border-gray-700">
                    <div class="avatar-container mb-4">
                        @if($user->getFirstMedia('avatar'))
                            <img src="{{ $user->getFirstMedia('avatar')->getUrl() }}" 
                                 alt="Avatar" 
                                 class="avatar-preview mx-auto">
                        @else
                            <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                        @endif
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
                </div>

                <!-- Navigation Menu -->
                <div>
                    <a href="{{ route('profile.edit') }}" class="profile-sidebar-item">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Bilgileri
                    </a>
                    <a href="{{ route('profile.avatar') }}" class="profile-sidebar-item bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Avatar Yönetimi
                    </a>
                    <a href="{{ route('profile.password') }}" class="profile-sidebar-item">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2h-2m-2 2v6m0 0v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2m2-2h4m-4 0V9a2 2 0 012-2h4a2 2 0 012 2v2"/>
                        </svg>
                        Şifre Değiştir
                    </a>
                    <a href="{{ route('profile.consents') }}" class="profile-sidebar-item">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        KVKK Onayları
                    </a>
                    <a href="{{ route('profile.delete') }}" class="profile-sidebar-item danger">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hesabı Sil
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3">
            <!-- Avatar Upload -->
            <div class="profile-card bg-white dark:bg-gray-800 rounded-xl p-6">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Avatar Yönetimi</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Profil fotoğrafınızı yükleyin veya güncelleyin</p>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <p class="text-blue-800 dark:text-blue-200">Avatar yönetimi sistemi yükleniyor...</p>
                    <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">Kullanıcı: {{ $user->name }}</p>
                </div>
                
                @livewire('usermanagement.avatar-upload', ['user' => $user])
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Avatar page scripts
        console.log('Avatar page loaded');
        
        // Listen for success messages
        window.addEventListener('show-success', event => {
            // Simple alert for now - could be replaced with toast notification
            alert(event.detail.message);
            
            // Refresh the page to show updated avatar in sidebar
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
        
        // Listen for avatar updated event to refresh sidebar
        window.addEventListener('livewire:load', function () {
            Livewire.on('avatarUpdated', () => {
                // Refresh the avatar in sidebar by reloading the page
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        });
    </script>
    @endpush
</x-app-layout>