{{-- LYRICS OVERLAY - Slides in from right when lyrics button clicked --}}
<div
    x-show="showLyrics"
    @click="showLyrics = false"
    class="fixed inset-0 bg-transparent z-40"
    style="display: none;"
></div>

<aside
    x-show="showLyrics"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-x-full"
    x-transition:enter-end="transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform translate-x-0"
    x-transition:leave-end="transform translate-x-full"
    style="display: none;"
    class="fixed top-0 right-0 bottom-0 w-full sm:w-80 md:w-96 bg-muzibu-gray border-l border-white/10 shadow-2xl z-50 flex flex-col"
>
    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b border-white/10">
        <h3 class="text-lg font-bold bg-gradient-to-r from-muzibu-coral to-pink-500 bg-clip-text text-transparent">
            🎤 Şarkı Sözleri
        </h3>
        <button @click="showLyrics = false" class="text-muzibu-text-gray hover:text-white transition-colors p-2">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-6">
        {{-- Current Song Info --}}
        <template x-if="currentSong">
            <div class="mb-6 pb-6 border-b border-white/10">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-pink-500 to-purple-600 flex-shrink-0 overflow-hidden shadow-lg">
                        <template x-if="currentSong.album_cover">
                            <img :src="getCoverUrl(currentSong.album_cover, 80, 80)" :alt="currentSong.song_title" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!currentSong.album_cover">
                            <div class="w-full h-full flex items-center justify-center text-3xl">🎵</div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-base font-bold text-white mb-1" x-text="currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || 'Şarkı'"></h4>
                        <p class="text-sm text-muzibu-text-gray" x-text="currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || 'Muzibu'"></p>
                        <p class="text-xs text-muzibu-text-gray/60 mt-1" x-text="currentSong.album_title?.tr || currentSong.album_title?.en || currentSong.album_title || ''"></p>
                    </div>
                </div>
            </div>
        </template>

        {{-- Lyrics Content --}}
        <template x-if="currentSong && currentSong.lyrics">
            <div class="lyrics-content">
                <div 
                    class="text-white/90 leading-loose whitespace-pre-line text-sm"
                    x-text="typeof currentSong.lyrics === 'string' ? currentSong.lyrics : (currentSong.lyrics?.tr || currentSong.lyrics?.en || '')"
                    style="font-size: 0.95rem; line-height: 1.9;"
                >
                </div>
            </div>
        </template>

        {{-- Empty State - No Lyrics --}}
        <template x-if="!currentSong || !currentSong.lyrics">
            <div class="text-center py-12 text-muzibu-text-gray">
                <i class="fas fa-microphone-slash text-5xl mb-4 opacity-30"></i>
                <p class="text-base font-semibold mb-2">Şarkı Sözü Yok</p>
                <p class="text-xs opacity-70">Bu şarkı için henüz sözler eklenmemiş</p>
            </div>
        </template>
    </div>

    {{-- Footer - Quick Actions --}}
    <div class="p-4 border-t border-white/10 bg-muzibu-gray-light">
        <div class="flex items-center justify-between text-xs text-muzibu-text-gray">
            <div class="flex items-center gap-3">
                <button @click="toggleLike()" :class="isLiked ? 'text-muzibu-coral' : 'text-muzibu-text-gray'" class="hover:text-muzibu-coral transition-colors">
                    <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'"></i>
                </button>
                <button class="hover:text-white transition-colors">
                    <i class="fas fa-share-alt"></i>
                </button>
            </div>
            <div class="text-muzibu-text-gray/60">
                <span x-text="formatTime(currentTime)">0:00</span> / <span x-text="formatTime(duration)">0:00</span>
            </div>
        </div>
    </div>
</aside>
