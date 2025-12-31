{{-- Kurumsal hesabi var ama playlist atanmamis - Premium contact CTA --}}
<div class="text-center py-12 sm:py-20">
    {{-- Header with Company Name --}}
    <div class="mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-br from-amber-500/20 to-orange-600/20 border-2 border-amber-500/40 mb-4">
            <i class="fas fa-crown text-3xl sm:text-4xl text-amber-400"></i>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-500/20 text-amber-400 rounded-full text-sm font-medium mb-3">
            <i class="fas fa-star"></i>
            Premium Ozellik
        </div>
    </div>

    <h3 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4">
        {{ __('muzibu::front.corporate.custom_playlists_title') }}
    </h3>

    <p class="text-gray-400 text-base sm:text-lg mb-3 max-w-2xl mx-auto">
        {{ __('muzibu::front.corporate.custom_playlists_description') }}
    </p>

    {{-- Company Badge --}}
    <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500/10 border border-purple-500/30 rounded-full text-purple-300 mb-8">
        <i class="fas fa-building text-sm"></i>
        <span class="font-medium">{{ $corporate->company_name }}</span>
    </div>

    {{-- Premium Features Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-3xl mx-auto mb-10">
        <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-left hover:bg-white/10 transition-all">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mb-3">
                <i class="fas fa-music text-white"></i>
            </div>
            <h4 class="text-white font-semibold mb-1">{{ __('muzibu::front.corporate.feature_curated') }}</h4>
            <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.feature_curated_desc') }}</p>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-left hover:bg-white/10 transition-all">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-3">
                <i class="fas fa-sliders-h text-white"></i>
            </div>
            <h4 class="text-white font-semibold mb-1">{{ __('muzibu::front.corporate.feature_tailored') }}</h4>
            <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.feature_tailored_desc') }}</p>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-xl p-5 text-left hover:bg-white/10 transition-all">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center mb-3">
                <i class="fas fa-sync text-white"></i>
            </div>
            <h4 class="text-white font-semibold mb-1">{{ __('muzibu::front.corporate.feature_updated') }}</h4>
            <p class="text-gray-400 text-sm">{{ __('muzibu::front.corporate.feature_updated_desc') }}</p>
        </div>
    </div>

    {{-- CTA Buttons --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="mailto:info@muzibu.com.tr?subject=Kurumsal%20Playlist%20Talebi%20-%20{{ urlencode($corporate->company_name) }}"
           class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-full transition-all transform hover:scale-105 shadow-lg hover:shadow-amber-500/30">
            <i class="fas fa-envelope text-xl"></i>
            <span>{{ __('muzibu::front.corporate.contact_for_playlists') }}</span>
        </a>

        <a href="https://wa.me/905xxxxxxxxx?text={{ urlencode('Merhaba, ' . $corporate->company_name . ' için kurumsal playlist hizmeti almak istiyorum.') }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-6 py-3 bg-green-500/20 hover:bg-green-500/30 text-green-400 font-semibold rounded-full transition-all border border-green-500/30">
            <i class="fab fa-whatsapp text-lg"></i>
            <span>WhatsApp</span>
        </a>
    </div>

    {{-- Info Note --}}
    <p class="text-gray-500 text-sm mt-8 max-w-lg mx-auto">
        <i class="fas fa-info-circle mr-1"></i>
        {{ __('muzibu::front.corporate.custom_playlists_note') }}
    </p>
</div>
