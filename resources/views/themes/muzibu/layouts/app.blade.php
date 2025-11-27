@php
    // 🔐 Şifre Koruması - Cookie tabanlı (route bağımsız)
    $constructionPassword = 'nn';
    $cookieName = 'mzb_auth_' . tenant('id');
    $cookieValue = md5($constructionPassword . 'salt2024');

    // $_COOKIE kullan (Laravel encrypt etmeden)
    $isAuthenticated = isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $cookieValue;
@endphp

@if(!$isAuthenticated)
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muzibu - Erişim Gerekli</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:system-ui,sans-serif;background:linear-gradient(135deg,#1DB954,#121212);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .box{background:#fff;padding:50px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);max-width:420px;width:100%;text-align:center}
        .icon{font-size:60px;color:#1DB954;margin-bottom:10px}
        h1{font-size:28px;color:#121212;margin-bottom:10px}
        p{color:#666;font-size:15px;margin-bottom:25px}
        input{width:100%;padding:16px;border:2px solid #e0e0e0;border-radius:50px;font-size:16px;outline:none;margin-bottom:15px}
        input:focus{border-color:#1DB954}
        button{width:100%;padding:16px;background:#1DB954;color:#fff;border:none;border-radius:50px;font-size:17px;font-weight:600;cursor:pointer}
        button:hover{background:#1ed760}
        .err{background:#fee;color:#c33;padding:12px;border-radius:10px;margin-bottom:15px;font-size:14px}
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">🎵</div>
        <h1>Muzibu</h1>
        <p>Platform yapım aşamasında<br><small>Erişim için şifre gereklidir</small></p>
        @if(request()->has('_mzb_pwd') && request()->input('_mzb_pwd') !== $constructionPassword)
        <div class="err">Şifre hatalı!</div>
        @endif
        <form method="GET" id="authForm">
            <input type="password" name="_mzb_pwd" id="pwdInput" placeholder="Erişim şifresi..." autofocus required>
            <button type="submit"><i class="fas fa-unlock-alt"></i> Giriş</button>
        </form>
    </div>
    <script>
        document.getElementById('authForm').addEventListener('submit', function(e) {
            const pwd = document.getElementById('pwdInput').value;
            if (pwd === '{{ $constructionPassword }}') {
                // Cookie set et (7 gün)
                document.cookie = '{{ $cookieName }}={{ md5($constructionPassword . "salt2024") }}; path=/; max-age=' + (7*24*60*60) + '; secure; samesite=lax';
                e.preventDefault();
                window.location.href = window.location.pathname;
            }
        });
    </script>
</body>
</html>
@else
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full" x-data="muzibuApp()" x-init="init()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Muzibu - İşletmenize Yasal ve Telifsiz Müzik')</title>

    <!-- Tenant-Aware Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('css/tenant-' . tenant('id') . '.css') }}?v=25112024-02">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/themes/muzibu/css/theme.css') }}?v=1764171131">
    <link rel="stylesheet" href="{{ asset('assets/themes/muzibu/css/play-limits.css') }}?v=1764171131">

    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/howler@2.2.4/dist/howler.min.js"></script>
    <!-- Alpine.js Livewire tarafından otomatik yükleniyor, CDN'den tekrar yükleme! -->

    @livewireStyles
    @yield('styles')
</head>
<body class="h-full bg-spotify-black text-white antialiased" :class="isDarkMode ? 'dark' : ''">
    <!-- Top CTA Bar (Guest only) -->
    @guest
    <div class="fixed top-0 left-0 right-0 bg-gradient-to-r from-spotify-green to-green-600 z-50 py-3 px-6 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-gift text-white text-xl"></i>
                <span class="font-semibold text-white">🎉 7 Gün Ücretsiz Premium Deneyin! <span class="opacity-90 text-sm">Kredi kartı gerekmez</span></span>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showAuthModal = 'login'" class="px-6 py-2 bg-white text-spotify-green rounded-full font-bold hover:scale-105 transition-all shadow-lg">Giriş Yap</button>
                <button @click="showAuthModal = 'register'" class="px-6 py-2 bg-black/30 backdrop-blur-sm text-white rounded-full font-bold hover:bg-black/50 transition-all border-2 border-white">Ücretsiz Başla</button>
            </div>
        </div>
    </div>
    @endguest

    @include('themes.muzibu.components.sidebar')

    <!-- Fixed Loading Indicator (over logo in sidebar) -->
    <div x-show="isLoading" x-transition
         class="fixed top-6 left-6 z-50 px-4 py-2 bg-spotify-green/95 backdrop-blur-sm rounded-full text-black text-sm font-semibold shadow-2xl flex items-center gap-2">
        <i class="fas fa-spinner fa-spin"></i>
        <span>Yükleniyor...</span>
    </div>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen pb-32 bg-gradient-to-b from-spotify-dark to-spotify-black {{ auth()->check() ? 'pt-0' : 'pt-16' }}">
        <!-- Top Bar -->
        <div class="sticky z-30 bg-gradient-to-b from-black/80 to-transparent backdrop-blur-sm {{ auth()->check() ? 'top-0' : 'top-14' }}">
            <div class="flex items-center justify-between px-8 py-4">
                <div class="relative flex-1 max-w-md" x-data="{ searchOpen: false, searchQuery: '' }">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-black"></i>
                    <input type="search" x-model="searchQuery" @focus="searchOpen = true" @click.away="searchOpen = false" placeholder="Şarkı, albüm, playlist ara..." class="w-full pl-12 pr-4 py-3 bg-white rounded-full text-black placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-spotify-green transition-all font-medium">
                    <div x-show="searchOpen && searchQuery.length > 0" x-transition class="absolute top-full left-0 right-0 mt-2 bg-spotify-gray rounded-lg shadow-2xl max-h-96 overflow-y-auto">
                        <div class="p-2">
                            <div class="px-3 py-2 text-xs text-gray-400 font-semibold">ŞARKILAR</div>
                            <template x-for="i in 3">
                                <button @click="playSong(0); searchOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-md hover:bg-white/10 transition-all">
                                    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=40&h=40&fit=crop" class="w-10 h-10 rounded">
                                    <div class="flex-1 text-left">
                                        <div class="text-sm font-medium text-white">Calling on You</div>
                                        <div class="text-xs text-gray-400">Muzibu Bossa Nova</div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button class="px-4 py-2 bg-transparent border border-white/30 rounded-full text-white text-sm font-semibold hover:border-white hover:scale-105 transition-all">
                        <i class="fas fa-crown text-spotify-green mr-2"></i>Premium'a Geç
                    </button>

                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" class="w-8 h-8 rounded-full bg-spotify-gray flex items-center justify-center hover:bg-white/10 transition-all" title="Tema Değiştir">
                        <i :class="isDarkMode ? 'fas fa-moon' : 'fas fa-sun'" class="text-white text-xs"></i>
                    </button>

                    <div class="relative" x-data="{ userMenuOpen: false }">
                        <!-- Normal State -->
                        <template x-if="!isLoggingOut">
                            <button @click="userMenuOpen = !userMenuOpen" class="w-8 h-8 rounded-full bg-spotify-gray flex items-center justify-center hover:bg-spotify-gray/80 transition-all">
                                <i class="fas fa-user text-white text-xs"></i>
                            </button>
                        </template>
                        <!-- Logging Out State -->
                        <template x-if="isLoggingOut">
                            <div class="w-8 h-8 rounded-full bg-spotify-gray flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-spotify-green text-xs"></i>
                            </div>
                        </template>
                        <div x-show="userMenuOpen && !isLoggingOut" @click.away="userMenuOpen = false" x-transition class="absolute top-full right-0 mt-2 w-48 bg-spotify-gray rounded-md shadow-2xl overflow-hidden">
                            <div class="p-1">
                                <button class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                    <i class="fas fa-user-circle text-gray-400"></i><span>Profil</span>
                                </button>
                                <button class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                    <i class="fas fa-cog text-gray-400"></i><span>Ayarlar</span>
                                </button>
                                <hr class="my-1 border-white/10">
                                <button @click="logout(); userMenuOpen = false" class="w-full flex items-center gap-3 px-3 py-2 rounded-sm hover:bg-white/10 transition-all text-left text-sm">
                                    <i class="fas fa-sign-out-alt text-gray-400"></i><span>Çıkış Yap</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @include('themes.muzibu.components.footer')
    @include('themes.muzibu.components.player')

    {{-- Session Check System - Tenant 1001 only --}}
    @include('themes.muzibu.components.session-check')

    {{-- Play Limits System - Modals --}}
    @include('themes.muzibu.components.play-limits-modals')

    @livewireScripts

    {{-- Play Limits System - JS --}}
    <script src="{{ asset('assets/themes/muzibu/js/play-limits.js') }}?v={{ filemtime(public_path('assets/themes/muzibu/js/play-limits.js')) }}"></script>

    @yield('scripts')

    <!-- instant.page for faster page transitions -->
    <script src="//instant.page/5.2.0" type="module" data-instant-intensity="viewport-all"></script>
</body>
</html>
@endif
