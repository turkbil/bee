{{-- QUEUE OVERLAY - Modern Spotify-style Design --}}

{{-- Queue Drag & Drop Styles --}}
<style>
    /* Queue itemlarda text seçimi engelle */
    .queue-item,
    .queue-drag-handle {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
        touch-action: manipulation;
    }

    /* Sürüklenen item - havada asılı efekti */
    .sortable-drag {
        opacity: 1 !important;
        transform: scale(1.02) rotate(1deg) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 107, 107, 0.3) !important;
        z-index: 9999 !important;
        background: linear-gradient(135deg, rgba(39, 39, 42, 0.98), rgba(24, 24, 27, 0.98)) !important;
        border: 1px solid rgba(255, 107, 107, 0.4) !important;
    }

    /* Drag sırasında Alpine hata vermemesi için orijinal içeriği gizle, klonu göster */
    .sortable-drag .queue-item-original {
        display: none !important;
    }
    .sortable-drag .queue-item-clone {
        display: flex !important;
    }
    .queue-item-clone {
        display: none;
    }

    /* Ghost - bırakılacak yeri gösteren placeholder */
    .sortable-ghost {
        opacity: 0.4 !important;
        background: linear-gradient(90deg, transparent, rgba(255, 107, 107, 0.15), transparent) !important;
        border: 2px dashed rgba(255, 107, 107, 0.5) !important;
        border-radius: 0.75rem !important;
    }

    /* Seçilen item */
    .sortable-chosen {
        background: rgba(255, 107, 107, 0.15) !important;
        border-radius: 0.75rem !important;
    }

    /* Diğer itemların kayma animasyonu */
    .queue-item-animate {
        transition: transform 200ms cubic-bezier(0.25, 1, 0.5, 1) !important;
    }

    /* Bırakıldığında parlama efekti */
    @keyframes queue-item-drop {
        0% {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.5);
        }
        50% {
            transform: scale(1);
            box-shadow: 0 0 30px rgba(255, 107, 107, 0.3);
        }
        100% {
            transform: scale(1);
            box-shadow: none;
        }
    }

    .queue-item-dropped {
        animation: queue-item-drop 400ms ease-out !important;
    }

    /* Sürükleme sırasında diğer itemların hafif solması */
    .queue-dragging .queue-item:not(.sortable-drag):not(.sortable-ghost) {
        opacity: 0.7;
        transition: opacity 150ms ease;
    }

    /* Default state: duration visible, remove button hidden */
    .queue-item .queue-duration {
        display: inline !important;
    }
    .queue-item .queue-remove-btn {
        display: none !important;
    }

    /* Hover state: duration hidden, remove button visible */
    .queue-item:hover .queue-duration {
        display: none !important;
    }
    .queue-item:hover .queue-remove-btn {
        display: flex !important;
    }
</style>

<template x-if="typeof queue !== 'undefined'">
<div>
{{-- Backdrop (invisible - no darkening) --}}
<div
    x-show="showQueue"
    @click="showQueue = false"
    class="fixed inset-0 bg-transparent z-[66]"
    style="display: none;"
></div>

{{-- Panel with Swipe-to-Dismiss --}}
<aside
    x-show="showQueue"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-x-full"
    x-transition:enter-end="transform translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform translate-x-0"
    x-transition:leave-end="transform translate-x-full"
    style="display: none;"
    class="fixed top-0 right-0 bottom-0 w-full sm:w-96 bg-gradient-to-b from-zinc-900 via-zinc-900 to-black border-l border-white/5 shadow-2xl z-[70] flex flex-col"
    x-data="{ startX: 0, currentX: 0, isDragging: false }"
    :style="isDragging && currentX > 0 ? `transform: translateX(${currentX}px)` : ''"
