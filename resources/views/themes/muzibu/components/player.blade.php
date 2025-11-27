<div class="muzibu-player">
    {{-- Left: Song Info --}}
    <div class="muzibu-player-song-info">
        <div class="muzibu-player-album-cover">
            <template x-if="currentSong && currentSong.album_cover">
                <img :src="currentSong.album_cover" :alt="currentSong.title">
            </template>
            <template x-if="!currentSong || !currentSong.album_cover">
                <span style="font-size: 24px;">🎵</span>
            </template>
        </div>
        <div class="muzibu-player-song-details">
            <h4 x-text="currentSong ? currentSong.title : 'Şarkı seç'"></h4>
            <p x-text="currentSong ? currentSong.artist_name : 'Sanatçı'"></p>
        </div>
        <button class="muzibu-player-like-btn"
                :class="isLiked ? 'liked' : ''"
                @click="toggleFavorite('song', currentSong?.song_id)">
            <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
    </div>

    {{-- Center: Controls --}}
    <div class="muzibu-player-controls">
        <div class="muzibu-controls-buttons">
            <button class="muzibu-control-btn"
                    :class="shuffle ? 'active' : ''"
                    @click="toggleShuffle()"
                    title="Karışık">
                <i class="fas fa-random"></i>
            </button>
            <button class="muzibu-control-btn"
                    @click="previousTrack()"
                    title="Önceki">
                <i class="fas fa-step-backward"></i>
            </button>
            <button class="muzibu-play-btn"
                    @click="togglePlayPause()"
                    :title="isPlaying ? 'Duraklat' : 'Çal'">
                <i :class="isPlaying ? 'fas fa-pause' : 'fas fa-play'"></i>
            </button>
            <button class="muzibu-control-btn"
                    @click="nextTrack()"
                    title="Sonraki">
                <i class="fas fa-step-forward"></i>
            </button>
            <button class="muzibu-control-btn"
                    :class="repeatMode !== 'off' ? 'active' : ''"
                    @click="cycleRepeat()"
                    title="Tekrarla">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        <div class="muzibu-progress-container">
            <span class="muzibu-time" x-text="formatTime(currentTime)"></span>
            <div class="muzibu-progress-bar" @click="seekTo($event)">
                <div class="muzibu-progress-fill" :style="`width: ${progressPercent}%`"></div>
            </div>
            <span class="muzibu-time" x-text="formatTime(duration)"></span>
        </div>
    </div>

    {{-- Right: Volume & Queue --}}
    <div class="muzibu-player-volume">
        <button class="muzibu-device-btn" title="Cihazlar">
            <i class="fas fa-hdd"></i>
        </button>
        <button class="muzibu-queue-btn"
                @click="showQueue = !showQueue"
                title="Kuyruk">
            <i class="fas fa-list"></i>
        </button>
        <button class="muzibu-volume-btn"
                @click="toggleMute()"
                title="Ses">
            <i :class="isMuted ? 'fas fa-volume-mute' : (volume > 50 ? 'fas fa-volume-up' : 'fas fa-volume-down')"></i>
        </button>
        <div class="muzibu-volume-bar" @click="setVolume($event)">
            <div class="muzibu-volume-fill" :style="`width: ${volume}%`"></div>
        </div>
        <button class="muzibu-fullscreen-btn" title="Tam ekran">
            <i class="fas fa-expand"></i>
        </button>
    </div>
</div>
