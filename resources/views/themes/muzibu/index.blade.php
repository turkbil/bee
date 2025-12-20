@extends('themes.muzibu.layouts.app')

@section('title', 'Muzibu - Ana Sayfa')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        if (window.Alpine && window.Alpine.store('sidebar')) {
            window.Alpine.store('sidebar').reset();
        }
    }, 100);
});
</script>

<div class="px-6 py-8">

{{-- Quick Access Cards (Spotify Style - 2 rows, Horizontal Scroll) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<x-muzibu.horizontal-scroll-section :grid-mode="true">
    @foreach($featuredPlaylists->take(8) as $index => $playlist)
        <x-muzibu.playlist-quick-card :playlist="$playlist" :index="$index" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Featured Playlists (Spotify Style) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<x-muzibu.horizontal-scroll-section title="Öne Çıkan Listeler">
    @foreach($featuredPlaylists as $playlist)
        <div class="flex-shrink-0 w-[190px]">
            <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
        </div>
    @endforeach
</x-muzibu.horizontal-scroll-section>

{{-- scrollbar-hide CSS moved to tenant-1001.css --}}
@endif

{{-- New Releases (Horizontal Scroll - Spotify Style) --}}
@if($newReleases && $newReleases->count() > 0)
<x-muzibu.horizontal-scroll-section title="Yeni Çıkanlar">
    @foreach($newReleases as $album)
        <div class="flex-shrink-0 w-[190px]">
            <x-muzibu.album-card :album="$album" :preview="true" />
        </div>
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- SONGS GRID - Popüler + Yeni Şarkılar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- POPULAR SONGS --}}
    @if($popularSongs && $popularSongs->count() > 0)
    <div>
        <h2 class="text-2xl font-bold text-white mb-2">Popüler Şarkılar</h2>

        <div class="space-y-1">
            @foreach($popularSongs->take(10) as $index => $song)
                <x-muzibu.song-row :song="$song" :index="$index" :show-album="true" />
            @endforeach
        </div>
    </div>
    @endif

    {{-- NEW SONGS --}}
    @if($popularSongs && $popularSongs->count() > 10)
    <div>
        <h2 class="text-2xl font-bold text-white mb-2">Yeni Şarkılar</h2>

        <div class="space-y-1">
            @foreach($popularSongs->slice(10)->take(10) as $index => $song)
                <x-muzibu.song-row :song="$song" :index="$index + 10" :show-album="true" />
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Genres (Horizontal Scroll - Spotify Style) --}}
@if($genres && $genres->count() > 0)
<x-muzibu.horizontal-scroll-section title="Kategoriler">
    @foreach($genres as $genre)
        <div class="flex-shrink-0 w-[190px]">
            <x-muzibu.genre-card :genre="$genre" :preview="true" />
        </div>
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif


</div>

{{-- Footer (Sadece ana sayfada gösterilir) --}}
@include('themes.muzibu.components.footer')

@endsection
