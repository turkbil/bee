@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-4 py-6 sm:px-6 sm:py-8">
    {{-- Header --}}
    <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-waveform-lines text-xl sm:text-2xl text-white fa-beat-fade" style="--fa-animation-duration: 2s; --fa-beat-fade-opacity: 0.4; --fa-beat-fade-scale: 1.1;"></i>
        </div>
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-0.5">Şarkılar</h1>
            <p class="text-gray-400 text-sm sm:text-base">En yeni ve popüler şarkılar</p>
        </div>
    </div>

    {{-- Songs List - Simple Design --}}
    @if($songs && $songs->count() > 0)
        <div class="bg-slate-900/50 rounded-lg overflow-hidden">
            @foreach($songs as $index => $song)
                <x-muzibu.song-simple-row :song="$song" :index="$index" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($songs->hasPages())
            <div class="mt-8">
                {{ $songs->links('themes.muzibu.partials.pagination') }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-music text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz şarkı yok</h3>
            <p class="text-gray-400">Yakında yeni şarkılar eklenecek</p>
        </div>
    @endif
</div>
@endsection
