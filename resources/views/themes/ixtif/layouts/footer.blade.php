{{-- SEARCH SECTION (Before Footer) --}}
@php
    use Modules\Search\App\Models\SearchQuery;
    $popularSearches = SearchQuery::getMarkedPopular(10);
@endphp

{{-- ULTRA PREMIUM SEARCH SECTION --}}
<section class="w-full py-20 relative overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0 bg-gradient-to-r from-gray-50 via-white to-gray-50 dark:from-navy-950 dark:via-navy-900 dark:to-navy-950"></div>
    <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-5"></div>

    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <div class="max-w-4xl mx-auto">
            {{-- Glass Card --}}
            <div class="
                bg-white/80 dark:bg-white/5 backdrop-blur-2xl
                border border-gray-200 dark:border-gold-dark/20
                rounded-[2rem]
                p-12
                shadow-xl dark:shadow-[0_0_60px_rgba(212,175,55,0.1)]
            ">
                {{-- Header --}}
                <div class="text-center mb-10">
                    <h2 class="text-4xl font-black mb-4">
                        <span class="text-gray-900 dark:text-white">Aradığınızı </span>
                        <span class="gold-gradient bg-clip-text text-transparent">Hemen Bulun</span>
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        20.000+ ürün arasından kolayca arama yapın
                    </p>
                </div>

                {{-- Livewire Search Bar with Custom Footer Styling --}}
                <div class="mb-6">
                    @livewire('search::search-bar', ['viewMode' => 'footer'])
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
                                   class="flex-shrink-0
                                          px-4 py-2 rounded-full
                                          bg-gray-100 dark:bg-white/5 backdrop-blur
                                          border border-gray-200 dark:border-gold-dark/20
                                          text-sm font-semibold text-gray-900 dark:text-gold-dark
                                          hover:bg-gray-200 dark:hover:bg-white/10
                                          hover:border-gray-300 dark:hover:border-gold-dark/40
                                          dark:hover:shadow-[0_0_15px_rgba(212,175,55,0.2)]
                                          transition-all duration-300">
                                    Popüler Aramalar
                                </a>

                                @foreach($popularSearches as $search)
                                    <a href="{{ href('Search', 'search') }}?q={{ urlencode($search->query) }}"
                                       class="flex-shrink-0
                                              px-4 py-2 rounded-full
                                              bg-gray-100 dark:bg-white/5 backdrop-blur
                                              border border-gray-200 dark:border-gold-dark/20
                                              text-sm text-gray-700 dark:text-gray-200
                                              hover:bg-gray-200 dark:hover:bg-white/10
                                              hover:border-gray-300 dark:hover:border-gold-dark/40
                                              hover:text-gray-900 dark:hover:text-white
                                              dark:hover:shadow-[0_0_15px_rgba(212,175,55,0.2)]
                                              transition-all duration-300">
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

