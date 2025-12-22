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
        <h1 class="text-4xl font-bold text-white mb-2">Albümler</h1>
        <p class="text-gray-400">En yeni ve popüler albümler</p>
    </div>

    {{-- Albums Grid --}}
    @if($albums && $albums->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($albums as $album)
                <x-muzibu.album-card :album="$album" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($albums->hasPages())
            <div class="mt-8">
                {{ $albums->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-compact-disc text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz albüm yok</h3>
            <p class="text-gray-400">Yakında yeni albümler eklenecek</p>
        </div>
    @endif
</div>
@endsection
