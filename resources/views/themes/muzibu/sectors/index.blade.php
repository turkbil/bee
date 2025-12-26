@extends('themes.muzibu.layouts.app')

@section('content')
{{-- 🎯 Reset sidebar to homepage state --}}
<script>
if (window.Alpine && window.Alpine.store('sidebar')) {
    window.Alpine.store('sidebar').reset();
}
</script>

<div class="px-6 py-8">
    {{-- Header - Alternatif 2: Icon + Text (FA Beat-Fade Animation) --}}
    <div class="mb-8 flex items-center gap-5">
        <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-building text-3xl text-white fa-beat-fade" style="--fa-animation-duration: 2s; --fa-beat-fade-opacity: 0.4; --fa-beat-fade-scale: 1.1;"></i>
        </div>
        <div>
            <h1 class="text-5xl font-extrabold text-white mb-1">Sektörler</h1>
            <p class="text-gray-400 text-lg">Müzik sektörlerini keşfet</p>
        </div>
    </div>

    {{-- Sectors Grid --}}
    @if($sectors && $sectors->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($sectors as $sector)
                <x-muzibu.sector-card :sector="$sector" :preview="false" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($sectors->hasPages())
            <div class="mt-8">
                {{ $sectors->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="mb-6">
                <i class="fas fa-th-large text-gray-600 text-6xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Henüz sektör yok</h3>
            <p class="text-gray-400">Yakında yeni sektörler eklenecek</p>
        </div>
    @endif
</div>
@endsection
