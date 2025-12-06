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

        {{-- Search Box - Meilisearch Powered --}}
        <div class="relative flex-1 max-w-2xl mx-auto hidden md:block"
             x-data="{
                query: '',
                songs: [],
                total: 0,
                isOpen: false,
                loading: false,
                error: null,
                highlightIndex: -1,
                get hasResults() {
                    return this.songs.length > 0;
                },
                get resultCount() {
                    return this.songs.length;
                },
                resetSuggestions() {
                    this.songs = [];
                    this.total = 0;
                    this.highlightIndex = -1;
                },
                showEmptyState() {
                    return this.query.trim().length >= 2 && !this.loading && !this.hasResults && !this.error;
                },
                openDropdown() {
                    const hasContent = this.hasResults || this.showEmptyState() || !!this.error;
                    this.isOpen = hasContent;
                    if (!hasContent) {
                        this.highlightIndex = -1;
                    }
                },
                async search() {
                    const trimmed = this.query.trim();
                    if (trimmed.length < 2) {
                        this.resetSuggestions();
                        this.isOpen = false;
                        this.error = null;
                        return;
                    }
                    this.loading = true;
                    this.error = null;
                    try {
                        const response = await fetch(`/api/muzibu/search?q=${encodeURIComponent(trimmed)}&type=songs`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const data = await response.json();

                        if (data.songs) {
                            this.songs = data.songs.slice(0, 6);
                            this.total = data.songs.length;
                            this.highlightIndex = -1;
                        } else {
                            this.resetSuggestions();
                        }
                    } catch (e) {
                        console.error('Search error:', e);
                        this.resetSuggestions();
                        this.error = 'Arama yapılamadı.';
                    }
                    this.loading = false;
                    this.openDropdown();
                },
                goToSearch() {
                    const trimmed = this.query.trim();
                    if (trimmed.length >= 1) {
                        window.location.href = `/search?q=${encodeURIComponent(trimmed)}`;
                    }
                },
                moveHighlight(step) {
                    if (!this.isOpen || this.resultCount === 0) return;
                    let next = this.highlightIndex + step;
                    if (next < 0) {
                        next = this.resultCount - 1;
                    } else if (next >= this.resultCount) {
                        next = 0;
                    }
                    this.highlightIndex = next;
                },
                isHighlighted(index) {
                    return this.highlightIndex === index;
                },
                setHighlight(index) {
                    this.highlightIndex = index;
                },
                clearHighlight() {
                    this.highlightIndex = -1;
                },
                selectHighlighted() {
                    if (this.highlightIndex < 0) {
                        this.goToSearch();
                        return;
                    }
                    const song = this.songs[this.highlightIndex];
                    if (song) {
                        this.selectSong(song);
                    }
                },
                selectSong(song) {
                    if (song?.slug) {
                        const locale = '{{ app()->getLocale() }}';
                        const slug = typeof song.slug === 'object' ? song.slug[locale] || song.slug.tr || song.slug.en : song.slug;
                        window.location.href = `/muzibu/song/${slug}`;
                    }
                },
                handleFocus() {
                    if (this.query.trim().length >= 2) {
                        this.openDropdown();
                    }
                },
                closeDropdown() {
                    this.isOpen = false;
                    this.clearHighlight();
                }
            }"
             @click.away="closeDropdown()">

            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-muzibu-coral group-focus-within:scale-110 transition-all duration-300 text-sm"></i>
                <input
                    type="search"
                    x-model="query"
                    @focus="handleFocus()"
                    @input.debounce.300ms="search()"
                    @keydown.enter.prevent="selectHighlighted()"
                    @keydown.arrow-down.prevent="moveHighlight(1)"
                    @keydown.arrow-up.prevent="moveHighlight(-1)"
                    @keydown.escape.prevent="closeDropdown()"
                    placeholder="Şarkı, sanatçı, albüm ara..."
                    class="w-full pl-11 pr-11 py-2 bg-white/10 hover:bg-white/15 focus:bg-white/20 border-0 rounded-full text-white placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/50 focus:shadow-lg focus:shadow-muzibu-coral/20 transition-all duration-300 text-sm"
                    autocomplete="off">

                {{-- Loading Spinner --}}
                <div x-show="loading" x-cloak class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                    <i class="fas fa-spinner fa-spin text-muzibu-coral text-sm"></i>
                </div>

                {{-- Clear Button --}}
                <button
                    x-show="query.length > 0 && !loading"
                    x-cloak
                    @click="query = ''; songs = []; closeDropdown();"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full hover:bg-white/10 transition-all group/clear">
                    <i class="fas fa-times text-zinc-400 group-hover/clear:text-white text-xs"></i>
                </button>
            </div>

            {{-- Search Results Dropdown --}}
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 class="absolute top-full left-0 right-0 mt-3 bg-zinc-900/95 backdrop-blur-xl shadow-2xl rounded-xl border border-white/10 overflow-hidden z-50"
                 style="display: none;">

                {{-- Error State --}}
                <template x-if="error">
                    <div class="px-5 py-6 text-sm text-red-400 flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-base"></i>
                        <span x-text="error"></span>
                    </div>
                </template>

                {{-- Results --}}
                <div class="max-h-96 overflow-y-auto">
                    <div x-show="songs.length > 0" class="p-3">
                        <div class="flex items-center justify-between text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-3 px-2">
                            <span><i class="fas fa-music text-muzibu-coral mr-1"></i> Şarkılar</span>
                            <span class="text-[10px]" x-text="`${songs.length}`"></span>
                        </div>

                        <div class="space-y-1">
                            <template x-for="(song, index) in songs" :key="'s-'+index">
                                <a href="#"
                                   @click.prevent="selectSong(song)"
                                   @mouseenter="setHighlight(index)"
                                   @mouseleave="clearHighlight()"
                                   :class="[
                                        'flex items-center gap-3 p-3 rounded-lg transition-all duration-200 group',
                                        isHighlighted(index)
                                            ? 'bg-muzibu-coral/20 border border-muzibu-coral/40'
                                            : 'hover:bg-white/5 border border-transparent'
                                    ]">

                                    {{-- Song Cover --}}
                                    <div class="w-12 h-12 rounded-lg bg-zinc-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        <i class="fas fa-music text-zinc-600 text-sm"></i>
                                    </div>

                                    {{-- Song Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-sm text-white leading-tight truncate" x-text="typeof song.title === 'object' ? (song.title.tr || song.title.en || song.title.ar) : song.title"></div>
                                        <div class="text-xs text-zinc-400 truncate mt-1">
                                            <span x-show="song.artist?.title" x-text="typeof song.artist?.title === 'object' ? (song.artist.title.tr || song.artist.title.en) : song.artist?.title"></span>
                                            <span x-show="song.album?.title">
                                                <span x-show="song.artist?.title"> • </span>
                                                <span x-text="typeof song.album?.title === 'object' ? (song.album.title.tr || song.album.title.en) : song.album?.title"></span>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Duration --}}
                                    <div class="text-xs text-zinc-500 flex-shrink-0">
                                        <span x-text="song.duration ? Math.floor(song.duration/60) + ':' + String(song.duration%60).padStart(2,'0') : ''"></span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="showEmptyState()" class="px-5 py-10 text-center text-sm text-zinc-400" x-cloak>
                    <i class="far fa-face-smile text-xl mb-2 block text-muzibu-coral"></i>
                    <span>Sonuç bulunamadı. Farklı bir kelime deneyin.</span>
                </div>

                {{-- View All Results Link --}}
                <a :href="`/search?q=${encodeURIComponent(query || '')}`"
                   x-show="total > 0"
                   class="block p-3 text-center text-muzibu-coral hover:bg-muzibu-coral/10 font-medium transition border-t border-white/10 text-sm">
                    <i class="fas fa-arrow-right mr-2"></i>
                    <span x-text="`Tüm ${total} sonucu gör`"></span>
                </a>
            </div>
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

                    {{-- Üyelik Tipi Badge (Sadece statik gösterim) --}}
                    @auth
                        @php
                            $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
                            $access = $subscriptionService->checkUserAccess(auth()->user());
                            $isTrial = $access['is_trial'] ?? false;
                            $isPremiumOrTrial = auth()->user()->isPremiumOrTrial();
                        @endphp

                        @if($isTrial)
                            <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-green-500/20 border border-green-500/30 rounded-full">
                                <i class="fas fa-gift text-green-400 text-[10px]"></i>
                                <span class="text-green-400 text-[10px] font-semibold">Deneme Üyesi</span>
                            </span>
                        @elseif($isPremiumOrTrial)
                            <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-yellow-500/20 border border-yellow-500/30 rounded-full">
                                <i class="fas fa-crown text-yellow-400 text-[10px]"></i>
                                <span class="text-yellow-400 text-[10px] font-semibold">Premium Üye</span>
                            </span>
                        @else
                            <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 bg-gray-500/20 border border-gray-500/30 rounded-full">
                                <span class="text-gray-400 text-[10px] font-semibold">Ücretsiz Üye</span>
                            </span>
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

                {{-- Premium'a Geç (ücretsiz üyeler için) --}}
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

                {{-- Üyeliğini Uzat (premium/trial üyeler için) --}}
                <a
                    href="/subscription/plans"
                    wire:navigate
                    x-show="currentUser?.is_premium"
                    @click="userMenuOpen = false"
                    class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-yellow-500/10 text-yellow-400 text-sm transition-colors"
                >
                    <i class="fas fa-sync-alt w-5"></i>
                    <span>Üyeliğini Uzat</span>
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
