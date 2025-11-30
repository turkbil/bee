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
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
        $blogIndexUrl = ($localePrefix ? $localePrefix : '') . '/' . ltrim($indexSlug ?? '', '/');
        $blogIndexUrl = '/' . ltrim($blogIndexUrl, '/');
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Ana Sayfa
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-angle-right text-gray-400"></i>
                        <a href="{{ $blogIndexUrl }}" class="ml-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 md:ml-2">
                            Blog
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-angle-right text-gray-400"></i>
                        <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">#{{ $displayTag ?? $tag }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <header class="text-center mb-10 lg:mb-14">
            <div class="inline-flex items-center px-4 py-2 text-sm font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-200 rounded-full mb-4">
                <i class="fa-solid fa-hashtag mr-2"></i>
                Etiket
            </div>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                #{{ $displayTag ?? $tag }}
            </h1>

            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                <span class="font-semibold">{{ $items->total() }}</span> yazı bu etiketle etiketlenmiş
            </p>
        </header>

        @if($items->count() > 0)
            <!-- Blog Posts Grid -->
            <div class="grid gap-8 lg:gap-12">
                @foreach($items as $post)
                    @php
                        $postTitle = $post->getTranslated('title', $currentLocale);
                        $postExcerpt = $post->getTranslated('excerpt', $currentLocale) ??
                                      \Illuminate\Support\Str::limit(strip_tags($post->getTranslated('body', $currentLocale) ?? ''), 180);
                        $postSlug = $post->getTranslated('slug', $currentLocale);
                        $postUrl = $post->getUrl($currentLocale);
                        $featuredImage = getFirstMediaWithFallback($post);
                        $publishedDate = $post->published_at
                            ? $post->published_at->translatedFormat('d F Y')
                            : $post->created_at->translatedFormat('d F Y');
                        $readingTime = $post->calculateReadingTime($currentLocale);
                        $categoryName = $post->category ? $post->category->getTranslated('name', $currentLocale) : null;
                        $postTags = $post->tag_list ?? [];
                    @endphp

                    <article class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="md:flex">
                            @if($featuredImage)
                                <div class="md:w-1/3 lg:w-2/5">
                                    <div class="aspect-[16/10] md:aspect-[4/3] lg:aspect-[16/10] overflow-hidden">
                                        <img src="{{ $featuredImage->getUrl() }}"
                                             alt="{{ $postTitle }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                </div>
                            @endif

                            <div class="p-6 lg:p-8 {{ $featuredImage ? 'md:w-2/3 lg:w-3/5' : 'w-full' }}">
                                @if($post->is_featured)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 text-gray-900 shadow-sm mb-3">
                                        Öne Çıkan
                                    </span>
                                @endif

                                @if($categoryName)
                                    <div class="mb-3">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-full">
                                            {{ $categoryName }}
                                        </span>
                                    </div>
                                @endif

                                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                    <a href="{{ $postUrl }}" class="stretched-link">
                                        {{ $postTitle }}
                                    </a>
                                </h2>

                                @if($postExcerpt)
                                    <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
                                        {{ $postExcerpt }}
                                    </p>
                                @endif

                                <!-- Meta Info -->
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-calendar-days"></i>
                                        <time datetime="{{ $post->published_at ? $post->published_at->toISOString() : $post->created_at->toISOString() }}">
                                            {{ $publishedDate }}
                                        </time>
                                    </div>
                                    @if($readingTime)
                                        <span class="flex items-center gap-2"><i class="fa-regular fa-clock"></i>{{ $readingTime }} dk okuma</span>
                                    @endif
                                </div>

                                <!-- Tags -->
                                @if(!empty($postTags))
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(array_slice($postTags, 0, 3) as $postTag)
                                            @php
                                                $seoFriendlyTag = \Illuminate\Support\Str::slug($postTag);
                                                $routeName = $currentLocale === $defaultLocale ? 'blog.tag' : 'blog.tag.localized';
                                                $routeParameters = $currentLocale === $defaultLocale
                                                    ? ['tag' => $seoFriendlyTag]
                                                    : ['locale' => $currentLocale, 'tag' => $seoFriendlyTag];

                                                if (\Illuminate\Support\Facades\Route::has($routeName)) {
                                                    $tagUrl = route($routeName, $routeParameters);
                                                } else {
                                                    $fallbackPath = trim(($localePrefix ?: '') . '/blog/tag/' . $seoFriendlyTag, '/');
                                                    $tagUrl = url($fallbackPath);
                                                }
                                            @endphp
                                            <a href="{{ $tagUrl }}"
                                               class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-200 rounded-full hover:bg-blue-100 dark:hover:bg-blue-800/60 transition-colors z-10 relative"
                                               rel="tag"
                                               data-tag="{{ $postTag }}"
                                               title="{{ $postTag }} etiketli yazılar">
                                                #{{ $postTag }}
                                            </a>
                                        @endforeach
                                        @if(count($postTags) > 3)
                                            <span class="inline-flex items-center px-2 py-1 text-xs text-gray-500 dark:text-gray-400">
                                                +{{ count($postTags) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $items->withQueryString()->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <i class="fa-regular fa-folder-closed text-3xl text-gray-400"></i>
                </div>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Bu etiketle yazı bulunamadı
                </h3>

                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    <strong>#{{ $displayTag ?? $tag }}</strong> etiketiyle etiketlenmiş henüz bir yazı yok.
                </p>

                <a href="{{ $blogIndexUrl }}"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Tüm Yazılara Dön
                </a>
            </div>
        @endif
    </div>
@endsection
