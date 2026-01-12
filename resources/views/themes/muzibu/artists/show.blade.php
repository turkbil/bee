@extends('themes.muzibu.layouts.app')

@section('content')
{{-- Hero Section - Full Width Background Image (Spotify Mobile Style) --}}
<div class="relative overflow-hidden">
    {{-- Full Width Background Image --}}
    @if($artist->getPhotoUrl())
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] md:aspect-[21/9]">
            <img src="{{ $artist->getPhotoUrl(1200, 800) }}"
                 alt="{{ $artist->getTranslation('title', app()->getLocale()) }}"
                 class="w-full h-full object-cover">
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>

            {{-- Action Buttons - Top Right --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$artist" size="lg" />
                {{-- Play Button --}}
                <button
                    @click="$dispatch('play-artist', { artistId: {{ $artist->artist_id }} })"
                    class="w-14 h-14 bg-muzibu-coral hover:scale-105 active:scale-95 rounded-full flex items-center justify-center shadow-xl transition-all">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content - Bottom Left --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Sanatçı</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-black text-white mb-1 leading-tight drop-shadow-lg">
                    {{ $artist->getTranslation('title', app()->getLocale()) }}
                </h1>
                <p class="text-sm text-white/70">{{ $albums->count() }} albüm • {{ $songs->count() }} şarkı</p>
            </div>
        </div>
    @else
        {{-- Fallback if no photo --}}
        <div class="relative w-full aspect-[4/3] sm:aspect-[16/9] bg-gradient-to-br from-purple-900 to-slate-900">
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-8xl">🎤</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>

            {{-- Action Buttons --}}
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <x-common.favorite-button :model="$artist" size="lg" />
                <button
                    @click="$dispatch('play-artist', { artistId: {{ $artist->artist_id }} })"
                    class="w-14 h-14 bg-muzibu-coral rounded-full flex items-center justify-center shadow-xl">
                    <i class="fas fa-play text-white text-xl ml-1"></i>
                </button>
            </div>

            {{-- Content --}}
            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6">
                <p class="text-xs font-bold text-muzibu-coral uppercase tracking-widest mb-1">Sanatçı</p>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-white mb-1">
                    {{ $artist->getTranslation('title', app()->getLocale()) }}
                </h1>
                <p class="text-sm text-white/70">{{ $albums->count() }} albüm • {{ $songs->count() }} şarkı</p>
            </div>
        </div>
    @endif
</div>

{{-- Content Section --}}
<div class="px-4 sm:px-6 pt-6">
    {{-- Bio (if exists) --}}
    @if($artist->getTranslation('bio', app()->getLocale()))
        <div class="mb-8 p-4 bg-white/5 rounded-lg">
            <h2 class="text-xl font-bold text-white mb-3">Hakkında</h2>
            <div class="text-gray-300 text-sm leading-relaxed">
                {{ clean_html($artist->getTranslation('bio', app()->getLocale())) }}
            </div>
        </div>
    @endif

    {{-- Albums Section --}}
    @if($albums && $albums->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Albümler</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
                @foreach($albums as $album)
                    <x-muzibu.album-card :album="$album" :preview="true" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Songs Section --}}
    @if($songs && $songs->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 sm:mb-6">Popüler Şarkılar</h2>
            {{-- Table Header - Desktop Only --}}
            <div class="hidden md:grid grid-cols-[40px_50px_6fr_4fr_100px_60px] gap-4 px-4 py-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-white/5">
                <div class="text-center">#</div>
                <div></div>
                <div>Başlık</div>
                <div>Albüm</div>
                <div class="text-right">Süre</div>
                <div></div>
            </div>

            <div class="space-y-0">
                @foreach($songs->take(20) as $index => $song)
                    <x-muzibu.song-detail-row :song="$song" :index="$index" :show-album="true" />
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if((!$albums || $albums->count() === 0) && (!$songs || $songs->count() === 0))
        <div class="text-center py-16 sm:py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-5xl sm:text-6xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">Henüz içerik yok</h3>
            <p class="text-sm sm:text-base text-gray-400">Bu sanatçıya ait albüm veya şarkı bulunamadı</p>
        </div>
    @endif
</div>
@endsection
