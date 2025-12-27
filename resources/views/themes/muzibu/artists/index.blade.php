@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header - Alternatif 2: Icon + Text (FA Beat-Fade Animation) --}}
    <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-microphone-stand text-xl sm:text-2xl text-white fa-beat-fade" style="--fa-animation-duration: 2s; --fa-beat-fade-opacity: 0.4; --fa-beat-fade-scale: 1.1;"></i>
        </div>
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-0.5">Sanatçılar</h1>
            <p class="text-gray-400 text-sm sm:text-base">En popüler sanatçılar</p>
        </div>
    </div>

    {{-- Artists Grid --}}
    @if($artists && $artists->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
            @foreach($artists as $artist)
                <a href="{{ route('muzibu.artists.show', $artist->getTranslation('slug', app()->getLocale())) }}"
                   data-spa
                   class="artist-card group bg-muzibu-gray hover:bg-gray-700 rounded-lg p-4 transition-all duration-300"
                   data-artist-id="{{ $artist->artist_id }}"
                   data-artist-title="{{ $artist->getTranslation('title', app()->getLocale()) }}"
                   data-is-favorite="{{ is_favorited('artist', $artist->artist_id) ? '1' : '0' }}">
                    <div class="relative mb-4">
                        @if($artist->media_id && $artist->photoMedia)
                            <img src="{{ thumb($artist->photoMedia, 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $artist->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full aspect-square object-cover rounded-full shadow-lg"
                                 loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-4xl shadow-lg">
                                🎤
                            </div>
                        @endif

                        {{-- Play Button - Spotify Style Bottom Right --}}
                        <button @click.stop.prevent="
                            $store.player.setPlayContext({
                                type: 'artist',
                                id: {{ $artist->artist_id }},
                                name: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}'
                            });
                            playArtist({{ $artist->artist_id }});
                        " class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 bg-muzibu-coral text-white rounded-full w-12 h-12 flex items-center justify-center shadow-xl hover:scale-110 hover:bg-green-500">
                            <i class="fas fa-play ml-1"></i>
                        </button>

                        {{-- 3-Dot Menu Button (Photo Sağ Üst) - HOVER'DA GÖRÜNÜR --}}
                        <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop.prevent>
                            <button @click="Alpine.store('contextMenu').openContextMenu($event, 'artist', {
                                id: {{ $artist->artist_id }},
                                title: '{{ addslashes($artist->getTranslation('title', app()->getLocale())) }}',
                                is_favorite: {{ is_favorited('artist', $artist->artist_id) ? 'true' : 'false' }}
                            })" class="w-8 h-8 bg-black/60 hover:bg-black/80 rounded-full flex items-center justify-center text-white transition-all">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <h3 class="font-semibold text-white mb-1 truncate text-center">
                        {{ $artist->getTranslation('title', app()->getLocale()) }}
                    </h3>

                    <p class="text-sm text-gray-400 truncate text-center">
                        Sanatçı
                    </p>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($artists->hasPages())
            <div class="mt-8">
                {{ $artists->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-microphone text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz sanatçı yok</h3>
            <p class="text-gray-400">Yakında yeni sanatçılar eklenecek</p>
        </div>
    @endif
</div>
@endsection
