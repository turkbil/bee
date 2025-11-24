<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            <i class="fa-solid fa-gear mr-1"></i> Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            <i class="fa-solid fa-user-pen text-blue-600 dark:text-blue-400 mr-2"></i>
            Profil Bilgileri
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
            <!-- Profile Update -->
            <div class="profile-card bg-white dark:bg-gray-800 rounded-xl p-6">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/50 rounded-xl mr-4">
                        <i class="fa-solid fa-user-circle text-2xl text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Profil Bilgileri</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Hesap bilgilerinizi güncelleyebilirsiniz</p>
                    </div>
                </div>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>
</x-app-layout>