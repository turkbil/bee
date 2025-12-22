@php
    // Tenant-aware sayılar (direkt tenant DB'den çek)
    $tenantDb = config('tenancy.database.prefix') . 'muzibu_' . substr(md5('1001'), 0, 6);

    try {
        // Tenant DB'den direkt sayıları çek
        $songs = \DB::connection('tenant')->table('muzibu_songs')->count();
        $albums = \DB::connection('tenant')->table('muzibu_albums')->count();
        $playlists = \DB::connection('tenant')->table('muzibu_playlists')->count();

        // x3 ile çarp
        $songs = $songs * 3;
        $albums = $albums * 3;
        $playlists = $playlists * 3;

        // Akıllı formatlama (1000'den büyükse K+ formatı)
        $songCount = $songs >= 1000 ? number_format($songs / 1000, 0) . 'K+' : number_format($songs);
        $albumCount = $albums >= 1000 ? number_format($albums / 1000, 1) . 'K+' : number_format($albums);
        $playlistCount = $playlists >= 1000 ? number_format($playlists / 1000, 0) . 'K+' : number_format($playlists);
    } catch (\Exception $e) {
        // Hata durumunda default değerler
        $songCount = '150';
        $albumCount = '18';
        $playlistCount = '27';
    }
@endphp

<footer class="bg-muzibu-dark text-white border-t border-white/10 pb-20">
    <div class="max-w-7xl mx-auto px-8 py-12">

        {{-- Logo Ortada Büyük --}}
        <div class="text-center mb-12">
            <h2 class="text-6xl md:text-8xl font-black bg-clip-text text-transparent bg-gradient-to-r from-muzibu-coral via-muzibu-coral-light to-muzibu-coral-dark mb-4">
                muzibu
            </h2>
            <p class="text-xl md:text-2xl text-gray-400 font-semibold">İşletmenize Yasal ve Telifsiz Müzik</p>
        </div>

        {{-- İstatistikler --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10">
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-muzibu-coral to-muzibu-coral-light mb-2">{{ $songCount }}</div>
                <div class="text-gray-400">{{ trans('muzibu::front.general.song') }}</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-400 mb-2">{{ $albumCount }}</div>
                <div class="text-gray-400">{{ trans('muzibu::front.general.album') }}</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-400 mb-2">{{ $playlistCount }}+</div>
                <div class="text-gray-400">{{ trans('muzibu::front.general.playlist') }}</div>
            </div>
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-muzibu-coral to-red-400 mb-2">24/7</div>
                <div class="text-gray-400">{{ trans('muzibu::front.footer.support') }}</div>
            </div>
        </div>

        {{-- Linkler 3 Sütun --}}
        <div class="flex justify-center mb-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-32">
            {{-- Hızlı Erişim --}}
            <div>
                <h3 class="text-lg font-bold text-white mb-4">{{ trans('muzibu::front.footer.quick_access') }}</h3>
                <ul class="space-y-2">
                    <li><a href="/playlists" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.playlists') }}</a></li>
                    <li><a href="/albums" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.albums') }}</a></li>
                    <li><a href="/playlists?type=radio" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.radios') }}</a></li>
                    <li><a href="/genres" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.genres') }}</a></li>
                </ul>
            </div>

            {{-- Hesap --}}
            <div>
                <h3 class="text-lg font-bold text-white mb-4">{{ trans('muzibu::front.general.account') }}</h3>
                <ul class="space-y-2">
                    <li><a href="/login" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.login') }}</a></li>
                    <li><a href="/register" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.footer.register') }}</a></li>
                </ul>
            </div>

            {{-- Kurumsal --}}
            <div>
                <h3 class="text-lg font-bold text-white mb-4">{{ trans('muzibu::front.footer.corporate') }}</h3>
                <ul class="space-y-2">
                    <li><a href="/iletisim" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.footer.contact') }}</a></li>
                    <li><a href="/kullanim-kosullari-ve-uyelik-sozlesmesi" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.user.terms_of_service') }}</a></li>
                </ul>
            </div>
        </div>
        </div>

        {{-- Sosyal Medya --}}
        <div class="flex justify-center gap-4 mb-10">
            <a href="#" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-facebook-f text-lg md:text-xl"></i>
            </a>
            <a href="#" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-instagram text-lg md:text-xl"></i>
            </a>
            <a href="#" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-twitter text-lg md:text-xl"></i>
            </a>
            <a href="#" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-youtube text-lg md:text-xl"></i>
            </a>
            <a href="https://wa.me/908501234567" target="_blank" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-whatsapp text-lg md:text-xl"></i>
            </a>
        </div>

        {{-- İletişim Bilgileri --}}
        <div class="flex flex-col md:flex-row justify-center items-center gap-6 md:gap-8 mb-6 text-gray-400 text-sm md:text-base">
            <div class="flex items-center gap-2">
                <i class="fas fa-phone text-muzibu-coral"></i>
                <a href="tel:08501234567" class="hover:text-white transition-colors">0850 123 45 67</a>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-purple-400"></i>
                <a href="mailto:destek@muzibu.com" class="hover:text-white transition-colors">destek@muzibu.com</a>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-comment-dots text-green-400"></i>
                <a href="#" class="hover:text-white transition-colors">{{ trans('muzibu::front.footer.live_support') }}</a>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="text-center text-gray-500 text-xs md:text-sm border-t border-white/10 pt-6">
            © {{ date('Y') }} Muzibu - {{ trans('muzibu::front.footer.all_rights_reserved') }}.
        </div>

    </div>
</footer>

{{-- Cache Clear & AI Clear JavaScript Functions - SIMPLIFIED --}}
<script>
(function() {
    'use strict';

    // Cache Clear Function
    window.clearSystemCache = function(button) {
        if (!button || button.disabled) return;

        const icon = button.querySelector('i');
        const originalIcon = icon.className;

        button.disabled = true;
        icon.className = 'fas fa-spinner fa-spin text-xs';

        fetch('/clear-cache', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error(data.message);

            // Success
            icon.className = 'fas fa-check text-xs';
            button.classList.remove('bg-red-500/20', 'text-red-400');
            button.classList.add('bg-green-500/20', 'text-green-400');

            setTimeout(() => location.reload(), 800);
        })
        .catch(error => {
            console.error('[Cache] Error:', error);
            icon.className = 'fas fa-times text-xs';

            setTimeout(() => {
                button.disabled = false;
                icon.className = originalIcon;
            }, 2000);
        });
    };

    // AI Conversation Clear Function
    window.clearAIConversation = function(button) {
        if (!button || button.disabled) return;

        const icon = button.querySelector('i');
        const originalIcon = icon.className;

        button.disabled = true;
        icon.className = 'fas fa-spinner fa-spin text-xs';

        try {
            // Clear AI conversation from localStorage
            localStorage.removeItem('ai_conversation_history');
            localStorage.removeItem('ai_conversation_id');

            // Success
            icon.className = 'fas fa-check text-xs';
            button.classList.remove('bg-purple-500/20', 'text-purple-400');
            button.classList.add('bg-green-500/20', 'text-green-400');

            setTimeout(() => location.reload(), 800);
        } catch (error) {
            console.error('[AI Chat] Error:', error);
            icon.className = 'fas fa-times text-xs';

            setTimeout(() => {
                button.disabled = false;
                icon.className = originalIcon;
            }, 2000);
        }
    };
})();
</script>
