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
        <x-muzibu.playlist-card :playlist="$playlist" :preview="true" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>

{{-- scrollbar-hide CSS moved to tenant-1001.css --}}
@endif

{{-- New Releases (Horizontal Scroll - Spotify Style) --}}
@if($newReleases && $newReleases->count() > 0)
<x-muzibu.horizontal-scroll-section title="Yeni Çıkanlar">
    @foreach($newReleases as $album)
        <x-muzibu.album-card :album="$album" :preview="true" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- SONGS GRID kaldırıldı - Şarkılar artık sağ sidebar'da gösteriliyor --}}

{{-- Genres (Horizontal Scroll - Spotify Style) --}}
@if($genres && $genres->count() > 0)
<x-muzibu.horizontal-scroll-section title="Kategoriler">
    @foreach($genres as $genre)
        <x-muzibu.genre-card :genre="$genre" :preview="true" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif


</div>

{{-- Footer (Sadece ana sayfada gösterilir) --}}
@include('themes.muzibu.components.footer')

@endsection
