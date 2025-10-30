{{-- SEARCH SECTION (Before Footer) --}}
@php
    use Modules\Search\App\Models\SearchQuery;
    $popularSearches = SearchQuery::getMarkedPopular(10);
@endphp

<section class="w-full py-12" @mouseenter="$dispatch('close-megamenu')">
    <div class="container mx-auto px-4 sm:px-4 md:px-2">
        <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 py-8 md:py-12 px-4 md:px-6 rounded-3xl shadow-2xl" style="overflow: visible;">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-black mb-3 text-gray-900 dark:text-white">Aradığınızı Bulamadınız Mı?</h2>
                <p class="text-base md:text-lg lg:text-xl text-gray-600 dark:text-gray-400 mb-6 md:mb-8">Binlerce ürün için hemen arayın!</p>

                {{-- Livewire Search Bar with Custom Footer Styling --}}
                <div class="mb-6" style="position: relative; z-index: 101;">
                    @livewire('search::search-bar-footer', key('footer-search-v3'))
                </div>

                {{-- Popüler Aramalar (SADECE LG+ EKRANLARDA) --}}
                @if($popularSearches->count() > 0)
                    <div class="space-y-4 hidden lg:!block"
                         x-data="{
                             scrollInterval: null,
                             scrollLeft() {
                                 this.$refs.scrollContainer.scrollBy({ left: -300, behavior: 'smooth' });
                             },
                             scrollRight() {
                                 this.$refs.scrollContainer.scrollBy({ left: 300, behavior: 'smooth' });
                             },
                             startAutoScroll(direction) {
                                 this.stopAutoScroll();
                                 this.scrollInterval = setInterval(() => {
                                     if (direction === 'left') {
                                         this.$refs.scrollContainer.scrollBy({ left: -5, behavior: 'auto' });
                                     } else {
                                         this.$refs.scrollContainer.scrollBy({ left: 5, behavior: 'auto' });
                                     }
                                 }, 20);
                             },
                             stopAutoScroll() {
                                 if (this.scrollInterval) {
                                     clearInterval(this.scrollInterval);
                                     this.scrollInterval = null;
                                 }
                             }
                         }">
                        <div class="flex items-center justify-center gap-3 relative">
                            {{-- Scroll Container --}}
                            <div x-ref="scrollContainer"
                                 class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide flex-1">
                                {{-- Popüler Aramalar Linki (İlk Sırada) --}}
                                <a href="{{ route('search.tags') }}"
                                   class="flex-shrink-0 bg-white/80 dark:bg-white/10 hover:bg-white dark:hover:bg-white/20 border border-gray-200 dark:border-gray-600 px-4 py-2 rounded-full text-sm font-semibold text-gray-900 dark:text-white transition-all hover:scale-105 hover:shadow-md">
                                    Popüler Aramalar
                                </a>

                                @foreach($popularSearches as $search)
                                    <a href="{{ href('Search', 'search') }}?q={{ urlencode($search->query) }}"
                                       class="flex-shrink-0 bg-white/80 dark:bg-white/10 hover:bg-white dark:hover:bg-white/20 border border-gray-200 dark:border-gray-600 px-4 py-2 rounded-full text-sm font-semibold text-gray-900 dark:text-white transition-all hover:scale-105 hover:shadow-md">
                                        {{ $search->query }}
                                    </a>
                                @endforeach
                            </div>

                            {{-- İleri-Geri Butonları (Yanyana - Sağda - Sadece İkon) --}}
                            <div class="flex items-start gap-3 pb-4">
                                <button @click="scrollLeft()"
                                        @mouseenter="startAutoScroll('left')"
                                        @mouseleave="stopAutoScroll()"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="fa-solid fa-chevron-left text-lg"></i>
                                </button>
                                <button @click="scrollRight()"
                                        @mouseenter="startAutoScroll('right')"
                                        @mouseleave="stopAutoScroll()"
                                        class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <i class="fa-solid fa-chevron-right text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- FOOTER-002 Design --}}
