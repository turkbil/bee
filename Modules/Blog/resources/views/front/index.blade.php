@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="blogsIndex()" x-init="init()">

        <!-- Hero Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                        {{ $moduleTitle ?? __('blog::front.general.blogs') }}
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        En son yazılarımızı keşfedin ve bilgi dolu içeriklerimizi okuyun
                    </p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            @if ($items->count() > 0)
                <!-- Blog Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($items as $item)
                        @php
                            $currentLocale = app()->getLocale();
                            $slugData = $item->getRawOriginal('slug');

                            if (is_array($slugData)) {
                                $slug = $slugData[$currentLocale] ?? ($slugData['tr'] ?? reset($slugData));
                            } else {
                                $slug = $item->getCurrentSlug() ?? $item->blog_id;
                            }

                            $showSlug = \App\Services\ModuleSlugService::getSlug('Blog', 'show');
                            $defaultLocale = get_tenant_default_locale();
                            $localePrefix = $currentLocale === $defaultLocale ? '' : '/' . $currentLocale;
                            $dynamicUrl = $localePrefix . '/' . $showSlug . '/' . $slug;

                            $publishedDate = $item->published_at
                                ? $item->published_at->translatedFormat('d M Y')
                                : $item->created_at->translatedFormat('d M Y');

                            $excerpt = $item->getTranslated('excerpt', $currentLocale)
                                ?? \Illuminate\Support\Str::limit(strip_tags($item->getTranslated('body', $currentLocale) ?? ''), 160);

                            $computedReadingTime = $item->calculateReadingTime($currentLocale);
                            $title = $item->getTranslated('title', $currentLocale);
                        @endphp

                        <article class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                            <!-- Featured Image Placeholder -->
                            <div class="aspect-video bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 relative overflow-hidden">
                                @if($item->getFirstMediaUrl('featured_image'))
                                    <img src="{{ $item->getFirstMediaUrl('featured_image') }}"
                                         alt="{{ $title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fa-regular fa-image text-4xl text-blue-300 dark:text-blue-600"></i>
                                    </div>
                                @endif

                                <!-- Category Badge -->
                                @if($item->category)
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/90 backdrop-blur-sm text-blue-700 border border-blue-200">
                                        {{ $item->category->getTranslated('name', $currentLocale) }}
                                    </span>
                                </div>
                                @endif

                                <!-- Featured Badge -->
                                @if($item->is_featured)
                                <div class="absolute top-4 right-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        ⭐ Öne Çıkan
                                    </span>
                                </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <!-- Meta Info -->
                                <div class="flex items-center gap-4 mb-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <i class="fa-regular fa-calendar-days mr-2"></i>
                                        {{ $publishedDate }}
                                    </div>

                                    @if($computedReadingTime)
                                        <div class="flex items-center" data-reading-base="{{ $computedReadingTime }}">
                                            <i class="fa-regular fa-clock mr-2"></i>
                                            <span class="reading-time-text">{{ $computedReadingTime }} dk</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Title -->
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    <a href="{{ $dynamicUrl }}" class="block">
                                        {{ $title }}
                                    </a>
                                </h2>

                                <!-- Excerpt -->
                                @if ($excerpt)
                                    <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3 text-sm leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($excerpt, 140) }}
                                    </p>
                                @endif

                                <!-- Tags -->
                                @php
                                    $postTags = $item->tag_list ?? [];
                                @endphp
                                @if(!empty($postTags))
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @foreach(array_slice($postTags, 0, 3) as $tag)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-200 rounded-md">
                                                <i class="fa-solid fa-hashtag"></i>{{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Read More -->
                                <div class="flex items-center justify-between">
                                    <a href="{{ $dynamicUrl }}"
                                        class="inline-flex items-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors group">
                                        {{ __('blog::front.general.read_more') }}
                                        <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </a>

                                    <!-- Status indicator (for logged in users) -->
                                    @auth
                                    <div class="text-xs">
                                        @if($item->isScheduled())
                                            <span class="text-orange-600 dark:text-orange-400">📅 Zamanlanmış</span>
                                        @elseif($item->isDraft())
                                            <span class="text-gray-500 dark:text-gray-400">📝 Taslak</span>
                                        @else
                                            <span class="text-green-600 dark:text-green-400">✅ Yayında</span>
                                        @endif
                                    </div>
                                    @endauth
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($items->hasPages())
                    <div class="mt-16 flex justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center space-x-4">
                                @if ($items->onFirstPage())
                                    <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                        ← {{ __('blog::front.general.previous') }}
                                    </span>
                                @else
                                    <a href="{{ $items->previousPageUrl() }}"
                                        class="px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors font-medium">
                                        ← {{ __('blog::front.general.previous') }}
                                    </a>
                                @endif

                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-md font-medium">
                                        {{ $items->currentPage() }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        / {{ $items->lastPage() }}
                                    </span>
                                </div>

                                @if ($items->hasMorePages())
                                    <a href="{{ $items->nextPageUrl() }}"
                                        class="px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors font-medium">
                                        {{ __('blog::front.general.next') }} →
                                    </a>
                                @else
                                    <span class="px-4 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                        {{ __('blog::front.general.next') }} →
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="max-w-md mx-auto">
                        <div class="mb-6">
                            <i class="fa-regular fa-newspaper text-5xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ __('blog::front.general.no_blogs_found') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">
                            {{ __('blog::front.general.no_blogs_description') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function blogsIndex() {
            return {
                loaded: false,

                init() {
                    // Instant load
                    this.loaded = true;
                    this.randomizeReadingTimes();
                },

                randomizeReadingTimes() {
                    window.requestAnimationFrame(() => {
                        document.querySelectorAll('[data-reading-base]').forEach(el => {
                            const base = parseInt(el.getAttribute('data-reading-base'), 10);
                            if (!base || Number.isNaN(base)) {
                                return;
                            }

                            const randomFactor = 0.85 + Math.random() * 0.4;
                            const minutes = Math.max(1, Math.round(base * randomFactor));
                            const textNode = el.querySelector('.reading-time-text');

                            if (textNode) {
                                textNode.textContent = `~${minutes} dk`;
                            }
                        });
                    });
                }
            }
        }
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
