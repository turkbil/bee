@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.listening_history.title') . ' - Muzibu')

@section('content')
<div x-data="listeningHistory()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-history mr-3 text-green-400"></i>{{ __('muzibu::front.listening_history.title') }}
                </h1>
                <p class="text-gray-400">{{ __('muzibu::front.listening_history.description') }}</p>
            </div>
            <a href="/dashboard" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg transition" data-spa>
                <i class="fas fa-arrow-left mr-2"></i>{{ __('muzibu::front.listening_history.back_to_dashboard') }}
            </a>
        </div>

        {{-- History List --}}
        <div class="bg-white/5 border border-white/10 rounded-xl overflow-hidden">
            @if($history->count() > 0)
                <div class="divide-y divide-white/5">
                    @foreach($history as $play)
                        @php
                            $song = $play->song;
                            if (!$song) continue;
                            $cover = $song->coverMedia ?? ($song->album ? $song->album->coverMedia : null) ?? null;
                            $coverUrl = $cover ? thumb($cover, 80, 80) : null;
                        @endphp
                        <div class="flex items-center gap-4 p-4 hover:bg-white/5 transition cursor-pointer group"
                             @click="playSong({{ $song->song_id }})">
                            <div class="relative w-14 h-14 rounded-lg overflow-hidden flex-shrink-0">
                                @if($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="{{ $song->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-muzibu-coral to-pink-600 flex items-center justify-center">
                                        <i class="fas fa-music text-white/50 text-xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                    <i class="fas fa-play text-white"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white font-medium truncate">{{ $song->title }}</p>
                                <p class="text-gray-400 text-sm truncate">
                                    {{ $song->album->artist->name ?? __('muzibu::front.dashboard.unknown_artist') }}
                                    @if($song->album)
                                        <span class="text-gray-600">•</span>
                                        {{ $song->album->title }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right hidden sm:block">
                                <p class="text-gray-500 text-sm">{{ $play->created_at->diffForHumans() }}</p>
                                <p class="text-gray-600 text-xs">{{ $play->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="text-gray-500 text-xs sm:hidden">
                                {{ $play->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($history->hasPages())
                    <div class="p-4 border-t border-white/10">
                        {{ $history->links('themes.muzibu.partials.pagination') }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center text-gray-400">
                    <i class="fas fa-history text-5xl mb-4 opacity-50"></i>
                    <p class="text-lg mb-2">{{ __('muzibu::front.listening_history.no_history_yet') }}</p>
                    <p class="text-sm mb-4">{{ __('muzibu::front.listening_history.no_history_description') }}</p>
                    <a href="/" class="inline-block px-6 py-2 bg-muzibu-coral hover:bg-red-600 text-white rounded-lg transition" data-spa>
                        <i class="fas fa-compass mr-2"></i>{{ __('muzibu::front.listening_history.discover') }}
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
function listeningHistory() {
    return {
        playSong(songId) {
            if (window.MuzibuPlayer) {
                window.MuzibuPlayer.playById(songId);
            }
        }
    }
}
</script>
@endpush
@endsection
