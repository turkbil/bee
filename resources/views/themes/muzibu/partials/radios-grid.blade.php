@if($radios && $radios->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6 animate-slide-up" style="animation-delay: 100ms">
        @foreach($radios as $radio)
            <div class="group bg-muzibu-gray hover:bg-gray-700 rounded-xl sm:rounded-2xl p-4 sm:p-6 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:shadow-muzibu-coral/20">
                {{-- Radio Logo/Icon --}}
                <div class="relative mb-4 sm:mb-6">
                    @if($radio->getFirstMedia('logo'))
                        <div class="w-full aspect-square bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl sm:rounded-2xl flex items-center justify-center p-4 sm:p-6 shadow-lg overflow-hidden">
                            <img src="{{ thumb($radio->getFirstMedia('logo'), 300, 300, ['scale' => 1]) }}"
                                 alt="{{ $radio->getTranslation('title', app()->getLocale()) }}"
                                 class="w-full h-full object-contain"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="w-full aspect-square bg-gradient-to-br from-red-500 via-pink-500 to-purple-600 rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-radio text-white text-5xl sm:text-6xl md:text-7xl opacity-90"></i>
                        </div>
                    @endif

                    {{-- Large Play Button Overlay --}}
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 rounded-xl sm:rounded-2xl flex items-center justify-center">
                        <button
                            @click="$dispatch('play-radio', {
                                radioId: {{ $radio->radio_id }},
                                title: '{{ addslashes($radio->getTranslation('title', app()->getLocale())) }}',
                                streamUrl: '{{ $radio->stream_url }}'
                            })"
                            class="opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-110 transition-all duration-300 bg-muzibu-coral hover:bg-opacity-90 text-white rounded-full w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center shadow-2xl hover:scale-125 hover:shadow-muzibu-coral/50"
                        >
                            <i class="fas fa-play text-2xl sm:text-3xl ml-1"></i>
                        </button>
                    </div>

                    {{-- Live Badge --}}
                    <div class="absolute top-2 sm:top-3 left-2 sm:left-3">
                        <div class="bg-red-600 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold flex items-center gap-1 sm:gap-2 shadow-lg animate-pulse">
                            <span class="w-2 h-2 bg-white rounded-full"></span>
                            CANLI
                        </div>
                    </div>
                </div>

                {{-- Radio Info --}}
                <div class="text-center">
                    <h3 class="font-bold text-white mb-1 sm:mb-2 truncate text-base sm:text-lg">
                        {{ $radio->getTranslation('title', app()->getLocale()) }}
                    </h3>

                    @if($radio->description)
                        <p class="text-xs sm:text-sm text-gray-400 line-clamp-2 mb-2 sm:mb-3">
                            {{ $radio->getTranslation('description', app()->getLocale()) }}
                        </p>
                    @endif

                    {{-- Genre/Category --}}
                    @if($radio->genre)
                        <span class="inline-flex items-center px-2 sm:px-3 py-1 bg-white/5 rounded-full text-xs text-gray-300">
                            <i class="fas fa-music mr-1 sm:mr-2 text-muzibu-coral"></i>
                            {{ $radio->genre }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($radios->hasPages())
        <div class="mt-8 sm:mt-12">
            {{ $radios->links() }}
        </div>
    @endif
@else
    {{-- Empty State --}}
    <div class="text-center py-12 sm:py-20">
        <div class="mb-6 sm:mb-8">
            <i class="fas fa-radio text-gray-600 text-5xl sm:text-6xl md:text-7xl"></i>
        </div>
        <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2 sm:mb-4">Henüz canlı radyo yok</h3>
        <p class="text-sm sm:text-base text-gray-400 mb-6 sm:mb-8">Yakında canlı radyo yayınları eklenecek</p>
        <a href="/" wire:navigate
           
           class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-muzibu-coral text-white font-semibold rounded-full hover:bg-opacity-90 transition-all text-sm sm:text-base">
            <i class="fas fa-home mr-2"></i>
            Ana Sayfaya Dön
        </a>
    </div>
@endif