>
    {{-- Mobile Handle Bar --}}
    <div class="sm:hidden flex justify-center py-2">
        <div class="w-12 h-1.5 bg-zinc-600 rounded-full"></div>
    </div>

    {{-- Header - Swipe Area --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-white/5 touch-none sm:touch-auto"
         @touchstart="startX = $event.touches[0].clientX; isDragging = true; currentX = 0"
         @touchmove.prevent="if(isDragging) { currentX = Math.max(0, $event.touches[0].clientX - startX); }"
         @touchend="if(currentX > 100) { showQueue = false; } isDragging = false; currentX = 0;">
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
                    <p class="text-xs text-zinc-400 truncate mt-0.5" x-text="currentSong.artist_title?.tr || currentSong.artist_title?.en || currentSong.artist_title || 'Muzibu'"></p>
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
        <div id="queue-list"
             class="p-2 space-y-0.5"
             x-ref="queueList"
             x-effect="
                if (showQueue && $refs.queueList && queue && queue.length > 0 && typeof Sortable !== 'undefined') {
                    $nextTick(() => {
                        if ($refs.queueList._sortable) $refs.queueList._sortable.destroy();
                        $refs.queueList._sortable = new Sortable($refs.queueList, {
                            animation: 250,
                            easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                            handle: '.queue-drag-handle',
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',
                            forceFallback: true,
                            fallbackClass: 'sortable-drag',
                            onClone: (evt) => {
                                // Klon oluşturulduğunda Alpine attribute'larını kaldır ve static içerik doldur
                                const clone = evt.clone;
                                const songTitle = clone.dataset.songTitle || 'Song';
                                const artistTitle = clone.dataset.artistTitle || 'Muzibu';
                                const coverUrl = clone.dataset.cover || '';

                                // TÜM Alpine attribute'larını kaldır (hata vermemesi için)
                                const removeAlpineAttrs = (el) => {
                                    const attrsToRemove = [];
                                    for (const attr of el.attributes) {
                                        if (attr.name.startsWith('x-') || attr.name.startsWith(':') || attr.name.startsWith('@')) {
                                            attrsToRemove.push(attr.name);
                                        }
                                    }
                                    attrsToRemove.forEach(attr => el.removeAttribute(attr));
                                    el.querySelectorAll('*').forEach(child => {
                                        const childAttrs = [];
                                        for (const attr of child.attributes) {
                                            if (attr.name.startsWith('x-') || attr.name.startsWith(':') || attr.name.startsWith('@')) {
                                                childAttrs.push(attr.name);
                                            }
                                        }
                                        childAttrs.forEach(attr => child.removeAttribute(attr));
                                    });
                                    // Template elemanlarını da kaldır
                                    el.querySelectorAll('template').forEach(t => t.remove());
                                };
                                removeAlpineAttrs(clone);

                                // Klon içeriğini doldur
                                const cloneTitle = clone.querySelector('.queue-clone-title');
                                const cloneArtist = clone.querySelector('.queue-clone-artist');
                                const cloneCover = clone.querySelector('.queue-clone-cover');
                                const cloneIcon = clone.querySelector('.queue-clone-icon');

                                if (cloneTitle) cloneTitle.textContent = songTitle;
                                if (cloneArtist) cloneArtist.textContent = artistTitle;

                                if (coverUrl && cloneCover) {
                                    cloneCover.src = coverUrl;
                                    cloneCover.style.display = 'block';
                                    if (cloneIcon) cloneIcon.style.display = 'none';
                                } else {
                                    if (cloneCover) cloneCover.style.display = 'none';
                                    if (cloneIcon) {
                                        cloneIcon.style.display = 'flex';
                                        cloneIcon.classList.remove('hidden');
                                    }
                                }
                            },
                            onStart: (evt) => {
                                $refs.queueList.classList.add('queue-dragging');
                                evt.item.style.transition = 'none';
                            },
                            onEnd: (evt) => {
                                $refs.queueList.classList.remove('queue-dragging');

                                // Drop animasyonu
                                evt.item.classList.add('queue-item-dropped');
                                setTimeout(() => evt.item.classList.remove('queue-item-dropped'), 400);

                                if (evt.oldIndex === evt.newIndex) return;

                                const movedSong = queue[evt.oldIndex];
                                const newQueue = [...queue];
                                newQueue.splice(evt.oldIndex, 1);
                                newQueue.splice(evt.newIndex, 0, movedSong);

                                if (queueIndex === evt.oldIndex) {
                                    queueIndex = evt.newIndex;
                                } else if (evt.oldIndex < queueIndex && evt.newIndex >= queueIndex) {
                                    queueIndex--;
                                } else if (evt.oldIndex > queueIndex && evt.newIndex <= queueIndex) {
                                    queueIndex++;
                                }

                                queue = newQueue;
                                showToast('{{ trans('muzibu::front.player.queue_updated') }}', 'success');
                            }
                        });
                    });
                }
             ">
            <template x-for="(song, index) in (queue || [])" :key="song?.song_id || ('queue-' + index)">
                <div
                    data-queue-item
                    :data-queue-index="index"
                    :data-song-title="song.song_title?.tr || song.song_title?.en || song.song_title || 'Song'"
                    :data-artist-title="song.artist_title?.tr || song.artist_title?.en || song.artist_title || 'Muzibu'"
                    :data-cover="song.album_cover ? getCoverUrl(song.album_cover, 100, 100) : ''"
                    @click="playFromQueue(index)"
                    class="queue-item flex items-center gap-2.5 p-2 rounded-xl cursor-pointer transition-all duration-150 group"
                    :class="{
                        'bg-muzibu-coral/10 border border-muzibu-coral/20': queueIndex === index,
                        'hover:bg-white/5': queueIndex !== index
                    }"
                >
                    {{-- ORIJINAL İÇERİK (Normal görünüm) --}}
                    <div class="queue-item-original contents">
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
                            <p class="text-xs text-gray-500 truncate" x-text="song.artist_title?.tr || song.artist_title?.en || song.artist_title || 'Muzibu'"></p>
                        </div>

                        {{-- Duration / Remove Button (Same Position - Toggle on Hover) --}}
                        <div class="w-10 h-6 flex items-center justify-center flex-shrink-0 relative">
                            {{-- Duration (Default State - hide on hover) --}}
                            <span class="queue-duration text-xs text-gray-600" x-show="song.duration" x-text="formatTime(song.duration)"></span>
                            {{-- Remove Button (Hover State) --}}
                            <button
                                x-show="queue.length > 1"
                                @click.stop="removeFromQueue(index)"
                                class="queue-remove-btn absolute inset-0 w-full h-full flex items-center justify-center rounded-full text-gray-400 hover:text-red-400 hover:bg-red-500/10 transition-all"
                                style="display: none;"
                                title="{{ trans('muzibu::front.player.remove_from_queue') }}"
                            >
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>

                        {{-- Drag handle (always visible) --}}
                        <div
                            class="queue-drag-handle w-7 h-7 flex items-center justify-center rounded-full text-gray-500 cursor-grab active:cursor-grabbing hover:bg-white/10 hover:text-white transition-all flex-shrink-0"
                            title="{{ trans('muzibu::front.player.drag') }}"
                        >
                            <i class="fas fa-grip-vertical text-xs"></i>
                        </div>
                    </div>

                    {{-- DRAG KLON İÇERİĞİ (Sadece sürüklerken görünür - Alpine bağımlılığı yok) --}}
                    <div class="queue-item-clone items-center gap-2.5 w-full">
                        {{-- Cover --}}
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-muzibu-coral to-orange-600 flex-shrink-0 overflow-hidden">
                            <img class="queue-clone-cover w-full h-full object-cover" src="" alt="">
                            <div class="queue-clone-icon w-full h-full hidden items-center justify-center">
                                <i class="fas fa-music text-white/30 text-xs"></i>
                            </div>
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="queue-clone-title text-sm font-medium truncate text-white"></h4>
                            <p class="queue-clone-artist text-xs text-gray-500 truncate"></p>
                        </div>
                        {{-- Drag icon --}}
                        <div class="w-7 h-7 flex items-center justify-center rounded-full text-gray-500 flex-shrink-0">
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
