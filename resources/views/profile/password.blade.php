<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            Şifre Değiştir
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
            <!-- Password Update -->
            <div class="profile-card bg-white dark:bg-gray-800 rounded-xl p-6">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Şifre Değiştir</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Hesap güvenliğinizi korumak için güçlü bir şifre kullanın</p>
                    </div>
                </div>
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Password page scripts
        console.log('Password page loaded');
    </script>
    @endpush
</x-app-layout>