{{-- LEFT SIDEBAR - Modern & Clean --}}
<aside
    class="muzibu-left-sidebar hidden lg:flex lg:flex-col animate-slide-up"
    :class="mobileMenuOpen ? 'flex fixed inset-0 z-50 lg:relative' : 'hidden lg:flex'"
    @click.away="mobileMenuOpen = false"
>
    {{-- Library Section --}}
    <div class="mb-3">
        <h3 class="px-4 text-xs font-bold text-muzibu-text-gray uppercase tracking-wider mb-2">Kitaplığım</h3>
        <nav class="space-y-1">
            <a href="{{ route('muzibu.my-playlists') }}" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-list w-5 text-base"></i>
                <span class="font-medium text-sm">Playlistlerim</span>
            </a>
            <a href="{{ route('muzibu.favorites') }}" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-heart w-5 text-base"></i>
                <span class="font-medium text-sm">Favorilerim</span>
            </a>
        </nav>
    </div>

    <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-4"></div>

    {{-- Browse Section --}}
    <div class="mb-3">
        <h3 class="px-4 text-xs font-bold text-muzibu-text-gray uppercase tracking-wider mb-2">Keşfet</h3>
        <nav class="space-y-1">
            <a href="{{ route('muzibu.playlists.index') }}" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-fire w-5 text-base"></i>
                <span class="font-medium text-sm">Popüler Playlistler</span>
            </a>
            <a href="{{ route('muzibu.albums.index') }}" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-compact-disc w-5 text-base"></i>
                <span class="font-medium text-sm">Albümler</span>
            </a>
            <a href="{{ route('muzibu.genres.index') }}" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-music w-5 text-base"></i>
                <span class="font-medium text-sm">Türler</span>
            </a>
            <a href="{{ route('muzibu.sectors.index') }}" class="flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
                <i class="fas fa-compass w-5 text-base"></i>
                <span class="font-medium text-sm">Kategoriler</span>
            </a>
        </nav>
    </div>

    <div class="h-px bg-gradient-to-r from-transparent via-white/10 to-transparent my-4"></div>

    {{-- Actions --}}
    <nav class="space-y-1">
        <button @click="$dispatch('open-create-playlist-modal')" class="w-full flex items-center gap-3 px-4 py-2 text-muzibu-text-gray hover:text-white hover:bg-white/5 rounded-lg group transition-all duration-300">
            <i class="fas fa-plus-circle w-5 text-base group-hover:text-muzibu-coral transition-colors"></i>
            <span class="font-medium text-sm">Playlist Oluştur</span>
        </button>
    </nav>

    {{-- Spacer to push auth card to bottom --}}
    <div class="flex-1"></div>

    {{-- User Profile Card - Bottom --}}
    <div
        x-show="isLoggedIn"
        x-cloak
        class="mt-4 bg-gradient-to-br from-[#ff6b6b] via-[#ff5252] to-[#e91e63] rounded-2xl p-4 shadow-xl relative overflow-hidden group"
    >
        {{-- Animated Background Pattern --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(255,255,255,0.3),transparent_50%)]"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10">
            {{-- User Info --}}
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl border-2 border-white/30">
                    🌟
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-bold text-sm truncate" x-text="currentUser?.name || 'Kullanıcı'"></h3>
                    <template x-if="currentUser?.is_premium">
                        <p class="text-white/90 text-xs flex items-center gap-1">
                            <i class="fas fa-crown text-yellow-300"></i>
                            <span>Premium</span>
                        </p>
                    </template>
                    <template x-if="!currentUser?.is_premium">
                        <p class="text-white/90 text-xs">Ücretsiz Üye</p>
                    </template>
                </div>
            </div>

            {{-- Today's Stats - DEVRE DIŞI (3 şarkı limiti kaldırıldı) --}}
            {{--
            <div class="bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 mb-3 border border-white/20">
                <p class="text-white/80 text-xs mb-1">Bugün Dinlenen</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-white font-bold text-2xl" x-text="todayPlayedCount || 0"></span>
                    <template x-if="!currentUser?.is_premium">
                        <span class="text-white/70 text-sm">/3 şarkı</span>
                    </template>
                    <template x-if="currentUser?.is_premium">
                        <span class="text-white/70 text-sm">şarkı</span>
                    </template>
                </div>
            </div>
            --}}

            {{-- Logout Button --}}
            <button
                @click="logout()"
                class="w-full bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 border border-white/20 hover:border-white/40"
            >
                <i class="fas fa-sign-out-alt mr-2"></i>
                Çıkış Yap
            </button>
        </div>
    </div>
</aside>
