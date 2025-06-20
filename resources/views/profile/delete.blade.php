<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">
            Hesabı Sil
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
            <!-- Delete Account -->
            <div class="profile-card bg-white dark:bg-gray-800 rounded-xl p-6 border border-red-200 dark:border-red-800">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">Tehlikeli Bölge</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bu işlem geri alınamaz</p>
                    </div>
                </div>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Delete account page scripts
        console.log('Delete account page loaded');
    </script>
    @endpush
</x-app-layout>