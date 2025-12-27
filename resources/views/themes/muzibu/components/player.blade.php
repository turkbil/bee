{{-- PLAYER BAR --}}

{{-- ==================== MOBILE PLAYER - Ring Progress (< 1024px) ==================== --}}
<style>
    @media (min-width: 1024px) { .mobile-player-wrapper { display: none !important; } }
    @media (max-width: 1023px) { .desktop-player-wrapper { display: none !important; } }

    /* Gradient Border */
    .mobile-player-wrapper {
        background: linear-gradient(#18181b, #18181b) padding-box,
                    linear-gradient(135deg, #ff8a00, #ff5e62, #ec4899) border-box;
        border: 2px solid transparent;
    }
</style>
<div class="mobile-player-wrapper row-start-3 col-span-full mx-3 mb-3 px-3 py-2 relative rounded-full shadow-lg">

    <div class="flex items-center gap-3">
        {{-- Cover with Progress Ring --}}
        <div class="relative w-12 h-12 flex-shrink-0">
            {{-- Progress Ring (pink) --}}
            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 48 48">
                <circle cx="24" cy="24" r="21" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3"/>
                <circle cx="24" cy="24" r="21" fill="none" stroke="#ff8a00" stroke-width="3"
                        stroke-linecap="round"
                        :stroke-dasharray="132"
                        :stroke-dashoffset="132 - (132 * progressPercent / 100)"/>
            </svg>
            {{-- Album Cover (simple circle) --}}
            <div class="absolute inset-[4px] rounded-full overflow-hidden bg-zinc-800 flex items-center justify-center">
                <img x-ref="mobileCover"
                     x-effect="const cover = currentSong?.album_cover || currentSong?.cover_url; if(cover && $refs.mobileCover) { $refs.mobileCover.src = (typeof cover === 'string' && cover.startsWith('http')) ? cover : `/thumb/${cover}/100/100`; }"
                     alt="Cover"
                     class="absolute inset-0 w-full h-full object-cover">
                <i x-show="!currentSong?.cover_url && !currentSong?.album_cover" class="fas fa-music text-zinc-600 text-sm"></i>
            </div>
            {{-- Time Badge --}}
            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-zinc-800 text-white text-[10px] px-1.5 py-0.5 rounded-full border border-zinc-700"
                 x-text="formatTime(currentTime)">0:00</div>
        </div>

        {{-- Song Info: Title + Artist --}}
        <div class="flex-1 min-w-0">
            <p class="text-white text-sm font-medium truncate"
               x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || 'Şarkı') : 'Şarkı Seç'">
                Şarkı Seç
            </p>
            <p class="text-zinc-400 text-xs truncate"
               x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || '') : ''">
            </p>
        </div>

        {{-- Controls: Prev, Play/Pause, Next --}}
        <div class="flex items-center gap-0.5">
            <button class="w-9 h-9 text-white/80 flex items-center justify-center active:scale-90 transition-transform"
                    @click="previousTrack()">
                <i class="fas fa-backward text-sm"></i>
            </button>
            <button class="w-10 h-10 text-white flex items-center justify-center active:scale-95 transition-transform"
                    @click="togglePlayPause()">
                <i x-show="isSongLoading" x-cloak class="fas fa-spinner fa-spin text-lg"></i>
                <i x-show="!isSongLoading && isPlaying" x-cloak class="fas fa-pause text-xl"></i>
                <i x-show="!isSongLoading && !isPlaying" class="fas fa-play text-xl ml-0.5"></i>
            </button>
            <button class="w-9 h-9 text-white/80 flex items-center justify-center active:scale-90 transition-transform"
                    @click="nextTrack()">
                <i class="fas fa-forward text-sm"></i>
            </button>
        </div>

        {{-- Three Dots Menu --}}
        <button class="w-8 h-8 text-white/60 flex items-center justify-center active:scale-90 transition-transform"
                @click="showMobileMenu = !showMobileMenu">
            <i class="fas fa-ellipsis-v"></i>
        </button>
    </div>

    {{-- Mobile Context Menu --}}
    <div x-show="showMobileMenu"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         @click.away="showMobileMenu = false"
         class="absolute bottom-full left-0 right-0 mb-2 mx-3 bg-zinc-900 rounded-xl border border-zinc-700 shadow-2xl overflow-hidden z-50">

        {{-- Menu Header --}}
        <div class="px-4 py-3 border-b border-zinc-700 bg-zinc-800">
            <p class="text-white font-medium text-sm truncate"
               x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title) : ''"></p>
            <p class="text-zinc-400 text-xs truncate"
               x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title) : ''"></p>
        </div>

        {{-- Menu Items --}}
        <div class="py-1 max-h-64 overflow-y-auto">
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="toggleLike(); showMobileMenu = false">
                <i :class="isLiked ? 'fas fa-heart text-pink-500' : 'far fa-heart text-zinc-400'" class="w-5 text-center"></i>
                <span x-text="isLiked ? 'Favorilerden Çıkar' : 'Favorilere Ekle'"></span>
            </button>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="showQueue = true; showMobileMenu = false">
                <i class="fas fa-list-ul text-orange-400 w-5 text-center"></i>
                <span>Sıradakiler</span>
            </button>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="if(currentSong) { $store.playlist.openModal(currentSong.song_id); } showMobileMenu = false">
                <i class="fas fa-plus text-green-400 w-5 text-center"></i>
                <span>Playlist'e Ekle</span>
            </button>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="toggleShuffle(); showMobileMenu = false">
                <i class="fas fa-random w-5 text-center" :class="shuffle ? 'text-emerald-400' : 'text-zinc-400'"></i>
                <span x-text="shuffle ? 'Karışık: Açık' : 'Karışık: Kapalı'"></span>
            </button>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="cycleRepeat(); showMobileMenu = false">
                <i class="fas fa-redo w-5 text-center" :class="repeatMode !== 'off' ? 'text-emerald-400' : 'text-zinc-400'"></i>
                <span x-text="repeatMode === 'off' ? 'Tekrar: Kapalı' : (repeatMode === 'all' ? 'Tekrar: Tümü' : 'Tekrar: Tek')"></span>
            </button>
            <div class="h-px bg-zinc-700 mx-4 my-1"></div>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="if(currentSong?.artist_slug) { window.location.href = '/artists/' + currentSong.artist_slug; } showMobileMenu = false"
                    x-show="currentSong?.artist_slug">
                <i class="fas fa-user text-purple-400 w-5 text-center"></i>
                <span>Sanatçıya Git</span>
            </button>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="if(currentSong?.album_slug) { window.location.href = '/albums/' + currentSong.album_slug; } showMobileMenu = false"
                    x-show="currentSong?.album_slug">
                <i class="fas fa-compact-disc text-cyan-400 w-5 text-center"></i>
                <span>Albüme Git</span>
            </button>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="if(navigator.share && currentSong) { navigator.share({ title: currentSong.song_title, url: window.location.origin + '/songs/' + currentSong.song_id }); } showMobileMenu = false">
                <i class="fas fa-share text-zinc-400 w-5 text-center"></i>
                <span>Paylaş</span>
            </button>
        </div>
    </div>
