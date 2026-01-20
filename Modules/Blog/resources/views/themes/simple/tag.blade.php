@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    @php
        $currentLocale = app()->getLocale();
        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
        $showSlug = \App\Services\ModuleSlugService::getSlug('Blog', 'show');
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
        $blogIndexUrl = ($localePrefix ? $localePrefix : '') . '/' . ltrim($indexSlug ?? '', '/');
        $blogIndexUrl = '/' . ltrim($blogIndexUrl, '/');

        $breadcrumbsArray = [
            ['label' => __('blog::front.general.home'), 'url' => url('/')],
            ['label' => __('blog::front.general.blogs'), 'url' => url($blogIndexUrl)],
            ['label' => '#' . ($displayTag ?? $tag)]
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
            <div class="flex items-center gap-3">
                <h1 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white">#{{ $displayTag ?? $tag }}</h1>
                <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full">
                    {{ $items->total() }} yazı
                </span>
            </div>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="bg-white dark:bg-gray-900 py-10 md:py-16">
        <div class="container mx-auto">
            @if($items->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($items as $post)
                        @php
                            $postTitle = $post->getTranslated('title', $currentLocale);
                            $postExcerpt = $post->getTranslated('excerpt', $currentLocale) ??
                                          \Illuminate\Support\Str::limit(strip_tags($post->getTranslated('body', $currentLocale) ?? ''), 160);
                            $postUrl = $post->getUrl($currentLocale);
                            $featuredImage = getFirstMediaWithFallback($post);
                            $publishedDate = $post->published_at
                                ? $post->published_at->translatedFormat('d M Y')
                                : $post->created_at->translatedFormat('d M Y');
                            $readingTime = $post->calculateReadingTime($currentLocale);
                            $categoryName = $post->category ? $post->category->getTranslated('name', $currentLocale) : null;
                        @endphp

                        <article class="group bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-500 transition-all duration-300">
                            {{-- Image --}}
                            <a href="{{ $postUrl }}" class="block overflow-hidden">
                                @if($featuredImage)
                                    <img src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                         alt="{{ $postTitle }}"
                                         loading="lazy"
                                         class="w-full h-48 md:h-52 object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-48 md:h-52 bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                        <i class="fa-solid fa-newspaper text-white/20 text-5xl"></i>
                                    </div>
                                @endif
                            </a>

                            {{-- Content --}}
                            <div class="p-5 md:p-6">
                                {{-- Category & Date --}}
                                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    @if($categoryName)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full font-medium">
                                            <i class="fa-solid fa-folder text-[10px]"></i>
                                            {{ $categoryName }}
                                        </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $publishedDate }}
                                    </span>
                                    @if($readingTime)
                                        <span class="flex items-center gap-1">
                                            <i class="fa-regular fa-clock"></i>
                                            {{ $readingTime }} dk
                                        </span>
                                    @endif
                                </div>

                                {{-- Title --}}
                                <a href="{{ $postUrl }}" class="block">
                                    <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        {{ $postTitle }}
                                    </h2>
                                </a>

                                {{-- Excerpt --}}
                                @if($postExcerpt)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-3 mb-4">
                                        {{ \Illuminate\Support\Str::limit($postExcerpt, 120) }}
                                    </p>
                                @endif

                                {{-- Featured Badge --}}
                                @if($post->is_featured)
                                    <div class="mb-4">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 text-gray-900 shadow-sm">
                                            <i class="fa-solid fa-star"></i>
                                            Öne Çıkan
                                        </span>
                                    </div>
                                @endif

                                {{-- Read More --}}
                                <a href="{{ $postUrl }}"
                                   class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors group/link">
                                    {{ __('blog::front.general.read_more') }}
                                    <i class="fa-solid fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($items->hasPages())
                    <div class="mt-12">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                            {{ $items->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-hashtag text-gray-400 dark:text-gray-500 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-heading">Bu etiketle yazı bulunamadı</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-body mb-6">
                        <strong>#{{ $displayTag ?? $tag }}</strong> etiketiyle etiketlenmiş henüz bir yazı yok.
                    </p>
                    <a href="{{ url($blogIndexUrl) }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-arrow-left"></i>
                        Tüm Yazılara Dön
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
