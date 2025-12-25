{{-- QUEUE OVERLAY - Modern Spotify-style Design --}}
<template x-if="typeof queue !== 'undefined'">
<div>
{{-- Backdrop (invisible - no darkening) --}}
<div
    x-show="showQueue"
    @click="showQueue = false"
    class="fixed inset-0 bg-transparent z-40"
    style="display: none;"
></div>

{{-- Panel --}}
<aside
    x-show="showQueue"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-x-full"
    x-transition:enter-end="transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform translate-x-0"
    x-transition:leave-end="transform translate-x-full"
    style="display: none;"
    class="fixed top-0 right-0 bottom-0 w-full sm:w-96 bg-gradient-to-b from-zinc-900 via-zinc-900 to-black border-l border-white/5 shadow-2xl z-50 flex flex-col"
>
    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-muzibu-coral/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-stream text-muzibu-coral text-sm"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white">{{ trans('muzibu::front.player.queue') }}</h3>
                <p class="text-xs text-zinc-500" x-text="(queue || []).length + ' {{ trans('muzibu::front.general.song') }}'"></p>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <button
                @click="clearQueue()"
                class="p-2 text-zinc-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all"
                title="{{ trans('muzibu::front.player.clear_queue') }}"
            >
                <i class="fas fa-trash-alt text-sm"></i>
            </button>
            <button
                @click="showQueue = false"
                class="p-2 text-zinc-500 hover:text-white hover:bg-white/10 rounded-lg transition-all"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- Now Playing --}}
    <template x-if="currentSong">
        <div class="px-5 py-4 bg-gradient-to-r from-muzibu-coral/10 to-transparent border-b border-white/5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-1.5 h-1.5 bg-muzibu-coral rounded-full animate-pulse"></div>
                <span class="text-xs font-semibold text-muzibu-coral uppercase tracking-wider">{{ trans('muzibu::front.player.now_playing') }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-muzibu-coral to-pink-600 flex-shrink-0 overflow-hidden shadow-lg shadow-muzibu-coral/20">
                    <template x-if="currentSong.album_cover">
                        <img :src="getCoverUrl(currentSong.album_cover, 56, 56)" :alt="currentSong.song_title" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!currentSong.album_cover">
                        <div class="w-full h-full flex items-center justify-center text-2xl">🎵</div>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-white truncate" x-text="currentSong.song_title?.tr || currentSong.song_title?.en || currentSong.song_title || (window.muzibuPlayerConfig?.frontLang?.general?.song || 'Song')"></h4>
                    <p class="text-xs text-zinc-400 truncate mt-0.5" x-text="currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || (window.muzibuPlayerConfig?.frontLang?.general?.artist || 'Artist')"></p>
                </div>
                <button
                    @click="toggleLike()"
                    class="p-2 transition-all"
                    :class="isLiked ? 'text-muzibu-coral' : 'text-zinc-500 hover:text-white'"
                >
                    <i :class="isLiked ? 'fas fa-heart' : 'far fa-heart'"></i>
                </button>
            </div>
        </div>
    </template>

    {{-- Queue Header --}}
    <div class="px-5 py-3 flex items-center justify-between border-b border-white/5">
        <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ trans('muzibu::front.player.next_songs') }}</span>
        <span class="text-xs text-zinc-600" x-show="queue && queue.length > 0">{{ trans('muzibu::front.player.drag_to_sort') }}</span>
    </div>

    {{-- Queue Content --}}
    <div class="flex-1 overflow-y-auto">
        {{-- Empty State --}}
        <template x-if="!queue || queue.length === 0">
            <div class="flex flex-col items-center justify-center h-full text-center px-8">
                <div class="w-20 h-20 bg-zinc-800/50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-music text-3xl text-zinc-600"></i>
                </div>
                <h4 class="text-base font-semibold text-zinc-400 mb-1">{{ trans('muzibu::front.player.queue_empty') }}</h4>
                <p class="text-sm text-zinc-600">{{ trans('muzibu::front.player.queue_empty_description') }}</p>
            </div>
        </template>

        {{-- Queue List --}}
        <div id="queue-list" class="p-2 space-y-0.5">
            <template x-for="(song, index) in (queue || [])" :key="'queue-' + index + '-' + (song?.song_id || 'song')">
                <div
                    @click="playFromQueue(index)"
                    @dragstart="dragStart($event, index)"
                    @dragover.prevent="dragOver(index)"
                    @dragleave="dropTargetIndex = null"
                    @drop.prevent="drop(index)"
                    @dragend="draggedIndex = null; dropTargetIndex = null"
                    draggable="true"
                    class="flex items-center gap-2.5 p-2 rounded-xl cursor-pointer transition-all duration-150 group"
                    :class="{
                        'bg-muzibu-coral/10 border border-muzibu-coral/20': queueIndex === index,
                        'hover:bg-white/5': queueIndex !== index,
                        'border-t-2 border-muzibu-coral': dropTargetIndex === index && draggedIndex !== null,
                        'opacity-50': draggedIndex === index
                    }"
                >
                    {{-- Cover with Play Overlay --}}
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-muzibu-coral to-orange-600 flex-shrink-0 overflow-hidden relative">
                        <template x-if="song.album_cover">
                            <img :src="getCoverUrl(song.album_cover, 100, 100)" :alt="song.song_title" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!song.album_cover">
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-music text-white/30 text-xs"></i>
                            </div>
                        </template>
                        {{-- Play overlay on hover --}}
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <i class="fas fa-play text-white text-xs"></i>
                        </div>
                        {{-- Playing indicator --}}
                        <template x-if="queueIndex === index">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <i class="fas fa-volume-up text-muzibu-coral text-xs animate-pulse"></i>
                            </div>
                        </template>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h4
                            class="text-sm font-medium truncate transition-colors"
                            :class="queueIndex === index ? 'text-muzibu-coral' : 'text-white group-hover:text-muzibu-coral'"
                            x-text="song.song_title?.tr || song.song_title?.en || song.song_title || (window.muzibuPlayerConfig?.frontLang?.general?.song || 'Song')"
                        ></h4>
                        <p class="text-xs text-gray-500 truncate" x-text="song.artist_title?.tr || song.artist_title?.en || song.artist_title || (window.muzibuPlayerConfig?.frontLang?.general?.artist || 'Artist')"></p>
                    </div>

                    {{-- Duration (hide on hover) --}}
                    <div class="text-xs text-gray-600 flex-shrink-0 group-hover:hidden" x-show="song.duration" x-text="formatTime(song.duration)"></div>

                    {{-- Actions (show on hover) --}}
                    <div class="hidden group-hover:flex items-center gap-1 flex-shrink-0">
                        {{-- Remove from queue button --}}
                        <button
                            x-show="queue.length > 1"
                            @click.stop="removeFromQueue(index)"
                            class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-all"
                            title="{{ trans('muzibu::front.player.remove_from_queue') }}"
                        >
                            <i class="fas fa-times text-xs"></i>
                        </button>
                        {{-- Drag handle --}}
                        <div class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 cursor-grab active:cursor-grabbing" title="{{ trans('muzibu::front.player.drag') }}">
                            <i class="fas fa-grip-vertical text-xs"></i>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="px-5 py-3 border-t border-white/5 bg-transparent">
        <div class="flex items-center justify-between text-xs text-zinc-600">
            <span x-show="queue && queue.length > 0">
                <i class="fas fa-info-circle mr-1"></i>
                {{ trans('muzibu::front.player.click_to_play') }}
            </span>
            {{-- Keyboard hint - temporarily hidden
            <span x-show="queue && queue.length > 0">
                <i class="fas fa-keyboard mr-1"></i>
                0-9 {{ trans('muzibu::front.player.quick_access') }}
            </span>
            --}}
        </div>
    </div>
</aside>
</div>
</template>
