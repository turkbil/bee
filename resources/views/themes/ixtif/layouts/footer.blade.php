{{-- Modern Footer - CLS Optimized --}}
<footer class="bg-white dark:bg-gray-800 shadow-inner border-t border-gray-100 dark:border-gray-700 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="relative">

            {{-- Main Footer Content --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                {{-- Company Info --}}
                <div class="space-y-4">
                    @php
                        $logoService = app(\App\Services\LogoService::class);
                        $logos = $logoService->getLogos();
                        $siteDescription = setting('site_description', 'Modern web çözümleri ile dijital dünyanızı şekillendiriyoruz.');
                    @endphp

                    @include('components.logo.responsive-logo', [
                        'logos' => $logos,
                        'baseClass' => 'h-8 w-auto',
                        'priority' => 'low',
                        'location' => 'footer'
                    ])

                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ $siteDescription }}
                    </p>

                    {{-- Social Links with Hover Effects --}}
                    <div class="flex space-x-3">
                        @php
                            $socialLinks = [
                                ['icon' => 'facebook', 'url' => '#', 'color' => 'hover:text-blue-600', 'label' => 'Facebook'],
                                ['icon' => 'twitter', 'url' => '#', 'color' => 'hover:text-sky-400', 'label' => 'Twitter'],
                                ['icon' => 'linkedin', 'url' => '#', 'color' => 'hover:text-blue-700', 'label' => 'LinkedIn'],
                                ['icon' => 'instagram', 'url' => '#', 'color' => 'hover:text-pink-500', 'label' => 'Instagram']
                            ];
                        @endphp

                        @foreach($socialLinks as $social)
                        <a href="{{ $social['url'] }}"
                           aria-label="{{ $social['label'] }} sayfamızı ziyaret edin"
                           class="group p-2 bg-gray-100 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300 {{ $social['color'] }} dark:hover:text-white transition-all duration-300 hover:scale-110 hover:shadow-lg"
                           x-data="{ hovered: false }"
                           @mouseenter="hovered = true"
                           @mouseleave="hovered = false">
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'scale-110': hovered }" fill="currentColor" viewBox="0 0 24 24">
                                @if($social['icon'] === 'facebook')
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                @elseif($social['icon'] === 'twitter')
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                @elseif($social['icon'] === 'linkedin')
                                    <path fill-rule="evenodd" d="M19 0H5a5 5 0 00-5 5v14a5 5 0 005 5h14a5 5 0 005-5V5a5 5 0 00-5-5zM8 19H5V8h3v11zM6.5 6.732c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zM20 19h-3v-5.604c0-3.368-4-3.113-4 0V19h-3V8h3v1.765c1.396-2.586 7-2.777 7 2.476V19z" clip-rule="evenodd" />
                                @elseif($social['icon'] === 'instagram')
                                    <path fill-rule="evenodd" d="M12.017 0C8.396 0 7.989.013 6.77.072 5.55.132 4.708.333 3.999.63a5.995 5.995 0 00-2.176 1.425A5.995 5.995 0 00.294 4.231C-.003 4.94-.204 5.781-.264 7.001-.323 8.22-.336 8.627-.336 12.248s.013 4.028.072 5.248c.06 1.22.261 2.062.558 2.771.306.729.717 1.349 1.425 2.176a5.995 5.995 0 002.176 1.425c.709.297 1.551.498 2.771.558 1.22.059 1.627.072 5.248.072s4.028-.013 5.248-.072c1.22-.06 2.062-.261 2.771-.558a5.995 5.995 0 002.176-1.425 5.995 5.995 0 001.425-2.176c.297-.709.498-1.551.558-2.771.059-1.22.072-1.627.072-5.248s-.013-4.028-.072-5.248c-.06-1.22-.261-2.062-.558-2.771a5.995 5.995 0 00-1.425-2.176A5.995 5.995 0 0016.788.294C16.079-.003 15.238-.204 14.018-.264 12.799-.323 12.392-.336 8.771-.336S4.743-.323 3.524-.264C2.304-.204 1.462-.003.753.294A5.995 5.995 0 00-1.423 1.719 5.995 5.995 0 00-2.848 3.895c-.297.709-.498 1.551-.558 2.771C-3.465 7.886-3.478 8.293-3.478 11.914s.013 4.028.072 5.248c.06 1.22.261 2.062.558 2.771.306.729.717 1.349 1.425 2.176a5.995 5.995 0 002.176 1.425c.709.297 1.551.498 2.771.558 1.22.059 1.627.072 5.248.072s4.028-.013 5.248-.072c1.22-.06 2.062-.261 2.771-.558a5.995 5.995 0 002.176-1.425 5.995 5.995 0 001.425-2.176c.297-.709.498-1.551.558-2.771.059-1.22.072-1.627.072-5.248s-.013-4.028-.072-5.248c-.06-1.22-.261-2.062-.558-2.771a5.995 5.995 0 00-1.425-2.176A5.995 5.995 0 0016.788.294C16.079-.003 15.238-.204 14.018-.264 12.799-.323 12.392-.336 8.771-.336S4.743-.323 3.524-.264zm-.005 21.584c-3.368 0-3.794-.015-5.005-.072-1.194-.055-1.843-.253-2.273-.42a3.796 3.796 0 01-1.414-.923 3.796 3.796 0 01-.923-1.414c-.167-.43-.365-1.079-.42-2.273-.057-1.211-.072-1.637-.072-5.005s.015-3.794.072-5.005c.055-1.194.253-1.843.42-2.273A3.796 3.796 0 011.347 3.347a3.796 3.796 0 011.414-.923c.43-.167 1.079-.365 2.273-.42 1.211-.057 1.637-.072 5.005-.072s3.794.015 5.005.072c1.194.055 1.843.253 2.273.42.512.192.978.45 1.414.923.473.436.731.902.923 1.414.167.43.365 1.079.42 2.273.057 1.211.072 1.637.072 5.005s-.015 3.794-.072 5.005c-.055 1.194-.253 1.843-.42 2.273a3.796 3.796 0 01-.923 1.414 3.796 3.796 0 01-1.414.923c-.43.167-1.079.365-2.273.42-1.211.057-1.637.072-5.005.072z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M12.017 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12.017 16a4 4 0 110-8 4 4 0 010 8z" clip-rule="evenodd" />
                                    <circle cx="18.406" cy="5.594" r="1.44" />
                                @endif
                            </svg>
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Hızlı Erişim</h2>
                    @php
                        $quickLinks = [
                            ['name' => 'Anasayfa', 'url' => href('Page', 'index')],
                            ['name' => 'Hakkımızda', 'url' => '#'],
                            ['name' => 'Hizmetlerimiz', 'url' => '#'],
                            ['name' => 'İletişim', 'url' => '#'],
                            ['name' => 'Blog', 'url' => '#']
                        ];
                    @endphp

                    <ul class="space-y-2">
                        @foreach($quickLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                               class="group flex items-center text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200"
                               x-data="{ linkHover: false }"
                               @mouseenter="linkHover = true"
                               @mouseleave="linkHover = false">
                                <svg class="w-3 h-3 mr-2 text-gray-400 group-hover:text-blue-500 transition-all duration-200"
                                     :class="{ 'translate-x-1': linkHover }"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                {{ $link['name'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contact Info --}}
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">İletişim</h2>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span>İstanbul, Türkiye</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            <span>+90 (555) 123 45 67</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            <span>info@example.com</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Copyright and Bottom Links --}}
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    {{-- Copyright --}}
                    <div class="text-center md:text-left">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ \App\Services\SeoMetaTagService::generateAutomaticCopyright(setting('site_title', config('app.name')), app()->getLocale()) }}
                        </p>
                    </div>

                    {{-- Footer Links --}}
                    <div class="flex flex-wrap justify-center md:justify-end gap-4 text-sm">
                        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Gizlilik Politikası
                        </a>
                        <span class="text-gray-400 dark:text-gray-500">•</span>
                        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Kullanım Koşulları
                        </a>
                        <span class="text-gray-400 dark:text-gray-500">•</span>
                        <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                            Çerez Politikası
                        </a>
                    </div>
                </div>

                {{-- SEO Tools & Debug Info --}}
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Sitemap --}}
                            <a href="{{ route('sitemap') }}"
                               title="XML Sitemap"
                               class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                Sitemap
                            </a>

                            {{-- Schema.org Test --}}
                            <a href="https://search.google.com/test/rich-results?url={{ urlencode(url()->current()) }}"
                               target="_blank"
                               title="Google Rich Results Test"
                               class="inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Schema
                            </a>

                            {{-- PageSpeed Test --}}
                            <a href="https://pagespeed.web.dev/analysis?url={{ urlencode(url()->current()) }}"
                               target="_blank"
                               title="PageSpeed Insights"
                               class="inline-flex items-center px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded hover:bg-orange-200 dark:hover:bg-orange-800 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                </svg>
                                Speed
                            </a>

                            {{-- Cache Clear Button - System Cache --}}
                            <button onclick="clearSystemCache(this)"
                                    title="Sistem Önbelleğini Temizle"
                                    class="inline-flex items-center px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded hover:bg-red-200 dark:hover:bg-red-800 transition-colors cursor-pointer">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="button-text">Cache</span>
                                <svg class="w-3 h-3 ml-1 loading-spinner hidden animate-spin" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm6 14a6 6 0 100-12 6 6 0 000 12z"/>
                                </svg>
                            </button>

                            {{-- Theme Badge --}}
                            @php
                                $themeService = app(\App\Services\ThemeService::class);
                                $activeTheme = $themeService->getActiveTheme();
                                $themeName = $activeTheme ? $activeTheme->name : 'simple';
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded text-xs font-medium">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"></path>
                                </svg>
                                Tema: {{ $themeName }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- Widget Integration --}}
    @widgetstyles
    @widgetscripts

    {{-- Livewire Scripts - NO DEFER (Alpine.js dropdown fix) --}}
    @livewireScripts

    {{-- Alpine.js is already loaded by Livewire, don't load it again --}}
    <script>
        // Initialize Livewire and Alpine
        document.addEventListener('DOMContentLoaded', function() {
            // Livewire and Alpine.js initialization
        });

        // clearSystemCache function is in header.blade.php
    </script>

    {{-- GLightbox - Image Lightbox Library (Async Loading) --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css"></noscript>
    <script defer src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js" onload="initGLightbox()"></script>
    <script>
        // GLightbox Global Initialization - Deferred
        function initGLightbox() {
            if (typeof GLightbox !== 'undefined') {
                const lightbox = GLightbox({
                    selector: '.glightbox',
                    touchNavigation: true,
                    loop: true,
                    autoplayVideos: false,
                    zoomable: true,
                    draggable: true,
                    skin: 'clean',
                    closeButton: true
                });
            }
        }
    </script>

    {{-- Core System Scripts - Mandatory for all themes (Deferred) --}}
    <script defer src="{{ asset('js/core-system.js') }}?v=1.0.0"></script>

    {{-- Theme Main Scripts --}}
    @php
        $themeService = app(\App\Services\ThemeService::class);
        $activeTheme = $themeService->getActiveTheme();
        $themeName = $activeTheme ? $activeTheme->name : 'simple';
    @endphp
    <script defer src="{{ asset('assets/js/themes/' . $themeName . '/main.js') }}?v=1.0.1"></script>

    {{-- Dynamic Script Stack --}}
    @stack('scripts')

    {{-- AI Chat Components --}}
    <x-ai.chat-store />
    <x-ai.floating-widget button-text="AI Destek" theme="blue" />
</body>
</html>
