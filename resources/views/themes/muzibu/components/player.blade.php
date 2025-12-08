{{-- PLAYER BAR --}}
<div class="muzibu-player xl:col-span-3 lg:col-span-2 col-span-1 grid grid-cols-[auto_1fr_auto] sm:grid-cols-[1fr_2fr_1fr] items-center px-2 sm:px-3 py-1.5 gap-2 sm:gap-3">
    {{-- Song Info --}}
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        <div class="w-10 h-10 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-purple-600 rounded flex items-center justify-center text-xl sm:text-2xl flex-shrink-0 shadow-lg overflow-hidden">
            <template x-if="currentSong && currentSong.album_cover">
                <img :src="getCoverUrl(currentSong.album_cover, 56, 56)" :alt="currentSong.song_title" class="w-full h-full object-cover">
            </template>
            <template x-if="!currentSong || !currentSong.album_cover">
                <span>🎵</span>
            </template>
        </div>
        <div class="min-w-0 flex-1 hidden xs:block sm:block">
            <h4 class="text-xs sm:text-sm font-semibold text-white truncate" x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || 'Şarkı') : 'Şarkı seç'"></h4>
            <p class="text-[10px] sm:text-xs text-muzibu-text-gray truncate" x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || 'Sanatçı') : 'Sanatçı'"></p>
        </div>
        <button class="text-muzibu-text-gray hover:text-muzibu-coral transition-all hidden sm:block" @click="toggleLike()" :class="{ 'text-muzibu-coral': isLiked }">
            <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
    </div>

    {{-- Player Controls --}}
    <div class="flex flex-col gap-1 sm:gap-2">
        <div class="flex items-center justify-center gap-3 sm:gap-6">
            <button class="text-muzibu-text-gray hover:text-white transition-all hidden sm:block" :class="shuffle ? 'text-muzibu-coral' : ''" @click="toggleShuffle()">
                <i class="fas fa-random"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="previousTrack()">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black transition-all shadow-lg hover:shadow-white/50" @click="togglePlayPause()">
                <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play ml-0.5'"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="nextTrack()">
                <i class="fas fa-step-forward"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all hidden sm:block" :class="repeatMode !== 'off' ? 'text-muzibu-coral' : ''" @click="cycleRepeat()">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        <div class="flex items-center gap-1 sm:gap-2">
            <span class="text-[10px] sm:text-xs text-muzibu-text-gray w-8 sm:w-10 text-right" x-text="formatTime(currentTime)">0:00</span>
            <div class="flex-1 h-1 sm:h-1.5 bg-muzibu-text-gray/30 rounded-full cursor-pointer group" @click="seekTo($event)">
                <div class="h-full bg-white rounded-full relative group-hover:bg-muzibu-coral transition-colors" :style="`width: ${progressPercent}%`">
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-2 sm:w-3 h-2 sm:h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg"></div>
                </div>
            </div>
            <span class="text-[10px] sm:text-xs text-muzibu-text-gray w-8 sm:w-10" x-text="formatTime(duration)">0:00</span>
        </div>
    </div>

    {{-- Volume Controls --}}
    <div class="flex items-center justify-end gap-1 sm:gap-2">
        <button
            class="text-muzibu-text-gray hover:text-white transition-all hidden md:block"
            @click="showKeyboardHelp = !showKeyboardHelp"
            title="Klavye kısayolları"
        >
            <i class="fas fa-keyboard"></i>
        </button>
        <button
            x-show="currentSong && currentSong.lyrics"
            x-cloak
            class="text-muzibu-coral hover:text-white transition-all hidden sm:block"
            @click="showLyrics = !showLyrics"
            title="Şarkı sözlerini göster"
        >
            <i class="fas fa-microphone"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all" @click="showQueue = !showQueue">
            <i class="fas fa-list text-sm sm:text-base"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all hidden sm:block" @click="toggleMute()">
            <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : 'fas fa-volume-down')"></i>
        </button>
        <div class="w-20 h-1.5 bg-muzibu-text-gray/30 rounded-full cursor-pointer group hidden md:block" @click="setVolume($event)">
            <div class="h-full bg-white rounded-full group-hover:bg-muzibu-coral transition-colors" :style="`width: ${volume}%`"></div>
        </div>
    </div>
</div>
