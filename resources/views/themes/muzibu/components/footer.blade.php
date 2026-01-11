@php
    // Tenant-aware sayılar (Model kullan - otomatik tenant context)
    // Muzibu modülü - gerçek veriler (FALLBACK YOK!)
    $songs = \Modules\Muzibu\App\Models\Song::count();
    $albums = \Modules\Muzibu\App\Models\Album::count();
    $playlists = \Modules\Muzibu\App\Models\Playlist::count();
    $radios = \Modules\Muzibu\App\Models\Radio::where('is_active', 1)->count();

    // x3 ile çarp (radyo hariç, gerçek sayı)
    $songs = $songs * 3;
    $albums = $albums * 3;
    $playlists = $playlists * 3;

    // Akıllı formatlama (1000'den büyükse B+ formatı - Türkçe: Bin)
    $songCount = $songs >= 1000 ? number_format($songs / 1000, 0) . 'B+' : $songs;
    $albumCount = $albums >= 1000 ? number_format($albums / 1000, 0) . 'B+' : $albums;
    $playlistCount = $playlists >= 1000 ? number_format($playlists / 1000, 0) . 'B+' : $playlists;
    $radioCount = $radios >= 100 ? $radios . '+' : $radios;

    // SettingManagement modülünden site bilgileri (Group 10: İletişim, Group 6: Site)
    // FALLBACK YOK! Ayarlar doldurulmazsa gösterilmez.
    $siteName = setting('site_title');
    $sitePhone = setting('contact_phone_1');
    $siteEmail = setting('contact_email_1');
    $whatsappNumber = setting('contact_whatsapp_1');
    $facebookUrl = setting('social_facebook');
    $instagramUrl = setting('social_instagram');
    $twitterUrl = setting('social_twitter');
    $youtubeUrl = setting('social_youtube');
@endphp

