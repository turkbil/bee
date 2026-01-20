{{-- t-4 Unimad Madencilik Theme - Footer (v5 Dynamic Logo) --}}
@php
    use Modules\Service\App\Models\ServiceCategory;

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];

    // WhatsApp
    $siteWhatsapp = setting('contact_whatsapp_1');
    $whatsappUrl = $siteWhatsapp && function_exists('whatsapp_link') ? whatsapp_link() : null;

    // Kategorileri çek
    $categories = ServiceCategory::where('is_active', true)
        ->orderBy('category_id')
        ->limit(6)
        ->get();

    // Dinamik URL helpers (ModuleSlugService kullanarak)
    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
    $currentLocale = app()->getLocale();
    $serviceIndexSlug = $moduleSlugService->getMultiLangSlug('Service', 'index', $currentLocale);
    $serviceCategorySlug = \App\Services\ModuleSlugService::getSlug('Service', 'category');
    $pageShowSlug = \App\Services\ModuleSlugService::getSlug('Page', 'show');
    $defaultLocale = get_tenant_default_locale();
    $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';

    $homeUrl = url('/');
    $hakkimizdaUrl = url('/' . $pageShowSlug . '/hakkimizda');
    $iletisimUrl = url('/' . $pageShowSlug . '/iletisim');
    // Kategori URL - route-based: /service/kategori/{slug}
    $categoryUrl = fn($cat) => url($localePrefix . '/' . $serviceIndexSlug . '/' . $serviceCategorySlug . '/' . ($cat->slug['tr'] ?? $cat->slug));

    // Settings
    $siteName = setting('site_title', 'Unimad Madencilik');
    $siteSlogan = setting('site_slogan', 'Madencilik & Mühendislik');
    $phone1 = setting('contact_phone_1', '+90 (312) 212 68 09');
    $email = setting('contact_email_1', 'info@unimadmadencilik.com');
    $addressLine1 = setting('contact_address_line_1', 'Emek Mah. M. A. C. Kırımoğlu Sok. No:14/1');
    $addressLine2 = setting('contact_address_line_2', 'Çankaya');
    $city = setting('contact_city', 'Ankara');

    $socialLinkedin = setting('social_linkedin', '#');
    $socialInstagram = setting('social_instagram', '#');
    $socialTwitter = setting('social_twitter', '#');
    $socialFacebook = setting('social_facebook', '#');
@endphp

