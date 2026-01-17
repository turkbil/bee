{{-- PLAYER BAR --}}

{{-- ==================== MOBILE PLAYER - Ring Progress (< 1024px) ==================== --}}
<style>
    @media (min-width: 1024px) { .mobile-player-wrapper { display: none !important; } }
    @media (max-width: 1023px) { .desktop-player-wrapper { display: none !important; } }

    /* 🎨 Dinamik Gradient Border - Şarkıya göre renk değişimi */
    :root {
        --player-hue1: 30;
        --player-hue2: 350;
        --player-hue3: 320;
    }

    /* Hareketli Gradient Animasyonu */
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Gradient Border - Mobile & Desktop */
    .mobile-player-wrapper,
    .desktop-player-wrapper {
        background:
            linear-gradient(#18181b, #18181b) padding-box,
            linear-gradient(
                135deg,
                hsl(var(--player-hue1), 80%, 50%),
                hsl(var(--player-hue2), 80%, 50%),
                hsl(var(--player-hue3), 80%, 50%),
                hsl(var(--player-hue1), 80%, 50%)
            ) border-box;
        background-size: 100% 100%, 300% 300%;
        border: 2px solid transparent;
        animation: gradientShift 8s ease infinite;
        transition: --player-hue1 1s ease, --player-hue2 1s ease, --player-hue3 1s ease;
    }

    /* Progress Ring Gradient Transition */
    .progress-ring-gradient {
        transition: stroke 1s ease;
    }
</style>
<div class="mobile-player-wrapper row-start-3 col-span-full mx-3 my-3 px-3 py-2 relative rounded-full shadow-lg">

    {{-- Grid Layout: 3 alanlar [Cover+Info] [Controls] [Menu] --}}
    <div class="grid grid-cols-[auto_1fr_auto] items-center gap-2">

        {{-- Sol: Cover + Song Info --}}
        <div class="flex items-center gap-2 min-w-0">
            {{-- Cover with Progress Ring --}}
            <div class="relative w-12 h-12 flex-shrink-0">
                {{-- Progress Ring (Dinamik Gradient) --}}
                <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 48 48">
                    <defs>
                        <linearGradient id="mobileRingGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" class="mobile-gradient-stop1" :style="`stop-color: hsl(${currentSong?.color_hues?.[0] || 30}, 80%, 55%)`"/>
                            <stop offset="50%" class="mobile-gradient-stop2" :style="`stop-color: hsl(${currentSong?.color_hues?.[1] || 350}, 80%, 55%)`"/>
                            <stop offset="100%" class="mobile-gradient-stop3" :style="`stop-color: hsl(${currentSong?.color_hues?.[2] || 320}, 80%, 55%)`"/>
                        </linearGradient>
                    </defs>
                    <circle cx="24" cy="24" r="21" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3"/>
                    <circle cx="24" cy="24" r="21" fill="none" stroke="url(#mobileRingGradient)" stroke-width="3"
                            stroke-linecap="round"
                            class="progress-ring-gradient"
                            :stroke-dasharray="132"
                            :stroke-dashoffset="132 - (132 * progressPercent / 100)"/>
                </svg>
                {{-- Album Cover (simple circle) with Coral Fallback --}}
                <div class="absolute inset-[4px] rounded-full overflow-hidden flex items-center justify-center"
                     :class="(currentSong?.album_cover || currentSong?.cover_url) ? 'bg-zinc-800' : 'bg-gradient-to-br from-muzibu-coral via-orange-500 to-pink-500'">
                    <img x-ref="mobileCover"
                         x-show="currentSong?.album_cover || currentSong?.cover_url"
                         x-effect="const cover = currentSong?.album_cover || currentSong?.cover_url; if(cover && $refs.mobileCover) { $refs.mobileCover.src = (typeof cover === 'string' && cover.startsWith('http')) ? cover : `/thumb/${cover}/100/100`; }"
                         alt="Cover"
                         class="absolute inset-0 w-full h-full object-cover">
                    <i x-show="!currentSong?.cover_url && !currentSong?.album_cover" class="fas fa-music text-white/80 text-sm"></i>
                </div>
                {{-- Time Badge - Kalan süre --}}
                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-zinc-800 text-white text-[10px] px-1.5 py-0.5 rounded-full border border-zinc-700"
                     x-text="duration > 0 ? formatTime(Math.max(0, duration - currentTime)) : '0:00'">0:00</div>
            </div>

            {{-- Song Info: Title + Artist (Sabit genişlik) --}}
            <div class="w-[85px]">
                <p class="text-white text-xs font-medium truncate leading-tight"
                   x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || 'Şarkı') : 'Şarkı Seç'">
                    Şarkı Seç
                </p>
                <p class="text-zinc-400 text-[10px] truncate leading-tight"
                   x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || '') : ''">
                </p>
            </div>
        </div>

        {{-- Orta: Controls (Prev, Play/Pause, Next) - Sağa yaslanmış --}}
        <div class="flex items-center justify-end gap-0.5">
            <button class="w-9 h-9 text-white/80 flex items-center justify-center active:scale-90 transition-transform"
                    @click="previousTrack()">
                <i class="fas fa-backward text-sm"></i>
            </button>
            <button class="w-10 h-10 text-white flex items-center justify-center active:scale-95 transition-transform"
                    @click="togglePlayPause()">
                <i x-show="isSongLoading || isSeeking" x-cloak class="fas fa-spinner fa-spin text-lg"></i>
                <i x-show="!isSongLoading && !isSeeking && isPlaying" x-cloak class="fas fa-pause text-xl"></i>
                <i x-show="!isSongLoading && !isSeeking && !isPlaying" class="fas fa-play text-xl ml-0.5"></i>
            </button>
            <button class="w-9 h-9 text-white/80 flex items-center justify-center active:scale-90 transition-transform"
                    @click="nextTrack()">
                <i class="fas fa-forward text-sm"></i>
            </button>
        </div>

        {{-- Sağ: Three Dots Menu --}}
        <button class="w-9 h-9 text-white/60 flex items-center justify-center active:scale-90 transition-transform flex-shrink-0"
                @click.stop="showMobileMenu = !showMobileMenu">
            <i class="fas fa-ellipsis-v text-base"></i>
        </button>
    </div>

    {{-- Mobile Context Menu with Swipe-to-Dismiss --}}
    <div x-show="showMobileMenu"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         @click.outside="showMobileMenu = false"
         class="absolute bottom-full left-0 right-0 mb-2 mx-3 bg-zinc-900 rounded-xl border border-zinc-700 shadow-2xl overflow-hidden z-[60]"
         x-data="{ startY: 0, currentY: 0, isDragging: false }"
         :style="isDragging && currentY > 0 ? `transform: translateY(${currentY}px); opacity: ${1 - currentY/150}` : ''">

        {{-- Handle Bar --}}
        <div class="flex justify-center pt-2 pb-1">
            <div class="w-10 h-1 bg-zinc-600 rounded-full"></div>
        </div>

        {{-- Menu Header - Swipe Area --}}
        <div class="px-4 py-2 border-b border-zinc-700 bg-zinc-800 touch-none"
             @touchstart="startY = $event.touches[0].clientY; isDragging = true; currentY = 0"
             @touchmove.prevent="if(isDragging) { currentY = Math.max(0, $event.touches[0].clientY - startY); }"
             @touchend="if(currentY > 60) { showMobileMenu = false; } isDragging = false; currentY = 0;">
            <p class="text-white font-medium text-sm truncate"
               x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title) : ''"></p>
            <p class="text-zinc-400 text-xs truncate"
               x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title) : ''"></p>
        </div>

        {{-- Mobile Progress Bar (Touch Draggable) - Geniş Touch Area --}}
        <div class="px-4 py-4 border-b border-zinc-700" x-data="{ isDragging: false, seekTime: 0, touchUsed: false }">
            <div class="flex items-center">
                {{-- Current Time --}}
                <span class="text-xs text-zinc-400 w-10 text-right tabular-nums shrink-0" x-text="formatTime(isDragging ? seekTime : currentTime)">0:00</span>

                {{-- Progress Bar Container - Büyük touch area için padding --}}
                <div class="flex-1 mx-3 py-3 -my-3 cursor-pointer touch-none"
                     @click="if(!touchUsed && duration > 0) { const bar = $el.querySelector('.progress-track'); const rect = bar.getBoundingClientRect(); const percent = Math.max(0, Math.min(1, ($event.clientX - rect.left) / rect.width)); seekTo(duration * percent); }"
                     @touchstart.prevent="isDragging = true; touchUsed = true; seekTime = currentTime"
                     @touchmove.prevent="if(isDragging && duration > 0) { const touch = $event.touches[0]; const bar = $el.querySelector('.progress-track'); const rect = bar.getBoundingClientRect(); const percent = Math.max(0, Math.min(1, (touch.clientX - rect.left) / rect.width)); seekTime = duration * percent; }"
                     @touchend.prevent="if(isDragging && duration > 0) { seekTo(seekTime); } isDragging = false; setTimeout(() => touchUsed = false, 300);">
                    {{-- Visible Progress Track --}}
                    <div class="progress-track h-2.5 bg-zinc-700 rounded-full relative">
                        {{-- Progress Fill with Gradient --}}
                        <div class="h-full rounded-full transition-all pointer-events-none"
                             :style="`width: ${isDragging ? (seekTime / duration * 100) : progressPercent}%; background: linear-gradient(90deg, hsl(${currentSong?.color_hues?.[0] || 30}, 80%, 55%), hsl(${currentSong?.color_hues?.[1] || 350}, 80%, 55%), hsl(${currentSong?.color_hues?.[2] || 320}, 80%, 55%))`">
                        </div>
                        {{-- Drag Handle - Daha büyük --}}
                        <div class="absolute top-1/2 -translate-y-1/2 w-5 h-5 bg-white rounded-full shadow-lg pointer-events-none border-2 border-zinc-300"
                             :class="isDragging ? 'scale-125' : ''"
                             :style="`left: calc(${isDragging ? (seekTime / duration * 100) : progressPercent}% - 10px)`"></div>
                    </div>
                </div>

                {{-- Duration --}}
                <span class="text-xs text-zinc-400 w-10 tabular-nums shrink-0" x-text="formatTime(duration)">0:00</span>
            </div>

        </div>

        {{-- Menu Items --}}
        <div class="py-1 max-h-64 overflow-y-auto">
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="toggleLike(); showMobileMenu = false">
                <i :class="isLiked ? 'fas fa-heart text-pink-500' : 'far fa-heart text-zinc-400'" class="w-5 text-center"></i>
                <span x-text="isLiked ? 'Favorilerden Çıkar' : 'Favorilere Ekle'"></span>
            </button>
            <button x-show="$store.muzibu?.playContext?.type !== 'radio'"
                    class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="showQueue = true; showMobileMenu = false">
                <i class="fas fa-list-ul text-orange-400 w-5 text-center"></i>
                <span>Sıradakiler</span>
            </button>
            <button class="w-full px-4 py-3 flex items-center gap-3 text-sm text-white active:bg-zinc-700"
                    @click="if(currentSong) { $store.playlistModal.showForSong(currentSong.song_id, currentSong); } showMobileMenu = false">
                <i class="fas fa-plus text-green-400 w-5 text-center"></i>
                <span>Playlist'e Ekle</span>
            </button>
        </div>
    </div>
