<header class="xl:col-span-3 lg:col-span-2 col-span-1 bg-black/80 backdrop-blur-md border-b border-white/5 px-4 flex items-center justify-between sticky top-0 z-50">
    <div class="flex items-center gap-4 flex-1">
        {{-- Mobile Hamburger --}}
        <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="lg:hidden text-muzibu-text-gray hover:text-white transition-colors"
        >
            <i class="fas fa-bars text-xl"></i>
        </button>

        {{-- Logo with animation - Settings powered --}}
        <a href="/" wire:navigate class="text-2xl font-bold group flex items-center">
            @php
                // LogoService kullan - Settings'den logo çek
                $logoService = app(\App\Services\LogoService::class);
                $logos = $logoService->getLogos();

                $logoUrl = $logos['light_logo_url'] ?? null;
                $logoDarkUrl = $logos['dark_logo_url'] ?? null;
                $fallbackMode = $logos['fallback_mode'] ?? 'title_only';
                $siteTitle = $logos['site_title'] ?? setting('site_title', 'muzibu');
            @endphp

            @if($fallbackMode === 'both')
                {{-- Her iki logo da var - Dark mode'da otomatik değiş --}}
                <img src="{{ $logoUrl }}"
                     alt="{{ $siteTitle }}"
                     class="dark:hidden object-contain h-10 w-auto"
                     title="{{ $siteTitle }}">
                <img src="{{ $logoDarkUrl }}"
                     alt="{{ $siteTitle }}"
                     class="hidden dark:block object-contain h-10 w-auto"
                     title="{{ $siteTitle }}">
            @elseif($fallbackMode === 'light_only' && $logoUrl)
                {{-- Sadece light logo var --}}
                <img src="{{ $logoUrl }}"
                     alt="{{ $siteTitle }}"
                     class="object-contain h-10 w-auto"
                     title="{{ $siteTitle }}">
            @elseif($fallbackMode === 'dark_only' && $logoDarkUrl)
                {{-- Sadece dark logo var --}}
                <img src="{{ $logoDarkUrl }}"
                     alt="{{ $siteTitle }}"
                     class="object-contain h-10 w-auto"
                     title="{{ $siteTitle }}">
            @else
                {{-- Fallback: Gradient text logo --}}
                <span class="text-xl font-bold bg-gradient-to-r from-muzibu-coral via-muzibu-coral-light to-muzibu-coral bg-clip-text text-transparent animate-gradient">
                    {{ $siteTitle }}
                </span>
            @endif
        </a>

        {{-- Cache Clear Button - Icon Only (Logonun yanında) --}}
        <button
            @click="clearCache()"
            class="w-9 h-9 bg-white/5 hover:bg-muzibu-coral/20 rounded-lg flex items-center justify-center text-muzibu-text-gray hover:text-muzibu-coral transition-all duration-300 group"
            title="Cache Temizle"
            x-data="{
                clearCache() {
                    fetch('/admin/cache/clear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Cache cleared:', data);
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('Cache clear error:', error);
                        window.location.reload();
                    });
                }
            }"
        >
            <i class="fas fa-sync-alt text-sm group-hover:rotate-180 transition-transform duration-500"></i>
        </button>

        {{-- Search Box - Centered & Modern (Meilisearch) --}}
        <div class="relative flex-1 max-w-2xl mx-auto hidden md:block group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-muzibu-coral group-focus-within:scale-110 transition-all duration-300 text-sm"></i>
            <input
                type="text"
                placeholder="Şarkı, sanatçı, albüm ara..."
                x-model="searchQuery"
                @focus="searchOpen = true"
                class="w-full pl-11 pr-5 py-2 bg-white/10 hover:bg-white/15 focus:bg-white/20 border-0 rounded-full text-white placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50 focus:shadow-lg focus:shadow-muzibu-coral/20 transition-all duration-300 text-sm"
            >
        </div>
    </div>

    <div class="flex items-center gap-5">
        {{-- Premium Button (non-premium only) - SPA Reactive --}}
        <a
            href="/subscription/plans"
            wire:navigate
            x-show="isLoggedIn && (!currentUser?.is_premium)"
            x-cloak
            class="hidden sm:flex items-center gap-2 px-4 py-2 border border-muzibu-coral/40 hover:border-muzibu-coral hover:bg-muzibu-coral/10 rounded-full text-muzibu-coral text-sm font-semibold transition-all duration-300"
        >
            <i class="fas fa-crown text-xs"></i>
            <span class="hidden md:inline">Premium'a Geç</span>
            <span class="md:hidden">Premium</span>
        </a>

        {{-- Notification with badge - SPA Reactive --}}
        <button
            x-show="isLoggedIn"
            x-cloak
            class="relative w-10 h-10 bg-white/5 hover:bg-white/10 rounded-full flex items-center justify-center text-white/80 hover:text-white transition-all duration-300 group"
        >
            <i class="far fa-bell text-lg"></i>
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-muzibu-coral rounded-full animate-pulse ring-2 ring-black"></span>
        </button>

        {{-- User Dropdown - SPA Reactive --}}
        <div x-show="isLoggedIn" x-cloak class="relative" x-data="{ userMenuOpen: false }">
            <button
                @click="userMenuOpen = !userMenuOpen"
                class="relative w-10 h-10 bg-gradient-to-br from-[#ff6b6b] via-[#ff5252] to-[#e91e63] hover:opacity-90 rounded-full text-white font-bold text-sm transition-all duration-300 shadow-lg hover:shadow-xl"
            >
                <span x-text="currentUser?.name ? currentUser.name.charAt(0).toUpperCase() : 'U'"></span>
            </button>
            <div x-show="userMenuOpen"
                 @click.away="userMenuOpen = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 class="absolute right-0 mt-3 w-64 bg-zinc-900/95 backdrop-blur-xl rounded-xl shadow-2xl border border-white/10 py-2 overflow-hidden z-50"
                 style="display: none;">
                <div class="px-4 py-3 border-b border-white/10">
                    <p class="text-white font-semibold text-sm" x-text="currentUser?.name || 'Kullanıcı'"></p>
                    <p class="text-zinc-400 text-xs" x-text="currentUser?.email || ''"></p>

                    {{-- Premium Badge --}}
                    <div
                        x-show="currentUser?.is_premium"
                        class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-full"
                    >
                        <i class="fas fa-crown text-yellow-400 text-xs"></i>
                        <span class="text-yellow-400 text-xs font-semibold">Premium Üye</span>
                    </div>

                    {{-- Trial Subscription Widget --}}
                    @auth
                        @php
                            $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
                            $access = $subscriptionService->checkUserAccess(auth()->user());
                            $isTrial = $access['is_trial'] ?? false;
                            $expiresAt = $access['expires_at'] ?? null;
                            $daysRemaining = $expiresAt ? now()->diffInDays($expiresAt) : 0;
                        @endphp

                        @if($isTrial && $expiresAt)
                            <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-500/30 rounded-full">
                                <i class="fas fa-gift text-green-400 text-xs"></i>
                                <span class="text-green-400 text-xs font-semibold">
                                    Trial: {{ $daysRemaining }} gün kaldı
                                </span>
                            </div>
                        @endif
                    @endauth
                </div>

                {{-- Dashboard Link --}}
                <a href="/dashboard" wire:navigate @click="userMenuOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-white text-sm transition-colors">
                    <i class="fas fa-th-large w-5"></i>
                    <span>Kullanıcı Paneli</span>
                </a>

                {{-- Profile Link --}}
                <a href="/profile" wire:navigate @click="userMenuOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-white text-sm transition-colors">
                    <i class="fas fa-user w-5"></i>
                    <span>Profil</span>
                </a>

                <div class="h-px bg-white/10 my-1"></div>

                {{-- Premium Link (non-premium only) --}}
                <a
                    href="/subscription/plans"
                    wire:navigate
                    x-show="!currentUser?.is_premium"
                    @click="userMenuOpen = false"
                    class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-yellow-500/10 text-yellow-400 text-sm transition-colors"
                >
                    <i class="fas fa-crown w-5"></i>
                    <span>Premium'a Geç</span>
                </a>

                <div class="h-px bg-white/10 my-1"></div>

                {{-- Logout --}}
                <button @click.prevent="logout()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-500/10 text-red-400 text-sm transition-colors">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Çıkış Yap</span>
                </button>
            </div>
        </div>

        {{-- Login/Register Buttons - SPA Reactive --}}
        <div x-show="!isLoggedIn" x-cloak class="flex items-center gap-3">
            <button
                @click="showAuthModal = 'login'"
                class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 rounded-full text-white text-sm font-semibold transition-all duration-300"
            >
                <i class="fas fa-sign-in-alt text-xs"></i>
                <span>Giriş Yap</span>
            </button>
            <button
                @click="showAuthModal = 'register'"
                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light hover:from-muzibu-coral-light hover:to-muzibu-coral rounded-full text-white text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/30"
            >
                <i class="fas fa-user-plus text-xs"></i>
                <span class="hidden md:inline">Üye Ol</span>
                <span class="md:hidden">Kaydol</span>
            </button>
        </div>
    </div>
</header>