{{-- ULTRA PREMIUM FOOTER --}}
<footer class="relative pt-20 pb-10 overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0 bg-gradient-to-b from-white to-gray-50 dark:from-navy-900 dark:to-navy-950"></div>

    {{-- Gold Accent Line --}}
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gold-dark to-transparent"></div>

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
                    {{-- Logo yok - ULTRA PREMIUM Gold Gradient Title --}}
                    <div class="flex flex-col items-center gap-3">
                        <h2 class="text-5xl sm:text-6xl md:text-7xl font-black gold-gradient bg-clip-text text-transparent">
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

        {{-- İstatistikler --}}
        @php
            use Modules\Shop\app\Models\ShopProduct;
            use Modules\Shop\app\Models\ShopCategory;

            $totalProducts = ShopProduct::count();
            $totalCategories = ShopCategory::count();

            $topCategories = ShopCategory::withCount('products')
                ->orderBy('products_count', 'desc')
                ->take(2)
                ->get();

            $stats = [
                ['count' => $totalProducts * 10, 'label' => 'Toplam Ürün', 'gradient' => 'from-blue-400 to-cyan-400'],
                ['count' => $totalCategories, 'label' => 'Kategori', 'gradient' => 'from-green-400 to-emerald-400'],
            ];

            foreach($topCategories as $index => $category) {
                $gradients = [
                    'from-purple-400 to-pink-400',
                    'from-orange-400 to-red-400',
                ];
                $stats[] = [
                    'count' => $category->products_count * 10,
                    'label' => $category->getTranslated('title', app()->getLocale()) ?? 'Kategori',
                    'gradient' => $gradients[$index] ?? 'from-gray-400 to-gray-500'
                ];
            }
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            @foreach($stats as $stat)
            <div class="text-center">
                <div class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r {{ $stat['gradient'] }} mb-2">
                    {{ $stat['count'] }}
                </div>
                <div class="text-gray-600 dark:text-gray-400">
                    {{ $stat['label'] }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Linkler Yatay --}}
        @php
            $footerLinks = [
                ['name' => 'Hakkımızda', 'url' => '#'],
                ['name' => 'Kategoriler', 'url' => href('Shop', 'index')],
                ['name' => 'Kampanyalar', 'url' => '#'],
                ['name' => 'Blog', 'url' => '#'],
                ['name' => 'İletişim', 'url' => '#'],
                ['name' => 'Kariyer', 'url' => '#'],
                ['name' => 'SSS', 'url' => '#'],
                ['name' => 'İade & Değişim', 'url' => '#']
            ];
        @endphp

        <div class="flex flex-wrap justify-center gap-6 mb-12 text-sm">
            @foreach($footerLinks as $index => $link)
                @if($index > 0)
                    <span class="text-gray-400 dark:text-gray-600">•</span>
                @endif
                <a href="{{ $link['url'] }}"
                   class="text-gray-600 dark:text-gray-400
                          hover:text-gray-900 dark:hover:text-white
                          transition-colors">
                    {{ $link['name'] }}
                </a>
            @endforeach
        </div>

        {{-- Sosyal Medya --}}
        @php
            $socialMedia = [
                ['icon' => 'facebook-f', 'url' => setting('social_facebook', 'https://facebook.com/ixtif'), 'gradient' => 'from-blue-500 to-blue-600'],
                ['icon' => 'instagram', 'url' => setting('social_instagram', 'https://instagram.com/ixtifcom'), 'gradient' => 'from-pink-500 to-rose-600'],
                ['icon' => 'twitter', 'url' => setting('social_twitter', 'https://twitter.com/ixtifcom'), 'gradient' => 'from-blue-400 to-cyan-500'],
                ['icon' => 'linkedin-in', 'url' => setting('social_linkedin', 'https://linkedin.com/company/ixtif'), 'gradient' => 'from-blue-600 to-blue-700'],
                ['icon' => 'whatsapp', 'url' => 'https://wa.me/' . preg_replace('/[^0-9]/', '', setting('contact_whatsapp_1', '905010056758')), 'gradient' => 'from-green-500 to-green-600']
            ];
        @endphp

        {{-- ULTRA PREMIUM Social Links --}}
        <div class="flex justify-center gap-3 mb-12">
            @foreach($socialMedia as $social)
            <a href="{{ $social['url'] }}"
               class="group w-12 h-12 rounded-full
                      bg-gray-100 dark:bg-white/5 backdrop-blur
                      border border-gray-200 dark:border-gold-dark/20
                      flex items-center justify-center
                      hover:bg-gray-200 dark:hover:bg-gold-dark/20
                      hover:border-gray-300 dark:hover:border-gold-dark/40
                      dark:hover:shadow-[0_0_20px_rgba(212,175,55,0.3)]
                      transition-all duration-300"
               aria-label="{{ $social['icon'] }}">
                <i class="fa-brands fa-{{ $social['icon'] }} text-gray-700 dark:text-gray-300
                          group-hover:text-gray-900 dark:group-hover:text-gold-light
                          group-hover:scale-110 transition-all"></i>
            </a>
            @endforeach
        </div>

        {{-- İletişim Bilgileri --}}
        @php
            $contactPhone = setting('contact_phone_1', '0216 755 3 555');
            $contactWhatsapp = setting('contact_whatsapp_1', '0501 005 67 58');
            $contactEmail = setting('contact_email_1', 'info@ixtif.com');
        @endphp

        {{-- ULTRA PREMIUM Contact Info --}}
        <div class="flex flex-wrap justify-center items-center gap-6 mb-8 text-sm text-gray-600 dark:text-gray-400">
            <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
               class="flex items-center gap-2 hover:text-gray-900 dark:hover:text-gold-light transition-colors">
                <i class="fa-solid fa-phone text-blue-600 dark:text-gold-dark"></i>
                {{ $contactPhone }}
            </a>
            <span class="text-gray-400 dark:text-gold-dark/30">•</span>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}"
               target="_blank"
               class="flex items-center gap-2 hover:text-gray-900 dark:hover:text-gold-light transition-colors">
                <i class="fa-brands fa-whatsapp text-green-600 dark:text-gold-dark"></i>
                {{ $contactWhatsapp }}
            </a>
            <span class="text-gray-400 dark:text-gold-dark/30">•</span>
            <a href="mailto:{{ $contactEmail }}"
               class="flex items-center gap-2 hover:text-gray-900 dark:hover:text-gold-light transition-colors">
                <i class="fa-solid fa-envelope text-purple-600 dark:text-gold-dark"></i>
                {{ $contactEmail }}
            </a>
        </div>

        {{-- Copyright --}}
        <div class="text-center text-gray-500 dark:text-gray-500 text-sm
                    border-t border-gray-300 dark:border-white/10 pt-8">
            <div class="mb-3">
                {{ \App\Services\SeoMetaTagService::generateAutomaticCopyright($companyName ?? $siteTitle, app()->getLocale()) }}
            </div>
            <div class="flex flex-wrap items-center justify-center gap-2 text-xs">
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Gizlilik Politikası</a>
                <span class="text-gray-400 dark:text-gray-600">|</span>
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Kullanım Koşulları</a>
                <span class="text-gray-400 dark:text-gray-600">|</span>
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">KVKK</a>
            </div>
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

    {{-- Floating Gradient Orbs for ULTRA PREMIUM Effect --}}
    <div class="absolute -top-20 -right-20 w-96 h-96 bg-gradient-to-br from-blue-500/10 to-purple-500/10 dark:from-gold-dark/10 dark:to-gold-light/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-gradient-to-br from-purple-500/10 to-pink-500/10 dark:from-gold-light/10 dark:to-gold-dark/10 rounded-full blur-[100px] pointer-events-none"></div>
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

    </div> {{-- Close relative z-10 wrapper --}}
</body>
</html>
