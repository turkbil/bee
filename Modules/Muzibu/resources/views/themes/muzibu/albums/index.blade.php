@extends('themes.muzibu.layouts.app')

@section('content')
<section class="relative h-64 mb-8 bg-gradient-to-b from-blue-900 via-blue-800 to-transparent">
    <div class="container mx-auto px-8 h-full flex flex-col justify-end pb-12">
        <h1 class="text-5xl font-black mb-2 text-white drop-shadow-2xl">Albümler</h1>
        <p class="text-lg text-white/90">En yeni ve popüler albümler</p>
    </div>
</section>

<section class="px-8 pb-12">
    @if($albums->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($albums as $album)
                <x-muzibu.album-card :album="$album" :preview="true" />
            @endforeach
        </div>

        @if($albums->hasPages())
            <div class="mt-8 flex justify-center">{{ $albums->links() }}</div>
        @endif
    @else
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-spotify-gray rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-record-vinyl text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">Henüz albüm yok</h3>
        </div>
    @endif
</section>
@endsection
