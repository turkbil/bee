<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Müzik Türleri</h1>
        <p class="text-gray-400">Favori müzik türlerini keşfet</p>
    </div>

    {{-- Genres Grid --}}
    @if($genres && $genres->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($genres as $genre)
                <a href="/genres/{{ $genre->getTranslation('slug', app()->getLocale()) }}"
                   x-on:contextmenu.prevent.stop="$store.contextMenu.openContextMenu($event, 'genre', {
                       id: {{ $genre->genre_id }},
                       title: '{{ addslashes($genre->getTranslation('title', app()->getLocale())) }}',
                       slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                       is_favorite: {{ auth()->check() && method_exists($genre, 'isFavoritedBy') && $genre->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
                   })"
                   x-data="{
                       touchTimer: null,
                       touchStartPos: { x: 0, y: 0 }
                   }"
                   x-on:touchstart="
                       touchStartPos = { x: $event.touches[0].clientX, y: $event.touches[0].clientY };
                       touchTimer = setTimeout(() => {
                           if (navigator.vibrate) navigator.vibrate(50);
                           $store.contextMenu.openContextMenu({
                               clientX: $event.touches[0].clientX,
                               clientY: $event.touches[0].clientY
                           }, 'genre', {
                               id: {{ $genre->genre_id }},
                               title: '{{ addslashes($genre->getTranslation('title', app()->getLocale())) }}',
                               slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                               is_favorite: {{ auth()->check() && method_exists($genre, 'isFavoritedBy') && $genre->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
                           });
                       }, 500);
                   "
                   x-on:touchend="clearTimeout(touchTimer)"
                   x-on:touchmove="
                       const moved = Math.abs($event.touches[0].clientX - touchStartPos.x) > 10 ||
                                    Math.abs($event.touches[0].clientY - touchStartPos.y) > 10;
                       if (moved) clearTimeout(touchTimer);
                   "
                   class="group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300">
                    <div class="relative mb-4">
                        @if($genre->media_id && $genre->iconMedia)
                            <img src="{{ thumb($genre->iconMedia, 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $genre->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                 loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center text-4xl shadow-lg">
                                🎸
                            </div>
                        @endif

                        {{-- Play Button - Spotify Style Bottom Right --}}
                        <button x-on:click.stop.prevent="
                            $store.player.setPlayContext({
                                type: 'genre',
                                id: {{ $genre->genre_id }},
                                name: '{{ addslashes($genre->getTranslation('title', app()->getLocale())) }}'
                            });
                            playGenre({{ $genre->genre_id }});
                        " class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
                            <i class="fas fa-play ml-1"></i>
                        </button>

                        {{-- 3-Dot Menu Button (Cover Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
                        <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" x-on:click.stop.prevent>
                            <button x-on:click="$store.contextMenu.openContextMenu($event, 'genre', {
                                id: {{ $genre->genre_id }},
                                title: '{{ addslashes($genre->getTranslation('title', app()->getLocale())) }}',
                                slug: '{{ $genre->getTranslation('slug', app()->getLocale()) }}',
                                is_favorite: {{ auth()->check() && method_exists($genre, 'isFavoritedBy') && $genre->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}
                            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <h3 class="font-semibold text-white mb-1 truncate">
                        {{ $genre->getTranslation('title', app()->getLocale()) }}
                    </h3>

                    @if($genre->description)
                        <p class="text-sm text-gray-400 truncate">
                            {{ $genre->getTranslation('description', app()->getLocale()) }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($genres->hasPages())
            <div class="mt-8">
                {{ $genres->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-guitar text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz müzik türü yok</h3>
            <p class="text-gray-400">Yakında yeni müzik türleri eklenecek</p>
        </div>
    @endif
</div>
