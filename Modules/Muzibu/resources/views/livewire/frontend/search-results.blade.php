<div class="min-h-screen bg-gradient-to-br from-zinc-900 via-black to-zinc-900 text-white pt-8 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search Header --}}
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-6 bg-gradient-to-r from-muzibu-coral via-muzibu-coral-light to-muzibu-coral bg-clip-text text-transparent">
                Arama Sonuclari
            </h1>

            {{-- Search Input --}}
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-muzibu-coral transition-colors"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="query"
                    placeholder="Sarki, sanatci, album, playlist ara..."
                    class="w-full pl-12 pr-5 py-4 bg-zinc-800/50 hover:bg-zinc-800/70 focus:bg-zinc-800 border border-zinc-700 hover:border-zinc-600 focus:border-muzibu-coral rounded-xl text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/30 transition-all"
                    autocomplete="off">
            </div>

            {{-- Result Count --}}
            @if($totalCount > 0)
                <div class="mt-4 flex items-center gap-3 text-sm text-zinc-400">
                    <span><strong class="text-white">{{ number_format($totalCount) }}</strong> sonuc bulundu</span>
                    <span class="text-xs text-zinc-500">({{ $responseTime }}ms{{ $fromCache ? ' - cache' : '' }})</span>
                </div>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="mb-6 flex flex-wrap gap-2">
            @php
                $tabs = [
                    'all' => ['label' => 'Tumu', 'icon' => 'fa-grid-2', 'count' => $totalCount],
                    'songs' => ['label' => 'Sarkilar', 'icon' => 'fa-music', 'count' => $counts['songs'] ?? 0],
                    'albums' => ['label' => 'Albumler', 'icon' => 'fa-compact-disc', 'count' => $counts['albums'] ?? 0],
                    'artists' => ['label' => 'Sanatcilar', 'icon' => 'fa-microphone', 'count' => $counts['artists'] ?? 0],
                    'playlists' => ['label' => 'Playlistler', 'icon' => 'fa-list-music', 'count' => $counts['playlists'] ?? 0],
                    'genres' => ['label' => 'Turler', 'icon' => 'fa-guitar', 'count' => $counts['genres'] ?? 0],
                    'sectors' => ['label' => 'Sektorler', 'icon' => 'fa-building', 'count' => $counts['sectors'] ?? 0],
                    'radios' => ['label' => 'Radyolar', 'icon' => 'fa-radio', 'count' => $counts['radios'] ?? 0],
                    'myplaylists' => ['label' => 'Playlistlerim', 'icon' => 'fa-folder-music', 'count' => $counts['myplaylists'] ?? 0],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                @if($tab['count'] > 0 || $key === 'all')
                <button
                    wire:click="$set('activeTab', '{{ $key }}')"
                    class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center gap-2
                        {{ $activeTab === $key
                            ? 'bg-muzibu-coral text-white shadow-lg shadow-muzibu-coral/30'
                            : 'bg-zinc-800/50 text-zinc-400 hover:bg-zinc-800 hover:text-white border border-zinc-700' }}">
                    <i class="fas {{ $tab['icon'] }}"></i>
                    <span>{{ $tab['label'] }}</span>
                    @if($tab['count'] > 0)
                        <span class="px-1.5 py-0.5 text-xs rounded-full {{ $activeTab === $key ? 'bg-white/20' : 'bg-zinc-700' }}">
                            {{ $tab['count'] }}
                        </span>
                    @endif
                </button>
                @endif
            @endforeach
        </div>

        {{-- Results --}}
        @if($totalCount > 0)
            {{-- Songs Section (Satir formatinda - song-row component) --}}
            @if(($activeTab === 'all' || $activeTab === 'songs') && $songs->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-music text-muzibu-coral"></i>
                            Sarkilar
                        </h2>
                    @endif
                    <div class="bg-zinc-900/50 rounded-xl border border-zinc-800 overflow-hidden">
                        @foreach($songs as $index => $song)
                            <x-muzibu.song-row :song="$song" :index="$index" :show-album="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Albums Section (Card grid) --}}
            @if(($activeTab === 'all' || $activeTab === 'albums') && $albums->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-compact-disc text-muzibu-coral"></i>
                            Albumler
                        </h2>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($albums as $album)
                            <x-muzibu.album-card :album="$album" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Artists Section (Card grid - rounded style) --}}
            @if(($activeTab === 'all' || $activeTab === 'artists') && $artists->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-microphone text-muzibu-coral"></i>
                            Sanatcilar
                        </h2>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($artists as $artist)
                            <x-muzibu.artist-card :artist="$artist" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Playlists Section --}}
            @if(($activeTab === 'all' || $activeTab === 'playlists') && $playlists->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-list-music text-muzibu-coral"></i>
                            Playlistler
                        </h2>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($playlists as $playlist)
                            <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Genres Section --}}
            @if(($activeTab === 'all' || $activeTab === 'genres') && $genres->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-guitar text-muzibu-coral"></i>
                            Turler
                        </h2>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($genres as $genre)
                            <x-muzibu.genre-card :genre="$genre" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Sectors Section --}}
            @if(($activeTab === 'all' || $activeTab === 'sectors') && $sectors->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-building text-muzibu-coral"></i>
                            Sektorler
                        </h2>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($sectors as $sector)
                            <x-muzibu.sector-card :sector="$sector" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Radios Section --}}
            @if(($activeTab === 'all' || $activeTab === 'radios') && $radios->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-radio text-muzibu-coral"></i>
                            Radyolar
                        </h2>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($radios as $radio)
                            <x-muzibu.radio-card :radio="$radio" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- My Playlists Section --}}
            @if(($activeTab === 'all' || $activeTab === 'myplaylists') && $myPlaylists->count() > 0)
                <div class="mb-8">
                    @if($activeTab === 'all')
                        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-folder-music text-muzibu-coral"></i>
                            Playlistlerim
                        </h2>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($myPlaylists as $playlist)
                            <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
                        @endforeach
                    </div>
                </div>
            @endif

        @elseif(strlen($query) >= 2)
            {{-- No Results --}}
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-zinc-800/50 flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-zinc-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-zinc-300 mb-2">Sonuc bulunamadi</h3>
                <p class="text-zinc-500">
                    "<span class="text-white">{{ $query }}</span>" icin sonuc bulunamadi. Farkli bir arama terimi deneyin.
                </p>
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-muzibu-coral/10 flex items-center justify-center">
                    <i class="fas fa-magnifying-glass text-3xl text-muzibu-coral"></i>
                </div>
                <h3 class="text-xl font-semibold text-zinc-300 mb-2">Arama yapmaya baslayin</h3>
                <p class="text-zinc-500">
                    Sarki, album, sanatci veya playlist arayin
                </p>
            </div>
        @endif
    </div>
</div>
