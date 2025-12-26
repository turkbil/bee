{{-- PLAYER BAR --}}
<div class="muzibu-player row-start-3 col-span-full grid grid-cols-[auto_1fr_auto] sm:grid-cols-[1fr_2fr_1fr] items-center px-3 pt-2 pb-0 gap-2 sm:gap-3">


    {{-- Song Info --}}
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        {{-- Album Cover + Mini Heart Overlay (Mobile) --}}
        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-purple-600 rounded flex items-center justify-center text-xl sm:text-2xl flex-shrink-0 shadow-lg overflow-hidden relative">
            <template x-if="currentSong && currentSong.album_cover" x-cloak>
                <img :src="getCoverUrl(currentSong.album_cover, 120, 120)" :alt="currentSong.song_title" class="w-full h-full object-cover">
            </template>
            <template x-if="!currentSong || !currentSong.album_cover">
                <span>🎵</span>
            </template>

            {{-- Mini Heart Button (Mobile Only - Overlay on Cover) --}}
            <button
                x-show="currentSong"
                @click="toggleLike()"
                :class="{ 'text-muzibu-coral border-muzibu-coral': isLiked, 'text-white border-white/50': !isLiked }"
                class="absolute -top-1 -right-1 w-5 h-5 bg-black/80 backdrop-blur-sm rounded-full flex items-center justify-center border shadow-lg transition-all sm:hidden"
                :aria-label="isLiked ? (window.muzibuPlayerConfig?.frontLang?.player?.remove_from_favorites || 'Remove from favorites') : (window.muzibuPlayerConfig?.frontLang?.player?.add_to_favorites || 'Add to favorites')"
                :aria-pressed="isLiked"
            >
                <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'" class="text-[10px]"></i>
            </button>
        </div>

        <div class="min-w-0 flex-1 hidden xs:block sm:block">
            <h4 class="text-xs sm:text-sm font-semibold text-white truncate" x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || (window.muzibuPlayerConfig?.frontLang?.general?.song || 'Song')) : (window.muzibuPlayerConfig?.frontLang?.general?.select_song || 'Select Song')">{{ trans('muzibu::front.general.select_song') }}</h4>
            <p class="text-[10px] sm:text-xs text-muzibu-text-gray truncate" x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || (window.muzibuPlayerConfig?.frontLang?.general?.artist || 'Artist')) : (window.muzibuPlayerConfig?.frontLang?.general?.artist || 'Artist')">{{ trans('muzibu::front.general.artist') }}</p>
        </div>

        {{-- Desktop Heart Button (Desktop Only) --}}
        <button class="text-muzibu-text-gray hover:text-muzibu-coral transition-all hidden sm:block" @click="toggleLike()" :class="{ 'text-muzibu-coral': isLiked }" :aria-label="isLiked ? (window.muzibuPlayerConfig?.frontLang?.player?.remove_from_favorites || 'Remove from favorites') : (window.muzibuPlayerConfig?.frontLang?.player?.add_to_favorites || 'Add to favorites')" :aria-pressed="isLiked">
            <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
    </div>

    {{-- Player Controls --}}
    <div class="flex flex-col gap-1 sm:gap-2">
        <div class="flex items-center justify-center gap-3 sm:gap-6">
            <button class="hover:text-white transition-all hidden sm:block" :class="shuffle ? 'text-emerald-400' : 'text-muzibu-text-gray'" @click="toggleShuffle()" :aria-label="shuffle ? (window.muzibuPlayerConfig?.frontLang?.player?.disable_shuffle || 'Disable shuffle') : (window.muzibuPlayerConfig?.frontLang?.player?.enable_shuffle || 'Enable shuffle')" :aria-pressed="shuffle">
                <i class="fas fa-random"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="previousTrack()" aria-label="{{ trans('muzibu::front.player.previous_song') }}">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black transition-all shadow-lg hover:shadow-white/50" @click="togglePlayPause()" :aria-label="isSongLoading ? (window.muzibuPlayerConfig?.frontLang?.general?.loading || 'Loading...') : (isPlaying ? (window.muzibuPlayerConfig?.frontLang?.player?.pause || 'Pause') : (window.muzibuPlayerConfig?.frontLang?.player?.play || 'Play'))">
                {{-- 🎵 Loading state: Spinner animation --}}
                <i x-show="isSongLoading" x-cloak class="fas fa-spinner fa-spin"></i>
                {{-- Playing state: Stop icon --}}
                <i x-show="!isSongLoading && isPlaying" x-cloak class="fas fa-stop translate-y-[1px]"></i>
                {{-- Paused state: Play icon (visible by default) --}}
                <i x-show="!isSongLoading && !isPlaying" class="fas fa-play ml-px translate-y-[1px]"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="nextTrack()" aria-label="{{ trans('muzibu::front.player.next_song') }}">
                <i class="fas fa-step-forward"></i>
            </button>
            <button class="hover:text-white transition-all hidden sm:block" :class="repeatMode !== 'off' ? 'text-emerald-400' : 'text-muzibu-text-gray'" @click="cycleRepeat()" :aria-label="repeatMode === 'off' ? (window.muzibuPlayerConfig?.frontLang?.player?.enable_repeat || 'Enable repeat') : ((window.muzibuPlayerConfig?.frontLang?.player?.repeat_mode || 'Repeat mode') + ': ' + repeatMode)" :aria-pressed="repeatMode !== 'off'">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        <div class="flex items-center gap-1 sm:gap-2 max-w-2xl mx-auto w-full">
            <span class="text-[10px] sm:text-xs text-muzibu-text-gray w-8 sm:w-10 text-right" x-text="formatTime(currentTime)">0:00</span>
            <div class="flex-1 h-1 sm:h-1.5 bg-muzibu-text-gray/30 rounded-full cursor-pointer group" @click="seekTo($event)" role="progressbar" :aria-valuenow="Math.round(progressPercent)" aria-valuemin="0" aria-valuemax="100" aria-label="{{ trans('muzibu::front.player.song_progress') }}">
                <div class="h-full bg-white rounded-full relative group-hover:bg-muzibu-coral transition-colors" :style="`width: ${progressPercent}%`">
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 sm:w-3 h-2 sm:h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg"></div>
                </div>
            </div>
            <span class="text-[10px] sm:text-xs text-muzibu-text-gray w-8 sm:w-10" x-text="formatTime(duration)">0:00</span>
            {{-- 🎵 Stream Type Indicator (inline, minimal, no box) --}}
            <span
                x-show="showDebugInfo && currentSong && currentStreamType"
                x-cloak
                class="text-[10px] sm:text-xs font-medium uppercase"
                :class="currentStreamType === 'hls' ? 'text-green-400' : 'text-blue-400'"
                x-text="currentStreamType"
            ></span>
        </div>
    </div>

    {{-- Volume Controls --}}
    <div class="flex items-center justify-end gap-1 sm:gap-2">
        {{-- Keyboard shortcuts button - temporarily hidden
        <button
            class="text-muzibu-text-gray hover:text-white transition-all hidden md:block"
            @click="showKeyboardHelp = !showKeyboardHelp"
            aria-label="{{ trans('muzibu::front.player.show_keyboard_shortcuts') }}"
        >
            <i class="fas fa-keyboard"></i>
        </button>
        --}}
        <button
            x-show="currentSong && currentSong.lyrics"
            x-cloak
            class="text-muzibu-coral hover:text-white transition-all hidden sm:block"
            @click="showLyrics = !showLyrics"
            aria-label="{{ trans('muzibu::front.player.show_lyrics') }}"
        >
            <i class="fas fa-microphone"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all" @click="showQueue = !showQueue" :aria-label="showQueue ? (window.muzibuPlayerConfig?.frontLang?.player?.hide_queue || 'Hide queue') : (window.muzibuPlayerConfig?.frontLang?.player?.show_queue || 'Show queue')" :aria-pressed="showQueue">
            <i class="fas fa-list text-sm sm:text-base"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all hidden sm:block" @click="toggleMute()" :aria-label="isMuted ? (window.muzibuPlayerConfig?.frontLang?.player?.unmute || 'Unmute') : (window.muzibuPlayerConfig?.frontLang?.player?.mute || 'Mute')" :aria-pressed="isMuted">
            <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : 'fas fa-volume-down')"></i>
        </button>
        <!-- Volume Slider with Tooltip & Drag (smooth control) -->
        <div class="relative hidden md:flex items-center gap-1" x-data="{
            showVolumeTooltip: false,
            tooltipX: 0,
            isDragging: false
        }">
            <div
                class="w-20 py-4 cursor-pointer group"
                @mousedown="isDragging = true; setVolume($event); showVolumeTooltip = true"
                @mouseenter="showVolumeTooltip = true"
                @mousemove="tooltipX = $event.offsetX; if (isDragging) setVolume($event)"
                @mouseleave="showVolumeTooltip = false; isDragging = false"
                @mouseup="isDragging = false"
                role="slider"
                :aria-valuenow="Math.round(volume)"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-label="{{ trans('muzibu::front.player.volume') }}"
            >
                <!-- Bar (player bar ile aynı stil) -->
                <div class="h-1.5 bg-muzibu-text-gray/30 rounded-full relative">
                    <div class="h-full bg-white rounded-full relative group-hover:bg-muzibu-coral transition-colors" :style="`width: ${volume}%`">
                        <!-- Handle (player bar'daki gibi white circle) -->
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg transition-opacity"></div>
                    </div>
                </div>

                <!-- Tooltip (mouse tracking, transparent) -->
                <div
                    x-show="showVolumeTooltip"
                    x-transition
                    :style="`left: ${tooltipX}px`"
                    class="absolute -top-9 transform -translate-x-1/2 bg-black/60 text-white px-1.5 py-0.5 rounded text-xs font-medium pointer-events-none z-30"
                >
                    <span x-text="volume >= 95 ? 'MAX' : Math.round(volume)"></span>
                </div>
            </div>
            <!-- MAX button (quick 100%) -->
            <button
                @click="volume = 100; isMuted = false"
                class="w-1.5 h-1.5 rounded-full transition-all bg-muzibu-text-gray/50 hover:bg-white"
                aria-label="{{ trans('muzibu::front.player.max_volume') }}"
                title="100%"
            ></button>
        </div>
    </div>
</div>
