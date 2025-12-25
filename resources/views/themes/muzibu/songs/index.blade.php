@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Şarkılar</h1>
        <p class="text-gray-400">En yeni ve popüler şarkılar</p>
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
                {{ $songs->links() }}
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
