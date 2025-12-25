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

        {{-- History List - Simple Design --}}
        <div class="bg-slate-900/50 rounded-lg overflow-hidden">
            @if($history->count() > 0)
                @foreach($history as $index => $play)
                    @php
                        $song = $play->song;
                        if (!$song) continue;
                    @endphp
                    <x-muzibu.song-simple-row :song="$song" :index="$index" />
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
