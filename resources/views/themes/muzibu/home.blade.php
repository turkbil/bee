@extends('themes.muzibu.layouts.app')

@section('title', 'Muzibu - Ana Sayfa')

@section('content')
<div class="px-4 py-6 sm:px-6 sm:py-8">

{{-- Quick Access Cards (Spotify Style - 2 rows) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<div class="mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
        @foreach($featuredPlaylists->take(8) as $playlist)
            <x-muzibu.playlist-quick-card :playlist="$playlist" :index="$loop->index" />
        @endforeach
    </div>
</div>
@endif

{{-- Featured Playlists (Spotify Style) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<x-muzibu.horizontal-scroll-section title="Öne Çıkan Listeler" icon="fa-list-music" view-all-url="/playlists">
    @foreach($featuredPlaylists as $playlist)
        <x-muzibu.playlist-card :playlist="$playlist" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- New Releases (Horizontal Scroll - Spotify Style) --}}
@if($newReleases && $newReleases->count() > 0)
<x-muzibu.horizontal-scroll-section title="Yeni Çıkanlar" icon="fa-record-vinyl" view-all-url="/albums">
    @foreach($newReleases as $album)
        <x-muzibu.album-card :album="$album" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- SONGS GRID - Popüler + Yeni Şarkılar --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
    {{-- POPULAR SONGS --}}
    @if($popularSongs && $popularSongs->count() > 0)
    <div>
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-fire text-muzibu-coral"></i>
                Popüler Şarkılar
            </h2>
            <a href="/songs" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/70 hover:text-white transition-all" title="Tümünü Gör">
                <i class="fas fa-chevron-right text-sm"></i>
            </a>
        </div>

        <div class="grid grid-cols-1">
            @foreach($popularSongs->take(5) as $index => $song)
                <x-muzibu.song-card :song="$song" :list="true" />
            @endforeach
        </div>
    </div>
    @endif

    {{-- NEW SONGS --}}
    @if($popularSongs && $popularSongs->count() > 5)
    <div>
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-star text-muzibu-coral"></i>
                Yeni Şarkılar
            </h2>
        </div>

        <div class="grid grid-cols-1">
            @foreach($popularSongs->slice(5)->take(5) as $index => $song)
                <x-muzibu.song-card :song="$song" :list="true" />
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Genres (Horizontal Scroll - Spotify Style) --}}
@if($genres && $genres->count() > 0)
<x-muzibu.horizontal-scroll-section title="Kategoriler" icon="fa-folder" view-all-url="/genres">
    @foreach($genres as $genre)
        <x-muzibu.genre-card :genre="$genre" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Öne Çıkan Radyolar (Horizontal Scroll - Spotify Style) --}}
@if($radios && $radios->count() > 0)
<x-muzibu.horizontal-scroll-section title="Öne Çıkan Radyolar" icon="fa-broadcast-tower" view-all-url="/radios">
    @foreach($radios as $radio)
        <x-muzibu.radio-card :radio="$radio" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Sektörler (Horizontal Scroll - Spotify Style) --}}
@if($sectors && $sectors->count() > 0)
<x-muzibu.horizontal-scroll-section title="Sektörler" icon="fa-building" view-all-url="/sectors">
    @foreach($sectors as $sector)
        <x-muzibu.sector-card :sector="$sector" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

</div>
@endsection
