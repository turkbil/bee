@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="blogsInfiniteScroll()" x-init="init()">

        {{-- Categories Slider (Shop tarzı) --}}
        @if(isset($categories) && $categories->count() > 0)
        <section class="py-6 border-b border-gray-200 dark:border-white/10 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm">
            <div class="container mx-auto px-4 sm:px-4 md:px-2">
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

        {{-- Alt Kategoriler (Sadece kategori seçiliyse ve alt kategorileri varsa) --}}
        @if(isset($selectedCategory) && $selectedCategory && $selectedCategory->children && $selectedCategory->children->count() > 0)
        <section class="py-8 border-b border-gray-200 dark:border-white/10">
            <div class="container mx-auto px-4 sm:px-4 md:px-2">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-folder-tree text-blue-600 dark:text-blue-400"></i>
                    {{ $selectedCategory->getTranslated('title', app()->getLocale()) }} - Alt Kategoriler
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($selectedCategory->children as $child)
                        @php
                            $childSlug = $child->getTranslated('slug', app()->getLocale());
                            $childTitle = $child->getTranslated('title', app()->getLocale());
                        @endphp
                        <a href="{{ url('/blog/category/' . $childSlug) }}"
                           class="group relative overflow-hidden bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400 transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="relative z-10">
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $childTitle }}
                                </h3>
                            </div>
                            <div class="absolute -bottom-4 -right-4 text-6xl text-blue-200 dark:text-white/5 opacity-50 group-hover:opacity-100 transition-opacity">
                                <i class="fa-solid fa-folder"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <div class="py-12">
            <div class="container mx-auto px-4 sm:px-4 md:px-2">
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
                                            <span onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('login') }}'"
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
