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

                        @if($hasChildren)
                            {{-- Ana kategori + alt kategoriler dropdown --}}
                            <div class="relative flex-shrink-0" x-data="{ open: false }">
                                <button @click="open = !open"
                                        @click.away="open = false"
                                        class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all flex items-center gap-2 {{ $isActive ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                                    {{ $category->getTranslated('title', app()->getLocale()) }}
                                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                {{-- Dropdown --}}
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-1"
                                     class="absolute top-full left-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-white/10 py-2 z-50"
                                     style="display: none;">
                                    {{-- Ana kategori linki --}}
                                    <a href="{{ url('/blog/category/' . $categorySlug) }}"
                                       class="block px-4 py-2.5 text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                                        <i class="fa-solid fa-folder mr-2 text-blue-500"></i>
                                        Tümü
                                        @if($category->blogs_count > 0)
                                            <span class="text-xs opacity-60 ml-1">({{ $category->blogs_count }})</span>
                                        @endif
                                    </a>

                                    <div class="border-t border-gray-200 dark:border-white/10 my-1"></div>

                                    {{-- Alt kategoriler --}}
                                    @foreach($category->children as $child)
                                        @php
                                            $childSlug = $child->getTranslated('slug', app()->getLocale());
                                            $isChildActive = isset($selectedCategory) && $selectedCategory && $selectedCategory->category_id === $child->category_id;
                                        @endphp
                                        <a href="{{ url('/blog/category/' . $childSlug) }}"
                                           class="block px-4 py-2.5 text-sm {{ $isChildActive ? 'text-blue-600 dark:text-blue-400 font-semibold bg-blue-50 dark:bg-blue-900/20' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10' }} transition-colors">
                                            {{ $child->getTranslated('title', app()->getLocale()) }}
                                            @if($child->blogs_count > 0)
                                                <span class="text-xs opacity-60 ml-1">({{ $child->blogs_count }})</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Alt kategorisi olmayan ana kategori --}}
                            <a href="{{ url('/blog/category/' . $categorySlug) }}"
                               class="flex-shrink-0 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all {{ $isActive ? 'bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white shadow-lg' : 'bg-white dark:bg-white/5 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-white/10 hover:border-blue-300 dark:hover:border-blue-400' }}">
                                {{ $category->getTranslated('title', app()->getLocale()) }}
                                @if($category->blogs_count > 0)
                                    <span class="ml-1.5 text-xs opacity-70">({{ $category->blogs_count }})</span>
                                @endif
                            </a>
                        @endif
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
                                $excerpt = $item->getTranslated('excerpt', $currentLocale) ?: \Illuminate\Support\Str::limit(strip_tags($body), 150);

                                // Module slug service kullanarak dinamik slug prefix al
                                $moduleSlugService = app(\App\Services\ModuleSlugService::class);
                                $slugPrefix = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
                                $defaultLocale = get_tenant_default_locale();
                                $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
                                $blogUrl = $localePrefix . '/' . $slugPrefix . '/' . $slug;

                                // Featured image'i al (thumbmaker ile optimize)
                                $featuredMedia = $item->getFirstMedia('featured_image');
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
                                            <i class="fa-regular fa-calendar mr-1"></i>
                                            {{ $item->created_at->format('d.m.Y') }}
                                        </span>
                                        <span class="text-blue-600 dark:text-blue-400 font-medium group-hover:translate-x-1 transition-transform">
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
                        rootMargin: '0px 0px 600px 0px'
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
