@extends('themes.muzibu.layouts.app')

@section('title', __('muzibu::front.listening_history.title') . ' - Muzibu')

@section('content')
<div x-data="{
    playSong(songId) {
        if (window.MuzibuPlayer) {
            window.MuzibuPlayer.playById(songId);
        }
    }
}">
    <div class="px-4 py-6 sm:px-6 sm:py-8">

        {{-- Header --}}
        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-xl sm:text-2xl text-green-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-0.5">
                        {{ __('muzibu::front.listening_history.title') }}
                    </h1>
                    <p class="text-gray-400 text-sm sm:text-base">{{ __('muzibu::front.listening_history.description') }}</p>
                </div>
            </div>
            <a href="/dashboard" class="inline-flex items-center justify-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm rounded-lg transition" data-spa>
                <i class="fas fa-arrow-left mr-2"></i>{{ __('muzibu::front.listening_history.back_to_dashboard') }}
            </a>
        </div>

        {{-- History List - With Date/Time/IP --}}
        <div class="bg-slate-900/50 rounded-lg overflow-hidden">
            @if($history->count() > 0)
                @foreach($history as $index => $play)
                    <x-muzibu.song-history-row :play="$play" :index="$index" />
                @endforeach

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
                    <a href="/" class="inline-block px-6 py-2 bg-gradient-to-r from-muzibu-coral to-[#ff9966] hover:opacity-90 text-white rounded-lg transition" data-spa>
                        <i class="fas fa-compass mr-2"></i>{{ __('muzibu::front.listening_history.discover') }}
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