</div>


{{-- ==================== DESKTOP PLAYER (>= 1024px) ==================== --}}
<div class="desktop-player-wrapper row-start-3 col-span-full mx-4 px-4 py-2.5 relative rounded-full shadow-lg flex items-center gap-4">

    {{-- Cover with Progress Ring --}}
    <div class="relative w-14 h-14 flex-shrink-0">
        {{-- Progress Ring (Dinamik Gradient) --}}
        <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 56 56">
            <defs>
                <linearGradient id="desktopRingGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" class="desktop-gradient-stop1" :style="`stop-color: hsl(${currentSong?.color_hues?.[0] || 30}, 80%, 55%)`"/>
                    <stop offset="50%" class="desktop-gradient-stop2" :style="`stop-color: hsl(${currentSong?.color_hues?.[1] || 350}, 80%, 55%)`"/>
                    <stop offset="100%" class="desktop-gradient-stop3" :style="`stop-color: hsl(${currentSong?.color_hues?.[2] || 320}, 80%, 55%)`"/>
                </linearGradient>
            </defs>
            <circle cx="28" cy="28" r="25" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3"/>
            <circle cx="28" cy="28" r="25" fill="none" stroke="url(#desktopRingGradient)" stroke-width="3"
                    stroke-linecap="round"
                    class="progress-ring-gradient"
                    :stroke-dasharray="157"
                    :stroke-dashoffset="157 - (157 * progressPercent / 100)"/>
        </svg>
        {{-- Album Cover (simple circle) with Coral Fallback --}}
        <div class="absolute inset-[4px] rounded-full overflow-hidden flex items-center justify-center"
             :class="(currentSong?.album_cover || currentSong?.cover_url) ? 'bg-zinc-800' : 'bg-gradient-to-br from-muzibu-coral via-orange-500 to-pink-500'">
            <img x-ref="desktopCover"
                 x-show="currentSong?.album_cover || currentSong?.cover_url"
                 x-effect="const cover = currentSong?.album_cover || currentSong?.cover_url; if(cover && $refs.desktopCover) { $refs.desktopCover.src = (typeof cover === 'string' && cover.startsWith('http')) ? cover : `/thumb/${cover}/120/120`; }"
                 alt="Cover"
                 class="absolute inset-0 w-full h-full object-cover">
            <i x-show="!currentSong?.cover_url && !currentSong?.album_cover" class="fas fa-music text-white/80 text-lg"></i>
        </div>
    </div>

    {{-- Heart Button (önce) --}}
    <button class="w-9 h-9 text-white/60 hover:text-pink-500 flex items-center justify-center transition-colors flex-shrink-0"
            @click="toggleLike()" :class="{ 'text-pink-500': isLiked }">
        <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'" class="text-base"></i>
    </button>

    {{-- Song Info: Title + Artist --}}
    <div class="min-w-[176px] max-w-[176px]">
        <p class="text-white text-sm font-medium truncate"
           x-text="currentSong ? (currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || 'Şarkı') : 'Şarkı Seç'">
            Şarkı Seç
        </p>
        <p class="text-zinc-400 text-xs truncate"
           x-text="currentSong ? (currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || '') : ''">
        </p>
    </div>

    {{-- Controls: Prev, Play/Pause, Next --}}
    <div class="flex items-center gap-1 flex-1 justify-center">
        <button class="w-10 h-10 text-white/80 hover:text-white flex items-center justify-center transition-colors"
                @click="previousTrack()">
            <i class="fas fa-backward text-base"></i>
        </button>
        <button class="w-12 h-12 text-white flex items-center justify-center transition-transform hover:scale-105"
                @click="togglePlayPause()">
            <i x-show="isSongLoading || isSeeking" x-cloak class="fas fa-spinner fa-spin text-2xl"></i>
            <i x-show="!isSongLoading && !isSeeking && isPlaying" x-cloak class="fas fa-pause text-2xl"></i>
            <i x-show="!isSongLoading && !isSeeking && !isPlaying" class="fas fa-play text-2xl ml-1"></i>
        </button>
        <button class="w-10 h-10 text-white/80 hover:text-white flex items-center justify-center transition-colors"
                @click="nextTrack()">
            <i class="fas fa-forward text-base"></i>
        </button>
    </div>

    {{-- Progress Bar (Linear - Dynamic Gradient) --}}
    <div class="flex items-center gap-2 w-80">
        {{-- Geçen Süre (Desktop) - Spotify Standard --}}
        <span class="text-xs text-zinc-400 w-10 text-right tabular-nums" x-text="formatTime(currentTime)">0:00</span>
        <div class="flex-1 h-1.5 bg-white/20 rounded-full cursor-pointer group" @click="seekTo($event)">
            <div class="h-full rounded-full relative transition-all progress-bar-gradient"
                 :style="`width: ${progressPercent}%; background: linear-gradient(90deg, hsl(${currentSong?.color_hues?.[0] || 30}, 80%, 55%), hsl(${currentSong?.color_hues?.[1] || 350}, 80%, 55%), hsl(${currentSong?.color_hues?.[2] || 320}, 80%, 55%))`">
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rounded-full opacity-0 group-hover:opacity-100 shadow-lg transition-opacity"></div>
            </div>
        </div>
        {{-- Toplam Süre (Desktop) - Animated --}}
        <span class="text-xs text-zinc-400 w-10 tabular-nums" x-text="formatTime(animatedDuration)">0:00</span>
    </div>

    {{-- Volume Control - Mute/Unmute Toggle --}}
    <button class="w-9 h-9 text-white/60 hover:text-white flex items-center justify-center transition-colors"
            @click="toggleMute()">
        <i class="fas" :class="isMuted ? 'fa-volume-mute' : 'fa-volume-up'"></i>
    </button>

    {{-- Queue Button (Radyo modunda gizli) --}}
    <button x-show="$store.muzibu?.playContext?.type !== 'radio'"
            class="w-9 h-9 text-white/60 hover:text-white flex items-center justify-center transition-colors"
            @click="showQueue = !showQueue">
        <i class="fas fa-list-ul"></i>
    </button>

    {{-- Lyrics Button --}}
    <button x-show="currentSong && currentSong.lyrics" x-cloak
            class="w-9 h-9 text-orange-400 hover:text-orange-300 flex items-center justify-center transition-colors"
            @click="showLyrics = !showLyrics">
        <i class="fas fa-microphone"></i>
    </button>
</div>
