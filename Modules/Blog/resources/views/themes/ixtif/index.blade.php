@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="{ ...blogsInfiniteScroll(), ...heroSlider() }" x-init="init(); initHero()">

        {{-- FULL SCREEN HERO SLIDER --}}
        @if($items->count() >= 4)
        <section class="relative w-full overflow-hidden bg-black" style="height: 85vh; min-height: 600px;">
            {{-- Slides Container --}}
            <div class="absolute inset-0">
                @foreach($items->take(4) as $index => $heroItem)
                    @php
                        $currentLocale = app()->getLocale();
                        $slugData = $heroItem->getRawOriginal('slug');
                        if (is_string($slugData)) {
                            $slugData = json_decode($slugData, true) ?: [];
                        }
                        $slug = (is_array($slugData) && isset($slugData[$currentLocale])) ? $slugData[$currentLocale] : 'blog-' . $heroItem->blog_id;
                        $title = $heroItem->getTranslated('title', $currentLocale);
                        $body = $heroItem->getTranslated('body', $currentLocale);
                        $excerpt = $heroItem->getCleanExcerpt($currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body), 200);
                        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                        $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                        $defaultLocale = get_tenant_default_locale();
                        $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                        $heroUrl = url($localePrefix . '/' . $slugPrefix . '/' . $slug);
                        $heroMedia = getFirstMediaWithFallback($heroItem);
                        $heroImage = $heroMedia ? thumb($heroMedia, 1920, 1080, ['quality' => 95, 'format' => 'webp']) : asset('images/blog-default.jpg');
                    @endphp

                    {{-- Slide {{ $index + 1 }} --}}
                    <div x-show="heroIndex === {{ $index }}"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-500"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0"
                         style="display: {{ $index === 0 ? 'block' : 'none' }};">

                        {{-- Background Image --}}
                        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $heroImage }}');">
                            {{-- Minimal Overlay - Bottom Only --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                        </div>

                        {{-- Content (Sabit Height - Kayma Yok) --}}
                        <div class="absolute inset-0 flex items-end">
                            <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-8 md:pb-12">
                                <div class="max-w-4xl">
                                    {{-- Category Badge (Sabit Pozisyon) --}}
                                    <div style="height: 40px;" class="mb-2">
                                        @if($heroItem->categories && $heroItem->categories->first())
                                            <span class="inline-block px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 text-white text-sm font-bold uppercase tracking-wider rounded-lg shadow-lg">
                                                {{ $heroItem->categories->first()->getTranslated('title', $currentLocale) }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Title (Gazete Tarzı, Dikkat Çekici Gradient) --}}
                                    <a href="{{ $heroUrl }}" class="block group">
                                        <div style="height: 200px;" class="mb-4 flex items-end">
                                            <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black leading-[1.1] line-clamp-2 transition-all duration-500" style="background: linear-gradient(135deg, #ffffff 0%, #60a5fa 50%, #a78bfa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-fill-color: transparent; filter: drop-shadow(0 4px 20px rgba(0,0,0,0.8)); font-weight: 900; background-size: 200% 200%; animation: gradientShift 5s ease infinite;">
                                                {{ $title }}
                                            </h1>
                                        </div>
                                    </a>

                                    {{-- Date Badge (Sabit Pozisyon) --}}
                                    <div style="height: 36px;" class="mb-6">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 backdrop-blur-md border border-white/20 text-white text-sm md:text-base font-medium rounded-lg shadow-lg">
                                            <i class="far fa-clock"></i>
                                            {{ $heroItem->created_at->locale('tr')->diffForHumans() }}
                                        </span>
                                    </div>

                                    {{-- Excerpt (Sabit Height, Sadece Desktop) --}}
                                    <div style="height: 84px;" class="hidden md:block mb-8">
                                        <p class="text-lg lg:text-xl text-white/90 leading-relaxed line-clamp-3" style="text-shadow: 0 2px 10px rgba(0,0,0,0.6);">
                                            {{ $excerpt }}
                                        </p>
                                    </div>

                                    {{-- Navigation (Sabit Pozisyon) --}}
                                    <div class="flex items-center gap-3">
                                        {{-- Prev Button --}}
                                        <button @click="prevHero()"
                                                class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-white/5 hover:bg-white/15 backdrop-blur-sm border border-white/10 flex items-center justify-center transition-all">
                                            <i class="fas fa-chevron-left text-white/70 text-xs"></i>
                                        </button>

                                        {{-- Dots --}}
                                        <div class="flex items-center gap-2">
                                            @foreach($items->take(4) as $dotIndex => $dot)
                                                <button @click="heroIndex = {{ $dotIndex }}; resetAutoPlay()"
                                                        :class="heroIndex === {{ $dotIndex }} ? 'w-10 md:w-12 bg-white' : 'w-2 md:w-3 bg-white/50 hover:bg-white/70'"
                                                        class="h-2 md:h-3 rounded-full transition-all duration-300"></button>
                                            @endforeach
                                        </div>

                                        {{-- Next Button --}}
                                        <button @click="nextHero()"
                                                class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-white/5 hover:bg-white/15 backdrop-blur-sm border border-white/10 flex items-center justify-center transition-all">
                                            <i class="fas fa-chevron-right text-white/70 text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Gradient Animation CSS --}}
        <style>
            @keyframes gradientShift {
                0%, 100% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
            }
        </style>
        @endif

        {{-- Categories Slider (Shop tarzı) --}}
        @if(isset($categories) && $categories->count() > 0)
        <section class="py-6 border-b border-gray-200 dark:border-white/10 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm">
            <div class="container mx-auto">
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide">
                    {{-- Tüm Yazılar --}}
                    <a href="{{ url('/blog') }}"
                       class="flex-shrink-0 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all {{ !request('category') && !($tag ?? null) ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                        <i class="fa-solid fa-newspaper mr-2"></i>
                        Tüm Yazılar
                    </a>

                    {{-- Ana Kategoriler --}}
                    @foreach($categories as $category)
                        @php
                            $categorySlug = $category->getTranslated('slug', app()->getLocale());
                            $isActive = isset($selectedCategory) && $selectedCategory && (
                                $selectedCategory->category_id === $category->category_id ||
                                ($selectedCategory->parent_id && $selectedCategory->parent_id === $category->category_id)
                            );
                            $hasChildren = $category->children && $category->children->count() > 0;
                        @endphp

                        {{-- Tüm kategoriler direkt link (alt kategoriler varsa kategori sayfasında gösterilir) --}}
                        <a href="{{ url('/blog/category/' . $categorySlug) }}"
                           class="flex-shrink-0 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                            {{ $category->getTranslated('title', app()->getLocale()) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- NEWS PORTAL SECTION: 8+4 Column Layout (ixtif theme only) --}}
        @if($themeName === 'ixtif' && $items->count() >= 12)
        <section class="py-8 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800" x-data="newsSlider()">
            <div class="container mx-auto">
                <div class="grid lg:grid-cols-12 gap-6">

                    {{-- 8 COLUMNS: Main Slider (6 slides - 5-10. haberler) --}}
                    <div class="lg:col-span-8">
                        <div class="relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl" style="height: 518px;">
                            {{-- Slides --}}
                            @foreach($items->skip(4)->take(6) as $slideItem)
                                @php
                                    $slideIndex = $loop->index; // 0-5 arası index
                                    $currentLocale = app()->getLocale();
                                    $slugData = $slideItem->getRawOriginal('slug');
                                    if (is_string($slugData)) {
                                        $slugData = json_decode($slugData, true) ?: [];
                                    }
                                    $slug = (is_array($slugData) && isset($slugData[$currentLocale])) ? $slugData[$currentLocale] : 'blog-' . $slideItem->blog_id;
                                    $title = $slideItem->getTranslated('title', $currentLocale);
                                    $body = $slideItem->getTranslated('body', $currentLocale);
                                    $excerpt = $slideItem->getCleanExcerpt($currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body), 150);
                                    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                    $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                    $defaultLocale = get_tenant_default_locale();
                                    $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                    $slideUrl = url($localePrefix . '/' . $slugPrefix . '/' . $slug);
                                    $slideMedia = getFirstMediaWithFallback($slideItem);
                                    $slideImage = $slideMedia ? thumb($slideMedia, 1200, 800, ['quality' => 90, 'format' => 'webp']) : null;
                                @endphp

                                <div x-show="newsIndex === {{ $slideIndex }}"
                                     x-transition:enter="transition ease-out duration-600"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-600"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0"
                                     style="display: {{ $slideIndex === 0 ? 'block' : 'none' }};">

                                    <a href="{{ $slideUrl }}" class="block h-full group">
                                        {{-- Background Image --}}
                                        @if($slideImage)
                                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
                                             style="background-image: url('{{ $slideImage }}');"></div>
                                        @else
                                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                                            <i class="fas fa-image text-white/20 text-9xl relative z-10"></i>
                                        </div>
                                        @endif

                                        {{-- Content --}}
                                        <div class="absolute inset-0 flex items-end p-8">
                                            <div class="w-full">
                                                {{-- Category Badge --}}
                                                @if($slideItem->categories && $slideItem->categories->first())
                                                <span class="inline-block px-4 py-2 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg mb-4 shadow-2xl">
                                                    {{ $slideItem->categories->first()->getTranslated('title', $currentLocale) }}
                                                </span>
                                                @endif

                                                {{-- Title: PC 1 satır, Mobil 2 satır --}}
                                                <h2 class="text-4xl font-black text-white mb-3 group-hover:text-blue-300 transition-colors md:line-clamp-1 line-clamp-2"
                                                    style="text-shadow: 0 0 30px rgba(0,0,0,1), 0 0 20px rgba(0,0,0,1), 0 0 10px rgba(0,0,0,0.9), 0 4px 8px rgba(0,0,0,0.8); min-height: 3.5rem; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;">
                                                    {{ $title }}
                                                </h2>

                                                {{-- Excerpt: Sabit 2 satır --}}
                                                <p class="text-lg text-white mb-4"
                                                   style="text-shadow: 0 0 20px rgba(0,0,0,1), 0 0 15px rgba(0,0,0,0.9), 0 2px 6px rgba(0,0,0,0.8); min-height: 3.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    {{ $excerpt }}
                                                </p>

                                                {{-- Date --}}
                                                <div class="flex items-center gap-2 text-white text-sm"
                                                     style="text-shadow: 0 0 15px rgba(0,0,0,1), 0 2px 4px rgba(0,0,0,0.8);">
                                                    <i class="far fa-clock"></i>
                                                    <span>{{ $slideItem->created_at->locale('tr')->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                            {{-- Navigation Numbers (Bottom Center) - INSTANT HOVER --}}
                            <div class="absolute bottom-6 left-0 right-0 flex justify-center items-center gap-3 z-10">
                                @for($i = 0; $i < 6; $i++)
                                <button @click="goToSlide({{ $i }})"
                                        @mouseenter="goToSlide({{ $i }})"
                                        :class="newsIndex === {{ $i }} ? 'bg-white text-gray-900 scale-110 shadow-xl' : 'bg-black/60 text-white hover:bg-black/70 border border-white/30'"
                                        class="w-10 h-10 rounded-full font-bold text-sm transition-all duration-300 flex items-center justify-center backdrop-blur-md">
                                    {{ $i + 1 }}
                                </button>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- 4 COLUMNS: Side News (2 vertical cards - 11-12. haberler) --}}
                    <div class="lg:col-span-4 space-y-6">
                        @foreach($items->skip(10)->take(2) as $sideIndex => $sideItem)
                            @php
                                $currentLocale = app()->getLocale();
                                $slugData = $sideItem->getRawOriginal('slug');
                                if (is_string($slugData)) {
                                    $slugData = json_decode($slugData, true) ?: [];
                                }
                                $slug = (is_array($slugData) && isset($slugData[$currentLocale])) ? $slugData[$currentLocale] : 'blog-' . $sideItem->blog_id;
                                $title = $sideItem->getTranslated('title', $currentLocale);
                                $body = $sideItem->getTranslated('body', $currentLocale);
                                $excerpt = $sideItem->getCleanExcerpt($currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body), 100);
                                $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                $defaultLocale = get_tenant_default_locale();
                                $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                $sideUrl = url($localePrefix . '/' . $slugPrefix . '/' . $slug);
                                $sideMedia = getFirstMediaWithFallback($sideItem);
                                $sideImage = $sideMedia ? thumb($sideMedia, 600, 400, ['quality' => 85, 'format' => 'webp']) : null;
                            @endphp

                            <a href="{{ $sideUrl }}"
                               class="group block relative bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300"
                               style="height: 247px;">
                                {{-- Background Image --}}
                                @if($sideImage)
                                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105"
                                     style="background-image: url('{{ $sideImage }}');"></div>
                                @else
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                                    <i class="fas fa-image text-white/20 text-7xl relative z-10"></i>
                                </div>
                                @endif

                                {{-- Content --}}
                                <div class="absolute inset-0 flex items-end p-6">
                                    <div class="w-full">
                                        {{-- Category Badge --}}
                                        @if($sideItem->categories && $sideItem->categories->first())
                                        <span class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg mb-3 shadow-2xl">
                                            {{ $sideItem->categories->first()->getTranslated('title', $currentLocale) }}
                                        </span>
                                        @endif

                                        {{-- Title --}}
                                        <h3 class="text-2xl font-black text-white mb-2 line-clamp-2 group-hover:text-blue-300 transition-colors"
                                            style="text-shadow: 0 0 25px rgba(0,0,0,1), 0 0 15px rgba(0,0,0,1), 0 0 8px rgba(0,0,0,0.9), 0 4px 6px rgba(0,0,0,0.8);">
                                            {{ $title }}
                                        </h3>

                                        {{-- Date --}}
                                        <div class="flex items-center gap-2 text-white text-sm"
                                             style="text-shadow: 0 0 15px rgba(0,0,0,1), 0 2px 4px rgba(0,0,0,0.8);">
                                            <i class="far fa-clock"></i>
                                            <span>{{ $sideItem->created_at->locale('tr')->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>

        {{-- FEATURED NEWS BANNER (Özel Haber) - 13. haber --}}
        @if($themeName === 'ixtif' && $items->count() >= 13)
        @php
            $featuredItem = $items->skip(12)->first();
            $currentLocale = app()->getLocale();
            $slugData = $featuredItem->getRawOriginal('slug');
            if (is_string($slugData)) {
                $slugData = json_decode($slugData, true) ?: [];
            }
            $slug = (is_array($slugData) && isset($slugData[$currentLocale])) ? $slugData[$currentLocale] : 'blog-' . $featuredItem->blog_id;
            $title = $featuredItem->getTranslated('title', $currentLocale);
            $body = $featuredItem->getTranslated('body', $currentLocale);
            $excerpt = $featuredItem->getCleanExcerpt($currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body), 200);
            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
            $featuredUrl = url($localePrefix . '/' . $slugPrefix . '/' . $slug);
            $featuredMedia = getFirstMediaWithFallback($featuredItem);
            $featuredImage = $featuredMedia ? thumb($featuredMedia, 800, 600, ['quality' => 90, 'format' => 'webp']) : null;
        @endphp

        <section class="py-6">
            <div class="container mx-auto">
                <a href="{{ $featuredUrl }}" class="group block relative bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 dark:from-indigo-900 dark:via-purple-900 dark:to-indigo-900 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">

                        {{-- Left: Content (8 columns) --}}
                        <div class="lg:col-span-8 p-4 md:p-6 flex items-center gap-4 relative z-10 order-2 lg:order-1">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-yellow-400/20 flex items-center justify-center group-hover:bg-yellow-400/30 transition-colors">
                                    <i class="fas fa-bolt text-yellow-300 text-xl md:text-2xl"></i>
                                </div>
                            </div>

                            {{-- Title + Info --}}
                            <div class="flex-1">
                                <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-white leading-tight group-hover:text-yellow-300 transition-colors mb-2"
                                    style="text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                                    {{ $title }}
                                </h3>

                                {{-- Excerpt --}}
                                <p class="text-sm text-white/80 line-clamp-1 mb-2"
                                   style="text-shadow: 0 1px 5px rgba(0,0,0,0.4);">
                                    {{ $excerpt }}
                                </p>

                                {{-- Date --}}
                                <div class="flex items-center gap-2 text-white/70 text-xs"
                                     style="text-shadow: 0 1px 5px rgba(0,0,0,0.4);">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $featuredItem->created_at->locale('tr')->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Right: Image (4 columns) --}}
                        <div class="lg:col-span-4 relative h-40 lg:h-auto lg:min-h-[160px] order-1 lg:order-2">
                            @if($featuredImage)
                            <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-500"
                                 style="background-image: url('{{ $featuredImage }}');"></div>
                            @else
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                                <i class="fas fa-newspaper text-white/20 text-5xl"></i>
                            </div>
                            @endif
                        </div>

                    </div>
                </a>
            </div>
        </section>
        @endif

        {{-- MAIN CONTENT WITH SIDEBAR: 8-4 Layout --}}
        @if($themeName === 'ixtif' && $items->count() >= 14)
        <section class="py-8">
            <div class="container mx-auto">
                <div class="grid lg:grid-cols-12 gap-8">

                    {{-- LEFT SIDE (SOL): 8 Columns - Main Content --}}
                    <div class="lg:col-span-8">

                        {{-- TOP: 4-4 Layout (1 BIG NEWS) --}}
                        @php
                            $bigNews = $items->skip(13)->first(); // 14. haber
                            if ($bigNews) {
                                $currentLocale = app()->getLocale();
                                $bigSlug = $bigNews->getTranslated('slug', $currentLocale) ?: 'blog-' . $bigNews->blog_id;
                                $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                $defaultLocale = get_tenant_default_locale();
                                $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                $bigUrl = url($localePrefix . '/' . $slugPrefix . '/' . $bigSlug);
                                $bigTitle = $bigNews->getTranslated('title', $currentLocale) ?? 'Başlık bulunamadı';
                                $bigExcerpt = $bigNews->getTranslated('excerpt', $currentLocale) ?? '';
                                // Görsel debug
                                $bigMediaObj = getFirstMediaWithFallback($bigNews);
                                $bigImage = $bigMediaObj ? thumb($bigMediaObj, 600, 400, ['quality' => 90, 'format' => 'webp']) : null;
                            }
                        @endphp

                        @if(isset($bigNews))
                        <a href="{{ $bigUrl }}" class="group block mb-8">
                            <div class="grid md:grid-cols-2 gap-6 bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">

                                {{-- Left: Image (4 cols) --}}
                                <div class="relative h-80 md:h-full min-h-[450px] overflow-hidden">
                                    @if($bigImage)
                                    <div class="absolute inset-0 group-hover:scale-105 transition-transform duration-500"
                                         style="background-image: url('{{ $bigImage }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                                    @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                                        <i class="fas fa-robot text-white/20 text-6xl"></i>
                                    </div>
                                    @endif
                                </div>

                                {{-- Right: Content (4 cols) --}}
                                <div class="p-6 flex flex-col justify-center">
                                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4 line-clamp-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $bigTitle }}
                                    </h3>

                                    <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3 leading-relaxed">
                                        {{ $bigExcerpt }}
                                    </p>

                                    {{-- Meta --}}
                                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center gap-2">
                                            <i class="far fa-clock"></i>
                                            <span>{{ $bigNews->created_at->locale('tr')->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endif

                        {{-- BOTTOM: 4-4 Layout (2 SMALL NEWS) --}}
                        <div class="grid md:grid-cols-2 gap-6">
                            @foreach($items->skip(14)->take(2) as $smallNews)
                                @php
                                    $currentLocale = app()->getLocale();
                                    $smallSlug = $smallNews->getTranslated('slug', $currentLocale) ?: 'blog-' . $smallNews->blog_id;
                                    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                    $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                    $defaultLocale = get_tenant_default_locale();
                                    $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                    $smallUrl = url($localePrefix . '/' . $slugPrefix . '/' . $smallSlug);
                                    $smallTitle = $smallNews->getTranslated('title', $currentLocale) ?? 'Başlık bulunamadı';
                                    $smallExcerpt = $smallNews->getTranslated('excerpt', $currentLocale) ?? '';
                                    // Görsel sistemini slider'daki gibi yap
                                    $smallMediaObj = getFirstMediaWithFallback($smallNews);
                                    $smallImage = $smallMediaObj ? thumb($smallMediaObj, 400, 300, ['quality' => 90, 'format' => 'webp']) : null;
                                @endphp

                                <div class="group flex flex-col">
                                    <a href="{{ $smallUrl }}" class="flex flex-col h-full bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">

                                        {{-- Image --}}
                                        <div class="relative h-56 overflow-hidden flex-shrink-0">
                                            @if($smallImage)
                                            <div class="absolute inset-0 group-hover:scale-105 transition-transform duration-500"
                                                 style="background-image: url('{{ $smallImage }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                                            @else
                                            <div class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                                                <i class="fas fa-image text-white/20 text-5xl"></i>
                                            </div>
                                            @endif
                                        </div>

                                        {{-- Content (BELOW IMAGE) - Flex grow --}}
                                        <div class="p-5 flex flex-col flex-grow">
                                            {{-- Title: Fixed 2 lines --}}
                                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" style="min-height: 3.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $smallTitle }}
                                            </h4>

                                            {{-- Excerpt: Fixed 2 lines --}}
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 flex-grow" style="min-height: 2.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $smallExcerpt }}
                                            </p>

                                            {{-- Meta - Bottom --}}
                                            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mt-auto">
                                                <div class="flex items-center gap-1.5">
                                                    <i class="far fa-clock"></i>
                                                    <span>{{ $smallNews->created_at->locale('tr')->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        {{-- 3'LÜ HABERLER: 3-3-3 Layout --}}
                        @php
                            $tripleItems = $items->skip(16)->take(3);
                        @endphp
                        {{-- DEBUG: Toplam: {{ $items->count() }}, Triple count: {{ $tripleItems->count() }} --}}

                        @if($tripleItems->count() > 0)
                        <div class="grid md:grid-cols-3 gap-6 mt-8">
                            @foreach($tripleItems as $tripleNews)
                                @php
                                    $currentLocale = app()->getLocale();
                                    $tripleSlug = $tripleNews->getTranslated('slug', $currentLocale) ?: 'blog-' . $tripleNews->blog_id;
                                    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                    $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                    $defaultLocale = get_tenant_default_locale();
                                    $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                    $tripleUrl = url($localePrefix . '/' . $slugPrefix . '/' . $tripleSlug);
                                    $tripleTitle = $tripleNews->getTranslated('title', $currentLocale) ?? 'Başlık bulunamadı';
                                    $tripleExcerpt = $tripleNews->getTranslated('excerpt', $currentLocale) ?? '';
                                    $tripleMediaObj = getFirstMediaWithFallback($tripleNews);
                                    $tripleImage = $tripleMediaObj ? thumb($tripleMediaObj, 400, 250, ['quality' => 90, 'format' => 'webp']) : null;
                                @endphp

                                <div class="group flex flex-col">
                                    <a href="{{ $tripleUrl }}" class="flex flex-col h-full bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">

                                        {{-- Image (TOP) --}}
                                        <div class="relative h-48 overflow-hidden flex-shrink-0">
                                            @if($tripleImage)
                                            <div class="absolute inset-0 group-hover:scale-105 transition-transform duration-500"
                                                 style="background-image: url('{{ $tripleImage }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                                            @else
                                            <div class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                                                <i class="fas fa-image text-white/20 text-4xl"></i>
                                            </div>
                                            @endif
                                        </div>

                                        {{-- Content (BOTTOM) - Flex grow --}}
                                        <div class="p-4 flex flex-col flex-grow">
                                            {{-- Title: Fixed 2 lines --}}
                                            <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" style="min-height: 3rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $tripleTitle }}
                                            </h4>

                                            {{-- Excerpt: Fixed 2 lines --}}
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 flex-grow" style="min-height: 2.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $tripleExcerpt }}
                                            </p>

                                            {{-- Meta - Bottom --}}
                                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-auto">
                                                <i class="far fa-clock"></i>
                                                <span>{{ $tripleNews->created_at->locale('tr')->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        @else
                        <div class="mt-8 p-4 bg-red-100 dark:bg-red-900/20 rounded-lg">
                            <p class="text-red-800 dark:text-red-200 font-bold text-center mb-3">⚠️ 3'lü haberler için yeterli içerik yok!</p>
                            <div class="text-sm text-red-600 dark:text-red-300 space-y-1">
                                <p>• Toplam haber sayısı: <strong>{{ $items->count() }}</strong></p>
                                <p>• Kullanılan haberler:</p>
                                <p class="ml-4">- 0-3: Full hero slider (4 haber)</p>
                                <p class="ml-4">- 4-9: 8-col slider (6 haber)</p>
                                <p class="ml-4">- 10-11: 4-col side (2 haber)</p>
                                <p class="ml-4">- 12: Featured banner (1 haber)</p>
                                <p class="ml-4">- 13: Büyük haber (1 haber)</p>
                                <p class="ml-4">- 14-15: İkili haberler (2 haber)</p>
                                <p class="ml-4">- <strong>16-18: Üçlü haberler (3 haber) ← BUNLAR EKSİK!</strong></p>
                                <p class="mt-2">• 3'lü haberler için kalan: <strong>{{ $tripleItems->count() }}</strong> / 3</p>
                                <p class="text-xs text-red-500 dark:text-red-400 mt-2">👉 En az <strong>19 haber</strong> olmalı (şu an {{ $items->count() }})</p>
                            </div>
                        </div>
                        @endif

                        {{-- GÜNDEM TARZI: 8-4 Layout (3 kez tekrar) --}}
                        @php
                            $gundemItems = $items->skip(19)->take(9); // 19-27: 3 set x 3 haber
                        @endphp

                        @if($gundemItems->count() >= 3)
                            @foreach($gundemItems->chunk(3) as $chunkIndex => $setItems)
                                @php
                                    if($setItems->count() < 3) {
                                        echo "<!-- DEBUG: Skipping incomplete set, only " . $setItems->count() . " items -->";
                                        break;
                                    }

                                    $setIndex = $chunkIndex; // 0, 1, 2

                                    // Büyük haber (sol 8 kolon)
                                    $bigItem = $setItems->first();
                                    $currentLocale = app()->getLocale();
                                    $bigSlug = $bigItem->getTranslated('slug', $currentLocale) ?: 'blog-' . $bigItem->blog_id;
                                    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                    $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                    $defaultLocale = get_tenant_default_locale();
                                    $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                    $bigUrl = url($localePrefix . '/' . $slugPrefix . '/' . $bigSlug);
                                    $bigTitle = $bigItem->getTranslated('title', $currentLocale);
                                    $bigExcerpt = $bigItem->getTranslated('excerpt', $currentLocale) ?? '';
                                    $bigMediaObj = getFirstMediaWithFallback($bigItem);
                                    $bigImage = $bigMediaObj ? thumb($bigMediaObj, 800, 500, ['quality' => 90, 'format' => 'webp']) : null;
                                @endphp

                                <div class="grid md:grid-cols-12 gap-6 mt-8">
                                    {{-- SOL: Büyük Haber (8 kolon) --}}
                                    <div class="md:col-span-8">
                                        <a href="{{ $bigUrl }}" class="group block relative h-[400px] rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300">
                                            {{-- Background Image --}}
                                            @if($bigImage)
                                            <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-500"
                                                 style="background-image: url('{{ $bigImage }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                                            @else
                                            <div class="absolute inset-0 bg-gradient-to-br from-red-600 to-orange-600"></div>
                                            @endif

                                            {{-- Overlay --}}
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

                                            {{-- Content --}}
                                            <div class="absolute bottom-0 left-0 right-0 p-8">
                                                <span class="inline-block bg-red-600 text-white px-4 py-2 text-xs font-bold uppercase tracking-wider mb-4 rounded">
                                                    Gündem
                                                </span>
                                                <h1 class="text-3xl lg:text-4xl font-black text-white leading-tight mb-4 line-clamp-2 group-hover:text-red-400 transition-colors">
                                                    {{ $bigTitle }}
                                                </h1>
                                                <p class="text-gray-200 text-base mb-4 line-clamp-2">
                                                    {{ $bigExcerpt }}
                                                </p>
                                                <div class="flex items-center gap-4 text-gray-300 text-sm">
                                                    <span><i class="far fa-clock mr-1"></i> {{ $bigItem->created_at->locale('tr')->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    {{-- SAĞ: 2 Küçük Haber (4 kolon) --}}
                                    <div class="md:col-span-4 flex flex-col gap-6">
                                        @foreach($setItems->slice(1, 2)->values() as $smallIndex => $smallItem)
                                            @php
                                                $currentLocale = app()->getLocale();
                                                $smallSlug = $smallItem->getTranslated('slug', $currentLocale) ?: 'blog-' . $smallItem->blog_id;
                                                $smallUrl = url($localePrefix . '/' . $slugPrefix . '/' . $smallSlug);
                                                $smallTitle = $smallItem->getTranslated('title', $currentLocale);
                                                $smallMediaObj = getFirstMediaWithFallback($smallItem);
                                                $smallImage = $smallMediaObj ? thumb($smallMediaObj, 400, 300, ['quality' => 90, 'format' => 'webp']) : null;

                                                // Doğru numara hesapla: Set başlangıcı (19) + (set * 3) + (1 + smallIndex)
                                                $actualNewsNumber = 19 + ($setIndex * 3) + (1 + $smallIndex);
                                            @endphp

                                            <a href="{{ $smallUrl }}" class="group block relative h-[190px] rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                                                {{-- Background Image --}}
                                                @if($smallImage)
                                                <div class="absolute inset-0 bg-cover bg-center group-hover:scale-105 transition-transform duration-500"
                                                     style="background-image: url('{{ $smallImage }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                                                @else
                                                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600"></div>
                                                @endif

                                                {{-- Overlay --}}
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

                                                {{-- Content --}}
                                                <div class="absolute bottom-0 left-0 right-0 p-5">
                                                    <span class="inline-block bg-blue-600 text-white px-3 py-1 text-xs font-bold uppercase mb-3 rounded">
                                                        Teknoloji
                                                    </span>
                                                    <h2 class="text-lg font-bold text-white leading-tight group-hover:text-blue-400 transition-colors line-clamp-2">
                                                        {{ $smallTitle }}
                                                    </h2>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    </div>

                    {{-- RIGHT SIDE (SAĞ): 4 Columns - Sidebar --}}
                    <div class="lg:col-span-4 space-y-6">

                        {{-- En Çok Okunanlar --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-red-600 to-orange-600 p-4">
                                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-fire"></i> En Çok Okunanlar
                                </h3>
                            </div>
                            <div class="p-4 space-y-4">
                                @foreach($items->take(5) as $popularIndex => $popularItem)
                                    @php
                                        $currentLocale = app()->getLocale();
                                        $popularSlug = $popularItem->getTranslated('slug', $currentLocale) ?: 'blog-' . $popularItem->blog_id;
                                        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                        $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                        $defaultLocale = get_tenant_default_locale();
                                        $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                        $popularUrl = url($localePrefix . '/' . $slugPrefix . '/' . $popularSlug);
                                        $popularTitle = $popularItem->getTranslated('title', $currentLocale);
                                    @endphp

                                    <a href="{{ $popularUrl }}" class="group flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-all">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-red-600 to-orange-600 flex items-center justify-center text-white font-bold text-sm">
                                            {{ $popularIndex + 1 }}
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                                {{ $popularTitle }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                <i class="far fa-clock"></i> {{ $popularItem->created_at->locale('tr')->diffForHumans() }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Son Haberler --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4">
                                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-newspaper"></i> Son Haberler
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($items->skip(28)->take(6) as $sidebarItem)
                                    @php
                                        $currentLocale = app()->getLocale();
                                        $sidebarSlug = $sidebarItem->getTranslated('slug', $currentLocale) ?: 'blog-' . $sidebarItem->blog_id;
                                        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                        $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                        $defaultLocale = get_tenant_default_locale();
                                        $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                        $sidebarUrl = url($localePrefix . '/' . $slugPrefix . '/' . $sidebarSlug);
                                        $sidebarTitle = $sidebarItem->getTranslated('title', $currentLocale);
                                        $sidebarMediaObj = getFirstMediaWithFallback($sidebarItem);
                                        $sidebarImage = $sidebarMediaObj ? thumb($sidebarMediaObj, 200, 150, ['quality' => 85, 'format' => 'webp']) : null;
                                    @endphp

                                    <a href="{{ $sidebarUrl }}" class="group flex gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                                        {{-- Image --}}
                                        <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-700">
                                            @if($sidebarImage)
                                            <div class="w-full h-full bg-cover bg-center group-hover:scale-110 transition-transform duration-300"
                                                 style="background-image: url('{{ $sidebarImage }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
                                            @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                                            </div>
                                            @endif
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-2">
                                                {{ $sidebarTitle }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <i class="far fa-clock"></i> {{ $sidebarItem->created_at->locale('tr')->diffForHumans() }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>
        @endif

        {{-- News Slider Alpine.js Component --}}
        <script>
            function newsSlider() {
                return {
                    newsIndex: 0,
                    autoPlayInterval: null,

                    init() {
                        console.log('🎬 News Slider initialized');
                        this.startAutoPlay();
                    },

                    startAutoPlay() {
                        if (this.autoPlayInterval) {
                            clearInterval(this.autoPlayInterval);
                        }
                        this.autoPlayInterval = setInterval(() => {
                            this.newsIndex = (this.newsIndex + 1) % 6;
                            console.log('⏱️ Auto-play: slide', this.newsIndex);
                        }, 5000);
                    },

                    goToSlide(index) {
                        console.log('👆 Manual/Hover: slide', index);
                        this.newsIndex = index;
                        this.startAutoPlay(); // Reset timer
                    }
                }
            }
        </script>
        @endif

        {{-- Alt Kategoriler (Sadece kategori seçiliyse ve alt kategorileri varsa) --}}
        @if(isset($selectedCategory) && $selectedCategory && $selectedCategory->children && $selectedCategory->children->count() > 0)
        <section class="py-8 border-b border-gray-200 dark:border-white/10">
            <div class="container mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <i class="fa-light fa-folder-tree text-blue-600 dark:text-blue-400"></i>
                    Alt Kategoriler
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-8">
                    @foreach($selectedCategory->children as $child)
                        @php
                            $childSlug = $child->getTranslated('slug', app()->getLocale());
                            $childTitle = $child->getTranslated('title', app()->getLocale());
                        @endphp
                        <a href="{{ url('/blog/category/' . $childSlug) }}"
                           class="group bg-white/60 dark:bg-white/5 backdrop-blur-sm border border-gray-200 dark:border-white/10 rounded-2xl p-6 hover:bg-white/80 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
                            <div class="flex flex-col items-center justify-center text-center h-full min-h-[120px]">
                                @if($child->icon_class)
                                    <i class="{{ $child->icon_class }} text-5xl text-blue-500 dark:text-blue-400 mb-3 group-hover:scale-110 transition-transform"></i>
                                @else
                                    <i class="fa-light fa-folder text-5xl text-blue-500 dark:text-blue-400 mb-3 group-hover:scale-110 transition-transform"></i>
                                @endif
                                <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $childTitle }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <div class="py-12">
            <div class="container mx-auto">
                @if ($items->count() > 0)
                    <!-- Articles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="blogs-grid">
                        @foreach ($items as $item)
                            @php
                                $currentLocale = app()->getLocale();
                                $slugData = $item->getRawOriginal('slug');

                                if (is_string($slugData)) {
                                    $slugData = json_decode($slugData, true) ?: [];
                                }

                                // Slug için öncelikle mevcut dili kontrol et, yoksa fallback ama URL'de mevcut dili koru
                                if (is_array($slugData) && isset($slugData[$currentLocale]) && !empty($slugData[$currentLocale])) {
                                    $slug = $slugData[$currentLocale];
                                } elseif (is_array($slugData) && !empty($slugData)) {
                                    $slug = reset($slugData);
                                } elseif (is_string($slugData) && !empty($slugData)) {
                                    $slug = $slugData;
                                } else {
                                    $slug = 'blog-' . $item->blog_id;
                                }

                                $title = $item->getTranslated('title', $currentLocale);
                                $body = $item->getTranslated('body', $currentLocale);
                                $excerpt = $item->getCleanExcerpt($currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body), 150);

                                // Module slug service kullanarak dinamik slug prefix al
                                $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                $defaultLocale = get_tenant_default_locale();
                                $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                $blogUrl = $localePrefix . '/' . $slugPrefix . '/' . $slug;

                                // Helper function ile multi-collection fallback
                                $featuredMedia = getFirstMediaWithFallback($item);
                                $featuredImage = $featuredMedia
                                    ? thumb($featuredMedia, 400, 300, ['quality' => 85, 'format' => 'webp'])
                                    : null;
                            @endphp

                            <a href="{{ url($blogUrl) }}"
                               class="group block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                                @if ($featuredImage)
                                    <div class="relative h-48 overflow-hidden">
                                        <img src="{{ $featuredImage }}"
                                             alt="{{ $title }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                             loading="lazy">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                        </div>

                                        {{-- Floating Stats --}}
                                        <div class="absolute top-2 left-2 right-2 sm:top-4 sm:left-4 sm:right-4 flex justify-between items-start">
                                            <div class="flex items-center gap-1 sm:gap-2">
                                                <span class="inline-flex items-center gap-1 px-2 py-1 sm:px-3 sm:py-1.5 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg text-[10px] sm:text-xs font-medium text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 shadow-lg"
                                                      title="Yayın Tarihi: {{ $item->published_at ? $item->published_at->format('d.m.Y H:i') : $item->created_at->format('d.m.Y H:i') }}">
                                                    <i class="fa-regular fa-calendar text-blue-500 dark:text-blue-400 hidden sm:inline"></i>
                                                    {{ $item->created_at->format('d.m.Y') }}
                                                </span>
                                                @php
                                                    $readTime = $item->calculateReadingTime($currentLocale);
                                                @endphp
                                                <span class="inline-flex items-center gap-1 px-2 py-1 sm:px-3 sm:py-1.5 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg text-[10px] sm:text-xs font-medium text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 shadow-lg"
                                                      title="Tahmini Okuma Süresi: {{ $readTime }} dakika">
                                                    <i class="fa-regular fa-clock text-blue-500 dark:text-blue-400 hidden sm:inline"></i>
                                                    {{ $readTime }} dk
                                                </span>
                                            </div>
                                            {{-- Favoriye Ekle Butonu --}}
                                            @auth
                                            <div x-data="favoriteButton('{{ addslashes(get_class($item)) }}', {{ $item->blog_id }}, {{ $item->isFavoritedBy(auth()->id()) ? 'true' : 'false' }})"
                                                 @click.prevent.stop="toggleFavorite()"
                                                 class="group/fav w-6 h-6 sm:w-8 sm:h-8 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600 shadow-lg hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-300 dark:hover:border-red-500/50 hover:scale-110 transition-all duration-200 cursor-pointer"
                                                 title="Favorilere Ekle">
                                                <i :class="favorited ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart text-gray-400 group-hover/fav:text-red-400'" class="text-[10px] sm:text-sm transition-all duration-200"></i>
                                            </div>
                                            @else
                                            <span onclick="event.preventDefault(); event.stopPropagation(); savePendingFavorite('{{ addslashes(get_class($item)) }}', {{ $item->blog_id }}, '{{ url($blogUrl) }}')"
                                               class="group/fav w-6 h-6 sm:w-8 sm:h-8 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600 shadow-lg hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-300 dark:hover:border-red-500/50 hover:scale-110 transition-all duration-200 cursor-pointer"
                                               title="Favorilere eklemek için giriş yapın">
                                                <i class="fa-regular fa-heart text-gray-400 group-hover/fav:text-red-400 text-[10px] sm:text-sm transition-all duration-200"></i>
                                            </span>
                                            @endauth
                                        </div>
                                    </div>
                                @else
                                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <i class="fa-solid fa-newspaper text-6xl text-white/30"></i>
                                    </div>
                                @endif

                                <div class="p-6">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                        {{ $title }}
                                    </h2>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-4">
                                        {{ $excerpt }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-500">
                                            Devamını Oku →
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Scroll Sentinel (preload trigger) --}}
                    <div x-ref="sentinel" class="h-1"></div>

                    {{-- Infinity Scroll Loading Indicator --}}
                    <div x-show="loading" class="mt-8 flex justify-center">
                        <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Yükleniyor...</span>
                        </div>
                    </div>

                    {{-- End of Content Message --}}
                    <div x-show="!hasMore && !loading" class="mt-8 text-center pb-4">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            <i class="fa-solid fa-check-circle mr-1"></i>
                            Tüm yazılar yüklendi
                        </p>
                    </div>
                @else
                    {{-- No posts --}}
                    <div class="text-center py-20">
                        <i class="fa-solid fa-newspaper text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">
                            Henüz blog yazısı bulunmamaktadır.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function heroSlider() {
            return {
                heroIndex: 0,
                heroInterval: null,

                initHero() {
                    this.startAutoPlay();
                },

                startAutoPlay() {
                    if (this.heroInterval) {
                        clearInterval(this.heroInterval);
                    }
                    this.heroInterval = setInterval(() => {
                        this.nextHero();
                    }, 5000);
                },

                resetAutoPlay() {
                    this.startAutoPlay();
                },

                nextHero() {
                    this.heroIndex = (this.heroIndex + 1) % 4;
                },

                prevHero() {
                    this.heroIndex = this.heroIndex === 0 ? 3 : this.heroIndex - 1;
                    this.resetAutoPlay();
                }
            }
        }

        function blogsInfiniteScroll() {
            return {
                loading: false,
                hasMore: {{ $items->hasMorePages() ? 'true' : 'false' }},
                currentPage: {{ $items->currentPage() }},
                tag: '{{ $tag ?? '' }}',
                category: '{{ $selectedCategory ? $selectedCategory->getTranslated("slug", app()->getLocale()) : "" }}',

                init() {
                    if (!this.hasMore) return;

                    // Container'ı root olarak kullan - div sonu bazlı
                    const container = document.querySelector('.container');

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !this.loading && this.hasMore) {
                                this.loadMore();
                            }
                        });
                    }, {
                        root: null,
                        rootMargin: '0px 0px 1500px 0px' // Prefetch: 1500px önceden yüklemeye başla
                    });

                    observer.observe(this.$refs.sentinel);
                },

                async loadMore() {
                    if (this.loading || !this.hasMore) return;

                    this.loading = true;
                    this.currentPage++;

                    try {
                        let url = `/api/blog/load-more?page=${this.currentPage}`;
                        if (this.tag) {
                            url += `&tag=${encodeURIComponent(this.tag)}`;
                        }
                        if (this.category) {
                            url += `&category=${encodeURIComponent(this.category)}`;
                        }

                        const response = await fetch(url);
                        const data = await response.json();

                        if (data.data && data.data.length > 0) {
                            const grid = document.getElementById('blogs-grid');

                            data.data.forEach(blog => {
                                const card = this.createBlogCard(blog);
                                grid.insertAdjacentHTML('beforeend', card);
                            });
                        }

                        this.hasMore = data.has_more;
                    } catch (error) {
                        console.error('Error loading more blogs:', error);
                        this.currentPage--;
                    } finally {
                        this.loading = false;
                    }
                },

                createBlogCard(blog) {
                    const imageHtml = blog.image
                        ? `<div class="relative h-48 overflow-hidden">
                               <img src="${blog.image}"
                                    alt="${this.escapeHtml(blog.title)}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    loading="lazy">
                               <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                           </div>`
                        : `<div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                               <i class="fa-solid fa-newspaper text-6xl text-white/30"></i>
                           </div>`;

                    return `
                        <a href="${blog.url}"
                           class="group block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                            ${imageHtml}
                            <div class="p-6">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                    ${this.escapeHtml(blog.title)}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-4">
                                    ${this.escapeHtml(blog.excerpt)}
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500 dark:text-gray-500">
                                        <i class="fa-regular fa-calendar mr-1"></i>
                                        ${blog.date}
                                    </span>
                                    <span class="text-blue-600 dark:text-blue-400 font-medium group-hover:translate-x-1 transition-transform">
                                        Devamını Oku →
                                    </span>
                                </div>
                            </div>
                        </a>
                    `;
                },

                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            }
        }
    </script>
@endsection
