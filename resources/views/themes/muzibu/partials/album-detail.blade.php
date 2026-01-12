{{-- 🎯 Sidebar Data - Track list for right sidebar --}}
<script>
document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        if (window.Alpine && window.Alpine.store('sidebar')) {
            window.Alpine.store('sidebar').setContent(
                'album',
                @json($songs->map(function($song) {
                    return [
                        'id' => $song->song_id,
                        'title' => $song->getTranslation('title', app()->getLocale()),
                        'artist' => $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '',
                        'duration' => gmdate('i:s', $song->duration ?? 0)
                    ];
                })),
                {
                    type: 'Albüm',
                    title: @json($album->getTranslation('title', app()->getLocale())),
                    cover: @json($album->getCoverUrl(100, 100)),
                    id: {{ $album->album_id }}
                }
            );
        }
    }, 100);
});

if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').setContent(
        'album',
        @json($songs->map(function($song) {
            return [
                'id' => $song->song_id,
                'title' => $song->getTranslation('title', app()->getLocale()),
                'artist' => $song->artist ? $song->artist->getTranslation('title', app()->getLocale()) : '',
                'duration' => gmdate('i:s', $song->duration ?? 0)
            ];
        })),
        {
            type: 'Albüm',
            title: @json($album->getTranslation('title', app()->getLocale())),
            cover: @json($album->getFirstMedia('album_cover') ? thumb($album->getFirstMedia('album_cover'), 100, 100, ['scale' => 1]) : null),
            id: {{ $album->album_id }}
        }
    );
}
</script>

{{-- Hero Section with Gradient Background --}}
<div class="relative mb-8">
    {{-- Dynamic Gradient Background --}}
    <div class="absolute inset-0 bg-gradient-to-b from-blue-900/40 via-transparent to-transparent h-96 -z-10"></div>

    <div class="px-4 sm:px-6 py-8 sm:py-12">
        {{-- Album Header - Modern Hero Style --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 sm:gap-8 mb-8">
            {{-- Album Cover with Shadow --}}
            <div class="relative flex-shrink-0 group">
                @if($album->getCoverUrl())
                    <img src="{{ $album->getCoverUrl(300, 300) }}"
                         alt="{{ $album->getTranslation('title', app()->getLocale()) }}"
                         class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover rounded-xl shadow-2xl shadow-black/50">
                @else
                    <div class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-5xl sm:text-6xl md:text-7xl shadow-2xl shadow-black/50">
                        💿
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
                <p class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider mb-3 sm:mb-4">Albüm</p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black text-white mb-4 sm:mb-6 leading-tight">
                    {{ $album->getTranslation('title', app()->getLocale()) }}
                </h1>

                @if($album->artist)
                    <div class="mb-4 sm:mb-6">
                        <a href="/artists/{{ $album->artist->getTranslation('slug', app()->getLocale()) }}"
                           
                           class="text-lg sm:text-xl md:text-2xl font-bold text-white hover:underline transition-colors">
                            {{ $album->artist->getTranslation('title', app()->getLocale()) }}
                        </a>
                    </div>
                @endif

                <div class="flex items-center justify-center sm:justify-start gap-2 text-sm sm:text-base text-white">
                    @if($album->release_year)
                        <span class="font-semibold">{{ $album->release_year }}</span>
                        <span class="text-gray-400">•</span>
                    @endif
                    <span class="font-semibold">{{ $songs->count() }} şarkı</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Content Section --}}
<div class="px-4 sm:px-6">
    {{-- Actions - Larger Buttons --}}
    <div class="flex items-center gap-6 mb-8 sm:mb-10">
        <button
            @click="$dispatch('play-all-songs', { albumId: {{ $album->album_id }} })"
            class="w-14 h-14 sm:w-16 sm:h-16 bg-muzibu-coral hover:scale-105 active:scale-100 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-200">
            <i class="fas fa-play text-white text-xl sm:text-2xl ml-1"></i>
        </button>

        <x-common.favorite-button :model="$album" size="lg" />

        <button class="text-gray-400 hover:text-white transition-colors" title="Daha fazla">
            <i class="fas fa-ellipsis-h text-2xl"></i>
        </button>
    </div>

    {{-- Songs List - Simple Design --}}
    @if($songs && $songs->count() > 0)
        <div class="bg-slate-900/50 rounded-lg overflow-hidden">
            @foreach($songs as $index => $song)
                <x-muzibu.song-simple-row :song="$song" :index="$index" :context-data="['album_id' => $album->id]" />
            @endforeach
        </div>
    @else
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-record-vinyl text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Bu albümde henüz şarkı yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Albüme şarkı eklendiğinde burada görünecek</p>
        </div>
    @endif
</div>