</div>


{{-- ==================== DESKTOP PLAYER (>= 1024px) ==================== --}}
<div class="desktop-player-wrapper muzibu-player row-start-3 col-span-full grid grid-cols-[1fr_2fr_1fr] items-center px-3 pt-2 pb-0 gap-3">

    {{-- Song Info --}}
    <div class="flex items-center gap-3 min-w-0">
        {{-- Album Cover --}}
        <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-purple-600 rounded flex items-center justify-center text-2xl flex-shrink-0 shadow-lg overflow-hidden relative">
            <template x-if="currentSong && currentSong.album_cover" x-cloak>
                <img :src="getCoverUrl(currentSong.album_cover, 120, 120)" :alt="currentSong.song_title" class="w-full h-full object-cover">
            </template>
            <template x-if="!currentSong || !currentSong.album_cover">
                <span>🎵</span>
            </template>
        </div>

        <div class="min-w-0 flex-1">
            <h4 class="text-sm font-semibold text-white truncate" x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || (window.muzibuPlayerConfig?.frontLang?.general?.song || 'Song')) : (window.muzibuPlayerConfig?.frontLang?.general?.select_song || 'Select Song')">{{ trans('muzibu::front.general.select_song') }}</h4>
            <p class="text-xs text-muzibu-text-gray truncate" x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || (window.muzibuPlayerConfig?.frontLang?.general?.artist || 'Artist')) : (window.muzibuPlayerConfig?.frontLang?.general?.artist || 'Artist')">{{ trans('muzibu::front.general.artist') }}</p>
        </div>

        {{-- Heart Button --}}
        <button class="text-muzibu-text-gray hover:text-muzibu-coral transition-all" @click="toggleLike()" :class="{ 'text-muzibu-coral': isLiked }">
            <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
    </div>

    {{-- Player Controls --}}
    <div class="flex flex-col gap-2">
        <div class="flex items-center justify-center gap-6">
            <button class="hover:text-white transition-all" :class="shuffle ? 'text-emerald-400' : 'text-muzibu-text-gray'" @click="toggleShuffle()">
                <i class="fas fa-random"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="previousTrack()">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black transition-all shadow-lg hover:shadow-white/50" @click="togglePlayPause()">
                <i x-show="isSongLoading" x-cloak class="fas fa-spinner fa-spin translate-y-[1px]"></i>
                <i x-show="!isSongLoading && isPlaying" x-cloak class="fas fa-stop translate-y-[1px]"></i>
                <i x-show="!isSongLoading && !isPlaying" class="fas fa-play ml-px translate-y-[1px]"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="nextTrack()">
                <i class="fas fa-step-forward"></i>
            </button>
            <button class="hover:text-white transition-all" :class="repeatMode !== 'off' ? 'text-emerald-400' : 'text-muzibu-text-gray'" @click="cycleRepeat()">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        <div class="flex items-center gap-2 max-w-2xl mx-auto w-full">
            <span class="text-xs text-muzibu-text-gray w-10 text-right" x-text="formatTime(currentTime)">0:00</span>
            <div class="flex-1 h-1.5 bg-muzibu-text-gray/30 rounded-full cursor-pointer group" @click="seekTo($event)">
                <div class="h-full bg-white rounded-full relative group-hover:bg-muzibu-coral transition-colors" :style="`width: ${progressPercent}%`">
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg"></div>
                </div>
            </div>
            <span class="text-xs text-muzibu-text-gray w-10" x-text="formatTime(duration)">0:00</span>
        </div>
    </div>

    {{-- Volume Controls --}}
    <div class="flex items-center justify-end gap-2">
        <button x-show="currentSong && currentSong.lyrics" x-cloak class="text-muzibu-coral hover:text-white transition-all" @click="showLyrics = !showLyrics">
            <i class="fas fa-microphone"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all" @click="showQueue = !showQueue">
            <i class="fas fa-list"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all" @click="toggleMute()">
            <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : 'fas fa-volume-down')"></i>
        </button>
        <div class="relative flex items-center gap-1" x-data="{ showVolumeTooltip: false, tooltipX: 0, isDragging: false }">
            <div class="w-20 py-4 cursor-pointer group"
                 @mousedown="isDragging = true; setVolume($event); showVolumeTooltip = true"
                 @mouseenter="showVolumeTooltip = true"
                 @mousemove="tooltipX = $event.offsetX; if (isDragging) setVolume($event)"
                 @mouseleave="showVolumeTooltip = false; isDragging = false"
                 @mouseup="isDragging = false">
                <div class="h-1.5 bg-muzibu-text-gray/30 rounded-full relative">
                    <div class="h-full bg-white rounded-full relative group-hover:bg-muzibu-coral transition-colors" :style="`width: ${volume}%`">
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg transition-opacity"></div>
                    </div>
                </div>
                <div x-show="showVolumeTooltip" x-transition :style="`left: ${tooltipX}px`"
                     class="absolute -top-9 transform -translate-x-1/2 bg-black/60 text-white px-1.5 py-0.5 rounded text-xs font-medium pointer-events-none z-30">
                    <span x-text="volume >= 95 ? 'MAX' : Math.round(volume)"></span>
                </div>
            </div>
        </div>
    </div>
</div>
