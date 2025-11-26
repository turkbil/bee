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
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach($albums as $album)
                <div class="group cursor-pointer" @click="playAlbum({{ $album->album_id }})">
                    <div class="relative bg-spotify-gray rounded-lg p-4 hover:bg-spotify-gray/80 transition-all mb-4">
                        <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=200&h=200&fit=crop"
                             class="w-full aspect-square object-cover rounded-md mb-4 shadow-lg">
                        <div class="absolute bottom-6 right-6 w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all shadow-2xl">
                            <i class="fas fa-play text-black ml-0.5"></i>
                        </div>
                        <h3 class="text-white font-bold text-base mb-2 truncate">
                            {{ $album->title['tr'] ?? $album->title['en'] ?? 'Album' }}
                        </h3>
                        <p class="text-sm text-gray-400 truncate">{{ $album->artist_title['tr'] ?? $album->artist_title['en'] ?? '' }}</p>
                    </div>
                </div>
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
