@extends('themes.muzibu.layouts.app')

@section('title', 'Dashboard - Muzibu')

@php
    $user = auth()->user();
    $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
    $access = $subscriptionService->checkUserAccess($user);
    $isTrial = $access['is_trial'] ?? false;
    $isPremiumOrTrial = $user->isPremiumOrTrial();
    $expiresAt = $access['expires_at'] ?? null;

    // İstatistikler
    $playlistsCount = \DB::table('muzibu_playlists')->where('user_id', $user->id)->count();
    $favoritesCount = \DB::table('favorites')->where('user_id', $user->id)->count();
    $playsCount = \DB::table('muzibu_song_plays')->where('user_id', $user->id)->count();

    // Kalan süre hesaplama
    $daysLeft = 0;
    $hoursLeft = 0;
    $minutesLeft = 0;
    if ($expiresAt) {
        $expiry = \Carbon\Carbon::parse($expiresAt);
        $now = now();
        if ($expiry->isFuture()) {
            $diffInSeconds = (int) floor($now->diffInSeconds($expiry, false));
            $daysLeft = (int) floor($diffInSeconds / 86400);
            $hoursLeft = (int) floor(($diffInSeconds % 86400) / 3600);
            $minutesLeft = (int) floor(($diffInSeconds % 3600) / 60);
        }
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-6">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">
            👋 Hoş geldin, {{ $user->name }}!
        </h1>
        <p class="text-muzibu-text-gray">Muzibu Dashboard</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Premium Status - Dinamik --}}
        @if($isTrial && $expiresAt)
            {{-- Deneme Üyeliği --}}
            <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 rounded-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-gift text-green-400 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-green-400 text-sm font-semibold">Deneme Üyeliği</p>
                        <p class="text-white text-lg font-bold">
                            @if($daysLeft > 0)
                                {{ $daysLeft }} gün {{ $hoursLeft }} saat
                            @elseif($hoursLeft > 0)
                                {{ $hoursLeft }} saat {{ $minutesLeft }} dk
                            @else
                                {{ $minutesLeft }} dakika
                            @endif
                            <span class="text-green-400/80 text-sm font-normal">kaldı</span>
                        </p>
                    </div>
                </div>
                <a href="/subscription/plans" class="mt-3 block text-center text-sm text-green-400 hover:text-green-300 underline">
                    <i class="fas fa-crown mr-1"></i> Premium'a yükselt
                </a>
            </div>
        @elseif($isPremiumOrTrial && $expiresAt)
            {{-- Premium Üyelik --}}
            <div class="bg-gradient-to-br from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-crown text-yellow-400 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-yellow-400 text-sm font-semibold">Premium Üye</p>
                        <p class="text-white text-lg font-bold">
                            @if($daysLeft > 0)
                                {{ $daysLeft }} gün {{ $hoursLeft }} saat
                            @elseif($hoursLeft > 0)
                                {{ $hoursLeft }} saat {{ $minutesLeft }} dk
                            @else
                                {{ $minutesLeft }} dakika
                            @endif
                            <span class="text-yellow-400/80 text-sm font-normal">kaldı</span>
                        </p>
                    </div>
                </div>
                <a href="/subscription/plans" class="mt-3 block text-center text-sm text-yellow-400 hover:text-yellow-300 underline">
                    <i class="fas fa-sync-alt mr-1"></i> Üyeliğini uzat
                </a>
            </div>
        @elseif($isPremiumOrTrial)
            {{-- Premium - Süresiz --}}
            <div class="bg-gradient-to-br from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-crown text-yellow-400 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-muzibu-text-gray text-sm">Üyelik</p>
                        <p class="text-yellow-400 text-xl font-bold">Premium</p>
                    </div>
                </div>
            </div>
        @else
            {{-- Ücretsiz --}}
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-500/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-gray-400 text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-muzibu-text-gray text-sm">Üyelik</p>
                        <p class="text-white text-xl font-bold">Ücretsiz</p>
                    </div>
                </div>
                <a href="/subscription/plans" class="mt-3 block text-center text-sm text-yellow-400 hover:text-yellow-300 underline">
                    <i class="fas fa-crown mr-1"></i> Premium'a geç
                </a>
            </div>
        @endif

        {{-- Playlists Count --}}
        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-muzibu-coral/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-muzibu-coral text-2xl"></i>
                </div>
                <div>
                    <p class="text-muzibu-text-gray text-sm">Playlistlerim</p>
                    <p class="text-white text-xl font-bold">{{ $playlistsCount }}</p>
                </div>
            </div>
        </div>

        {{-- Favorites Count --}}
        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heart text-red-400 text-2xl"></i>
                </div>
                <div>
                    <p class="text-muzibu-text-gray text-sm">Favoriler</p>
                    <p class="text-white text-xl font-bold">{{ $favoritesCount }}</p>
                </div>
            </div>
        </div>

        {{-- Play Count --}}
        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play text-green-400 text-2xl"></i>
                </div>
                <div>
                    <p class="text-muzibu-text-gray text-sm">Dinleme</p>
                    <p class="text-white text-xl font-bold">{{ $playsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white/5 backdrop-blur-sm rounded-lg p-8 border border-white/10">
        <h2 class="text-xl font-bold text-white mb-6">Hızlı İşlemler</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/"
                    class="block p-6 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-muzibu-coral/50 rounded-lg transition-all duration-300 group">
                <i class="fas fa-home text-3xl text-muzibu-coral mb-3"></i>
                <h3 class="text-white font-semibold mb-2">Anasayfa</h3>
                <p class="text-muzibu-text-gray text-sm">Müzik keşfet</p>
            </a>

            <a href="/profile"
                    class="block p-6 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-muzibu-coral/50 rounded-lg transition-all duration-300 group">
                <i class="fas fa-user text-3xl text-muzibu-coral mb-3"></i>
                <h3 class="text-white font-semibold mb-2">Profil</h3>
                <p class="text-muzibu-text-gray text-sm">Bilgilerini düzenle</p>
            </a>

            @if($isPremiumOrTrial)
                <a href="/subscription/plans"
                        class="block p-6 bg-gradient-to-br from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 hover:border-yellow-500/50 rounded-lg transition-all duration-300 group">
                    <i class="fas fa-sync-alt text-3xl text-yellow-400 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Üyeliğini Uzat</h3>
                    <p class="text-yellow-400 text-sm">Planını yenile</p>
                </a>
            @else
                <a href="/subscription/plans"
                        class="block p-6 bg-gradient-to-br from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 hover:border-yellow-500/50 rounded-lg transition-all duration-300 group">
                    <i class="fas fa-crown text-3xl text-yellow-400 mb-3"></i>
                    <h3 class="text-white font-semibold mb-2">Premium'a Geç</h3>
                    <p class="text-yellow-400 text-sm">Sınırsız müzik</p>
                </a>
            @endif
        </div>
    </div>

    {{-- Account Settings --}}
    <div class="bg-white/5 backdrop-blur-sm rounded-lg p-8 border border-white/10">
        <h2 class="text-xl font-bold text-white mb-6">Hesap Ayarları</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Profile Edit --}}
            <a href="/profile"
                    class="block p-6 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-muzibu-coral/50 rounded-lg transition-all duration-300 group">
                <i class="fas fa-user-pen text-2xl text-muzibu-coral mb-3"></i>
                <h3 class="text-white font-semibold mb-2">Profil Bilgileri</h3>
                <p class="text-muzibu-text-gray text-sm">Ad, soyad, email</p>
            </a>

            {{-- Avatar --}}
            <a href="/profile/avatar"
                    class="block p-6 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-500/50 rounded-lg transition-all duration-300 group">
                <i class="fas fa-camera text-2xl text-purple-400 mb-3"></i>
                <h3 class="text-white font-semibold mb-2">Avatar</h3>
                <p class="text-muzibu-text-gray text-sm">Profil fotoğrafı</p>
            </a>

            {{-- Password --}}
            <a href="/profile/password"
                    class="block p-6 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-yellow-500/50 rounded-lg transition-all duration-300 group">
                <i class="fas fa-key text-2xl text-yellow-400 mb-3"></i>
                <h3 class="text-white font-semibold mb-2">Şifre</h3>
                <p class="text-muzibu-text-gray text-sm">Şifre değiştir</p>
            </a>

            {{-- Delete Account --}}
            <a href="/profile/delete"
                    class="block p-6 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 hover:border-red-500/50 rounded-lg transition-all duration-300 group">
                <i class="fas fa-trash-alt text-2xl text-red-400 mb-3"></i>
                <h3 class="text-red-400 font-semibold mb-2">Hesabı Sil</h3>
                <p class="text-red-400/70 text-sm">Kalıcı olarak sil</p>
            </a>
        </div>
    </div>
</div>
@endsection
