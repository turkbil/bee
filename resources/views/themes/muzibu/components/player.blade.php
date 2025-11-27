{{-- PLAYER BAR --}}
<div class="xl:col-span-3 lg:col-span-2 col-span-1 bg-spotify-gray/95 backdrop-blur-md grid grid-cols-[1fr_2fr_1fr] items-center px-4 py-3 gap-4 border-t border-white/10 shadow-2xl">
    {{-- Song Info --}}
    <div class="flex items-center gap-3">
        <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-purple-600 rounded flex items-center justify-center text-2xl flex-shrink-0 shadow-lg">
            <template x-if="currentSong && currentSong.album_cover">
                <img :src="currentSong.album_cover" :alt="currentSong.title" class="w-full h-full rounded object-cover">
            </template>
            <template x-if="!currentSong || !currentSong.album_cover">
                <span>🎵</span>
            </template>
        </div>
        <div class="min-w-0 hidden sm:block">
            <h4 class="text-sm font-semibold text-white truncate" x-text="currentSong ? currentSong.title : 'Şarkı seç'"></h4>
            <p class="text-xs text-spotify-text-gray truncate" x-text="currentSong ? currentSong.artist_name : 'Sanatçı'"></p>
        </div>
        <button class="text-spotify-text-gray hover:text-spotify-green ml-auto transition-all" @click="toggleFavorite('song', currentSong?.song_id)">
            <i :class="isLiked ? 'fas fa-heart text-spotify-green animate-pulse' : 'far fa-heart'"></i>
        </button>
    </div>

    {{-- Player Controls --}}
    <div class="flex flex-col gap-2">
        <div class="flex items-center justify-center gap-4">
            <button class="text-spotify-text-gray hover:text-white transition-all" :class="shuffle ? 'text-spotify-green' : ''" @click="toggleShuffle()">
                <i class="fas fa-random"></i>
            </button>
            <button class="text-spotify-text-gray hover:text-white transition-all" @click="previousTrack()">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black transition-all shadow-lg hover:shadow-white/50" @click="togglePlayPause()">
                <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play ml-0.5'"></i>
            </button>
            <button class="text-spotify-text-gray hover:text-white transition-all" @click="nextTrack()">
                <i class="fas fa-step-forward"></i>
            </button>
            <button class="text-spotify-text-gray hover:text-white transition-all" :class="repeatMode !== 'off' ? 'text-spotify-green' : ''" @click="cycleRepeat()">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-spotify-text-gray w-10 text-right" x-text="formatTime(currentTime)">0:00</span>
            <div class="flex-1 h-1 bg-spotify-text-gray/30 rounded-full cursor-pointer group" @click="seekTo($event)">
                <div class="h-full bg-white rounded-full relative group-hover:bg-spotify-green transition-colors" :style="`width: ${progressPercent}%`">
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg"></div>
                </div>
            </div>
            <span class="text-xs text-spotify-text-gray w-10" x-text="formatTime(duration)">0:00</span>
        </div>
    </div>

    {{-- Volume Controls --}}
    <div class="flex items-center justify-end gap-2">
        <button class="text-spotify-text-gray hover:text-white hidden lg:block transition-all">
            <i class="fas fa-hdd"></i>
        </button>
        <button class="text-spotify-text-gray hover:text-white transition-all" @click="showQueue = !showQueue">
            <i class="fas fa-list"></i>
        </button>
        <button class="text-spotify-text-gray hover:text-white transition-all" @click="toggleMute()">
            <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : 'fas fa-volume-down')"></i>
        </button>
        <div class="w-20 h-1 bg-spotify-text-gray/30 rounded-full cursor-pointer group hidden md:block" @click="setVolume($event)">
            <div class="h-full bg-white rounded-full group-hover:bg-spotify-green transition-colors" :style="`width: ${volume}%`"></div>
        </div>
    </div>
</div>
