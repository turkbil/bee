{{-- Hero Section - Full Width Background Image (Spotify Mobile Style) --}}
<div class="relative overflow-hidden">
    @php
        $heroMedia = $sector->getFirstMedia('hero');
        // WebP + JPG fallback için her iki format
        $heroUrlWebp = $heroMedia ? thumb($heroMedia, 1200, 800, ['scale' => 1, 'format' => 'webp']) : null;
        $heroUrlJpg = $heroMedia ? thumb($heroMedia, 1200, 800, ['scale' => 1, 'format' => 'jpg']) : null;
    @endphp
    {{-- Full Width Background Image --}}
    @if($heroUrlWebp || $heroUrlJpg)
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] md:aspect-[21/9]">
            {{-- Picture tag with WebP + JPG fallback for old devices --}}
            <picture>
                @if($heroUrlWebp)
                    <source srcset="{{ $heroUrlWebp }}" type="image/webp">
                @endif
                <img src="{{ $heroUrlJpg ?: $heroUrlWebp }}"
                     alt="{{ $sector->getTranslation('title', app()->getLocale()) }}"
                     class="w-full h-full object-cover"
                     loading="eager">
            </picture>
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

            {{-- Action Buttons - Top Right --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$sector" size="lg" />
            </div>

            {{-- Content - Bottom Left --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Sektör</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-white mb-2 leading-tight drop-shadow-lg">
                    {{ $sector->getTranslation('title', app()->getLocale()) }}
                </h1>
                @if($sector->description)
                    <p class="text-sm text-white/80 mb-2 line-clamp-2 max-w-2xl">
                        {{ clean_html($sector->getTranslation('description', app()->getLocale())) }}
                    </p>
                @endif
                <p class="text-sm text-white/70">{{ $playlists->count() }} playlist</p>
            </div>
        </div>
    @else
        {{-- Fallback if no hero --}}
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] bg-gradient-to-br from-pink-900 to-slate-900">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-8xl">🎭</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>

            {{-- Action Buttons --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$sector" size="lg" />
            </div>

            {{-- Content --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Sektör</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white mb-2">
                    {{ $sector->getTranslation('title', app()->getLocale()) }}
                </h1>
                <p class="text-sm text-white/70">{{ $playlists->count() }} playlist</p>
            </div>
        </div>
    @endif
</div>

{{-- Content Section --}}
<div class="px-4 sm:px-6 pt-6">
    {{-- RADYOLAR BÖLÜMÜ (Üstte) --}}
    @if(isset($radios) && $radios->count() > 0)
        <div class="mb-8 sm:mb-12">
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                <i class="fas fa-radio text-red-500"></i>
                Canlı Radyolar
                <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full animate-pulse">CANLI</span>
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($radios as $radio)
                    <x-muzibu.radio-card :radio="$radio" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- PLAYLİSTLER BÖLÜMÜ (Altta) --}}
    @if($playlists && $playlists->count() > 0)
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6 flex items-center gap-2 sm:gap-3">
                <i class="fas fa-list text-muzibu-coral"></i>
                Playlistler
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($playlists as $playlist)
                    <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Bu sektörde henüz playlist yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Yakında yeni playlistler eklenecek</p>
        </div>
    @endif
</div>
