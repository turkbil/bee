<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            Profil Ayarları
        </h1>
    </x-slot>

    <x-profile-layout-styles />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
            @livewire('profile-sidebar')
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3">
            <!-- Profile Information -->
            <div class="profile-card bg-white dark:bg-gray-800 rounded-xl p-6">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Profil Bilgileri</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Hesap bilgilerinizi güncelleyin</p>
                    </div>
                </div>
                @include('profile.partials.update-profile-information-form')
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        // Profile edit page scripts
        console.log('Profile edit page loaded');
    </script>
    @endpush
</x-app-layout>