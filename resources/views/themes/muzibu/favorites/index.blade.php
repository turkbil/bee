@extends('themes.muzibu.layouts.app')

@section('content')
{{-- Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Favorilerim</h1>
        <p class="text-gray-400">Beğendiğin içerikler</p>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-8 border-b border-gray-800">
        <nav class="flex space-x-4 sm:space-x-8 overflow-x-auto scrollbar-hide" x-data="{ activeTab: '{{ $type }}' }">
            @if(($counts['songs'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'songs']) }}"
                   class="pb-4 px-2 border-b-2 font-medium text-sm sm:text-base transition-colors whitespace-nowrap flex items-center gap-2"
                   :class="activeTab === 'songs' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                    <i class="fas fa-music text-xs"></i>
                    <span>Şarkılar</span>
                    <span class="px-2 py-0.5 bg-gray-800 rounded-full text-xs">{{ $counts['songs'] }}</span>
                </a>
            @endif

            @if(($counts['albums'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'albums']) }}"
                   class="pb-4 px-2 border-b-2 font-medium text-sm sm:text-base transition-colors whitespace-nowrap flex items-center gap-2"
                   :class="activeTab === 'albums' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                    <i class="fas fa-compact-disc text-xs"></i>
                    <span>Albümler</span>
                    <span class="px-2 py-0.5 bg-gray-800 rounded-full text-xs">{{ $counts['albums'] }}</span>
                </a>
            @endif

            @if(($counts['playlists'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'playlists']) }}"
                   class="pb-4 px-2 border-b-2 font-medium text-sm sm:text-base transition-colors whitespace-nowrap flex items-center gap-2"
                   :class="activeTab === 'playlists' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                    <i class="fas fa-list text-xs"></i>
                    <span>Playlistler</span>
                    <span class="px-2 py-0.5 bg-gray-800 rounded-full text-xs">{{ $counts['playlists'] }}</span>
                </a>
            @endif

            @if(($counts['genres'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'genres']) }}"
                   class="pb-4 px-2 border-b-2 font-medium text-sm sm:text-base transition-colors whitespace-nowrap flex items-center gap-2"
                   :class="activeTab === 'genres' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                    <i class="fas fa-guitar text-xs"></i>
                    <span>Türler</span>
                    <span class="px-2 py-0.5 bg-gray-800 rounded-full text-xs">{{ $counts['genres'] }}</span>
                </a>
            @endif

            @if(($counts['sectors'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'sectors']) }}"
                   class="pb-4 px-2 border-b-2 font-medium text-sm sm:text-base transition-colors whitespace-nowrap flex items-center gap-2"
                   :class="activeTab === 'sectors' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                    <i class="fas fa-briefcase text-xs"></i>
                    <span>Sektörler</span>
                    <span class="px-2 py-0.5 bg-gray-800 rounded-full text-xs">{{ $counts['sectors'] }}</span>
                </a>
            @endif

            @if(($counts['radios'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'radios']) }}"
                   class="pb-4 px-2 border-b-2 font-medium text-sm sm:text-base transition-colors whitespace-nowrap flex items-center gap-2"
                   :class="activeTab === 'radios' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                    <i class="fas fa-broadcast-tower text-xs"></i>
                    <span>Radyolar</span>
                    <span class="px-2 py-0.5 bg-gray-800 rounded-full text-xs">{{ $counts['radios'] }}</span>
                </a>
            @endif

            @if(($counts['blogs'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'blogs']) }}"
                   class="pb-4 px-2 border-b-2 font-medium text-sm sm:text-base transition-colors whitespace-nowrap flex items-center gap-2"
                   :class="activeTab === 'blogs' ? 'border-muzibu-coral text-muzibu-coral' : 'border-transparent text-gray-400 hover:text-white hover:border-gray-600'">
                    <i class="fas fa-blog text-xs"></i>
                    <span>Bloglar</span>
                    <span class="px-2 py-0.5 bg-gray-800 rounded-full text-xs">{{ $counts['blogs'] }}</span>
                </a>
            @endif
        </nav>
    </div>

    {{-- Content --}}
    @if($favorites->count() > 0)

        @if($type === 'songs')
            {{-- Songs - Simple List Design --}}
            <div class="rounded-lg overflow-hidden">
                @foreach($favorites as $index => $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.song-simple-row :song="$favorite->favoritable" :index="$index" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'albums')
            {{-- Albums - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.album-card :album="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'playlists')
            {{-- Playlists - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.playlist-card :playlist="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'genres')
            {{-- Genres - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.genre-card :genre="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'sectors')
            {{-- Sectors - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.sector-card :sector="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'radios')
            {{-- Radios - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.radio-card :radio="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'blogs')
            {{-- Blogs - Grid with Inline Component --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        @php
                            $blog = $favorite->favoritable;
                            $cover = $blog->coverMedia;
                            $coverUrl = $cover ? thumb($cover, 400, 300) : '/images/default-blog.png';
                        @endphp
                        <article class="group relative border border-white/10 rounded-xl overflow-hidden hover:border-muzibu-coral transition">
                            <a href="/blog/{{ $blog->slug }}" class="block" data-spa>
                                {{-- Cover Image --}}
                                <div class="aspect-[4/3] overflow-hidden bg-gray-900">
                                    <img src="{{ $coverUrl }}"
                                         alt="{{ $blog->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                         loading="lazy">
                                </div>

                                {{-- Content --}}
                                <div class="p-4">
                                    <h3 class="text-white font-semibold text-base mb-2 line-clamp-2 group-hover:text-muzibu-coral transition">
                                        {{ $blog->title }}
                                    </h3>

                                    @if($blog->excerpt)
                                        <p class="text-gray-400 text-sm line-clamp-2 mb-3">{{ $blog->excerpt }}</p>
                                    @endif

                                    @if($blog->published_at)
                                        <div class="flex items-center gap-2 text-gray-500 text-xs">
                                            <i class="far fa-calendar"></i>
                                            <time datetime="{{ $blog->published_at->format('Y-m-d') }}">
                                                {{ $blog->published_at->format('d.m.Y') }}
                                            </time>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </article>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Pagination --}}
        @if($favorites->hasPages())
            <div class="mt-8">
                @include('themes.muzibu.partials.pagination', ['paginator' => $favorites])
            </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="text-center py-16 px-4">
            <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-800/50">
                <i class="fas fa-heart text-gray-600 text-4xl"></i>
            </div>

            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
                @if($type === 'songs')
                    Henüz favori şarkın yok
                @elseif($type === 'albums')
                    Henüz favori albümün yok
                @elseif($type === 'playlists')
                    Henüz favori playlistin yok
                @elseif($type === 'genres')
                    Henüz favori türün yok
                @elseif($type === 'sectors')
                    Henüz favori sektörün yok
                @elseif($type === 'radios')
                    Henüz favori radyon yok
                @elseif($type === 'blogs')
                    Henüz favori blogun yok
                @else
                    Henüz favori eklemedin
                @endif
            </h3>

            <p class="text-gray-400 mb-8 max-w-md mx-auto">
                Beğendiğin içerikleri favorilere ekleyerek kolayca ulaşabilirsin
            </p>

            <a href="{{ route('muzibu.home') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-muzibu-coral hover:bg-muzibu-coral/90 text-white font-semibold rounded-full transition-all transform hover:scale-105">
                <i class="fas fa-home"></i>
                <span>Ana Sayfaya Dön</span>
            </a>
        </div>
    @endif
</div>

{{-- Custom Styles for Scrollbar Hide --}}
<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection
