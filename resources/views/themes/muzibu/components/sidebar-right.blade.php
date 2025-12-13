{{-- RIGHT SIDEBAR - FEATURED PLAYLISTS (Server Rendered) --}}
<div class="muzibu-right-sidebar space-y-8">
    {{-- Section 1: Sizin İçin --}}
    <div>
        <div class="mb-4 px-3">
            <h3 class="text-lg font-bold bg-gradient-to-r from-white via-zinc-100 to-muzibu-text-gray bg-clip-text text-transparent">
                Sizin İçin
            </h3>
        </div>

        {{-- Featured Playlists --}}
        @if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
        <div class="space-y-0">
            @foreach($featuredPlaylists->take(5) as $playlist)
            <a href="/playlists/{{ $playlist->getTranslation('slug', app()->getLocale()) }}"
               wire:navigate
               class="flex items-center gap-3 p-2 hover:bg-white/10 rounded cursor-pointer transition-all group">
                <div class="w-12 h-12 rounded bg-gradient-to-br from-purple-500 to-pink-600 flex-shrink-0 overflow-hidden relative">
                    @if($playlist->coverMedia)
                        <img src="{{ thumb($playlist->coverMedia, 48, 48, ['scale' => 1]) }}" alt="{{ getLocaleTitle($playlist->title, 'Playlist') }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xl">🎵</div>
                    @endif
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="fas fa-play text-white"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                        {{ getLocaleTitle($playlist->title, 'Playlist') }}
                    </h4>
                    <p class="text-xs text-muzibu-text-gray truncate">
                        {{ $playlist->songs()->count() }} şarkı
                    </p>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-muzibu-text-gray">
            <i class="fas fa-music text-2xl mb-2"></i>
            <p class="text-sm">Henüz playlist yok</p>
        </div>
        @endif
    </div>

    {{-- Section 2: Popüler Şarkılar --}}
    <div>
        <div class="mb-4 px-3">
            <h3 class="text-lg font-bold bg-gradient-to-r from-muzibu-coral via-orange-400 to-yellow-400 bg-clip-text text-transparent">
                Popüler Şarkılar
            </h3>
        </div>
        <div class="space-y-0">
            @for($i = 1; $i <= 5; $i++)
            <a href="#" class="flex items-center gap-3 p-2 hover:bg-white/10 rounded cursor-pointer transition-all group">
                <div class="w-8 h-8 rounded bg-gradient-to-br from-orange-500 to-red-600 flex-shrink-0 flex items-center justify-center text-white font-bold text-xs">
                    {{ $i }}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                        Popüler Şarkı {{ $i }}
                    </h4>
                    <p class="text-xs text-muzibu-text-gray truncate">
                        Sanatçı Adı
                    </p>
                </div>
            </a>
            @endfor
        </div>
    </div>

    {{-- Section 3: Yeni Eklenenler --}}
    <div>
        <div class="mb-4 px-3">
            <h3 class="text-lg font-bold bg-gradient-to-r from-green-400 via-emerald-400 to-teal-400 bg-clip-text text-transparent">
                Yeni Eklenenler
            </h3>
        </div>
        <div class="space-y-0">
            @for($i = 1; $i <= 4; $i++)
            <a href="#" class="flex items-center gap-3 p-2 hover:bg-white/10 rounded cursor-pointer transition-all group">
                <div class="w-12 h-12 rounded bg-gradient-to-br from-green-500 to-teal-600 flex-shrink-0 overflow-hidden relative">
                    <div class="w-full h-full flex items-center justify-center text-xl">🆕</div>
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <i class="fas fa-play text-white"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-white truncate group-hover:text-muzibu-coral transition-colors">
                        Yeni Albüm {{ $i }}
                    </h4>
                    <p class="text-xs text-muzibu-text-gray truncate">
                        Yeni Sanatçı
                    </p>
                </div>
            </a>
            @endfor
        </div>
    </div>

    {{-- Section 4: Türler --}}
    <div>
        <div class="mb-4 px-3">
            <h3 class="text-lg font-bold bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent">
                Türler
            </h3>
        </div>
        <div class="grid grid-cols-2 gap-2 px-3">
            @foreach(['Pop', 'Rock', 'Jazz', 'Klasik', 'Elektronik', 'R&B'] as $genre)
            <a href="#" class="bg-gradient-to-br from-blue-600/20 to-purple-600/20 hover:from-blue-600/30 hover:to-purple-600/30 border border-blue-500/20 rounded-lg p-3 text-center transition-all group">
                <div class="text-2xl mb-1">🎸</div>
                <div class="text-xs font-semibold text-white group-hover:text-muzibu-coral transition-colors">
                    {{ $genre }}
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
