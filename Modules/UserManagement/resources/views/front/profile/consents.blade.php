<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            Hesap Yönetimi
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            KVKK Onayları
        </h1>
    </x-slot>

    @push('scripts')
    <script>
        // Global modal functions for page content
        window.pageModalState = {
            show: false,
            title: '',
            content: '',
            loading: false
        };

        window.openPageModal = async function(pageId) {
            window.pageModalState.show = true;
            window.pageModalState.title = 'Yükleniyor...';
            window.pageModalState.content = '';
            window.pageModalState.loading = true;

            try {
                const response = await fetch(`/api/page-content/${pageId}`);
                const data = await response.json();

                if (data.success && data.data) {
                    window.pageModalState.title = data.data.title || 'Sayfa';
                    window.pageModalState.content = data.data.body || '<p class="text-gray-300">İçerik bulunamadı.</p>';
                } else {
                    window.pageModalState.title = 'Hata';
                    window.pageModalState.content = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
                }
            } catch (error) {
                console.error('Page content load error:', error);
                window.pageModalState.title = 'Hata';
                window.pageModalState.content = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
            } finally {
                window.pageModalState.loading = false;
            }

            // Trigger Alpine update
            if (window.Alpine) {
                window.Alpine.store('pageModal', window.pageModalState);
            }
        };

        window.closePageModal = function() {
            window.pageModalState.show = false;
            window.pageModalState.title = '';
            window.pageModalState.content = '';
        };
    </script>
    @endpush

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
                    <a href="{{ route('profile.avatar') }}" class="profile-sidebar-item">
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
                    <a href="{{ route('profile.consents') }}" class="profile-sidebar-item bg-primary-50 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400">
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
            <!-- Success Message -->
            @if (session('status') === 'consents-updated')
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-green-800 dark:text-green-200 font-medium">KVKK onaylarınız başarıyla güncellendi.</p>
                    </div>
                </div>
            @endif

            <!-- KVKK Consents Form -->
            <div class="profile-card bg-white dark:bg-gray-800 rounded-xl p-6">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">KVKK Onayları</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Kişisel verilerinizle ilgili onaylarınızı yönetin</p>
                    </div>
                </div>

                <!-- Current Consents Info -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Mevcut Onaylarınız</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Kullanım Koşulları ve Üyelik Sözleşmesi</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->terms_accepted ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $user->terms_accepted ? 'Onaylandı' : 'Onaylanmadı' }}
                            </span>
                        </div>
                        @if($user->terms_accepted && $user->terms_accepted_at)
                            <p class="text-xs text-gray-500 dark:text-gray-400 ml-4">
                                Onay Tarihi: {{ $user->terms_accepted_at->format('d.m.Y H:i') }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Üyelik ve Satın Alım Aydınlatma Metni</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->privacy_accepted ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $user->privacy_accepted ? 'Onaylandı' : 'Onaylanmadı' }}
                            </span>
                        </div>
                        @if($user->privacy_accepted && $user->privacy_accepted_at)
                            <p class="text-xs text-gray-500 dark:text-gray-400 ml-4">
                                Onay Tarihi: {{ $user->privacy_accepted_at->format('d.m.Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Marketing Consent Form -->
                <form method="POST" action="{{ route('profile.consents.update') }}">
                    @csrf

                    <div class="space-y-4">
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">Not:</p>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Kullanım Koşulları ve Aydınlatma Metni onayları zorunludur ve değiştirilemez. Sadece pazarlama onayınızı güncelleyebilirsiniz.</p>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-4">Ticari Elektronik İleti Gönderimi</h4>

                            <div class="space-y-3">
                                <!-- Radio 1: Rıza Göstermiyorum -->
                                <div class="flex items-start">
                                    <input
                                        type="radio"
                                        id="marketing_no"
                                        name="marketing_accepted"
                                        value="0"
                                        {{ !$user->marketing_accepted ? 'checked' : '' }}
                                        class="mt-1 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <label for="marketing_no" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                        <a href="javascript:void(0)" onclick="openPageModal(5)" class="text-blue-600 dark:text-blue-400 hover:underline">Ticari Elektronik İleti Gönderimi Aydınlatma Metni</a>'nde belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine <strong>açık rıza göstermediğimi beyan ederim</strong>.
                                    </label>
                                </div>

                                <!-- Radio 2: Rıza Veriyorum -->
                                <div class="flex items-start">
                                    <input
                                        type="radio"
                                        id="marketing_yes"
                                        name="marketing_accepted"
                                        value="1"
                                        {{ $user->marketing_accepted ? 'checked' : '' }}
                                        class="mt-1 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <label for="marketing_yes" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                        <a href="javascript:void(0)" onclick="openPageModal(5)" class="text-blue-600 dark:text-blue-400 hover:underline">Ticari Elektronik İleti Gönderimi Aydınlatma Metni</a>'nde belirtilen hususlar doğrultusunda, kişisel verilerimin ürün/hizmet ve stratejik pazarlama faaliyetleri ile ticari elektronik ileti gönderimi kapsamında işlenmesine <strong>açık rıza verdiğimi kabul ve beyan ederim</strong>.
                                    </label>
                                </div>
                            </div>

                            @if($user->marketing_accepted_at)
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                    Son Güncelleme: {{ $user->marketing_accepted_at->format('d.m.Y H:i') }}
                                </p>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end pt-4">
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Onayları Güncelle
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Ensure openPageModal is available
        if (typeof window.openPageModal !== 'function') {
            window.openPageModal = async function(pageId) {
                // Create modal if not exists
                let modal = document.getElementById('kvkkPageModal');
                if (!modal) {
                    modal = document.createElement('div');
                    modal.id = 'kvkkPageModal';
                    modal.innerHTML = `
                        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
                            <div class="relative w-full max-w-4xl max-h-[90vh] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="sticky top-0 z-10 flex items-center justify-between px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-700">
                                    <h3 class="text-2xl font-black text-white pr-8" id="modalTitle">Yükleniyor...</h3>
                                    <button onclick="closePageModal()" class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white transition-all duration-200 hover:scale-110">
                                        <i class="fa-solid fa-xmark text-xl"></i>
                                    </button>
                                </div>
                                <div class="overflow-y-auto max-h-[calc(90vh-140px)] px-8 pt-6 pb-24">
                                    <div id="modalLoading" class="flex items-center justify-center py-12">
                                        <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-600"></i>
                                    </div>
                                    <div id="modalContent" class="hidden text-gray-900 dark:text-white prose prose-lg max-w-none dark:prose-invert"></div>
                                </div>
                                <div class="sticky bottom-0 z-10 flex items-center justify-end px-8 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                                    <button onclick="closePageModal()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl font-bold text-white hover:shadow-lg hover:shadow-blue-600/30 transition-all duration-200 hover:scale-105">
                                        <i class="fa-solid fa-check mr-2"></i>
                                        Anladım
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                }

                modal.style.display = 'block';
                document.getElementById('modalLoading').classList.remove('hidden');
                document.getElementById('modalContent').classList.add('hidden');

                try {
                    const response = await fetch(`/api/page-content/${pageId}`);
                    const data = await response.json();

                    if (data.success && data.data) {
                        document.getElementById('modalTitle').textContent = data.data.title || 'Sayfa';
                        document.getElementById('modalContent').innerHTML = data.data.body || '<p class="text-gray-300">İçerik bulunamadı.</p>';
                    } else {
                        document.getElementById('modalTitle').textContent = 'Hata';
                        document.getElementById('modalContent').innerHTML = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
                    }
                } catch (error) {
                    console.error('Page content load error:', error);
                    document.getElementById('modalTitle').textContent = 'Hata';
                    document.getElementById('modalContent').innerHTML = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
                } finally {
                    document.getElementById('modalLoading').classList.add('hidden');
                    document.getElementById('modalContent').classList.remove('hidden');
                }
            };

            window.closePageModal = function() {
                const modal = document.getElementById('kvkkPageModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