<footer class="w-full bg-white dark:bg-transparent backdrop-blur-xl text-gray-900 dark:text-white rounded-3xl border border-gray-200 dark:border-white/10">

    <div class="container mx-auto px-4 sm:px-4 md:px-0 py-12 md:py-16">

        {{-- Logo Ortada Büyük --}}
        <div class="flex flex-col items-center justify-center mb-12">
            @php
                $logoService = app(\App\Services\LogoService::class);
                $logos = $logoService->getLogos();

                $logoUrl = $logos['light_logo_url'] ?? null;
                $logoDarkUrl = $logos['dark_logo_url'] ?? null;
                $fallbackMode = $logos['fallback_mode'] ?? 'title_only';
                $siteTitle = $logos['site_title'] ?? setting('site_title');
                $companyName = setting('company_name');
                $siteSlogan = setting('site_slogan');

                // Text uzunluklarına göre responsive boyutlandırma
                $companyNameLength = mb_strlen($companyName ?? '');
                $sloganLength = mb_strlen($siteSlogan ?? '');

                // Kurumsal isim için boyut
                if ($companyNameLength > 50) {
                    $companyClass = 'text-sm sm:text-base md:text-lg';
                } elseif ($companyNameLength > 30) {
                    $companyClass = 'text-base sm:text-lg md:text-xl';
                } else {
                    $companyClass = 'text-lg sm:text-xl md:text-2xl';
                }

                // Slogan için boyut
                if ($sloganLength > 40) {
                    $sloganClass = 'text-xs sm:text-sm md:text-base';
                } elseif ($sloganLength > 25) {
                    $sloganClass = 'text-sm sm:text-base md:text-lg';
                } else {
                    $sloganClass = 'text-base sm:text-lg md:text-xl';
                }
            @endphp

            <a href="{{ url('/') }}" class="mb-6 block">
                @if($fallbackMode === 'both')
                    {{-- Her iki logo da var - Dark mode'da otomatik değiş --}}
                    <img src="{{ $logoUrl }}"
                         alt="{{ $siteTitle }}"
                         class="dark:hidden object-contain h-24 sm:h-28 md:h-32 w-auto mx-auto"
                         title="{{ $siteTitle }}">
                    <img src="{{ $logoDarkUrl }}"
                         alt="{{ $siteTitle }}"
                         class="hidden dark:block object-contain h-24 sm:h-28 md:h-32 w-auto mx-auto"
                         title="{{ $siteTitle }}">
                @elseif($fallbackMode === 'light_only' || $logoUrl)
                    {{-- Sadece light logo var - Dark mode'da CSS ile beyaz yap --}}
                    <img src="{{ $logoUrl }}"
                         alt="{{ $siteTitle }}"
                         class="block object-contain h-24 sm:h-28 md:h-32 w-auto mx-auto logo-adaptive"
                         title="{{ $siteTitle }}">
                @elseif($fallbackMode === 'dark_only' || $logoDarkUrl)
                    {{-- Sadece dark logo var - Her modda göster --}}
                    <img src="{{ $logoDarkUrl }}"
                         alt="{{ $siteTitle }}"
                         class="block object-contain h-24 sm:h-28 md:h-32 w-auto mx-auto"
                         title="{{ $siteTitle }}">
                @else
                    {{-- Logo yok - Site title text göster --}}
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 rounded-3xl flex items-center justify-center">
                            <i class="fa-solid fa-forklift text-white text-5xl"></i>
                        </div>
                        <h2 class="text-4xl sm:text-6xl md:text-8xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400">
                            {{ $siteTitle }}
                        </h2>
                    </div>
                @endif
            </a>

            {{-- Kurumsal İsim + Slogan --}}
            <div class="flex flex-col items-center gap-2 text-center max-w-2xl px-4">
                @if($companyName)
                    <h3 class="{{ $companyClass }} text-gray-800 dark:text-gray-200 font-bold leading-tight">
                        {{ $companyName }}
                    </h3>
                @endif

                @if($siteSlogan)
                    <p class="{{ $sloganClass }} text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                        {{ $siteSlogan }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Linkler Grid (Kategorize) - Mobile: 2 kolon, Tablet: 3 kolon, Desktop: 5 kolon --}}
        @php
            // Ana kategorileri çek (parent_id = null)
            $mainCategories = \Modules\Shop\app\Models\ShopCategory::whereNull('parent_id')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get()
                ->map(function($cat) {
                    $slug = $cat->getTranslated('slug', app()->getLocale());
                    return [
                        'name' => $cat->getTranslated('title', app()->getLocale()),
                        'url' => href('Shop', 'category', $slug)
                    ];
                })
                ->toArray();

            $footerSections = [
                'Kurumsal' => [
                    'icon' => 'fa-solid fa-building',
                    'links' => [
                        ['name' => 'Hakkımızda', 'url' => href('Page', 'show', 'hakkimizda')],
                        ['name' => 'İletişim', 'url' => href('Page', 'show', 'iletisim')],
                        ['name' => 'Kariyer', 'url' => href('Page', 'show', 'kariyer')],
                    ]
                ],
                'Ürünler' => [
                    'icon' => 'fa-solid fa-grid-2',
                    'links' => $mainCategories
                ],
                'Alışveriş' => [
                    'icon' => 'fa-solid fa-shopping-cart',
                    'links' => [
                        ['name' => 'Ödeme Yöntemleri', 'url' => href('Page', 'show', 'odeme-yontemleri')],
                        ['name' => 'Teslimat & Kargo', 'url' => href('Page', 'show', 'teslimat-kargo')],
                        ['name' => 'Güvenli Alışveriş', 'url' => href('Page', 'show', 'guvenli-alisveris')],
                    ]
                ],
                'Müşteri Hizmetleri' => [
                    'icon' => 'fa-solid fa-headset',
                    'links' => [
                        ['name' => 'SSS', 'url' => href('Page', 'show', 'sss')],
                        ['name' => 'İptal & İade', 'url' => href('Page', 'show', 'iptal-iade')],
                        ['name' => 'Cayma Hakkı', 'url' => href('Page', 'show', 'cayma-hakki')],
                        ['name' => 'Mesafeli Satış Sözleşmesi', 'url' => href('Page', 'show', 'mesafeli-satis')],
                    ]
                ],
                'Yasal' => [
                    'icon' => 'fa-solid fa-gavel',
                    'links' => [
                        ['name' => 'Gizlilik Politikası', 'url' => href('Page', 'show', 'gizlilik-politikasi')],
                        ['name' => 'Kullanım Koşulları', 'url' => href('Page', 'show', 'kullanim-kosullari')],
                        ['name' => 'KVKK Aydınlatma', 'url' => href('Page', 'show', 'kvkk-aydinlatma')],
                        ['name' => 'Çerez Politikası', 'url' => href('Page', 'show', 'cerez-politikasi')],
                    ]
                ],
            ];

            // İletişim bilgileri
            $contactPhone = setting('contact_phone_1', '0216 755 3 555');
            $contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
            $contactEmail = setting('contact_email_1', 'info@ixtif.com');
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-6 md:gap-8 mb-12 text-sm">
            @foreach($footerSections as $title => $section)
                <div>
                    <h5 class="font-bold mb-4 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <i class="{{ $section['icon'] }} text-blue-600 dark:text-blue-400"></i>
                        {{ $title }}
                    </h5>
                    <ul class="space-y-2 text-sm">
                        @foreach($section['links'] as $link)
                            <li>
                                <a href="{{ $link['url'] }}"
                                   class="text-gray-600 dark:text-gray-400
                                          hover:text-blue-600 dark:hover:text-blue-400
                                          transition-colors">
                                    {{ $link['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            {{-- İletişim Kolonu --}}
            <div>
                <h5 class="font-bold mb-4 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <i class="fa-solid fa-phone text-blue-600 dark:text-blue-400"></i>
                    İletişim
                </h5>
                <ul class="space-y-3 text-sm">
                    <li>
                        <div class="text-gray-400 dark:text-gray-500 text-xs mb-1">Telefon</div>
                        <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                           class="text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            {{ $contactPhone }}
                        </a>
                    </li>
                    <li>
                        <div class="text-gray-400 dark:text-gray-500 text-xs mb-1">WhatsApp</div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}"
                           target="_blank"
                           class="text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            {{ $contactWhatsapp }}
                        </a>
                    </li>
                    <li>
                        <div class="text-gray-400 dark:text-gray-500 text-xs mb-1">E-posta</div>
                        <a href="mailto:{{ $contactEmail }}"
                           class="text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            {{ $contactEmail }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Sosyal Medya (SOL) + Ödeme Logoları (SAĞ) - 2 Kolon --}}
        @php
            $socialMedia = [
                ['icon' => 'facebook-f', 'url' => setting('social_facebook', 'https://facebook.com/ixtif'), 'gradient' => 'from-blue-500 to-blue-600'],
                ['icon' => 'instagram', 'url' => setting('social_instagram', 'https://instagram.com/ixtifcom'), 'gradient' => 'from-pink-500 to-rose-600'],
                ['icon' => 'twitter', 'url' => setting('social_twitter', 'https://twitter.com/ixtifcom'), 'gradient' => 'from-blue-400 to-cyan-500'],
                ['icon' => 'linkedin-in', 'url' => setting('social_linkedin', 'https://linkedin.com/company/ixtif'), 'gradient' => 'from-blue-600 to-blue-700'],
                ['icon' => 'whatsapp', 'url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', setting('contact_whatsapp_1', '905010056758')), 'gradient' => 'from-green-500 to-green-600']
            ];
        @endphp

        <div class="flex flex-col md:flex-row justify-between items-center gap-6 mb-12">
            {{-- Sosyal Medya (SOL) --}}
            <div class="flex justify-center md:justify-start gap-4">
                @foreach($socialMedia as $social)
                <a href="{{ $social['url'] }}"
                   class="group w-14 h-14 bg-gradient-to-br {{ $social['gradient'] }}
                          rounded-2xl flex items-center justify-center"
                   aria-label="{{ $social['icon'] }}">
                    <i class="fa-brands fa-{{ $social['icon'] }} text-xl text-white
                              group-hover:scale-125 transition-transform duration-300"></i>
                </a>
                @endforeach
            </div>

            {{-- Ödeme Logoları (SAĞ) --}}
            <div class="flex justify-center md:justify-end">
                <img src="https://ixtif.com/storage/tenant2/81/cc.png"
                     alt="Ödeme Yöntemleri - Mastercard, Visa"
                     class="h-8 md:h-10 w-auto object-contain"
                     loading="lazy">
            </div>
        </div>

        {{-- Copyright --}}
        <div class="text-center text-gray-500 dark:text-gray-500 text-sm
                    border-t border-gray-300 dark:border-white/10 pt-8">
            {{ \App\Services\SeoMetaTagService::generateAutomaticCopyright($companyName ?? $siteTitle, app()->getLocale()) }}
        </div>
    </div>

    {{-- SEO Tools & Debug (Mobile & Desktop) --}}
    <div class="bg-slate-50/80 dark:bg-slate-900/40
                border-t border-gray-200 dark:border-white/5">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 pb-24 md:pb-4">
            <div class="flex flex-wrap items-center justify-center gap-2 text-xs">

                {{-- Sitemap --}}
                <a href="{{ route('sitemap') }}"
                   title="XML Sitemap"
                   class="inline-flex items-center px-2 py-1
                          bg-green-100 dark:bg-green-500/20
                          text-green-700 dark:text-green-300
                          rounded hover:bg-green-200 dark:hover:bg-green-500/30
                          transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    Sitemap
                </a>

                {{-- Schema.org Test --}}
                <a href="https://search.google.com/test/rich-results?url={{ urlencode(url()->current()) }}"
                   target="_blank"
                   title="Google Rich Results Test"
                   class="inline-flex items-center px-2 py-1
                          bg-blue-100 dark:bg-blue-500/20
                          text-blue-700 dark:text-blue-300
                          rounded hover:bg-blue-200 dark:hover:bg-blue-500/30
                          transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Schema
                </a>

                {{-- PageSpeed Test --}}
                <a href="https://pagespeed.web.dev/analysis?url={{ urlencode(url()->current()) }}"
                   target="_blank"
                   title="PageSpeed Insights"
                   class="inline-flex items-center px-2 py-1
                          bg-orange-100 dark:bg-orange-500/20
                          text-orange-700 dark:text-orange-300
                          rounded hover:bg-orange-200 dark:hover:bg-orange-500/30
                          transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                    Speed
                </a>

                {{-- 🔐 ADMIN ONLY: Admin Tools (Cache + AI Chat) --}}
                @auth
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('root'))
                        {{-- Cache Clear Button --}}
                        <button onclick="clearSystemCache(this)"
                                title="[ADMIN] Sistem Önbelleğini Temizle"
                                class="inline-flex items-center px-2 py-1
                                       bg-red-100 dark:bg-red-500/20
                                       text-red-700 dark:text-red-300
                                       rounded hover:bg-red-200 dark:hover:bg-red-500/30
                                       transition-colors cursor-pointer">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="button-text">Cache</span>
                            <svg class="w-3 h-3 ml-1 loading-spinner hidden animate-spin" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm6 14a6 6 0 100-12 6 6 0 000 12z"/>
                            </svg>
                        </button>

                        {{-- Clear AI Conversation Button --}}
                        <button
                            onclick="clearAIConversation(this)"
                            title="[ADMIN] AI Konuşma Geçmişini Temizle"
                            class="inline-flex items-center px-2 py-1
                                   bg-purple-100 dark:bg-purple-500/20
                                   text-purple-700 dark:text-purple-300
                                   rounded hover:bg-purple-200 dark:hover:bg-purple-500/30
                                   transition-colors cursor-pointer">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                            </svg>
                            <span class="button-text">AI Chat</span>
                            <svg class="w-3 h-3 ml-1 loading-spinner hidden animate-spin" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm6 14a6 6 0 100-12 6 6 0 000 12z"/>
                            </svg>
                        </button>
                    @endif
                @endauth

                {{-- Theme Badge --}}
                @php
                    $themeService = app(\App\Services\ThemeService::class);
                    $activeTheme = $themeService->getActiveTheme();
                    $themeName = $activeTheme ? $activeTheme->name : 'simple';
                @endphp
                <span class="inline-flex items-center px-2 py-1
                             bg-purple-100 dark:bg-purple-500/20
                             text-purple-700 dark:text-purple-300
                             rounded text-xs font-medium">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                    </svg>
                    Tema: {{ $themeName }}
                </span>
            </div>
        </div>
    </div>

</footer>

{{-- Widget Integration --}}
@widgetstyles
@widgetscripts

{{-- Livewire Scripts --}}
@livewireScripts

{{-- AI Chat JS - MUST load AFTER Livewire/Alpine.js --}}
<script src="/assets/js/ai-chat.js?v=<?php echo time(); ?>"></script>

{{-- Google Tag Manager Event Tracking --}}
<script defer src="{{ asset('js/ga-events.js') }}?v=1.0.0"></script>

{{-- Core System Scripts --}}
<script defer src="{{ asset('js/core-system.js') }}?v=1.0.0"></script>

{{-- iXtif Theme Scripts (ÖNCE yükle - initGLightbox fonksiyonu için) --}}
<script src="{{ asset('js/ixtif-theme.js') }}?v={{ now()->timestamp }}"></script>

{{-- GLightbox (ixtif-theme.js yüklendikten SONRA) --}}
<link rel="preload" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css"></noscript>
<script defer src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js" onload="initGLightbox()"></script>

{{-- AI Chat Admin Functions: clearAIConversation() → /public/assets/js/ai-chat.js --}}

{{-- Theme Main Scripts --}}
@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
<script defer src="{{ asset('assets/js/themes/' . $themeName . '/main.js') }}?v=1.0.1"></script>
<script src="{{ mix('js/app.js') }}" defer></script>

{{-- Web Share API Helper --}}
<script defer src="{{ asset('js/web-share.js') }}"></script>

{{-- instant.page - Preload on hover for instant navigation --}}
<script src="//instant.page/5.2.0" type="module" data-instant-intensity="mousedown"></script>

{{-- Back to Top Button Script --}}
<script defer src="{{ asset('js/back-to-top.js') }}"></script>

{{-- Dynamic Script Stack --}}
@stack('scripts')

{{-- MOBILE BOTTOM BAR - Footer dışında, body içinde --}}
{{-- Styles: public/css/ixtif-mobile-bottom-bar.css --}}
<div class="mobile-bottom-bar bg-gradient-to-r from-green-500 to-blue-500">
    <div class="grid grid-cols-2 gap-0 max-w-md mx-auto">
        {{-- Phone Button (SOL) --}}
        <a href="tel:{{ str_replace(' ', '', setting('contact_phone_1', '02167553555')) }}"
           class="flex items-center justify-center gap-2 py-3 text-white hover:bg-black/10 transition-all duration-300"
           aria-label="Telefon İletişim">
            <i class="fas fa-phone text-xl"></i>
            <span class="text-sm font-medium">Telefon</span>
        </a>

        {{-- WhatsApp Button (SAĞ) --}}
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp_1', '905010056758')) }}"
           class="flex items-center justify-center gap-2 py-3 text-white hover:bg-black/10 transition-all duration-300 border-l border-white/20"
           aria-label="WhatsApp İletişim">
            <i class="fab fa-whatsapp text-xl"></i>
            <span class="text-sm font-medium">WhatsApp</span>
        </a>
    </div>
</div>

{{-- AI Chat Floating Widget (JS/CSS now in header) --}}
<x-ai.floating-widget button-text="AI Destek" theme="blue" />

{{-- PWA Service Worker Registration --}}
<script>
    // Register Service Worker for PWA installability
    if ('serviceWorker' in navigator && document.readyState === 'complete') {
        try {
            navigator.serviceWorker.register('/sw.js')
                .then((registration) => {
                    console.log('[PWA] Service Worker registered:', registration.scope);
                })
                .catch((error) => {
                    // Suppress error from console (likely ad blocker or missing sw.js)
                    if (error.name !== 'InvalidStateError') {
                        console.error('[PWA] Service Worker registration failed:', error);
                    }
                });
        } catch (e) {
            // Suppress InvalidStateError silently
        }
    } else if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            try {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('[PWA] Service Worker registered:', registration.scope);
                    })
                    .catch((error) => {
                        // Suppress error from console
                        if (error.name !== 'InvalidStateError') {
                            console.error('[PWA] Service Worker registration failed:', error);
                        }
                    });
            } catch (e) {
                // Suppress InvalidStateError silently
            }
        });
    }
</script>

    </div> {{-- Close relative z-10 wrapper --}}
</body>
</html>
