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

{{-- Çalma Listeleri (Spotify Style) --}}
@if($featuredPlaylists && $featuredPlaylists->count() > 0)
<x-muzibu.horizontal-scroll-section title="Çalma Listeleri" icon="fa-list-music" viewAllUrl="/playlists">
    @foreach($featuredPlaylists as $playlist)
        <x-muzibu.playlist-card :playlist="$playlist" :preview="true" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Yeni Albümler (Horizontal Scroll - Spotify Style) --}}
@if($newReleases && $newReleases->count() > 0)
<x-muzibu.horizontal-scroll-section title="Albümler" icon="fa-microphone-lines" viewAllUrl="/albums">
    @foreach($newReleases as $album)
        <x-muzibu.album-card :album="$album" :preview="true" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Radyolar --}}
@if(isset($radios) && $radios->count() > 0)
<x-muzibu.horizontal-scroll-section title="Radyolar" icon="fa-radio" viewAllUrl="/radios">
    @foreach($radios as $radio)
        <x-muzibu.radio-card :radio="$radio" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Sektörler --}}
@if(isset($sectors) && $sectors->count() > 0)
<x-muzibu.horizontal-scroll-section title="Sektörler" icon="fa-building" viewAllUrl="/sectors">
    @foreach($sectors as $sector)
        <x-muzibu.sector-card :sector="$sector" :preview="true" :compact="true" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif


</div>

{{-- Footer (Sadece ana sayfada gösterilir) --}}
@include('themes.muzibu.components.footer')

@endsection
