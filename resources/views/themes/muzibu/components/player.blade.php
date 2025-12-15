{{-- PLAYER BAR --}}
<div class="muzibu-player xl:col-span-3 lg:col-span-2 col-span-1 grid grid-cols-[auto_1fr_auto] sm:grid-cols-[1fr_2fr_1fr] items-center px-2 sm:px-3 py-1.5 gap-2 sm:gap-3">

    {{-- 🎵 Stream Type Indicator (minimal) - showDebugInfo ile kontrol edilir --}}
    <div
        x-show="showDebugInfo && currentSong"
        x-cloak
        class="fixed bottom-20 right-4 z-40 flex items-center gap-1"
    >
        <span
            class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wide"
            :class="currentStreamType === 'hls' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30'"
            x-text="currentStreamType || 'N/A'"
        ></span>
        <span
            x-show="lastFallbackReason"
            class="px-2 py-1 rounded text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30"
            x-text="lastFallbackReason"
        ></span>
    </div>

    {{-- Song Info --}}
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        {{-- Album Cover + Mini Heart Overlay (Mobile) --}}
        <div class="w-10 h-10 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-purple-600 rounded flex items-center justify-center text-xl sm:text-2xl flex-shrink-0 shadow-lg overflow-hidden relative">
            <template x-if="currentSong && currentSong.album_cover">
                <img :src="getCoverUrl(currentSong.album_cover, 56, 56)" :alt="currentSong.song_title" class="w-full h-full object-cover">
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
                :aria-label="isLiked ? 'Favorilerden çıkar' : 'Favorilere ekle'"
                :aria-pressed="isLiked"
            >
                <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'" class="text-[10px]"></i>
            </button>
        </div>

        <div class="min-w-0 flex-1 hidden xs:block sm:block">
            <h4 class="text-xs sm:text-sm font-semibold text-white truncate" x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || 'Şarkı') : 'Şarkı seç'"></h4>
            <p class="text-[10px] sm:text-xs text-muzibu-text-gray truncate" x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || 'Sanatçı') : 'Sanatçı'"></p>
        </div>

        {{-- Desktop Heart Button (Desktop Only) --}}
        <button class="text-muzibu-text-gray hover:text-muzibu-coral transition-all hidden sm:block" @click="toggleLike()" :class="{ 'text-muzibu-coral': isLiked }" :aria-label="isLiked ? 'Favorilerden çıkar' : 'Favorilere ekle'" :aria-pressed="isLiked">
            <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
    </div>

    {{-- Player Controls --}}
    <div class="flex flex-col gap-1 sm:gap-2">
        <div class="flex items-center justify-center gap-3 sm:gap-6">
            <button class="text-muzibu-text-gray hover:text-white transition-all hidden sm:block" :class="shuffle ? 'text-muzibu-coral' : ''" @click="toggleShuffle()" :aria-label="shuffle ? 'Karıştırmayı kapat' : 'Karıştırmayı aç'" :aria-pressed="shuffle">
                <i class="fas fa-random"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="previousTrack()" aria-label="Önceki şarkı">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black transition-all shadow-lg hover:shadow-white/50" @click="togglePlayPause()" :aria-label="isPlaying ? 'Duraklat' : 'Çal'">
                <i :class="isPlaying ? 'fas fa-stop' : 'fas fa-play ml-0.5'"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all" @click="nextTrack()" aria-label="Sonraki şarkı">
                <i class="fas fa-step-forward"></i>
            </button>
            <button class="text-muzibu-text-gray hover:text-white transition-all hidden sm:block" :class="repeatMode !== 'off' ? 'text-muzibu-coral' : ''" @click="cycleRepeat()" :aria-label="repeatMode === 'off' ? 'Tekrarlamayı aç' : 'Tekrarlama modu: ' + repeatMode" :aria-pressed="repeatMode !== 'off'">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        <div class="flex items-center gap-1 sm:gap-2">
            <span class="text-[10px] sm:text-xs text-muzibu-text-gray w-8 sm:w-10 text-right" x-text="formatTime(currentTime)">0:00</span>
            <div class="flex-1 h-1 sm:h-1.5 bg-muzibu-text-gray/30 rounded-full cursor-pointer group" @click="seekTo($event)" role="progressbar" :aria-valuenow="Math.round(progressPercent)" aria-valuemin="0" aria-valuemax="100" aria-label="Şarkı ilerlemesi">
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
            aria-label="Klavye kısayollarını göster"
        >
            <i class="fas fa-keyboard"></i>
        </button>
        <button
            x-show="currentSong && currentSong.lyrics"
            x-cloak
            class="text-muzibu-coral hover:text-white transition-all hidden sm:block"
            @click="showLyrics = !showLyrics"
            aria-label="Şarkı sözlerini göster"
        >
            <i class="fas fa-microphone"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all" @click="showQueue = !showQueue" :aria-label="showQueue ? 'Sırayı kapat' : 'Sırayı aç'" :aria-pressed="showQueue">
            <i class="fas fa-list text-sm sm:text-base"></i>
        </button>
        <button class="text-muzibu-text-gray hover:text-white transition-all hidden sm:block" @click="toggleMute()" :aria-label="isMuted ? 'Sesi aç' : 'Sesi kapat'" :aria-pressed="isMuted">
            <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : 'fas fa-volume-down')"></i>
        </button>
        <div class="w-20 h-1.5 bg-muzibu-text-gray/30 rounded-full cursor-pointer group hidden md:block" @click="setVolume($event)" role="slider" :aria-valuenow="Math.round(volume)" aria-valuemin="0" aria-valuemax="100" aria-label="Ses seviyesi">
            <div class="h-full bg-white rounded-full group-hover:bg-muzibu-coral transition-colors" :style="`width: ${volume}%`"></div>
        </div>
    </div>
</div>
