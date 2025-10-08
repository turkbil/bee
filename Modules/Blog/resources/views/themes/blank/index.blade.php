@extends('themes.blank.layouts.app')

@section('module_content')
    <div class="relative" x-data="blogsList()" x-init="init()">

        <!-- Gradient Background -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 -z-10">
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 dark:from-blue-600/20 dark:to-purple-600/20">
            </div>
            <div class="relative py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl">
                        <h1
                            class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-4">
                            {{ $moduleTitle ?? __('blog::front.general.blogs') }}
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-400">
                            Bilgi sayfalarımızı keşfedin
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if ($items->count() > 0)
                    <!-- Articles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($items as $item)
                            @php
                                $currentLocale = app()->getLocale();
                                $slugData = $item->getRawOriginal('slug');

                                if (is_string($slugData)) {
                                    $slugData = json_decode($slugData, true) ?: [];
                                }
                                // Slug için öncelikle mevcut dili kontrol et, yoksa fallback ama URL'de mevcut dili koru
                                if (
                                    is_array($slugData) &&
                                    isset($slugData[$currentLocale]) &&
                                    !empty($slugData[$currentLocale])
                                ) {
                                    $slug = $slugData[$currentLocale];
                                } else {
                                    // Mevcut dilde slug yoksa fallback kullan ama URL'de mevcut dili koru
                                    $slug = is_array($slugData) ? $slugData['tr'] ?? reset($slugData) : $slugData;
                                }
                                $slug = $slug ?: $item->blog_id;

                                $title =
                                    $item->getTranslated('title') ??
                                    ($item->getRawOriginal('title') ?? ($item->title ?? 'Başlıksız'));

                                $showSlug = \App\Services\ModuleSlugService::getSlug('Blog', 'show');
                                // Locale-aware URL
                                $defaultLocale = get_tenant_default_locale();
                                if ($currentLocale === $defaultLocale) {
                                    $dynamicUrl = '/' . $showSlug . '/' . $slug;
                                } else {
                                    $dynamicUrl = '/' . $currentLocale . '/' . $showSlug . '/' . $slug;
                                }

                                $publishedDate = $item->published_at
                                    ? $item->published_at->translatedFormat('d M Y')
                                    : $item->created_at->translatedFormat('d M Y');
                                $readingTime = $item->calculateReadingTime($currentLocale);
                                $excerpt = $item->getTranslated('excerpt', $currentLocale)
                                    ?? \Illuminate\Support\Str::limit(strip_tags($item->getTranslated('body', $currentLocale) ?? ''), 160);
                            @endphp

                            <article
                                class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700"
                                @click="navigate('{{ $dynamicUrl }}')"
                                @mouseenter="prefetch('{{ $dynamicUrl }}'); hover = {{ $loop->index }}"
                                @mouseleave="hover = null" x-data="{ localHover: false }" @mouseenter.self="localHover = true"
                                @mouseleave.self="localHover = false">

                                <!-- Gradient Overlay -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>

                                <div class="relative p-6 cursor-pointer">

                                    @if($item->is_featured)
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-yellow-400 to-orange-400 text-gray-900 shadow-sm">
                                            Öne Çıkan
                                        </span>
                                    @endif

                                    <!-- Meta info -->
                                    <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-4 mt-4">
                                        <div class="flex items-center">
                                            <i class="fa-regular fa-calendar-days mr-1"></i>
                                            {{ $publishedDate }}
                                        </div>

                                        @if($readingTime)
                                        <div class="flex items-center" data-reading-base="{{ $readingTime }}">
                                            <i class="fa-regular fa-clock mr-1"></i>
                                            <span class="reading-time-text">{{ $readingTime }} dk okuma</span>
                                        </div>
                                        @endif

                                        @if($item->category)
                                            <div class="flex items-center">
                                                <i class="fa-solid fa-tag mr-1"></i>
                                                {{ $item->category->getTranslated('name', $currentLocale) }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Title with hover effect -->
                                    <h2
                                        class="text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-purple-600 group-hover:bg-clip-text transition-all duration-300">
                                        {{ $title }}
                                    </h2>

                                    <!-- Description -->
                                    @if ($excerpt)
                                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 line-clamp-3">
                                            {{ \Illuminate\Support\Str::limit($excerpt, 140) }}
                                        </p>
                                    @endif

                                    @php
                                        $postTags = $item->tag_list ?? [];
                                    @endphp
                                    @if(!empty($postTags))
                                        <div class="flex flex-wrap gap-2 mb-6">
                                            @foreach(array_slice($postTags, 0, 4) as $tagName)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-200 rounded-full">
                                                    <i class="fa-solid fa-hashtag"></i>{{ $tagName }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Read More with Alpine animation -->
                                    <div class="inline-flex items-center text-sm font-semibold text-transparent bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text group-hover:from-blue-700 group-hover:to-purple-700 transition-all duration-300"
                                        x-show="true" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-75 transform translate-x-0"
                                        x-transition:enter-end="opacity-100 transform translate-x-0">
                                        {{ __('blog::front.general.read_more') }}
                                        <i class="fa-solid fa-arrow-right ml-2 text-blue-600 group-hover:text-purple-600 transition-all duration-300" :class="localHover ? 'translate-x-1' : 'translate-x-0'"></i>
                                    </div>

                                    <!-- Hover Border Effect -->
                                    <div
                                        class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 to-purple-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                                    </div>
                                </div>
                            </article>
                        @endforeach
                        @php
                            // Foreach döngüsü sonrası $item değişkenini temizle
                            // Böylece header.blade.php'de yanlış kullanılmaz
                            unset($item);
                        @endphp
                    </div>

                    <!-- Pagination -->
                    @if ($items->hasPages())
                        <div class="mt-20">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                                {{ $items->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-20" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                        <div x-show="show" x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100" class="inline-block">
                            <div
                                class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <i class="fa-regular fa-file-lines text-3xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Henüz sayfa yok</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                {{ __('blog::front.general.no_blogs_found') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function blogsList() {
            return {
                loaded: false,
                hover: null,
                prefetchedUrls: new Set(),

                init() {
                    // Smooth fade in
                    this.$nextTick(() => {
                        this.loaded = true;
                        this.randomizeReadingTimes();
                    });
                },

                prefetch(url) {
                    if (this.prefetchedUrls.has(url)) return;

                    const link = document.createElement('link');
                    link.rel = 'prefetch';
                    link.href = url;
                    document.head.appendChild(link);
                    this.prefetchedUrls.add(url);
                },

                navigate(url) {
                    // Add a subtle loading state
                    document.body.style.cursor = 'wait';
                    window.location.href = url;
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
                            const target = el.querySelector('.reading-time-text');

                            if (target) {
                                target.textContent = `~${minutes} dk okuma`;
                            }
                        });
                    });
                }
            }
        }
    </script>
@endsection
