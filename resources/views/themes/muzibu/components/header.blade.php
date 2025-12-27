<header class="row-start-1 bg-black/80 backdrop-blur-md border-b border-white/5 px-4 flex items-center justify-between sticky top-0 z-50" style="grid-column: 1 / -1;">
    <div class="flex items-center gap-4 flex-1">
        {{-- Mobile Hamburger --}}
        <button
            onclick="toggleMobileMenu()"
            class="lg:hidden text-muzibu-text-gray hover:text-white transition-colors"
        >
            <i class="fas fa-bars text-xl"></i>
        </button>

        {{-- Logo with animation - Settings powered --}}
        <a href="/"
           @click="if ($store.sidebar) $store.sidebar.reset()"
           class="text-2xl font-bold group flex items-center">
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

        {{-- Cache Clear Button - Icon Only (Logonun yanında) - SADECE ADMIN/ROOT/EDITOR --}}
        @auth
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('root') || auth()->user()->hasRole('editor'))
        <button
            @click="clearCache()"
            class="w-9 h-9 bg-white/5 hover:bg-muzibu-coral/20 rounded-lg flex items-center justify-center text-muzibu-text-gray hover:text-muzibu-coral transition-all duration-300 group"
            title="{{ trans('muzibu::front.admin.clear_cache') }}"
            x-data="{
                async clearCache() {
                    // ✅ 1. AI Conversation'ı veritabanından sil
                    try {
                        const aiSession = localStorage.getItem('tenant1001_ai_session');
                        if (aiSession) {
                            const session = JSON.parse(aiSession);
                            const conversationId = session.conversationId;

                            if (conversationId) {
                                // DELETE conversation from DB
                                await fetch(`/api/ai/v1/conversation/${conversationId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    }
                                });
                                console.log('✅ AI conversation deleted from DB (ID:', conversationId, ')');
                            }
                        }

                        // ✅ 2. localStorage'dan da temizle
                        localStorage.removeItem('tenant1001_ai_session');
                        console.log('✅ AI conversation cleared from localStorage');
                    } catch (e) {
                        console.warn('⚠️ AI conversation clear failed:', e);
                    }

                    // ✅ 3. Cache temizleme isteği
                    fetch('/admin/cache/clear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('✅ Cache cleared:', data);
                        // Sayfa yenile (AI conversation da temizlenmiş olacak)
                        window.location.reload();
                    })
                    .catch(error => {
                        console.error('❌ Cache clear error:', error);
                        // Hata olsa bile sayfa yenile
                        window.location.reload();
                    });
                }
            }"
        >
            <i class="fas fa-sync-alt text-sm group-hover:rotate-180 transition-transform duration-500"></i>
        </button>
            @endif
        @endauth

        {{-- Search Box - Meilisearch Powered (All Types) --}}
        {{-- Hidden below 1024px (mobile/tablet mode) --}}
        <div class="relative flex-1 max-w-2xl mx-auto hidden lg:block"
             x-data="{
                query: '',
                results: { songs: [], albums: [], artists: [], playlists: [], genres: [], sectors: [], radios: [] },
                total: 0,
                isOpen: false,
                loading: false,
                error: null,
                highlightIndex: -1,
                get allResults() {
                    const all = [];
                    if (this.results.songs?.length) all.push(...this.results.songs.map(s => ({...s, _type: 'song'})));
                    if (this.results.albums?.length) all.push(...this.results.albums.map(a => ({...a, _type: 'album'})));
                    if (this.results.artists?.length) all.push(...this.results.artists.map(a => ({...a, _type: 'artist'})));
                    if (this.results.playlists?.length) all.push(...this.results.playlists.map(p => ({...p, _type: 'playlist'})));
                    if (this.results.genres?.length) all.push(...this.results.genres.map(g => ({...g, _type: 'genre'})));
                    if (this.results.sectors?.length) all.push(...this.results.sectors.map(s => ({...s, _type: 'sector'})));
                    if (this.results.radios?.length) all.push(...this.results.radios.map(r => ({...r, _type: 'radio'})));
                    return all.slice(0, 10);
                },
                get hasResults() {
                    return this.allResults.length > 0;
                },
                get resultCount() {
                    return this.allResults.length;
                },
                resetSuggestions() {
                    this.results = { songs: [], albums: [], artists: [], playlists: [], genres: [], sectors: [], radios: [] };
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
                        const response = await fetch(`/api/muzibu/search?q=${encodeURIComponent(trimmed)}&type=all`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const data = await response.json();
                        this.results = {
                            songs: (data.songs || []).slice(0, 4),
                            albums: (data.albums || []).slice(0, 2),
                            artists: (data.artists || []).slice(0, 2),
                            playlists: (data.playlists || []).slice(0, 2),
                            genres: (data.genres || []).slice(0, 2),
                            sectors: (data.sectors || []).slice(0, 2),
                            radios: (data.radios || []).slice(0, 2)
                        };
                        this.total = (data.songs?.length || 0) + (data.albums?.length || 0) + (data.artists?.length || 0) + (data.playlists?.length || 0) + (data.genres?.length || 0) + (data.sectors?.length || 0) + (data.radios?.length || 0);
                        this.highlightIndex = -1;
                    } catch (e) {
                        console.error('Search error:', e);
                        this.resetSuggestions();
                        this.error = window.muzibuPlayerConfig?.frontLang?.search?.search_failed || 'Search failed.';
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
                    const item = this.allResults[this.highlightIndex];
                    if (item) {
                        this.selectItem(item);
                    }
                },
                getSlug(item) {
                    const locale = '{{ app()->getLocale() }}';
                    return typeof item.slug === 'object' ? item.slug[locale] || item.slug.tr || item.slug.en : item.slug;
                },
                getTitle(item) {
                    const locale = '{{ app()->getLocale() }}';
                    return typeof item.title === 'object' ? item.title[locale] || item.title.tr || item.title.en : item.title;
                },
                selectItem(item) {
                    const slug = this.getSlug(item);
                    if (!slug) return;
                    const routes = {
                        song: `/songs/${slug}`,
                        album: `/albums/${slug}`,
                        artist: `/artists/${slug}`,
                        playlist: `/playlists/${slug}`,
                        genre: `/genres/${slug}`,
                        sector: `/sectors/${slug}`,
                        radio: `/radios/${slug}`
                    };
                    // Use SPA navigation
                    const targetUrl = routes[item._type];
                    if (targetUrl && window.muzibuApp) {
                        window.muzibuApp().navigateTo(targetUrl);
                    } else {
                        window.location.href = targetUrl || '/';
                    }
                    this.closeDropdown();
                },
                playSong(song, event) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (window.Alpine?.store('player')?.playSingle) {
                        window.Alpine.store('player').playSingle({
                            song_id: song.song_id,
                            title: song.title,
                            slug: song.slug,
                            duration: song.duration,
                            file_path: song.file_path,
                            hls_path: song.hls_path,
                            album: song.album,
                            artist: song.artist
                        });
                    }
                    this.closeDropdown();
                },
                getBadge(type) {
                    const lang = window.muzibuPlayerConfig?.frontLang?.general || {};
                    const badges = {
                        song: { icon: 'fa-music', label: lang.song || 'Song', color: 'bg-pink-500/20 text-pink-400' },
                        album: { icon: 'fa-record-vinyl', label: lang.album || 'Album', color: 'bg-purple-500/20 text-purple-400' },
                        artist: { icon: 'fa-user', label: lang.artist || 'Artist', color: 'bg-blue-500/20 text-blue-400' },
                        playlist: { icon: 'fa-list-music', label: lang.playlist || 'Playlist', color: 'bg-green-500/20 text-green-400' },
                        genre: { icon: 'fa-guitar', label: lang.genre || 'Genre', color: 'bg-yellow-500/20 text-yellow-400' },
                        sector: { icon: 'fa-building', label: lang.sector || 'Sector', color: 'bg-orange-500/20 text-orange-400' },
                        radio: { icon: 'fa-broadcast-tower', label: lang.radio || 'Radio', color: 'bg-red-500/20 text-red-400' }
                    };
                    return badges[type] || { icon: 'fa-circle', label: type, color: 'bg-gray-500/20 text-gray-400' };
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
                    placeholder="{{ trans('muzibu::front.search.placeholder') }}"
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

            {{-- Search Results Dropdown - 2 Column Layout --}}
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 class="absolute top-full left-0 right-0 mt-3 bg-zinc-900/95 backdrop-blur-xl shadow-2xl rounded-2xl border border-white/10 overflow-hidden z-50"
                 style="display: none;">

                {{-- Error State --}}
                <template x-if="error">
                    <div class="px-5 py-6 text-sm text-red-400 flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-base"></i>
                        <span x-text="error"></span>
                    </div>
                </template>

                {{-- Results - 2 Column Grid Layout --}}
                <div class="max-h-[500px] overflow-y-auto p-4">
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Left Column: Songs --}}
                        <div x-show="results.songs?.length > 0" class="space-y-2">
                            <div class="flex items-center gap-2 mb-3 pb-2 border-b border-white/5">
                                <i class="fas fa-music text-pink-400 text-xs"></i>
                                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.general.songs') }}</span>
                                <span class="ml-auto text-[10px] text-zinc-500" x-text="results.songs.length"></span>
                            </div>
                            <template x-for="(song, index) in results.songs" :key="'song-'+song.song_id">
                                <a href="#"
                                   @click.prevent="selectItem({...song, _type: 'song'})"
                                   class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-white/5 transition-all group">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-500/20 to-purple-500/20 flex items-center justify-center flex-shrink-0 relative overflow-hidden">
                                        <i class="fas fa-music text-pink-400/60 text-sm"></i>
                                        <button
                                            @click="playSong(song, $event)"
                                            class="absolute inset-0 bg-muzibu-coral flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all"
                                            title="{{ trans('muzibu::front.player.play') }}"
                                        >
                                            <i class="fas fa-play text-white text-xs ml-0.5"></i>
                                        </button>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-white truncate" x-text="getTitle(song)"></div>
                                        <div class="text-xs text-zinc-500 truncate" x-text="song.artist?.title ? (typeof song.artist.title === 'object' ? song.artist.title.tr || song.artist.title.en : song.artist.title) : ''"></div>
                                    </div>
                                    <span class="text-[10px] text-zinc-600 group-hover:hidden" x-text="song.duration ? Math.floor(song.duration/60) + ':' + String(song.duration%60).padStart(2,'0') : ''"></span>
                                </a>
                            </template>
                        </div>

                        {{-- Right Column: Other Types --}}
                        <div class="space-y-4">
                            {{-- Albums --}}
                            <div x-show="results.albums?.length > 0" class="space-y-2">
                                <div class="flex items-center gap-2 mb-2 pb-1.5 border-b border-white/5">
                                    <i class="fas fa-record-vinyl text-purple-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.general.albums') }}</span>
                                </div>
                                <template x-for="album in results.albums" :key="'album-'+album.album_id">
                                    <a href="#"
                                       @click.prevent="selectItem({...album, _type: 'album'})"
                                       class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/5 transition-all">
                                        <div class="w-8 h-8 rounded bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-record-vinyl text-purple-400/60 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-white truncate" x-text="getTitle(album)"></div>
                                        </div>
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-purple-500/20 text-purple-400">{{ trans('muzibu::front.general.album') }}</span>
                                    </a>
                                </template>
                            </div>

                            {{-- Artists --}}
                            <div x-show="results.artists?.length > 0" class="space-y-2">
                                <div class="flex items-center gap-2 mb-2 pb-1.5 border-b border-white/5">
                                    <i class="fas fa-user text-blue-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.general.artists') }}</span>
                                </div>
                                <template x-for="artist in results.artists" :key="'artist-'+artist.artist_id">
                                    <a href="#"
                                       @click.prevent="selectItem({...artist, _type: 'artist'})"
                                       class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/5 transition-all">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-blue-400/60 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-white truncate" x-text="getTitle(artist)"></div>
                                        </div>
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-blue-500/20 text-blue-400">{{ trans('muzibu::front.general.artist') }}</span>
                                    </a>
                                </template>
                            </div>

                            {{-- Playlists --}}
                            <div x-show="results.playlists?.length > 0" class="space-y-2">
                                <div class="flex items-center gap-2 mb-2 pb-1.5 border-b border-white/5">
                                    <i class="fas fa-list-music text-green-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.general.playlists') }}</span>
                                </div>
                                <template x-for="playlist in results.playlists" :key="'playlist-'+playlist.playlist_id">
                                    <a href="#"
                                       @click.prevent="selectItem({...playlist, _type: 'playlist'})"
                                       class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/5 transition-all">
                                        <div class="w-8 h-8 rounded bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-list-music text-green-400/60 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-white truncate" x-text="getTitle(playlist)"></div>
                                        </div>
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-green-500/20 text-green-400">{{ trans('muzibu::front.general.playlist') }}</span>
                                    </a>
                                </template>
                            </div>

                            {{-- Genres --}}
                            <div x-show="results.genres?.length > 0" class="space-y-2">
                                <div class="flex items-center gap-2 mb-2 pb-1.5 border-b border-white/5">
                                    <i class="fas fa-guitar text-yellow-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.general.genres') }}</span>
                                </div>
                                <template x-for="genre in results.genres" :key="'genre-'+genre.genre_id">
                                    <a href="#"
                                       @click.prevent="selectItem({...genre, _type: 'genre'})"
                                       class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/5 transition-all">
                                        <div class="w-8 h-8 rounded bg-gradient-to-br from-yellow-500/20 to-orange-500/20 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-guitar text-yellow-400/60 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-white truncate" x-text="getTitle(genre)"></div>
                                        </div>
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-yellow-500/20 text-yellow-400">{{ trans('muzibu::front.general.genre') }}</span>
                                    </a>
                                </template>
                            </div>

                            {{-- Sectors --}}
                            <div x-show="results.sectors?.length > 0" class="space-y-2">
                                <div class="flex items-center gap-2 mb-2 pb-1.5 border-b border-white/5">
                                    <i class="fas fa-building text-orange-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.general.sectors') }}</span>
                                </div>
                                <template x-for="sector in results.sectors" :key="'sector-'+sector.sector_id">
                                    <a href="#"
                                       @click.prevent="selectItem({...sector, _type: 'sector'})"
                                       class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/5 transition-all">
                                        <div class="w-8 h-8 rounded bg-gradient-to-br from-orange-500/20 to-red-500/20 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-building text-orange-400/60 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-white truncate" x-text="getTitle(sector)"></div>
                                        </div>
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-orange-500/20 text-orange-400">{{ trans('muzibu::front.general.sector') }}</span>
                                    </a>
                                </template>
                            </div>

                            {{-- Radios --}}
                            <div x-show="results.radios?.length > 0" class="space-y-2">
                                <div class="flex items-center gap-2 mb-2 pb-1.5 border-b border-white/5">
                                    <i class="fas fa-broadcast-tower text-red-400 text-xs"></i>
                                    <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.general.radios') }}</span>
                                </div>
                                <template x-for="radio in results.radios" :key="'radio-'+radio.radio_id">
                                    <a href="#"
                                       @click.prevent="selectItem({...radio, _type: 'radio'})"
                                       class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-white/5 transition-all">
                                        <div class="w-8 h-8 rounded bg-gradient-to-br from-red-500/20 to-pink-500/20 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-broadcast-tower text-red-400/60 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-white truncate" x-text="getTitle(radio)"></div>
                                        </div>
                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded bg-red-500/20 text-red-400">{{ trans('muzibu::front.general.radio') }}</span>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="showEmptyState()" class="px-5 py-10 text-center text-sm text-zinc-400" x-cloak>
                    <i class="far fa-face-smile text-xl mb-2 block text-muzibu-coral"></i>
                    <span>{{ trans('muzibu::front.search.try_different') }}</span>
                </div>

                {{-- View All Results Link --}}
                <a :href="`/search?q=${encodeURIComponent(query || '')}`"
                   x-show="total > 0"
                   class="block p-3 text-center text-muzibu-coral hover:bg-muzibu-coral/10 font-medium transition border-t border-white/10 text-sm rounded-b-2xl">
                    <i class="fas fa-arrow-right mr-2"></i>
                    <span x-text="(window.muzibuPlayerConfig?.frontLang?.search?.view_all_results || 'View all :count results').replace(':count', total)"></span>
                </a>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-5">
        {{-- 🧪 DEBUG: Auth Status (PHP + JS karşılaştırması) --}}
        <div class="hidden" x-data x-init="
            const phpAuth = {{ auth()->check() ? 'true' : 'false' }};
            const jsAuth = isLoggedIn;

            if (phpAuth !== jsAuth) {
                console.warn('⚠️ Auth TUTARSIZLIK:', {
                    php_auth: phpAuth,
                    js_isLoggedIn: jsAuth,
                    aciklama: phpAuth && !jsAuth
                        ? 'PHP login ama JS logout - muhtemelen HLS 401 sonrası yanlış logout'
                        : 'JS login ama PHP logout - session expired'
                });
            } else {
                console.log('✅ Auth Tutarlı:', { isLoggedIn: jsAuth, currentUser: currentUser?.name || null });
            }
        "></div>

        {{-- Premium Button (non-premium only) - SPA Reactive --}}
        <a
            href="/subscription/plans"
           
            x-show="isLoggedIn && (!currentUser?.is_premium)"
            x-cloak
            class="hidden sm:flex items-center gap-2 px-4 py-2 border border-muzibu-coral/40 hover:border-muzibu-coral hover:bg-muzibu-coral/10 rounded-full text-muzibu-coral text-sm font-semibold transition-all duration-300"
        >
            <i class="fas fa-crown text-xs"></i>
            <span class="hidden md:inline">{{ trans('muzibu::front.user.go_premium') }}</span>
            <span class="md:hidden">Premium</span>
        </a>

        {{-- 🛒 Cart Icon - Devre dışı (Muzibu'da sepet yok) --}}

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
                    <p class="text-white font-semibold text-sm" x-text="currentUser?.name || (window.muzibuPlayerConfig?.frontLang?.user?.user || 'User')"></p>
                    <p class="text-zinc-400 text-xs" x-text="currentUser?.email || ''"></p>

                    {{-- Premium Badge --}}
                    <div
                        x-show="currentUser?.is_premium"
                        class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-full"
                    >
                        <i class="fas fa-crown text-yellow-400 text-xs"></i>
                        <span class="text-yellow-400 text-xs font-semibold">{{ trans('muzibu::front.user.premium_member') }}</span>
                    </div>

                    {{-- Trial Badge (Sadece trial üyeler için) --}}
                    @auth
                        @php
                            $subscriptionService = app(\Modules\Subscription\App\Services\SubscriptionService::class);
                            $access = $subscriptionService->checkUserAccess(auth()->user());
                            $isTrial = $access['is_trial'] ?? false;
                            $isPremium = auth()->user()->isPremiumOrTrial();
                        @endphp

                        @if($isTrial && !$isPremium)
                            {{-- Sadece deneme üyeliği var, premium yok --}}
                            <div class="mt-2 inline-flex items-center gap-1 px-2 py-0.5 bg-green-500/20 border border-green-500/30 rounded-full">
                                <i class="fas fa-gift text-green-400 text-[10px]"></i>
                                <span class="text-green-400 text-[10px] font-semibold">{{ trans('muzibu::front.user.trial_member') }}</span>
                            </div>
                        @endif
                    @endauth
                </div>

                {{-- Admin Panel (Sadece admin/root/editor yetkisi olanlara) --}}
                @auth
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('root') || auth()->user()->hasRole('editor'))
                        <a href="/admin" target="_blank" rel="noopener noreferrer" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-blue-500/10 text-blue-400 text-sm transition-colors">
                            <i class="fas fa-user-shield w-5"></i>
                            <span>{{ trans('muzibu::front.user.admin_panel') }}</span>
                            <i class="fas fa-external-link-alt ml-auto text-xs opacity-50"></i>
                        </a>
                        <div class="h-px bg-white/10 my-1"></div>
                    @endif
                @endauth

                {{-- Kurumsal Panel (Sadece kurumsal sahipleri için) --}}
                @auth
                    @php
                        $isCorporateOwner = \Modules\Muzibu\App\Models\MuzibuCorporateAccount::where('user_id', auth()->id())
                            ->whereNull('parent_id')
                            ->exists();
                    @endphp
                    @if($isCorporateOwner)
                        <a href="/corporate/dashboard" @click="userMenuOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-purple-500/10 text-purple-400 text-sm transition-colors" data-spa>
                            <i class="fas fa-building w-5"></i>
                            <span>Kurumsal Panel</span>
                        </a>
                    @endif
                @endauth

                {{-- Dashboard Link --}}
                <a href="/dashboard" @click="userMenuOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-white text-sm transition-colors">
                    <i class="fas fa-th-large w-5"></i>
                    <span>{{ trans('muzibu::front.user.dashboard') }}</span>
                </a>

                {{-- Profile Link --}}
                <a href="/profile" @click="userMenuOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-white/5 text-white text-sm transition-colors">
                    <i class="fas fa-user w-5"></i>
                    <span>{{ trans('muzibu::front.user.profile') }}</span>
                </a>

                <div class="h-px bg-white/10 my-1"></div>

                {{-- Premium'a Geç (ücretsiz üyeler için) --}}
                <a
                    href="/subscription/plans"

                    x-show="!currentUser?.is_premium"
                    @click="userMenuOpen = false"
                    class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-yellow-500/10 text-yellow-400 text-sm transition-colors"
                >
                    <i class="fas fa-crown w-5"></i>
                    <span>{{ trans('muzibu::front.user.go_premium') }}</span>
                </a>

                {{-- Üyeliğini Uzat (premium/trial üyeler için - 30 günden az kaldıysa göster) --}}
                @auth
                    @php
                        $subscriptionServiceHeader = app(\Modules\Subscription\App\Services\SubscriptionService::class);
                        $accessHeader = $subscriptionServiceHeader->checkUserAccess(auth()->user());
                        $daysRemainingHeader = $accessHeader['days_remaining'] ?? null;
                        $showExtendButtonHeader = $daysRemainingHeader !== null && $daysRemainingHeader <= 30;
                    @endphp
                    @if($showExtendButtonHeader && auth()->user()->isPremiumOrTrial())
                        <a
                            href="/subscription/plans"
                            @click="userMenuOpen = false"
                            class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-yellow-500/10 text-yellow-400 text-sm transition-colors"
                        >
                            <i class="fas fa-sync-alt w-5"></i>
                            <span>{{ trans('muzibu::front.sidebar.extend_membership') }}</span>
                        </a>
                    @endif
                @endauth

                <div class="h-px bg-white/10 my-1"></div>

                {{-- Logout --}}
                <button @click.prevent="logout()" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-500/10 text-red-400 text-sm transition-colors">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>{{ trans('muzibu::front.general.logout') }}</span>
                </button>
            </div>
        </div>

        {{-- Login/Register Links - Direct page navigation --}}
        <div x-show="!isLoggedIn" x-cloak class="flex items-center gap-3">
            <a
                href="/login"
               
                class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 rounded-full text-white text-sm font-semibold transition-all duration-300"
            >
                <i class="fas fa-sign-in-alt text-xs"></i>
                <span>{{ trans('muzibu::front.general.login') }}</span>
            </a>
            <a
                href="/register"

                class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light hover:from-muzibu-coral-light hover:to-muzibu-coral rounded-full text-white text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-muzibu-coral/30"
            >
                <i class="fas fa-user-plus text-xs"></i>
                <span class="hidden md:inline">{{ trans('muzibu::front.general.register') }}</span>
                <span class="md:hidden">{{ trans('muzibu::front.general.register') }}</span>
            </a>
        </div>
    </div>
</header>