<!-- ========== FOOTER ========== -->
<footer class="bg-dark-900 dark:bg-dark-950 text-white pt-16 pb-8">
    <div class="container mx-auto px-4 sm:px-4 md:px-2">

        <!-- Footer Top -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 pb-12 border-b border-slate-800">

            <!-- Company Info -->
            <div class="col-span-2 lg:col-span-1">
                <a href="{{ $homeUrl }}" class="flex items-center gap-3 mb-6">
                    @if($hasLogo)
                        {{-- Footer'da genelde dark logo (beyaz arka plana karşı) veya light logo kullanılır --}}
                        @if($logos['has_dark'])
                            <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-12 lg:h-14 w-auto">
                        @else
                            <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-12 lg:h-14 w-auto">
                        @endif
                    @else
                        {{-- Fallback: İkon + Metin --}}
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                            <i class="fal fa-mountain text-white text-xl"></i>
                        </div>
                        <div>
                            <span class="font-heading text-2xl text-white tracking-wider">{{ strtoupper($siteName) }}</span>
                            <span class="block text-xs text-slate-400 -mt-1">{{ $siteSlogan }}</span>
                        </div>
                    @endif
                </a>
                <p class="text-slate-400 mb-6 text-sm">
                    Tecrübe ve bilgi birikimimizi güncel teknoloji ile birleştirerek profesyonel mühendislik çözümleri sunuyoruz.
                </p>
                <div class="flex gap-3">
                    @if($socialLinkedin && $socialLinkedin !== '#')
                    <a href="{{ $socialLinkedin }}" target="_blank" rel="noopener" class="w-9 h-9 bg-slate-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fab fa-linkedin-in text-sm"></i>
                    </a>
                    @endif
                    @if($socialInstagram && $socialInstagram !== '#')
                    <a href="{{ $socialInstagram }}" target="_blank" rel="noopener" class="w-9 h-9 bg-slate-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    @endif
                    @if($socialTwitter && $socialTwitter !== '#')
                    <a href="{{ $socialTwitter }}" target="_blank" rel="noopener" class="w-9 h-9 bg-slate-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fab fa-twitter text-sm"></i>
                    </a>
                    @endif
                    @if($socialFacebook && $socialFacebook !== '#')
                    <a href="{{ $socialFacebook }}" target="_blank" rel="noopener" class="w-9 h-9 bg-slate-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Kurumsal -->
            <div>
                <h4 class="font-heading text-base text-white mb-4">KURUMSAL</h4>
                <ul class="space-y-2">
                    <li><a href="{{ $homeUrl }}" class="text-slate-400 hover:text-primary-400 transition-colors text-sm">Ana Sayfa</a></li>
                    <li><a href="{{ $hakkimizdaUrl }}" class="text-slate-400 hover:text-primary-400 transition-colors text-sm">Hakkımızda</a></li>
                    <li><a href="{{ url('/blog') }}" class="text-slate-400 hover:text-primary-400 transition-colors text-sm">Blog</a></li>
                    <li><a href="{{ $iletisimUrl }}" class="text-slate-400 hover:text-primary-400 transition-colors text-sm">İletişim</a></li>
                </ul>
            </div>

            <!-- Hizmetler -->
            <div>
                <h4 class="font-heading text-base text-white mb-4">HİZMETLER</h4>
                <ul class="space-y-2">
                    @foreach($categories as $cat)
                    <li><a href="{{ $categoryUrl($cat) }}" class="text-slate-400 hover:text-primary-400 transition-colors text-sm">{{ $cat->title['tr'] ?? $cat->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- İletişim -->
            <div>
                <h4 class="font-heading text-base text-white mb-4">İLETİŞİM</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                        <i class="fal fa-map-marker-alt text-primary-500 mt-1 text-sm"></i>
                        <span class="text-slate-400 text-sm">{{ $addressLine1 }}<br>{{ $addressLine2 }}, {{ $city }}</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fal fa-phone text-primary-500 text-sm"></i>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone1) }}" class="text-slate-400 hover:text-primary-400 transition-colors text-sm">
                            {{ $phone1 }}
                        </a>
                    </li>
                    @if($whatsappUrl)
                    <li class="flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-500 text-sm"></i>
                        <a href="{{ $whatsappUrl }}" target="_blank" class="text-slate-400 hover:text-green-400 transition-colors text-sm">
                            {{ $siteWhatsapp }}
                        </a>
                    </li>
                    @endif
                    <li class="flex items-center gap-2">
                        <i class="fal fa-envelope text-primary-500 text-sm"></i>
                        <a href="mailto:{{ $email }}" class="text-slate-400 hover:text-primary-400 transition-colors text-sm">
                            {{ $email }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 text-sm text-center md:text-left">
                &copy; {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır.
            </p>
            <div class="flex items-center gap-6 text-sm text-slate-500">
                <a href="{{ $hakkimizdaUrl }}" class="hover:text-primary-400 transition-colors">Hakkımızda</a>
                <a href="{{ $iletisimUrl }}" class="hover:text-primary-400 transition-colors">İletişim</a>
            </div>
        </div>

        <!-- Credit -->
        <div class="pt-6 text-center">
            <p class="text-slate-600 text-xs">
                Yapay Zeka ve Web Tasarım: <a href="https://turkbilisim.com.tr" target="_blank" rel="noopener" class="text-primary-400 hover:text-primary-300 transition-colors">Türk Bilişim</a>
            </p>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button x-data="{ show: false }"
        x-init="window.addEventListener('scroll', () => { show = window.scrollY > 500 })"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 left-6 z-50 w-12 h-12 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all">
    <i class="fal fa-arrow-up text-lg"></i>
</button>

{{-- Floating WhatsApp Button (Conditional) --}}
@if($whatsappUrl)
<a href="{{ $whatsappUrl }}" target="_blank"
   class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg shadow-green-500/30 hover:shadow-green-500/50 flex items-center justify-center transition-all duration-300 hover:scale-110"
   title="WhatsApp ile iletişime geçin">
    <i class="fab fa-whatsapp text-2xl"></i>
</a>
@endif