<footer class="bg-muzibu-dark text-white border-t border-white/10 pb-20">
    <div class="max-w-7xl mx-auto px-8 py-12">

        {{-- Logo Ortada Büyük - Animated Gradient --}}
        <div class="text-center mb-12">
            <h2 class="text-6xl md:text-8xl font-black footer-logo-gradient mb-4">
                muzibu
            </h2>
            <p class="text-xl md:text-2xl text-gray-400 font-semibold">İşletmenize Yasal ve Telifsiz Müzik</p>
        </div>

        {{-- İstatistikler (Tutarlı Gradient Renkleri) --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-10 max-w-6xl mx-auto">
            {{-- 1. Şarkı --}}
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-muzibu-coral to-rose-500 mb-2">{{ $songCount }}</div>
                <div class="text-gray-400">{{ trans('muzibu::front.general.song') }}</div>
            </div>
            {{-- 2. Albüm --}}
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-cyan-400 to-blue-500 mb-2">{{ $albumCount }}</div>
                <div class="text-gray-400">{{ trans('muzibu::front.general.album') }}</div>
            </div>
            {{-- 3. Çalma Listesi --}}
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-pink-500 mb-2">{{ $playlistCount }}</div>
                <div class="text-gray-400">{{ trans('muzibu::front.general.playlist') }}</div>
            </div>
            {{-- 4. Radyo --}}
            <div class="text-center">
                <div class="text-3xl md:text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-yellow-400 to-orange-500 mb-2">{{ $radioCount }}</div>
                <div class="text-gray-400">{{ trans('muzibu::front.general.radios') }}</div>
            </div>
        </div>

        {{-- Linkler 4 Sütun --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 max-w-6xl mx-auto">

            {{-- 1. Keşfet --}}
            <div class="text-center">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-center gap-2">
                    <i class="fas fa-compass text-muzibu-coral"></i>
                    {{ trans('muzibu::front.general.discover') }}
                </h3>
                <ul class="space-y-2">
                    <li><a href="/playlists" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.playlists') }}</a></li>
                    <li><a href="/albums" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.albums') }}</a></li>
                    <li><a href="/genres" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.genres') }}</a></li>
                    <li><a href="/sectors" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.sectors') }}</a></li>
                    <li><a href="/radios" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.radios') }}</a></li>
                </ul>
            </div>

            {{-- 2. Hesap --}}
            <div class="text-center">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-center gap-2">
                    <i class="fas fa-user text-muzibu-coral"></i>
                    {{ trans('muzibu::front.general.account') }}
                </h3>
                <ul class="space-y-2">
                    @auth
                        @php
                            $footerUser = auth()->user();
                            $footerIsPremium = $footerUser->isPremium();
                            $footerExpiresAt = $footerUser->subscription_expires_at;
                            $footerDaysRemaining = $footerExpiresAt ? now()->diffInDays($footerExpiresAt, false) : null;
                            $footerShowExtend = $footerIsPremium && $footerDaysRemaining !== null && $footerDaysRemaining <= 7 && $footerDaysRemaining >= 0;
                        @endphp
                        <li><a href="/dashboard" class="text-gray-400 hover:text-muzibu-coral transition-colors">Dashboard</a></li>
                        <li><a href="/my-subscriptions" class="text-gray-400 hover:text-muzibu-coral transition-colors">Aboneliklerim</a></li>
                        <li><a href="/my-certificate" class="text-gray-400 hover:text-muzibu-coral transition-colors">Premium Belgesi</a></li>
                        <li><a href="/corporate/dashboard" class="text-gray-400 hover:text-muzibu-coral transition-colors">Kurumsal</a></li>
                        @if($footerShowExtend)
                            <li><a href="/subscription/plans" class="text-muzibu-coral hover:text-muzibu-coral-light transition-colors font-semibold">Üyeliğini Uzat</a></li>
                        @elseif(!$footerIsPremium)
                            <li><a href="/subscription/plans" class="text-muzibu-coral hover:text-muzibu-coral-light transition-colors font-semibold">Premium Ol</a></li>
                        @endif
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-muzibu-coral transition-colors">
                                    {{ trans('muzibu::front.general.logout') }}
                                </button>
                            </form>
                        </li>
                    @else
                        <li><a href="/login" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.login') }}</a></li>
                        <li><a href="/register" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.footer.register') }}</a></li>
                        <li><a href="/subscription/plans" class="text-muzibu-coral hover:text-muzibu-coral-light transition-colors font-semibold">Planlar & Fiyatlandırma</a></li>
                    @endauth
                </ul>
            </div>

            {{-- 3. Kitaplığım (Sadece Auth) --}}
            @auth
            <div class="text-center">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-center gap-2">
                    <i class="fas fa-book-open text-muzibu-coral"></i>
                    Kitaplığım
                </h3>
                <ul class="space-y-2">
                    <li><a href="/muzibu/favorites" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.favorites') }}</a></li>
                    <li><a href="/muzibu/my-playlists" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.sidebar.my_playlists') }}</a></li>
                    <li><a href="/muzibu/listening-history" class="text-gray-400 hover:text-muzibu-coral transition-colors">Dinleme Geçmişi</a></li>
                </ul>
            </div>
            @else
            <div class="text-center">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-center gap-2">
                    <i class="fas fa-music text-muzibu-coral"></i>
                    Keşfet
                </h3>
                <ul class="space-y-2">
                    <li><a href="/playlists" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.playlists') }}</a></li>
                    <li><a href="/albums" class="text-gray-400 hover:text-muzibu-coral transition-colors">{{ trans('muzibu::front.general.albums') }}</a></li>
                </ul>
            </div>
            @endauth

            {{-- 4. Yasal --}}
            <div class="text-center">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center justify-center gap-2">
                    <i class="fas fa-balance-scale text-muzibu-coral"></i>
                    Yasal
                </h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/kvkk-cerez-politikasi" class="text-gray-400 hover:text-muzibu-coral transition-colors">KVKK & Çerez</a></li>
                    <li><a href="/iletisim-aydinlatma" class="text-gray-400 hover:text-muzibu-coral transition-colors">İletişim Aydınlatması</a></li>
                    <li><a href="/kullanim-kosullari" class="text-gray-400 hover:text-muzibu-coral transition-colors">Kullanım Koşulları</a></li>
                    <li><a href="/mesafeli-satis" class="text-gray-400 hover:text-muzibu-coral transition-colors">Mesafeli Satış</a></li>
                    <li><a href="/kvkk-basvuru-formu" class="text-gray-400 hover:text-muzibu-coral transition-colors">KVKK Başvuru</a></li>
                    <li><a href="/ticari-ileti-aydinlatma" class="text-gray-400 hover:text-muzibu-coral transition-colors">Ticari İleti</a></li>
                    <li><a href="/uyelik-kvkk" class="text-gray-400 hover:text-muzibu-coral transition-colors">Üyelik KVKK</a></li>
                    <li><a href="/whatsapp-kvkk" class="text-gray-400 hover:text-muzibu-coral transition-colors">WhatsApp KVKK</a></li>
                </ul>
            </div>

        </div>

        {{-- Sosyal Medya (SEO Settings'ten Dinamik) --}}
        @if($facebookUrl || $instagramUrl || $twitterUrl || $youtubeUrl || $whatsappNumber)
        <div class="flex justify-center gap-4 mb-10">
            @if($facebookUrl)
            <a href="{{ $facebookUrl }}" target="_blank" rel="noopener" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-facebook-f text-lg md:text-xl"></i>
            </a>
            @endif
            @if($instagramUrl)
            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-instagram text-lg md:text-xl"></i>
            </a>
            @endif
            @if($twitterUrl)
            <a href="{{ $twitterUrl }}" target="_blank" rel="noopener" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-twitter text-lg md:text-xl"></i>
            </a>
            @endif
            @if($youtubeUrl)
            <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-youtube text-lg md:text-xl"></i>
            </a>
            @endif
            @if($whatsappNumber)
            <a href="{{ whatsapp_link($whatsappNumber) }}" target="_blank" rel="noopener" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center hover:scale-110 transition-transform">
                <i class="fab fa-whatsapp text-lg md:text-xl"></i>
            </a>
            @endif
        </div>
        @endif

        {{-- İletişim Bilgileri (SEO Settings'ten Dinamik) --}}
        <div class="flex flex-col md:flex-row justify-center items-center gap-6 md:gap-8 mb-6 text-gray-400 text-sm md:text-base">
            @if($sitePhone)
            <div class="flex items-center gap-2">
                <i class="fas fa-phone text-muzibu-coral"></i>
                <a href="tel:{{ preg_replace('/[^0-9]/', '', $sitePhone) }}" class="hover:text-white transition-colors">{{ $sitePhone }}</a>
            </div>
            @endif
            @if($siteEmail)
            <div class="flex items-center gap-2">
                <i class="fas fa-envelope text-muzibu-coral"></i>
                <a href="mailto:{{ $siteEmail }}" class="hover:text-white transition-colors">{{ $siteEmail }}</a>
            </div>
            @endif
            @if($whatsappNumber)
            <div class="flex items-center gap-2">
                <i class="fab fa-whatsapp text-muzibu-coral"></i>
                <a href="{{ whatsapp_link($whatsappNumber, 'Merhaba, bilgi almak istiyorum') }}" target="_blank" rel="noopener" class="hover:text-white transition-colors">WhatsApp Destek</a>
            </div>
            @endif
        </div>

        {{-- Copyright --}}
        <div class="text-center text-gray-500 text-xs md:text-sm border-t border-white/10 pt-6">
            <p class="mb-3">© {{ date('Y') }} Muzibu - {{ trans('muzibu::front.footer.all_rights_reserved') }}.</p>

            {{-- Telif Hakkı Bildirimi --}}
            <p class="mb-4 max-w-3xl mx-auto leading-relaxed">
                Muzibu.com'da dinlendiğiniz müziklerin telif hakkı muzibu'ya aittir.
                Müziği özgürce dinleyebilirsiniz, ancak izinsiz olarak kopyalanamaz, kaydedilemez veya üçüncü kişilerle paylaşılamaz.
            </p>

            {{-- Signature Style Credit --}}
            <p>
                Yapay Zeka, Yazılım, Tasarım:
                <a href="https://www.turkbilisim.com.tr" target="_blank" rel="noopener" class="text-gray-500 hover:text-white transition-colors">
                    Türk Bilişim
                </a>
            </p>
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
