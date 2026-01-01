{{-- LEFT SIDEBAR - Modern & Clean --}}
{{-- Desktop: Static sidebar (lg:flex) | Mobile/Tablet: Slide-in overlay (header altından başlar) --}}
<aside
    id="leftSidebar"
    class="muzibu-left-sidebar row-start-2 hidden lg:flex lg:flex-col"
>
    {{-- Scrollable Navigation Area --}}
    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent pt-3">
        {{-- Library Section --}}
        <div class="mb-3">
            <h3 class="px-4 text-xs font-bold text-muzibu-text-gray uppercase tracking-wider mb-2">{{ trans('muzibu::front.sidebar.my_library') }}</h3>
            <nav class="space-y-1">
                <a href="/muzibu/my-playlists" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-album-collection w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.sidebar.my_playlists') }}</span>
                </a>
                <a href="/muzibu/favorites" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-heart w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.general.favorites') }}</span>
                </a>
                <a href="/muzibu/corporate-playlists" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-briefcase w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.sidebar.corporate_playlists') }}</span>
                </a>
            </nav>
        </div>

        <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-4"></div>

        {{-- Browse Section --}}
        <div class="mb-3">
            <h3 class="px-4 text-xs font-bold text-muzibu-text-gray uppercase tracking-wider mb-2">{{ trans('muzibu::front.general.discover') }}</h3>
            <nav class="space-y-1">
                <a href="/playlists" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-list-music w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.general.playlists') }}</span>
                </a>
                <a href="/albums" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-microphone-lines w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.general.albums') }}</span>
                </a>
                <a href="/genres" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-guitars w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.general.genres') }}</span>
                </a>
                <a href="/sectors" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-building w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.general.sectors') }}</span>
                </a>
                <a href="/radios" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg transition-all duration-300"
                   x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false">
                    <i :class="h ? 'fas' : 'fal'" class="fa-radio w-5 text-base transition-all duration-200"></i>
                    <span class="font-medium text-sm">{{ trans('muzibu::front.general.radios') }}</span>
                </a>
            </nav>
        </div>
    </div>

    {{-- Anonslar Toggle - Sadece kurumsal kullanıcılar --}}
    @if(auth()->check())
    <div x-data="{
            spotEnabled: true,
            init() {
                // MuzibuSpotPlayer yüklendikten sonra durumu senkronize et
                const syncState = () => {
                    if (window.MuzibuSpotPlayer) {
                        this.spotEnabled = !window.MuzibuSpotPlayer.isPaused();
                    }
                };
                if (window.MuzibuSpotPlayer) {
                    setTimeout(syncState, 500);
                } else {
                    document.addEventListener('DOMContentLoaded', () => setTimeout(syncState, 500));
                }
            },
            toggle() {
                // Optimistic UI: Önce UI'ı değiştir
                this.spotEnabled = !this.spotEnabled;
                localStorage.setItem('muzibu_ads_enabled', this.spotEnabled);

                // Arka planda API çağır
                if (window.MuzibuSpotPlayer) {
                    window.MuzibuSpotPlayer.togglePause().then(result => {
                        if (!result.success) {
                            // API başarısız olursa geri al
                            this.spotEnabled = !this.spotEnabled;
                            localStorage.setItem('muzibu_ads_enabled', this.spotEnabled);
                        }
                    }).catch(() => {
                        // Hata olursa geri al
                        this.spotEnabled = !this.spotEnabled;
                        localStorage.setItem('muzibu_ads_enabled', this.spotEnabled);
                    });
                }
            }
         }"
         x-init="init()"
         class="flex-shrink-0 px-3 pb-2">
        <div @click="toggle()" class="flex items-center gap-2 cursor-pointer select-none py-1.5 px-2.5">
            <i class="fas text-[10px] text-white/50" :class="spotEnabled ? 'fa-bullhorn' : 'fa-ban'"></i>
            <span class="text-xs text-white/50" x-text="spotEnabled ? '{{ trans('muzibu::front.sidebar.spots_active') }}' : '{{ trans('muzibu::front.sidebar.spots_paused') }}'"></span>
            <div class="w-2.5 h-2.5 rounded-sm ml-auto"
                 :class="spotEnabled ? 'bg-green-400/60' : 'bg-red-400/60'"></div>
        </div>
    </div>
    @endif

    {{-- ============================================== --}}
    {{-- DESKTOP User Profile Card - Bottom (Style C) --}}
    {{-- ============================================== --}}
    <div class="hidden lg:block flex-shrink-0 p-3"
         x-data="{
            get currentUser() {
                return window.muzibuPlayerConfig?.currentUser || null;
            },
            get memberType() {
                const lang = window.muzibuPlayerConfig?.frontLang?.sidebar || {};
                if (!this.currentUser?.is_premium) {
                    return lang.free_member || 'Ücretsiz Üye';
                }
                if (this.currentUser?.is_trial) {
                    return lang.trial_member || 'Deneme Üyesi';
                }
                return lang.premium_member || 'Premium Üye';
            }
         }">

        {{-- LOGGED IN STATE --}}
        <div x-show="isLoggedIn" x-cloak class="bg-gradient-to-br from-[#ff6b6b] via-[#ff5252] to-[#e91e63] rounded-xl p-3 relative overflow-hidden">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(255,255,255,0.3),transparent_50%)]"></div>
            </div>

            {{-- Content --}}
            <div class="relative z-10">
                {{-- User Row - Avatar + Full Name --}}
                <div class="flex items-center gap-3 mb-3">
                    {{-- Avatar: Premium=Crown, Trial=Gift, Free=Letter --}}
                    <div class="w-10 h-10 flex-shrink-0 rounded-full bg-white/20 flex items-center justify-center border border-white/30">
                        <template x-if="currentUser?.is_premium && !currentUser?.is_trial">
                            <i class="fas fa-crown text-yellow-300 text-base"></i>
                        </template>
                        <template x-if="currentUser?.is_trial">
                            <i class="fas fa-gift text-yellow-300 text-base"></i>
                        </template>
                        <template x-if="!currentUser?.is_premium">
                            <span class="text-white font-bold text-sm" x-text="currentUser?.name ? currentUser.name.charAt(0).toUpperCase() : 'U'"></span>
                        </template>
                    </div>
                    {{-- Info - Full Width --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-semibold text-sm truncate" x-text="currentUser?.name || 'Kullanıcı'"></p>
                        <p class="text-white/70 text-xs truncate" x-text="memberType"></p>
                    </div>
                </div>

                {{-- Action Buttons - Side by Side --}}
                <div class="flex gap-2">
                    <a href="/profile" class="w-10 flex-shrink-0 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg text-sm font-medium text-center transition-all border border-white/20 hover:border-white/30">
                        <i class="fas fa-cog"></i>
                    </a>
                    <button
                        @click="logout()"
                        class="flex-1 bg-black/30 hover:bg-black/50 text-white py-2 rounded-lg text-sm font-medium transition-all border border-white/20 hover:border-white/30"
                    >
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        {{ trans('muzibu::front.general.logout') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- LOGGED OUT STATE --}}
        <div x-show="!isLoggedIn" x-cloak class="flex flex-col gap-2">
            <a href="/login" class="bg-gradient-to-r from-muzibu-coral to-[#ff9966] text-white py-2.5 rounded-lg text-sm font-semibold text-center hover:opacity-90 transition-all">
                <i class="fas fa-sign-in-alt mr-1"></i>
                {{ trans('muzibu::front.general.login') }}
            </a>
            <a href="/register" class="border border-muzibu-coral/60 hover:border-muzibu-coral hover:bg-muzibu-coral/10 text-muzibu-coral py-2.5 rounded-lg text-sm font-medium text-center transition-all">
                <i class="fas fa-user-plus mr-1"></i>
                {{ trans('muzibu::front.general.register') }}
            </a>
        </div>
    </div>

    {{-- ============================================== --}}
    {{-- MOBILE/TABLET Bottom User Card - Style C --}}
    {{-- ============================================== --}}
    <div class="lg:hidden flex-shrink-0 p-3 bg-[#0d0d0d]"
         x-data="{
            get currentUser() {
                return window.muzibuPlayerConfig?.currentUser || null;
            },
            get memberType() {
                const lang = window.muzibuPlayerConfig?.frontLang?.sidebar || {};
                if (!this.currentUser?.is_premium) {
                    return lang.free_member || 'Ücretsiz Üye';
                }
                if (this.currentUser?.is_trial) {
                    return lang.trial_member || 'Deneme Üyesi';
                }
                return lang.premium_member || 'Premium Üye';
            }
         }">

        {{-- LOGGED IN STATE --}}
        <div x-show="isLoggedIn" x-cloak class="bg-gradient-to-br from-[#ff6b6b] via-[#ff5252] to-[#e91e63] rounded-xl p-3 relative overflow-hidden">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(255,255,255,0.3),transparent_50%)]"></div>
            </div>

            {{-- Content --}}
            <div class="relative z-10">
                {{-- User Row - Avatar + Full Name --}}
                <div class="flex items-center gap-3 mb-3">
                    {{-- Avatar: Premium=Crown, Trial=Gift, Free=Letter --}}
                    <div class="w-10 h-10 flex-shrink-0 rounded-full bg-white/20 flex items-center justify-center border border-white/30">
                        <template x-if="currentUser?.is_premium && !currentUser?.is_trial">
                            <i class="fas fa-crown text-yellow-300 text-base"></i>
                        </template>
                        <template x-if="currentUser?.is_trial">
                            <i class="fas fa-gift text-yellow-300 text-base"></i>
                        </template>
                        <template x-if="!currentUser?.is_premium">
                            <span class="text-white font-bold text-sm" x-text="currentUser?.name ? currentUser.name.charAt(0).toUpperCase() : 'U'"></span>
                        </template>
                    </div>
                    {{-- Info - Full Width --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-semibold text-sm truncate" x-text="currentUser?.name || 'Kullanıcı'"></p>
                        <p class="text-white/70 text-xs truncate" x-text="memberType"></p>
                    </div>
                </div>

                {{-- Action Buttons - Side by Side --}}
                <div class="flex gap-2">
                    <a href="/profile" class="w-10 flex-shrink-0 bg-white/10 hover:bg-white/20 text-white py-2 rounded-lg text-sm font-medium text-center transition-all border border-white/20 hover:border-white/30">
                        <i class="fas fa-cog"></i>
                    </a>
                    <button
                        @click="logout(); closeMobileMenu();"
                        class="flex-1 bg-black/30 hover:bg-black/50 text-white py-2 rounded-lg text-sm font-medium transition-all border border-white/20 hover:border-white/30"
                    >
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        {{ trans('muzibu::front.general.logout') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- LOGGED OUT STATE --}}
        <div x-show="!isLoggedIn" x-cloak class="flex flex-col gap-2">
            <a href="/login" class="bg-gradient-to-r from-muzibu-coral to-[#ff9966] text-white py-2.5 rounded-lg text-sm font-semibold text-center hover:opacity-90 transition-all">
                <i class="fas fa-sign-in-alt mr-1"></i>
                {{ trans('muzibu::front.general.login') }}
            </a>
            <a href="/register" class="border border-muzibu-coral/60 hover:border-muzibu-coral hover:bg-muzibu-coral/10 text-muzibu-coral py-2.5 rounded-lg text-sm font-medium text-center transition-all">
                <i class="fas fa-user-plus mr-1"></i>
                {{ trans('muzibu::front.general.register') }}
            </a>
        </div>
    </div>
</aside>
