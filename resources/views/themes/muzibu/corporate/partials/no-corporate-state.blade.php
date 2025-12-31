{{-- Kurumsal hesabi olmayan kullanicilar icin --}}
<div class="text-center py-16 sm:py-24">
    <div class="mb-6 sm:mb-8">
        <div class="inline-flex items-center justify-center w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-gradient-to-br from-purple-600/20 to-pink-600/20 border-2 border-purple-600/40">
            <i class="fas fa-building text-4xl sm:text-5xl text-purple-400"></i>
        </div>
    </div>

    <h3 class="text-2xl sm:text-3xl font-bold text-white mb-3">
        {{ __('muzibu::front.corporate.no_corporate_yet') }}
    </h3>

    <p class="text-gray-400 text-base sm:text-lg mb-8 max-w-lg mx-auto">
        {{ __('muzibu::front.corporate.join_for_playlists') }}
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('muzibu.corporate.join') }}"
           data-spa
           class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-full transition-all transform hover:scale-105 shadow-lg hover:shadow-purple-500/30">
            <i class="fas fa-key text-xl"></i>
            <span>{{ __('muzibu::front.corporate.join_with_code') }}</span>
        </a>

        <a href="{{ route('muzibu.corporate.index') }}"
           data-spa
           class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full transition-all">
            <i class="fas fa-info-circle"></i>
            <span>{{ __('muzibu::front.corporate.learn_about_corporate') }}</span>
        </a>
    </div>
</div>
