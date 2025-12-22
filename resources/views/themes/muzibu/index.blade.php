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

<div class="px-6 py-4">

{{-- Quick Access Cards - Türler (Spotify Style - 2 rows, Horizontal Scroll) --}}
@if($genres && $genres->count() > 0)
<x-muzibu.horizontal-scroll-section :grid-mode="true">
    @foreach($genres->take(8) as $index => $genre)
        <x-muzibu.genre-quick-card :genre="$genre" :index="$index" />
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


</div>

{{-- Footer (Sadece ana sayfada gösterilir) --}}
@include('themes.muzibu.components.footer')

@endsection
