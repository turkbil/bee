<div class="min-h-screen bg-gradient-to-br from-zinc-900 via-black to-zinc-900 text-white pt-8 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Search Header --}}
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold mb-6 bg-gradient-to-r from-muzibu-coral via-muzibu-coral-light to-muzibu-coral bg-clip-text text-transparent">
                Arama Sonuçları
            </h1>

            {{-- Search Input --}}
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 group-focus-within:text-muzibu-coral transition-colors"></i>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="query"
                    placeholder="Şarkı, sanatçı, albüm, playlist ara..."
                    class="w-full pl-12 pr-5 py-4 bg-zinc-800/50 hover:bg-zinc-800/70 focus:bg-zinc-800 border border-zinc-700 hover:border-zinc-600 focus:border-muzibu-coral rounded-xl text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-muzibu-coral/30 transition-all"
                    autocomplete="off">
            </div>

            {{-- Result Count --}}
            @if($totalCount > 0)
                <div class="mt-4 flex items-center gap-3 text-sm text-zinc-400">
                    <span><strong class="text-white">{{ number_format($totalCount) }}</strong> sonuç bulundu</span>
                    <span class="text-xs text-zinc-500">({{ $responseTime }}ms{{ $fromCache ? ' • cache' : '' }})</span>
                </div>
            @endif
        </div>

        {{-- Tabs - Client-side filtering (no server roundtrip) --}}
        <div class="mb-6 flex flex-wrap gap-2">
            @php
                $tabs = [
                    'all' => ['label' => 'Tümü', 'icon' => 'fa-grid-2', 'count' => $totalCount],
                    'songs' => ['label' => 'Şarkılar', 'icon' => 'fa-music', 'count' => $counts['songs'] ?? 0],
                    'albums' => ['label' => 'Albümler', 'icon' => 'fa-compact-disc', 'count' => $counts['albums'] ?? 0],
                    'artists' => ['label' => 'Sanatçılar', 'icon' => 'fa-microphone', 'count' => $counts['artists'] ?? 0],
                    'playlists' => ['label' => 'Playlistler', 'icon' => 'fa-list-music', 'count' => $counts['playlists'] ?? 0],
                    'genres' => ['label' => 'Türler', 'icon' => 'fa-guitar', 'count' => $counts['genres'] ?? 0],
                    'sectors' => ['label' => 'Sektörler', 'icon' => 'fa-building', 'count' => $counts['sectors'] ?? 0],
                    'radios' => ['label' => 'Radyolar', 'icon' => 'fa-radio', 'count' => $counts['radios'] ?? 0],
                    'myplaylists' => ['label' => 'Playlistlerim', 'icon' => 'fa-folder-music', 'count' => $counts['myplaylists'] ?? 0],
                    'favorites' => ['label' => 'Favorilerim', 'icon' => 'fa-heart', 'count' => $counts['favorites'] ?? 0],
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
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($results as $item)
                    <a href="{{ $item['url'] }}"
                       wire:navigate
                       class="group bg-zinc-900/50 border border-zinc-800 hover:border-muzibu-coral/50 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-muzibu-coral/10 hover:-translate-y-1">
                        <div class="flex gap-4 p-4">
                            {{-- Image --}}
                            @if(!empty($item['image']))
                                <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-zinc-800">
                                    <img src="{{ $item['image'] }}"
                                         alt="{{ $item['title'] }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                         loading="lazy">
                                </div>
                            @else
                                <div class="w-20 h-20 flex-shrink-0 rounded-lg bg-zinc-800 flex items-center justify-center">
                                    <i class="fas fa-music text-zinc-600 text-2xl"></i>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                {{-- Title --}}
                                <h3 class="font-semibold text-white group-hover:text-muzibu-coral transition-colors truncate">
                                    {{ $item['title'] }}
                                </h3>

                                {{-- Meta Info --}}
                                <div class="mt-1 space-y-1">
                                    @if(!empty($item['artist']))
                                        <p class="text-sm text-zinc-400 truncate">
                                            <i class="fas fa-user text-xs mr-1"></i>
                                            {{ $item['artist'] }}
                                        </p>
                                    @endif

                                    @if(!empty($item['album']))
                                        <p class="text-xs text-zinc-500 truncate">
                                            <i class="fas fa-compact-disc text-xs mr-1"></i>
                                            {{ $item['album'] }}
                                        </p>
                                    @endif

                                    @if(!empty($item['duration']))
                                        <p class="text-xs text-zinc-500">
                                            <i class="fas fa-clock text-xs mr-1"></i>
                                            {{ $item['duration'] }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Type Badge --}}
                                <div class="mt-2">
                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-zinc-800 text-zinc-400">
                                        {{ $item['type_label'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @elseif(strlen($query) >= 2)
            {{-- No Results --}}
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-zinc-800/50 flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-zinc-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-zinc-300 mb-2">Sonuç bulunamadı</h3>
                <p class="text-zinc-500">
                    "<span class="text-white">{{ $query }}</span>" için sonuç bulunamadı. Farklı bir arama terimi deneyin.
                </p>
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-muzibu-coral/10 flex items-center justify-center">
                    <i class="fas fa-magnifying-glass text-3xl text-muzibu-coral"></i>
                </div>
                <h3 class="text-xl font-semibold text-zinc-300 mb-2">Arama yapmaya başlayın</h3>
                <p class="text-zinc-500">
                    Şarkı, albüm, sanatçı veya playlist arayın
                </p>
            </div>
        @endif
    </div>
</div>
