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
@if(isset($genres) && $genres->count() > 0)
<x-muzibu.horizontal-scroll-section :grid-mode="true">
    @foreach($genres->take(15) as $index => $genre)
        <x-muzibu.genre-quick-card :genre="$genre" :index="$index" />
    @endforeach

    {{-- Tüm Türler Kartı (16. sıra - Diğer kartlarla aynı tasarım) --}}
    <a href="/genres" data-spa class="genre-card group flex items-center gap-3 bg-white/5 hover:bg-white/10 rounded transition-all cursor-pointer overflow-hidden h-16 relative">
        {{-- Icon (Sol taraf - 64x64 kare) --}}
        <div class="w-16 h-16 flex-shrink-0 bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center">
            <i class="fas fa-th text-2xl text-white/90"></i>
        </div>

        {{-- Title (Sağ taraf) --}}
        <div class="flex-1 min-w-0 pr-4">
            <h3 class="font-semibold text-white text-sm truncate">
                Tüm Türler
            </h3>
        </div>

        {{-- Arrow Icon - HOVER'DA GÖRÜNÜR --}}
        <div class="absolute right-2 top-1/2 -translate-y-1/2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
            <div class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white">
                <i class="fas fa-arrow-right text-xs"></i>
            </div>
        </div>
    </a>
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Çalma Listeleri (Spotify Style) --}}
@if(isset($featuredPlaylists) && $featuredPlaylists->count() > 0)
<x-muzibu.horizontal-scroll-section title="Çalma Listeleri" icon="fa-list-music" viewAllUrl="/playlists">
    @foreach($featuredPlaylists as $playlist)
        <x-muzibu.playlist-card :playlist="$playlist" :preview="true" :compact="true" :index="$loop->index" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Yeni Albümler (Horizontal Scroll - Spotify Style) --}}
@if(isset($newReleases) && $newReleases->count() > 0)
<x-muzibu.horizontal-scroll-section title="Albümler" icon="fa-microphone-lines" viewAllUrl="/albums">
    @foreach($newReleases as $album)
        <x-muzibu.album-card :album="$album" :preview="true" :compact="true" :index="$loop->index" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Radyolar --}}
@if(isset($radios) && $radios->count() > 0)
<x-muzibu.horizontal-scroll-section title="Radyolar" icon="fa-radio" viewAllUrl="/radios">
    @foreach($radios as $radio)
        <x-muzibu.radio-card :radio="$radio" :compact="true" :index="$loop->index" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif

{{-- Sektörler --}}
@if(isset($sectors) && $sectors->count() > 0)
<x-muzibu.horizontal-scroll-section title="Sektörler" icon="fa-building" viewAllUrl="/sectors">
    @foreach($sectors as $sector)
        <x-muzibu.sector-card :sector="$sector" :preview="true" :compact="true" :index="$loop->index" />
    @endforeach
</x-muzibu.horizontal-scroll-section>
@endif


</div>

{{-- Footer (Sadece ana sayfada gösterilir) --}}
@include('themes.muzibu.components.footer')

@endsection
