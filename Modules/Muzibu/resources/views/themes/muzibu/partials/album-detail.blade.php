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
                    cover: @json($album->media_id && $album->coverMedia ? thumb($album->coverMedia, 100, 100, ['scale' => 1]) : null),
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

{{-- Hero Section - Full Width Background Image (Spotify Mobile Style) --}}
<div class="relative overflow-hidden">
    {{-- Full Width Background Image --}}
    @if($album->media_id && $album->coverMedia)
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] md:aspect-[21/9]">
            <img src="{{ thumb($album->coverMedia, 1200, 800, ['scale' => 1]) }}"
                 alt="{{ $album->getTranslation('title', app()->getLocale()) }}"
                 class="w-full h-full object-cover">
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

            {{-- Action Buttons - Top Right --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$album" size="lg" />
                {{-- Play Button --}}
                <button
                    @click="$dispatch('play-all-songs', { albumId: {{ $album->album_id }} })"
                    class="w-14 h-14 bg-muzibu-coral hover:scale-105 active:scale-95 rounded-full flex items-center justify-center shadow-xl transition-all">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content - Bottom Left --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Albüm</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-white mb-1 leading-tight drop-shadow-lg">
                    {{ $album->getTranslation('title', app()->getLocale()) }}
                </h1>
                @if($album->artist)
                    <a href="/artists/{{ $album->artist->getTranslation('slug', app()->getLocale()) }}"
                       class="text-sm font-semibold text-white/90 hover:underline">
                        {{ $album->artist->getTranslation('title', app()->getLocale()) }}
                    </a>
                    <span class="text-white/50 mx-2">•</span>
                @endif
                <span class="text-sm text-white/70">{{ $songs->count() }} şarkı</span>
            </div>
        </div>
    @else
        {{-- Fallback if no cover --}}
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] bg-gradient-to-br from-blue-900 to-slate-900">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-8xl">💿</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>

            {{-- Action Buttons --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$album" size="lg" />
                <button
                    @click="$dispatch('play-all-songs', { albumId: {{ $album->album_id }} }}"
                    class="w-14 h-14 bg-muzibu-coral rounded-full flex items-center justify-center shadow-xl">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Albüm</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white mb-1">
                    {{ $album->getTranslation('title', app()->getLocale()) }}
                </h1>
                <p class="text-sm text-white/70">{{ $songs->count() }} şarkı</p>
            </div>
        </div>
    @endif
</div>

{{-- Songs List Section --}}
<div class="px-4 sm:px-6 pt-6">
    {{-- Songs List - Modern Table Style --}}
    @if($songs && $songs->count() > 0)
        {{-- Table Header - Desktop Only --}}
        <div class="hidden md:grid grid-cols-[40px_50px_1fr_100px_60px] gap-4 px-4 py-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-white/5">
            <div class="text-center">#</div>
            <div></div>
            <div>Başlık</div>
            <div class="text-right">Süre</div>
            <div></div>
        </div>

        <div class="space-y-0">
            @foreach($songs as $index => $song)
                <x-muzibu.song-detail-row :song="$song" :index="$index" :show-album="false" :context-data="['album_id' => $album->id]" />
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
