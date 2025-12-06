{{-- QUEUE OVERLAY - Slides in from right when queue button clicked --}}
{{-- x-if guard: Render only when parent scope has 'queue' defined --}}
<template x-if="typeof queue !== 'undefined'">
<div>
<div
    x-show="showQueue"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="showQueue = false"
    class="fixed inset-0 bg-black/60 z-40"
    style="display: none;"
></div>

<aside
    x-show="showQueue"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-x-full"
    x-transition:enter-end="transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform translate-x-0"
    x-transition:leave-end="transform translate-x-full"
    style="display: none;"
    class="fixed top-0 right-0 bottom-0 w-96 bg-muzibu-gray border-l border-white/10 shadow-2xl z-50 flex flex-col"
>
    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b border-white/10">
        <h3 class="text-lg font-bold bg-gradient-to-r from-white via-zinc-100 to-muzibu-text-gray bg-clip-text text-transparent">
            Çalma Listesi
        </h3>
        <div class="flex items-center gap-2">
            <button @click="clearQueue()" class="text-xs text-muzibu-text-gray hover:text-muzibu-coral transition-colors px-2 py-1">
                <i class="fas fa-trash-alt"></i> Temizle
            </button>
            <button @click="showQueue = false" class="text-muzibu-text-gray hover:text-white transition-colors p-2">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto p-4">
        {{-- Current Playing --}}
        <template x-if="currentSong">
            <div class="mb-4 pb-4 border-b border-white/10">
                <div class="text-xs text-muzibu-coral font-semibold mb-2 flex items-center gap-2">
                    <i class="fas fa-play"></i> Şimdi Çalıyor
                </div>
                <div class="flex items-center gap-3 px-2 py-2 bg-muzibu-gray-light rounded">
                    <div class="w-10 h-10 rounded bg-gradient-to-br from-pink-500 to-purple-600 flex-shrink-0 overflow-hidden">
                        <template x-if="currentSong.album_cover">
                            <img :src="getCoverUrl(currentSong.album_cover, 40, 40)" :alt="currentSong.song_title" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!currentSong.album_cover">
                            <div class="w-full h-full flex items-center justify-center text-lg">🎵</div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-white truncate" x-text="currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || 'Şarkı'"></h4>
                        <p class="text-xs text-muzibu-text-gray truncate" x-text="currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || 'Sanatçı'"></p>
                    </div>
                </div>
            </div>
        </template>

        {{-- Queue Count --}}
        <div class="text-xs text-muzibu-text-gray font-semibold mb-3">
            Sırada <span x-text="(queue || []).length"></span> şarkı
        </div>

        {{-- Empty State --}}
        <template x-if="!queue || queue.length === 0">
            <div class="text-center py-8 text-muzibu-text-gray">
                <i class="fas fa-music text-3xl mb-2 opacity-30"></i>
                <p class="text-sm">Çalma listesi boş</p>
            </div>
        </template>

        {{-- Queue List --}}
        <div id="queue-list" class="space-y-1">
            <template x-for="(song, index) in (queue || [])" :key="song?.song_id || index">
                <div
                    @click="playFromQueue(index)"
                    @dragstart="dragStart($event, index)"
                    @dragover.prevent
                    @drop="drop($event, index)"
                    draggable="true"
                    class="flex items-center gap-3 px-2 py-2 hover:bg-muzibu-gray-light cursor-pointer group transition-all duration-200 border-l-2 border-transparent hover:border-muzibu-coral rounded"
                    :class="{ 'bg-muzibu-gray-light/50 border-muzibu-coral': queueIndex === index }"
                >
                    <div class="w-8 h-8 rounded bg-gradient-to-br from-blue-500 to-purple-600 flex-shrink-0 overflow-hidden relative">
                        <template x-if="song.album_cover">
                            <img :src="getCoverUrl(song.album_cover, 32, 32)" :alt="song.song_title" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!song.album_cover">
                            <div class="w-full h-full flex items-center justify-center text-xs">🎵</div>
                        </template>
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <i class="fas fa-play text-white text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors" x-text="song.song_title?.tr || song.song_title?.en || song.song_title || 'Şarkı'"></h4>
                        <p class="text-[10px] text-muzibu-text-gray truncate" x-text="song.artist_title?.tr || song.artist_title?.en || song.artist_title || 'Sanatçı'"></p>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click.stop="removeFromQueue(index)" class="text-muzibu-text-gray hover:text-red-500 transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                        <button class="text-muzibu-text-gray hover:text-white cursor-move">
                            <i class="fas fa-grip-vertical text-xs"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</aside>
</div>
</template>
