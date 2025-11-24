<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            <i class="fa-solid fa-gear mr-1"></i> Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            <i class="fa-solid fa-key text-yellow-600 dark:text-yellow-400 mr-2"></i>
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
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/50 rounded-xl mr-4">
                        <i class="fa-solid fa-lock text-2xl text-yellow-600 dark:text-yellow-400"></i>
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