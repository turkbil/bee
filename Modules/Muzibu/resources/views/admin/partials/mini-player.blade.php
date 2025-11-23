{{-- Muzibu Admin Mini Player --}}
{{-- Include this partial in Muzibu admin views that need audio preview --}}

@push('scripts')
{{-- HLS.js + Howler.js (Local) --}}
<script src="/admin-assets/libs/hls/hls.min.js"></script>
<script src="/admin-assets/libs/howler/howler.min.js"></script>
<script src="/admin-assets/js/admin-mini-player.js?v={{ time() }}"></script>
@endpush

{{-- Mini Player Component --}}
<div x-data="adminMiniPlayer()" x-show="isVisible" x-cloak
    class="admin-mini-player"
    style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1050; background: var(--tblr-bg-surface); border-top: 1px solid var(--tblr-border-color); box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">

    <div class="d-flex align-items-center px-3 py-2 gap-3">
        {{-- Song Info --}}
        <div class="d-flex align-items-center gap-2" style="min-width: 200px; max-width: 300px;">
            <div class="avatar avatar-sm bg-primary-lt">
                <i class="fas fa-music"></i>
            </div>
            <div class="text-truncate">
                <div class="fw-medium text-truncate" x-text="currentSong?.title || 'Şarkı Seçilmedi'" style="font-size: 0.875rem;"></div>
                <div class="small" x-text="currentSong?.artist || ''" style="opacity: 0.7;"></div>
            </div>
        </div>

        {{-- Controls --}}
        <div class="d-flex align-items-center gap-2">
            {{-- Play/Pause Button --}}
            <button @click="togglePlay()" class="btn btn-icon" :disabled="isLoading">
                <template x-if="isLoading">
                    <span class="spinner-border spinner-border-sm"></span>
                </template>
                <template x-if="!isLoading && !isPlaying">
                    <i class="fas fa-play"></i>
                </template>
                <template x-if="!isLoading && isPlaying">
                    <i class="fas fa-pause"></i>
                </template>
            </button>
        </div>

        {{-- Progress Bar --}}
        <div class="flex-grow-1 d-flex align-items-center gap-2">
            <span class="small" x-text="formatTime(currentTime)" style="min-width: 40px;"></span>

            <div @click="seek($event)"
                class="progress flex-grow-1"
                style="height: 6px; cursor: pointer; background: var(--tblr-border-color);">
                <div class="progress-bar bg-primary"
                    :style="'width: ' + progress + '%'"
                    style="transition: width 0.1s linear;"></div>
            </div>

            <span class="small" x-text="formatTime(duration)" style="min-width: 40px;"></span>
        </div>

        {{-- Volume --}}
        <div class="d-none d-md-flex align-items-center gap-2">
            <i class="fas fa-volume-up small"></i>
            <input type="range" min="0" max="1" step="0.1"
                :value="volume"
                @input="setVolume($event.target.value)"
                style="width: 80px; height: 4px;">
        </div>

        {{-- Close Button --}}
        <button @click="close()" class="btn btn-icon btn-ghost-secondary">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<style>
    .admin-mini-player {
        transition: transform 0.3s ease;
    }
    .admin-mini-player[x-cloak] {
        display: none !important;
    }
</style>
