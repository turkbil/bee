@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);

        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Portfolio', 'index', $currentLocale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
        $portfolioIndexUrl = $localePrefix . '/' . $indexSlug;

        // Media
        $heroImage = $item->getFirstMedia('hero');
        $featuredImage = $item->getFirstMedia('featured_image') ?: $heroImage;
        $galleryImages = $item->getMedia('gallery') ?? collect();

        // Breadcrumbs
        $breadcrumbsArray = [
            ['label' => __('portfolio::front.general.home'), 'url' => url('/')],
            ['label' => __('portfolio::front.general.portfolios'), 'url' => url($portfolioIndexUrl)],
            ['label' => $title]
        ];
    @endphp

    {{-- MINIMAL SUBHEADER --}}
    <section class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto py-4">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2 overflow-x-auto whitespace-nowrap scrollbar-hide">
                @foreach($breadcrumbsArray as $index => $crumb)
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">{{ $crumb['label'] }}</a>
                        @if($index < count($breadcrumbsArray) - 1)
                            <span class="mx-2">/</span>
                        @endif
                    @else
                        <span class="text-gray-900 dark:text-white font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white">{{ $title }}</h1>
        </div>
    </section>

    {{-- CONTENT SECTION --}}
    <section class="bg-white dark:bg-gray-900 py-10 md:py-16">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
                {{-- Main Content --}}
                <article class="{{ $featuredImage ? 'lg:col-span-3' : 'lg:col-span-5 max-w-4xl mx-auto' }}">
                    {{-- Body Content --}}
                    <div class="prose prose-base max-w-none dark:prose-invert font-body
                              prose-headings:font-heading prose-headings:text-gray-900 dark:prose-headings:text-white
                              prose-p:text-gray-600 dark:prose-p:text-gray-300
                              prose-a:text-primary-600 dark:prose-a:text-primary-400 hover:prose-a:underline
                              prose-strong:text-gray-900 dark:prose-strong:text-white
                              prose-ul:text-gray-600 dark:prose-ul:text-gray-300
                              prose-ol:text-gray-600 dark:prose-ol:text-gray-300
                              prose-blockquote:border-l-primary-500
                              prose-img:rounded-xl prose-img:shadow-lg">
                        @parsewidgets($body ?? '')
                    </div>
                </article>

                {{-- Sidebar --}}
                @if($featuredImage)
                    <aside class="lg:col-span-2">
                        <div class="sticky top-24 space-y-6">
                            {{-- Featured Image --}}
                            <figure class="rounded-2xl overflow-hidden shadow-2xl">
                                <a href="{{ $featuredImage->getUrl() }}" class="glightbox block" data-gallery="portfolio-main">
                                    <img src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                         alt="{{ $title }}"
                                         class="w-full aspect-square object-cover hover:scale-105 transition-transform duration-500">
                                </a>
                            </figure>
                        </div>
                    </aside>
                @endif
            </div>
        </div>
    </section>

    {{-- GALLERY SECTION --}}
    @if($galleryImages->count() > 0)
        <section class="bg-gray-50 dark:bg-gray-800 py-12 md:py-20">
            <div class="container mx-auto">
                <div class="text-center mb-8">
                    <h2 class="text-xl md:text-2xl font-bold font-heading text-gray-900 dark:text-white mb-3">Galeri</h2>
                    <div class="w-16 h-1 bg-gradient-to-r from-primary-500 to-primary-600 mx-auto rounded-full"></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach($galleryImages as $image)
                        <a href="{{ $image->getUrl() }}"
                           class="glightbox group relative aspect-square rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300"
                           data-gallery="portfolio-gallery">
                            <img src="{{ $image->hasGeneratedConversion('thumb') ? $image->getUrl('thumb') : $image->getUrl() }}"
                                 alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300 flex items-center justify-center">
                                <i class="fa-solid fa-expand text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Custom JS/CSS --}}
    @if(isset($item->js))
        <script>{!! $item->js !!}</script>
    @endif
    @if(isset($item->css))
        <style>{!! $item->css !!}</style>
    @endif

@endsection
