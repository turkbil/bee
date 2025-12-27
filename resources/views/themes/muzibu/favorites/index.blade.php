@extends('themes.muzibu.layouts.app')

@section('content')
{{-- Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header - Alternatif 2: Icon + Text (FA Beat-Fade Animation) --}}
    <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-heart text-xl sm:text-2xl text-white fa-beat-fade" style="--fa-animation-duration: 2s; --fa-beat-fade-opacity: 0.4; --fa-beat-fade-scale: 1.1;"></i>
        </div>
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-0.5">Favorilerim</h1>
            <p class="text-gray-400 text-sm sm:text-base">Beğendiğin içerikler</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-6 border-b border-white/10">
        <nav class="flex gap-1 overflow-x-auto scrollbar-hide -mb-px">
            @if(($counts['songs'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'songs']) }}" data-spa
                   class="px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center gap-2 border-b-2 {{ $type === 'songs' ? 'border-muzibu-coral text-white' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <i class="fas fa-music"></i>
                    <span>Şarkılar</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $counts['songs'] }}</span>
                </a>
            @endif

            @if(($counts['albums'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'albums']) }}" data-spa
                   class="px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center gap-2 border-b-2 {{ $type === 'albums' ? 'border-muzibu-coral text-white' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <i class="fas fa-record-vinyl"></i>
                    <span>Albümler</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $counts['albums'] }}</span>
                </a>
            @endif

            @if(($counts['playlists'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'playlists']) }}" data-spa
                   class="px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center gap-2 border-b-2 {{ $type === 'playlists' ? 'border-muzibu-coral text-white' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <i class="fas fa-list-music"></i>
                    <span>Playlistler</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $counts['playlists'] }}</span>
                </a>
            @endif

            @if(($counts['genres'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'genres']) }}" data-spa
                   class="px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center gap-2 border-b-2 {{ $type === 'genres' ? 'border-muzibu-coral text-white' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <i class="fas fa-guitar"></i>
                    <span>Türler</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $counts['genres'] }}</span>
                </a>
            @endif

            @if(($counts['sectors'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'sectors']) }}" data-spa
                   class="px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center gap-2 border-b-2 {{ $type === 'sectors' ? 'border-muzibu-coral text-white' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <i class="fas fa-briefcase"></i>
                    <span>Sektörler</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $counts['sectors'] }}</span>
                </a>
            @endif

            @if(($counts['radios'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'radios']) }}" data-spa
                   class="px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center gap-2 border-b-2 {{ $type === 'radios' ? 'border-muzibu-coral text-white' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <i class="fas fa-broadcast-tower"></i>
                    <span>Radyolar</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $counts['radios'] }}</span>
                </a>
            @endif

            @if(($counts['blogs'] ?? 0) > 0)
                <a href="{{ route('muzibu.favorites', ['type' => 'blogs']) }}" data-spa
                   class="px-4 py-3 text-sm font-medium whitespace-nowrap flex items-center gap-2 border-b-2 {{ $type === 'blogs' ? 'border-muzibu-coral text-white' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <i class="fas fa-blog"></i>
                    <span>Bloglar</span>
                    <span class="px-2 py-0.5 bg-white/10 rounded-full text-xs">{{ $counts['blogs'] }}</span>
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
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.album-card :album="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'playlists')
            {{-- Playlists - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.playlist-card :playlist="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'genres')
            {{-- Genres - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.genre-card :genre="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'sectors')
            {{-- Sectors - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($favorites as $favorite)
                    @if($favorite->favoritable)
                        <x-muzibu.sector-card :sector="$favorite->favoritable" :preview="true" />
                    @endif
                @endforeach
            </div>

        @elseif($type === 'radios')
            {{-- Radios - Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
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
