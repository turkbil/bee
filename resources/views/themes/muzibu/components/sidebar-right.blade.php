{{-- RIGHT SIDEBAR - FEATURED PLAYLISTS (Responsive Width) --}}
<aside class="hidden xl:block bg-black p-6 overflow-y-auto border-l border-white/5">
    <div class="mb-6">
        <h3 class="text-lg font-bold bg-gradient-to-r from-white via-zinc-100 to-muzibu-text-gray bg-clip-text text-transparent">
            Sizin İçin
        </h3>
    </div>

    {{-- Featured Playlists (Blade Render - same as main) --}}
    @if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
    <div class="space-y-0">
        @foreach($featuredPlaylists as $playlist)
        <div class="flex items-center gap-3 p-2 hover:bg-white/10 rounded cursor-pointer transition-all group">
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
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8 text-muzibu-text-gray">
        <i class="fas fa-music text-2xl mb-2"></i>
        <p class="text-sm">Henüz playlist yok</p>
    </div>
    @endif
</aside>
