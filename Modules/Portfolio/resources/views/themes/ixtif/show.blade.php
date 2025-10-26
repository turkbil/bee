@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="min-h-screen">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);
            $body = $item->getTranslated('body', $currentLocale);

            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            $indexSlug = $moduleSlugService->getMultiLangSlug('Portfolio', 'index', $currentLocale);
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
            $portfolioIndexUrl = $localePrefix . '/' . $indexSlug;

            // Medya verilerini çek
            $featuredImage = $item->getFirstMedia('featured_image');
            $galleryImages = $item->getMedia('gallery');
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            {{-- Sayfa Başlığı --}}
            <header class="mb-8 md:mb-12">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                    {{ $title }}
                </h1>
                <div class="h-1 w-20 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-8 lg:gap-10">
                {{-- Ana Görsel - Sol Taraf --}}
                @if($featuredImage)
                    <div class="lg:col-span-1 order-2 lg:order-1">
                        <figure class="sticky top-8">
                            <a href="{{ $featuredImage->getUrl() }}"
                               class="glightbox block relative"
                               data-gallery="portfolio-gallery"
                               data-title="{{ $featuredImage->getCustomProperty('title')[$currentLocale] ?? '' }}"
                               data-description="{{ $featuredImage->getCustomProperty('description')[$currentLocale] ?? '' }}"
                               x-data="{ loaded: false }">
                                {{-- Blur Placeholder (LQIP) - Mini 50x50 ~3KB --}}
                                <img src="{{ thumb($featuredImage, 50, 50, ['quality' => 50, 'scale' => 0, 'format' => 'webp']) }}"
                                     alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                     x-show="!loaded"
                                     class="absolute inset-0 w-full rounded-xl blur-2xl scale-110">

                                {{-- Actual Image --}}
                                <img src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                     alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                     x-show="loaded"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="w-full rounded-xl shadow-lg cursor-pointer hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] relative z-10"
                                     @load="loaded = true"
                                     loading="lazy">
                            </a>
                            @if($featuredImage->getCustomProperty('title')[$currentLocale] ?? false)
                                <figcaption class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <strong class="block font-semibold text-gray-900 dark:text-white mb-1">
                                        {{ $featuredImage->getCustomProperty('title')[$currentLocale] }}
                                    </strong>
                                    @if($featuredImage->getCustomProperty('description')[$currentLocale] ?? false)
                                        <span class="block leading-relaxed">
                                            {{ $featuredImage->getCustomProperty('description')[$currentLocale] }}
                                        </span>
                                    @endif
                                </figcaption>
                            @endif
                        </figure>
                    </div>
                @endif

                {{-- İçerik --}}
                <article class="{{ $featuredImage ? 'lg:col-span-2' : 'lg:col-span-3' }} order-1 lg:order-2">
                    <div class="prose prose-lg md:prose-xl max-w-none dark:prose-invert
                          prose-headings:font-bold prose-headings:tracking-tight
                          prose-headings:text-gray-900 dark:prose-headings:text-white
                          prose-h2:text-2xl prose-h2:md:text-3xl prose-h2:mt-12 prose-h2:mb-6
                          prose-h3:text-xl prose-h3:md:text-2xl prose-h3:mt-10 prose-h3:mb-4
                          prose-h4:text-lg prose-h4:md:text-xl prose-h4:mt-8 prose-h4:mb-3
                          prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-p:leading-relaxed prose-p:mb-6
                          prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline
                          prose-a:font-medium hover:prose-a:text-blue-700 dark:hover:prose-a:text-blue-300
                          prose-strong:text-gray-900 dark:prose-strong:text-white prose-strong:font-semibold
                          prose-ul:my-6 prose-ol:my-6 prose-li:my-2 prose-li:leading-relaxed
                          prose-blockquote:border-l-4 prose-blockquote:border-l-blue-500 prose-blockquote:bg-blue-50/50
                          dark:prose-blockquote:bg-blue-900/10 prose-blockquote:py-4 prose-blockquote:px-6
                          prose-blockquote:italic prose-blockquote:my-8
                          prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50
                          dark:prose-code:bg-blue-900/20 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                          prose-code:font-mono prose-code:text-sm
                          prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800 prose-pre:rounded-lg
                          prose-pre:shadow-lg prose-pre:my-8
                          prose-img:rounded-xl prose-img:shadow-md prose-img:my-8
                          prose-hr:my-12 prose-hr:border-gray-200 dark:prose-hr:border-gray-700">
                        @parsewidgets($body ?? '')
                    </div>
                </article>
            </div>

            {{-- Galeri - İçeriğin Altında --}}
            @if($galleryImages->count() > 0)
                <div class="mt-16 md:mt-20 pt-12 border-t-2 border-gray-200 dark:border-gray-700">
                    <header class="mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                            {{ __('mediamanagement::admin.gallery') }}
                        </h2>
                        <div class="h-1 w-16 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
                    </header>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 md:gap-8">
                        @foreach($galleryImages as $image)
                            <figure class="group relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300">
                                <a href="{{ $image->getUrl() }}"
                                   class="glightbox block relative"
                                   data-gallery="portfolio-gallery"
                                   data-title="{{ $image->getCustomProperty('title')[$currentLocale] ?? '' }}"
                                   data-description="{{ $image->getCustomProperty('description')[$currentLocale] ?? '' }}"
                                   x-data="{ loaded: false }">
                                    {{-- Blur Placeholder (LQIP) - Mini 40x40 ~2KB --}}
                                    <img src="{{ thumb($image, 40, 40, ['quality' => 50, 'scale' => 1, 'format' => 'webp']) }}"
                                         alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                         x-show="!loaded"
                                         class="absolute inset-0 w-full h-48 md:h-56 object-cover blur-2xl scale-110">

                                    {{-- Actual Image --}}
                                    <img src="{{ $image->getUrl('thumb') }}"
                                         alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                         x-show="loaded"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         class="w-full h-48 md:h-56 object-cover cursor-pointer transition-transform duration-500 group-hover:scale-110 relative z-10"
                                         @load="loaded = true"
                                         loading="lazy">
                                </a>
                                @if($image->getCustomProperty('title')[$currentLocale] ?? false)
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4 pointer-events-none">
                                        <div class="text-white transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                            <strong class="block text-sm font-semibold mb-1">
                                                {{ $image->getCustomProperty('title')[$currentLocale] }}
                                            </strong>
                                            @if($image->getCustomProperty('description')[$currentLocale] ?? false)
                                                <span class="block text-xs leading-relaxed line-clamp-2">
                                                    {{ $image->getCustomProperty('description')[$currentLocale] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (isset($item->js))
                <script>
                    {!! $item->js !!}
                </script>
            @endif

            @if (isset($item->css))
                <style>
                    {!! $item->css !!}
                </style>
            @endif

            <footer class="mt-16 md:mt-20 pt-8 border-t-2 border-gray-200 dark:border-gray-700">
                <a href="{{ $portfolioIndexUrl }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 dark:from-blue-500 dark:to-blue-400 dark:hover:from-blue-600 dark:hover:to-blue-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>{{ __('portfolio::front.general.all_portfolios') }}</span>
                </a>
            </footer>
        </div>
    </div>

@endsection
