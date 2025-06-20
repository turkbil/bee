<x-app-layout>
    <x-slot name="header">
        <div class="text-gray-500 dark:text-gray-400 text-sm">
            {{ tenant('title') ?? config('app.name') }}
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">
            Hoş Geldiniz, {{ auth()->user()->name }}!
        </h1>
    </x-slot>

    @push('styles')
    <style>
        .dashboard-card {
            border: 1px solid rgb(229 231 235);
        }
        .dark .dashboard-card {
            border-color: rgb(55 65 81);
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .progress-ring-circle {
            stroke-dasharray: 251.2;
            stroke-dashoffset: 62.8;
            transition: stroke-dashoffset 0.35s;
        }
    </style>
    @endpush

    <!-- Welcome Section -->
    <div class="dashboard-card bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-xl p-8 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Sisteme Hoş Geldiniz!</h2>
                <p class="text-primary-100 mb-4">
                    {{ tenant('title') ?? config('app.name') }} platformunda başarıyla giriş yaptınız.
                </p>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        <span class="text-sm">Profil %85 Tamamlandı</span>
                    </div>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="relative">
                    <svg class="progress-ring w-24 h-24" viewBox="0 0 84 84">
                        <circle cx="42" cy="42" r="40" stroke="rgba(255,255,255,0.3)" stroke-width="4" fill="none"/>
                        <circle class="progress-ring-circle" cx="42" cy="42" r="40" stroke="white" stroke-width="4" fill="none"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold">85%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Account Status -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if(auth()->user()->email_verified_at)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Doğrulanmış
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        Beklemede
                    </span>
                @endif
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Hesap Durumu</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ auth()->user()->email_verified_at ? 'Aktif' : 'Pasif' }}
            </p>
        </div>

        <!-- Membership Duration -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ auth()->user()->created_at->format('d.m.Y') }}
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Üyelik Süresi</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                @php
                    $createdAt = auth()->user()->created_at;
                    $now = now();
                    $diff = $createdAt->diff($now);
                    $days = $diff->days;
                    $hours = $diff->h;
                    $minutes = $diff->i;
                    
                    // Anlaşılır format oluştur
                    if ($days > 0) {
                        $timeText = $days . ' gün';
                        if ($hours > 0) {
                            $timeText .= ', ' . $hours . ' saat';
                        }
                    } elseif ($hours > 0) {
                        $timeText = $hours . ' saat';
                        if ($minutes > 0) {
                            $timeText .= ', ' . $minutes . ' dakika';
                        }
                    } else {
                        $timeText = $minutes . ' dakika';
                    }
                @endphp
                {{ $timeText }}
            </p>
        </div>

        <!-- User Role -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                @forelse(auth()->user()->roles as $role)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        {{ $role->name }}
                    </span>
                @empty
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                        Kullanıcı
                    </span>
                @endforelse
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Rol</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                @forelse(auth()->user()->roles as $role)
                    {{ ucfirst($role->name) }}
                @empty
                    Kullanıcı
                @endforelse
            </p>
        </div>

        <!-- Last Activity -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                    {{ request()->ip() }}
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Bağlantı</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Aktif</p>
        </div>
    </div>

    <!-- Quick Actions & Account Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-2">
            <div class="dashboard-card bg-white dark:bg-gray-800 rounded-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Hızlı İşlemler</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('profile.edit') }}" class="group p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-300 dark:hover:border-primary-600 transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3 group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition-colors">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">Profil Düzenle</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ad, email ve kişisel bilgiler</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('profile.password') }}" class="group p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-300 dark:hover:border-primary-600 transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg mr-3 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-800 transition-colors">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2h-2m-2 2v6m0 0v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2m2-2h4m-4 0V9a2 2 0 012-2h4a2 2 0 012 2v2"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">Şifre Değiştir</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Hesap güvenliğini artır</p>
                            </div>
                        </div>
                    </a>

                    @if(auth()->user()->hasAnyRole(['admin', 'root', 'editor']))
                    <a href="{{ route('admin.dashboard') }}" class="group p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-300 dark:hover:border-primary-600 transition-colors">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-3 group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-colors">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">Yönetim Paneli</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Site yönetimi ve içerik</p>
                            </div>
                        </div>
                    </a>
                    @endif

                    <button onclick="checkForUpdates()" class="group p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-primary-300 dark:hover:border-primary-600 transition-colors text-left">
                        <div class="flex items-center">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg mr-3 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800 transition-colors">
                                <svg id="update-icon" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <div>
                                <h4 id="update-title" class="font-medium text-gray-900 dark:text-gray-100">Güncellemeleri Kontrol Et</h4>
                                <p id="update-desc" class="text-sm text-gray-500 dark:text-gray-400">Sistem güncellemelerini kontrol et</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Account Summary -->
        <div class="dashboard-card bg-white dark:bg-gray-800 rounded-lg p-6">
            <div class="flex items-center mb-6">
                <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Hesap Özeti</h3>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Kullanıcı ID</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">#{{ auth()->user()->id }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->email }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Telefon</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->phone ?? 'Belirtilmemiş' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Kayıt Tarihi</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->created_at->format('d.m.Y') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Son Güncelleme</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->updated_at->format('d.m.Y') }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">IP Adresi</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">{{ request()->ip() }}</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function checkForUpdates() {
            const button = event.target.closest('button');
            const icon = document.getElementById('update-icon');
            const title = document.getElementById('update-title');
            const desc = document.getElementById('update-desc');
            
            // Loading state
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>';
            icon.classList.add('animate-spin');
            title.textContent = 'Kontrol Ediliyor...';
            desc.textContent = 'Güncellemeler aranıyor';
            button.disabled = true;
            
            // Simulate update check
            setTimeout(() => {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                icon.classList.remove('animate-spin');
                title.textContent = 'Güncel';
                desc.textContent = 'Tüm sistemler güncel';
                
                setTimeout(() => {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>';
                    title.textContent = 'Güncellemeleri Kontrol Et';
                    desc.textContent = 'Sistem güncellemelerini kontrol et';
                    button.disabled = false;
                }, 3000);
            }, 2000);
        }
    </script>
    @endpush
</x-app-layout>