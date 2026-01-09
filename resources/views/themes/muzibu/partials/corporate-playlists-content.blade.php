{{-- Corporate Playlists SPA Content --}}
<div class="px-4 py-6 sm:px-6 sm:py-8">
    @if($hasNoCorporate ?? false)
        {{-- Kullanici kurumsal degil --}}
        @include('themes.muzibu.corporate.partials.no-corporate-state')
    @elseif(!isset($playlists) || $playlists->isEmpty())
        {{-- Kurumsal hesap var ama playlist atanmamis --}}
        @include('themes.muzibu.corporate.partials.no-playlists-state', ['corporate' => $corporate])
    @else
        {{-- Header - Same as /playlists page --}}
        <div class="mb-4 sm:mb-6 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 bg-white/10 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-briefcase text-xl sm:text-2xl text-white fa-beat-fade" style="--fa-animation-duration: 2s; --fa-beat-fade-opacity: 0.4; --fa-beat-fade-scale: 1.1;"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white mb-0.5">{{ __('muzibu::front.corporate.playlists_title') }}</h1>
                <p class="text-gray-400 text-sm sm:text-base">{{ $corporate->company_name }} - {{ __('muzibu::front.corporate.exclusive_playlists') }}</p>
            </div>
        </div>

        {{-- Playlists Grid - Same as /playlists page --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4">
            @foreach($playlists as $playlist)
                <x-muzibu.playlist-card :playlist="$playlist" :preview="true" />
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($playlists->hasPages())
            <div class="mt-8">
                {{ $playlists->links('themes.muzibu.partials.pagination') }}
            </div>
        @endif
    @endif
</div>
