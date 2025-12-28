@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.dashboard.title') . ' - Muzibu')

@section('content')
{{-- Alpine functions loaded globally in layout.blade.php --}}
<div x-data="dashboardApp()" x-init="init()">
    <div class="px-4 py-6 sm:px-6 sm:py-8">

        {{-- Header --}}
        <div class="mb-4 sm:mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-th-large text-xl sm:text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-0.5">
                        {{ __('muzibu::front.dashboard.hello', ['name' => $user->name]) }}
                    </h1>
                    <p class="text-gray-400 text-sm sm:text-base">{{ __('muzibu::front.dashboard.personal_panel') }}</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-3">
                <a href="/" class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm transition" data-spa>
                    <i class="fas fa-compass mr-2"></i>{{ __('muzibu::front.dashboard.discover') }}
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6 sm:mb-8">
            {{-- Membership Status --}}
            @php
                $isTrial = $access['is_trial'] ?? false;
                $isPremium = $user->isPremiumOrTrial();
            @endphp

            @if($isTrial && !$timeLeft['expired'])
                <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 rounded-xl p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-gift text-green-400 text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-green-400 text-sm font-semibold">{{ __('muzibu::front.dashboard.trial') }}</p>
                            <p class="text-white text-lg font-bold">
                                @if($timeLeft['days'] > 0)
                                    {{ $timeLeft['days'] }}{{ __('muzibu::front.dashboard.days_short') }} {{ $timeLeft['hours'] }}{{ __('muzibu::front.dashboard.hours_short') }}
                                @elseif($timeLeft['hours'] > 0)
                                    {{ $timeLeft['hours'] }}{{ __('muzibu::front.dashboard.hours_short') }} {{ $timeLeft['minutes'] }}{{ __('muzibu::front.dashboard.minutes_short') }}
                                @else
                                    {{ $timeLeft['minutes'] }}{{ __('muzibu::front.dashboard.minutes_short') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($isPremium && !$timeLeft['expired'])
                <div class="bg-gradient-to-br from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-xl p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-crown text-yellow-400 text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-yellow-400 text-sm font-semibold">{{ __('muzibu::front.dashboard.premium') }}</p>
                            <p class="text-white text-lg font-bold">
                                @if($timeLeft['days'] > 0)
                                    {{ $timeLeft['days'] }}{{ __('muzibu::front.dashboard.days_short') }} {{ $timeLeft['hours'] }}{{ __('muzibu::front.dashboard.hours_short') }}
                                @elseif($timeLeft['hours'] > 0)
                                    {{ $timeLeft['hours'] }}{{ __('muzibu::front.dashboard.hours_short') }} {{ $timeLeft['minutes'] }}{{ __('muzibu::front.dashboard.minutes_short') }}
                                @else
                                    {{ $timeLeft['minutes'] }}{{ __('muzibu::front.dashboard.minutes_short') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white/5 border border-white/10 rounded-xl p-5 hover:border-yellow-500/30 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-crown text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">{{ __('muzibu::front.dashboard.membership') }}</p>
                            <p class="text-white text-2xl font-bold">{{ __('muzibu::front.dashboard.free') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Plays Count --}}
            <div class="bg-white/5 border border-white/10 rounded-xl p-5 hover:border-green-500/30 transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-play text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">{{ __('muzibu::front.dashboard.listening') }}</p>
                        <p class="text-white text-2xl font-bold">{{ number_format($stats['plays_count']) }}</p>
                    </div>
                </div>
            </div>

            {{-- Favorites --}}
            <div class="bg-white/5 border border-white/10 rounded-xl p-5 hover:border-red-500/30 transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-heart text-red-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">{{ __('muzibu::front.dashboard.favorite') }}</p>
                        <p class="text-white text-2xl font-bold">{{ $stats['favorites_count'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Playlists --}}
            <div class="bg-white/5 border border-white/10 rounded-xl p-5 hover:border-purple-500/30 transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-list-music text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">{{ __('muzibu::front.dashboard.playlist') }}</p>
                        <p class="text-white text-2xl font-bold">{{ $stats['playlists_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Corporate Owner Banner (Ana Şube - Üstte Görünür) --}}
        @if($corporate && $corporate['is_owner'])
            <div class="bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border border-yellow-500/30 rounded-xl overflow-hidden mb-8">
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-crown text-white text-xl"></i>
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <div class="flex items-center gap-2 justify-center sm:justify-start">
                                <p class="text-white font-semibold text-lg">{{ $corporate['company_name'] }}</p>
                                <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-400 text-xs rounded font-medium">{{ __('muzibu::front.corporate.main_branch') }}</span>
                            </div>
                            <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.members_count', ['count' => $corporate['members_count']]) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Kurumsal Kod --}}
                            <div class="hidden sm:flex items-center gap-2 bg-white/5 px-3 py-2 rounded-lg">
                                <span class="text-gray-400 text-xs">{{ __('muzibu::front.corporate.code') }}:</span>
                                <code class="font-mono font-bold text-white">{{ $corporate['corporate_code'] }}</code>
                                <button @click="copyCode('{{ $corporate['corporate_code'] }}')" class="p-1.5 hover:bg-white/10 rounded transition" title="{{ __('muzibu::front.corporate.copy_code') }}">
                                    <i class="fas fa-copy text-gray-400 text-sm"></i>
                                </button>
                            </div>
                            {{-- Yonet Butonu --}}
                            <a href="/corporate/dashboard" class="px-4 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 hover:opacity-90 text-white font-medium rounded-xl transition flex items-center gap-2" data-spa>
                                <i class="fas fa-users-cog"></i>
                                <span>Kurumsal Panel</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Content Grid --}}
        <div class="grid lg:grid-cols-2 gap-4 md:gap-6 mb-6 sm:mb-8">
            {{-- Recently Played --}}
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-white/10">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-history text-green-400"></i>
                        {{ __('muzibu::front.dashboard.recently_played') }}
                    </h2>
                    <a href="/muzibu/listening-history" class="text-sm text-muzibu-coral hover:text-white transition" data-spa>
                        {{ __('muzibu::front.dashboard.view_all') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div>
                    @forelse($recentlyPlayed->take(5) as $index => $play)
                        <x-muzibu.song-simple-row :song="$play->song" :index="$index" />
                    @empty
                        <div class="p-8 text-center text-gray-400">
                            <i class="fas fa-music text-4xl mb-3 opacity-50"></i>
                            <p>{{ __('muzibu::front.dashboard.no_songs_yet') }}</p>
                            <a href="/" class="text-muzibu-coral hover:underline text-sm mt-2 inline-block">{{ __('muzibu::front.dashboard.start_discovering') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Favorites --}}
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between p-5 border-b border-white/10">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-heart text-red-400"></i>
                        {{ __('muzibu::front.dashboard.my_favorites') }}
                    </h2>
                    <a href="/muzibu/favorites" class="text-sm text-muzibu-coral hover:text-white transition" data-spa>
                        {{ __('muzibu::front.dashboard.view_all') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @if($favorites->count() > 0)
                    <div>
                        @foreach($favorites->take(5) as $index => $song)
                            <x-muzibu.song-simple-row :song="$song" :index="$index" />
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400">
                        <i class="fas fa-heart text-4xl mb-3 opacity-50"></i>
                        <p>{{ __('muzibu::front.dashboard.no_favorites_yet') }}</p>
                        <a href="/" class="text-red-400 hover:underline text-sm mt-2 inline-block">{{ __('muzibu::front.dashboard.discover_songs') }}</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Playlists --}}
        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl overflow-hidden mb-6 sm:mb-8">
            <div class="flex items-center justify-between p-5 border-b border-white/10">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-list-music text-purple-400"></i>
                    {{ __('muzibu::front.dashboard.my_playlists') }}
                </h2>
                <a href="/muzibu/my-playlists" class="text-sm text-muzibu-coral hover:text-white transition" data-spa>
                    {{ __('muzibu::front.dashboard.view_all') }} <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @if($playlists->count() > 0)
                <div class="p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                        @foreach($playlists->take(5) as $playlist)
                            <x-muzibu.my-playlist-card :playlist="$playlist" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-list-music text-4xl mb-3 opacity-50"></i>
                    <p>{{ __('muzibu::front.dashboard.no_playlists_yet') }}</p>
                    <a href="/muzibu/my-playlists" class="text-purple-400 hover:underline text-sm mt-2 inline-block">
                        <i class="fas fa-plus mr-1"></i>{{ __('muzibu::front.dashboard.create_playlist') }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Corporate Section (Alt Üye veya Kurumsal Değil) --}}
        @if($corporate && !$corporate['is_owner'])
            {{-- ALT UYE: Sirket bilgisi + Cikis butonu --}}
            <div class="bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border border-blue-500/30 rounded-xl overflow-hidden mb-6 sm:mb-8">
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <p class="text-white font-semibold text-lg">{{ $corporate['company_name'] }}</p>
                            <p class="text-gray-400 text-sm">{{ $corporate['branch_name'] ?? __('muzibu::front.corporate.member') }} olarak baglisiniz</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="/corporate/my-corporate" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition flex items-center gap-2" data-spa>
                                <i class="fas fa-eye"></i>
                                <span>{{ __('muzibu::front.corporate.details') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(!$corporate)
            {{-- KURUMSAL DEGIL - Basit tasarim --}}
            <div class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-xl overflow-hidden mb-6 sm:mb-8" x-data="{ code: '', joining: false }">
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <h3 class="text-white font-bold text-lg">{{ __('muzibu::front.corporate.title') }}</h3>
                            <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.description') }}</p>
                        </div>
                        <a href="/corporate/join" class="text-purple-400 hover:text-purple-300 text-sm transition hidden sm:flex items-center gap-1" data-spa>
                            <i class="fas fa-crown text-yellow-400"></i>
                            <span>Kendi kurumsal yapınızı oluşturun</span>
                        </a>
                    </div>

                    {{-- Kod Girisi --}}
                    <div class="mt-4">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" x-model="code"
                                   @keyup.enter="if(code.length >= 8 && !joining) { joining = true; fetch('/corporate/join', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, body: JSON.stringify({ corporate_code: code.toUpperCase() }) }).then(r => r.json()).then(d => { if(d.success) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: d.message, type: 'success' } })); setTimeout(() => window.location.href = d.redirect || '/dashboard', 1000); } else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: d.message || 'Gecersiz kod', type: 'error' } })); joining = false; } }).catch(e => { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Hata olustu', type: 'error' } })); joining = false; }); }"
                                   placeholder="{{ __('muzibu::front.corporate.enter_code') }}" maxlength="8"
                                   class="flex-1 bg-white/5 border border-white/20 focus:border-purple-500 rounded-xl px-4 py-3 text-white placeholder-gray-500 uppercase font-mono tracking-wider text-center sm:text-left">
                            <button @click="if(code.length >= 8 && !joining) { joining = true; fetch('/corporate/join', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }, body: JSON.stringify({ corporate_code: code.toUpperCase() }) }).then(r => r.json()).then(d => { if(d.success) { window.dispatchEvent(new CustomEvent('toast', { detail: { message: d.message, type: 'success' } })); setTimeout(() => window.location.href = d.redirect || '/dashboard', 1000); } else { window.dispatchEvent(new CustomEvent('toast', { detail: { message: d.message || 'Gecersiz kod', type: 'error' } })); joining = false; } }).catch(e => { window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Hata olustu', type: 'error' } })); joining = false; }); }"
                                    :disabled="joining || code.length < 8"
                                    :class="code.length >= 8 ? 'bg-gradient-to-r from-purple-500 to-pink-500 hover:opacity-90' : 'bg-gray-600 cursor-not-allowed'"
                                    class="px-6 py-3 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2">
                                <span x-show="!joining"><i class="fas fa-sign-in-alt mr-1"></i>{{ __('muzibu::front.corporate.join') }}</span>
                                <span x-show="joining"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                        <p class="text-gray-500 text-xs mt-3"><i class="fas fa-info-circle mr-1"></i>{{ __('muzibu::front.corporate.code_hint') }}</p>
                        {{-- Mobil için link --}}
                        <a href="/corporate/join" class="text-purple-400 hover:text-purple-300 text-xs transition flex sm:hidden items-center gap-1 mt-2" data-spa>
                            <i class="fas fa-crown text-yellow-400"></i>
                            <span>Kendi kurumsal yapınızı oluşturun</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Ödeme Bekleyen (Varsa) --}}
        @if(!empty($subscriptionInfo['pending_payment']))
        <div class="bg-orange-500/10 border border-orange-500/30 rounded-xl overflow-hidden mb-6 sm:mb-8">
            <div class="p-4">
                <p class="text-orange-400 text-sm mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    Ödeme Bekleyen
                </p>
                @foreach($subscriptionInfo['pending_payment'] as $pp)
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-white font-medium">{{ $pp['plan_name'] }}</span>
                        <span class="text-gray-400 text-sm ml-1">({{ $pp['cycle_label'] ?? '' }})</span>
                    </div>
                    <a href="/subscription/checkout/{{ $pp['id'] }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition">
                        Ödeme Yap
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
            <a href="/" class="block p-5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-muzibu-coral/50 rounded-xl transition group" data-spa>
                <i class="fas fa-compass text-2xl text-muzibu-coral mb-3"></i>
                <h3 class="text-white font-semibold">{{ __('muzibu::front.dashboard.explore') }}</h3>
                <p class="text-gray-400 text-sm">{{ __('muzibu::front.dashboard.new_music') }}</p>
            </a>
            <a href="/profile" class="block p-5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-blue-500/50 rounded-xl transition group" data-spa>
                <i class="fas fa-user text-2xl text-blue-400 mb-3"></i>
                <h3 class="text-white font-semibold">{{ __('muzibu::front.dashboard.profile') }}</h3>
                <p class="text-gray-400 text-sm">{{ __('muzibu::front.dashboard.account_settings') }}</p>
            </a>
            <a href="/my-subscriptions" class="block p-5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-yellow-500/50 rounded-xl transition group" data-spa>
                <i class="fas fa-crown text-2xl text-yellow-400 mb-3"></i>
                <h3 class="text-white font-semibold">Aboneliklerim</h3>
                <p class="text-gray-400 text-sm">Geçmiş & Ödemeler</p>
            </a>
            <a href="/genres" class="block p-5 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-green-500/50 rounded-xl transition group" data-spa>
                <i class="fas fa-music text-2xl text-green-400 mb-3"></i>
                <h3 class="text-white font-semibold">{{ __('muzibu::front.dashboard.genres') }}</h3>
                <p class="text-gray-400 text-sm">{{ __('muzibu::front.dashboard.music_genres') }}</p>
            </a>
        </div>

    </div>
</div>
@endsection
