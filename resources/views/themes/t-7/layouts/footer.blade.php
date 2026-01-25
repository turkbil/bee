{{-- Varilsan Grup Footer --}}
@php
    $siteName = setting('site_name');
    $siteSlogan = setting('site_slogan');
    $sitePhone = setting('site_phone');
    $siteEmail = setting('site_email');
    $siteAddress = setting('site_address');

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];
@endphp

<footer id="iletisim" class="bg-slate-900 dark:bg-slate-950 pt-20">
    <div class="container mx-auto">
        {{-- Main Footer --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 lg:gap-12 pb-16 border-b border-slate-800">
            {{-- Logo & About --}}
            <div class="col-span-2">
                <a href="{{ url('/') }}" class="flex items-center gap-4 mb-6">
                    @if($hasLogo)
                        @if($logos['has_dark'])
                            <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-12 w-auto">
                        @elseif($logos['has_light'])
                            <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-12 w-auto">
                        @endif
                    @else
                        <div class="w-14 h-14 rounded-2xl gradient-shift flex items-center justify-center">
                            <span class="text-white font-heading font-bold text-2xl">V</span>
                        </div>
                        <div>
                            <span class="block font-heading font-bold text-xl text-white">{{ $siteName }}</span>
                            <span class="block text-xs text-slate-500 tracking-[0.2em]">GRUP</span>
                        </div>
                    @endif
                </a>
                <p class="text-slate-400 text-sm mb-6 leading-relaxed max-w-xs">
                    25+ yıllık tecrübesiyle endüstriyel ambalaj sektörünün güvenilir çözüm ortağı. Kalite tesadüf değildir.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-11 h-11 bg-slate-800 hover:bg-sky-600 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition-all">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="w-11 h-11 bg-slate-800 hover:bg-sky-600 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition-all">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-11 h-11 bg-slate-800 hover:bg-sky-600 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition-all">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="w-11 h-11 bg-slate-800 hover:bg-sky-600 rounded-xl flex items-center justify-center text-slate-400 hover:text-white transition-all">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="font-heading font-semibold text-white mb-5">Hızlı Erişim</h4>
                <ul class="space-y-3">
                    <li><a href="#anasayfa" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Ana Sayfa</a></li>
                    <li><a href="#hakkimizda" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Hakkımızda</a></li>
                    <li><a href="#sirketlerimiz" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Şirketlerimiz</a></li>
                    <li><a href="#blog" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Blog</a></li>
                </ul>
            </div>

            {{-- Companies --}}
            <div>
                <h4 class="font-heading font-semibold text-white mb-5">Şirketlerimiz</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Varilsan Polimer</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Varilsan Ambalaj</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Varilsan Plastik</a></li>
                </ul>
            </div>

            {{-- Products --}}
            <div>
                <h4 class="font-heading font-semibold text-white mb-5">Ürünler</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">IBC Tank</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Plastik Bidon</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Metal Varil</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">HDPE Granül</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-sky-400 text-sm transition-colors">Re-bottle</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="font-heading font-semibold text-white mb-5">İletişim</h4>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li class="flex items-start gap-3">
                        <i class="fa-light fa-location-dot text-sky-500 mt-1"></i>
                        <span>{{ $siteAddress }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-light fa-phone text-sky-500"></i>
                        <a href="tel:{{ $sitePhone }}" class="hover:text-sky-400 transition-colors">{{ $sitePhone }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-light fa-envelope text-sky-500"></i>
                        <a href="mailto:{{ $siteEmail }}" class="hover:text-sky-400 transition-colors">{{ $siteEmail }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-light fa-clock text-sky-500"></i>
                        <span>Pzt-Cum: 08:30-18:00</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom Footer --}}
        <div class="py-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır.
            </p>
            <div class="flex items-center gap-6 text-sm text-slate-500">
                <a href="#" class="hover:text-sky-400 transition-colors">Gizlilik Politikası</a>
                <a href="#" class="hover:text-sky-400 transition-colors">Kullanım Koşulları</a>
                <a href="#" class="hover:text-sky-400 transition-colors">KVKK</a>
            </div>
        </div>
    </div>
</footer>
