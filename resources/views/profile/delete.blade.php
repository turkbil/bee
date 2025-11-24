<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            <i class="fa-solid fa-gear mr-1"></i> Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">
            <i class="fa-solid fa-trash-can mr-2"></i>
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
                    <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-xl mr-4">
                        <i class="fa-solid fa-exclamation-triangle text-2xl text-red-600 dark:text-red-400"></i>
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