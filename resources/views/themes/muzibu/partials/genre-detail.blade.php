{{-- Hero Section with Gradient Background --}}
<div class="relative mb-8">
    {{-- Dynamic Gradient Background --}}
    <div class="absolute inset-0 bg-gradient-to-b from-green-900/40 via-transparent to-transparent h-96 -z-10"></div>

    <div class="px-4 sm:px-6 py-8 sm:py-12">
        {{-- Genre Header - Modern Hero Style --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 sm:gap-8 mb-8">
            {{-- Genre Cover with Shadow --}}
            <div class="relative flex-shrink-0 group">
                @php $iconUrl = $genre->getIconUrl(300, 300); @endphp
                @if($iconUrl)
                    <img src="{{ $iconUrl }}"
                         alt="{{ $genre->getTranslation('title', app()->getLocale()) }}"
                         class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 object-cover rounded-xl shadow-2xl shadow-black/50">
                @else
                    <div class="w-48 h-48 sm:w-56 sm:h-56 md:w-64 md:h-64 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center text-5xl sm:text-6xl md:text-7xl shadow-2xl shadow-black/50">
                        🎸
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 w-full sm:min-w-0 text-center sm:text-left pb-0 sm:pb-4">
                <p class="text-xs sm:text-sm font-bold text-white uppercase tracking-wider mb-3 sm:mb-4">Tür</p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black text-white mb-4 sm:mb-6 leading-tight">
                    {{ $genre->getTranslation('title', app()->getLocale()) }}
                </h1>

                @if($genre->description)
                    <p class="text-sm sm:text-base md:text-lg text-gray-300 mb-4 sm:mb-6 line-clamp-2 opacity-90">
                        {{ clean_html($genre->getTranslation('description', app()->getLocale())) }}
                    </p>
                @endif

                <div class="flex items-center justify-center sm:justify-start gap-2 text-sm sm:text-base text-white">
                    <span class="font-bold">Muzibu</span>
                    <span class="text-gray-400">•</span>
                    <span class="font-semibold">{{ $playlists->count() }} playlist</span>
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
            class="w-14 h-14 sm:w-16 sm:h-16 bg-muzibu-coral hover:scale-105 active:scale-100 rounded-full flex items-center justify-center shadow-xl hover:shadow-2xl transition-all duration-200">
            <i class="fas fa-play text-white text-xl sm:text-2xl ml-1"></i>
        </button>

        <x-common.favorite-button :model="$genre" size="lg" />

        <button class="text-gray-400 hover:text-white transition-colors" title="Daha fazla">
            <i class="fas fa-ellipsis-h text-2xl"></i>
        </button>
    </div>

    {{-- PLAYLİSTLER BÖLÜMÜ --}}
    @if($playlists && $playlists->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
            @foreach($playlists as $playlist)
                <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
            @endforeach
        </div>
    @else
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-list text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Bu türde henüz playlist yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Bu türe ait playlistler eklendiğinde burada görünecek</p>
        </div>
    @endif
</div>
