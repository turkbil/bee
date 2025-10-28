@php
    use Illuminate\Support\Str;

    $currentLocale = app()->getLocale();
    $defaultLocale = get_tenant_default_locale();

    $title = $item->getTranslated('title', $currentLocale);
    $body = $item->getTranslated('body', $currentLocale);
    $excerpt = $item->getTranslated('excerpt', $currentLocale) ?: Str::limit(strip_tags($body ?? ''), 180);

    $bodyWithAnchors = \App\Services\TocService::addHeadingAnchors($body ?? '');
    $toc = \App\Services\TocService::generateToc($body ?? '');

    $countTocItems = function(array $items) use (&$countTocItems): int {
        $sum = 0;
        foreach ($items as $entry) {
            $sum++;
            if (!empty($entry['children'])) {
                $sum += $countTocItems($entry['children']);
            }
        }
        return $sum;
    };
    $totalHeadings = $countTocItems($toc);

    $readingTime = $item->calculateReadingTime($currentLocale);
    $publishedDate = $item->published_at
        ? $item->published_at->translatedFormat('d F Y')
        : $item->created_at->translatedFormat('d F Y');
    $categoryName = $item->category ? $item->category->getTranslated('name', $currentLocale) : null;

    $tags = $item->relationLoaded('tags') ? $item->tags : $item->tags()->get();

    $featuredImage = $item->getFirstMedia('featured_image');
    $galleryImages = $item->getMedia('gallery') ?? collect();

    $shareUrl = method_exists($item, 'getUrl') ? $item->getUrl($currentLocale) : url()->current();

    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
    $indexSlug = $moduleSlugService->getMultiLangSlug('Blog', 'index', $currentLocale);
    $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
    $blogIndexUrl = ($localePrefix ?: '') . '/' . ltrim($indexSlug ?: 'blog', '/');
    $blogIndexUrl = '/' . ltrim($blogIndexUrl, '/');

    $prevPost = \Modules\Blog\App\Models\Blog::published()
        ->where('blog_id', '<', $item->blog_id)
        ->orderBy('blog_id', 'desc')
        ->first();

    $nextPost = \Modules\Blog\App\Models\Blog::published()
        ->where('blog_id', '>', $item->blog_id)
        ->orderBy('blog_id', 'asc')
        ->first();

    $tagRouteName = $currentLocale === $defaultLocale ? 'blog.tag' : 'blog.tag.localized';

    // İlgili yazıları getir
    $relatedBlogs = $item->getRelatedBlogs(6);
@endphp

<x-blog.reading-progress target=".content-body" />

<div class="min-h-screen bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        {{-- Başlık --}}
        <header class="mb-8 md:mb-12">
            @if($categoryName)
                <div class="mb-4">
                    <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                        {{ $categoryName }}
                    </span>
                </div>
            @endif

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                {{ $title }}
            </h1>
            <div class="h-1 w-20 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full mb-6"></div>

            @if($excerpt)
                <p class="text-xl leading-relaxed text-gray-600 dark:text-gray-400 mb-8">
                    {{ $excerpt }}
                </p>
            @endif

            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-500 dark:text-gray-500">
                <span><i class="fas fa-calendar mr-1"></i>{{ $publishedDate }}</span>
                @if($readingTime)
                    <span class="reading-time-text" data-reading-base="{{ $readingTime }}"><i class="fas fa-clock mr-1"></i>{{ $readingTime }} dk okuma</span>
                @endif
                <span><i class="fas fa-user mr-1"></i>{{ setting('site_title') ?? config('app.name') }}</span>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-8 lg:gap-10">
            {{-- Sol Sidebar --}}
            <aside class="order-2 lg:order-1">
                <div class="space-y-6 lg:sticky lg:top-8">
                    {{-- Featured Image --}}
                    @if($featuredImage)
                        <figure class="overflow-hidden rounded-xl shadow-lg">
                            <a href="{{ $featuredImage->getUrl() }}"
                               class="glightbox block"
                               data-gallery="blog-featured"
                               data-title="{{ $featuredImage->getCustomProperty('title')[$currentLocale] ?? '' }}"
                               data-description="{{ $featuredImage->getCustomProperty('description')[$currentLocale] ?? '' }}">
                                <img src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                     alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                     class="w-full max-h-80 object-cover cursor-pointer transition-all duration-300">
                            </a>
                            @if($featuredImage->getCustomProperty('title')[$currentLocale] ?? false)
                                <figcaption class="bg-white dark:bg-gray-800 p-4 border-t border-gray-200 dark:border-gray-700">
                                    <strong class="block font-semibold text-gray-900 dark:text-white mb-1">
                                        {{ $featuredImage->getCustomProperty('title')[$currentLocale] }}
                                    </strong>
                                    @if($featuredImage->getCustomProperty('description')[$currentLocale] ?? false)
                                        <span class="block text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                            {{ $featuredImage->getCustomProperty('description')[$currentLocale] }}
                                        </span>
                                    @endif
                                </figcaption>
                            @endif
                        </figure>
                    @endif

                    {{-- TOC --}}
                    @if(!empty($toc))
                        <x-blog.toc :toc="$toc" title="İçindekiler" :count="$totalHeadings" />
                    @endif
                </div>
            </aside>

            {{-- Ana İçerik --}}
            <article class="order-1 lg:order-2 {{ !empty($toc) || $featuredImage ? 'lg:col-span-2' : 'lg:col-span-3' }} space-y-10">
                <div class="content-body prose prose-lg md:prose-xl max-w-none
                          prose-headings:font-bold prose-headings:tracking-tight prose-headings:scroll-mt-24
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
                    @php
                        // İki aşamalı render: Önce widget parse, sonra Blade render
                        $parsedBody = parse_widget_shortcodes($bodyWithAnchors ?? '');
                    @endphp
                    {!! Blade::render($parsedBody, [], true) !!}
                </div>

                {{-- Galeri --}}
                @if($galleryImages->count() > 0)
                    <section class="mt-16 md:mt-20 pt-12 border-t-2 border-gray-200 dark:border-gray-700">
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
                                       class="glightbox"
                                       data-gallery="blog-gallery"
                                       data-title="{{ $image->getCustomProperty('title')[$currentLocale] ?? '' }}"
                                       data-description="{{ $image->getCustomProperty('description')[$currentLocale] ?? '' }}">
                                        <img src="{{ $image->getUrl('thumb') }}"
                                             alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                             class="w-full h-48 md:h-56 object-cover cursor-pointer transition-transform duration-500 group-hover:scale-110">
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
                    </section>
                @endif

                {{-- İlgili Yazılar --}}
                @if($relatedBlogs->isNotEmpty())
                    <section class="mt-16 md:mt-20 pt-12 border-t-2 border-gray-200 dark:border-gray-700">
                        <header class="mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                Bunları da Beğenebilirsin
                            </h2>
                            <div class="h-1 w-16 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
                        </header>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($relatedBlogs as $relatedBlog)
                                <article class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                                    <a href="{{ $relatedBlog->getUrl($currentLocale) }}" class="block">
                                        @if($relatedBlog->getFirstMedia('featured_image'))
                                            <img src="{{ $relatedBlog->getFirstMedia('featured_image')->getUrl('thumb') }}"
                                                 alt="{{ $relatedBlog->getTranslated('title', $currentLocale) }}"
                                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        @endif
                                        <div class="p-6">
                                            <h3 class="font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $relatedBlog->getTranslated('title', $currentLocale) }}
                                            </h3>
                                            @if($relatedBlog->getTranslated('excerpt', $currentLocale))
                                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                                                    {{ $relatedBlog->getTranslated('excerpt', $currentLocale) }}
                                                </p>
                                            @endif
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                        <div class="mt-8 text-center">
                            <a href="{{ $blogIndexUrl }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 dark:from-blue-500 dark:to-blue-400 dark:hover:from-blue-600 dark:hover:to-blue-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                                Tüm Yazıları Gör
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </section>
                @endif

                {{-- Etiketler --}}
                @if($tags->isNotEmpty())
                    <div class="pt-8 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Etiketler:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                @php
                                    $tagName = $tag->name;
                                    $seoFriendlyTag = $tag->slug ?: \Illuminate\Support\Str::slug($tagName);
                                    $routeParameters = $currentLocale === $defaultLocale
                                        ? ['tag' => $seoFriendlyTag]
                                        : ['locale' => $currentLocale, 'tag' => $seoFriendlyTag];

                                    if (\Illuminate\Support\Facades\Route::has($tagRouteName)) {
                                        $tagUrl = route($tagRouteName, $routeParameters);
                                    } else {
                                        $fallbackPath = trim(($localePrefix ?: '') . '/blog/tag/' . $seoFriendlyTag, '/');
                                        $tagUrl = url($fallbackPath);
                                    }
                                @endphp
                                <a href="{{ $tagUrl }}"
                                   class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-800 transition-all duration-300 shadow-sm hover:shadow-md dark:bg-gradient-to-r dark:from-blue-500/20 dark:to-blue-600/20 dark:text-blue-200 dark:hover:from-blue-500/30 dark:hover:to-blue-600/30"
                                   rel="tag"
                                   title="{{ $tagName }} etiketli yazılar">
                                    <i class="fas fa-hashtag text-blue-600 dark:text-blue-300"></i>{{ $tagName }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Önceki/Sonraki --}}
                @if($prevPost || $nextPost)
                    <nav class="mt-12 grid gap-8 md:grid-cols-2">
                        @if($prevPost)
                            <a href="{{ $prevPost->getUrl($currentLocale) }}"
                               class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-500">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3 block">
                                    <i class="fas fa-arrow-left text-blue-500 mr-2"></i>Önceki Yazı
                                </span>
                                <span class="line-clamp-2 text-lg font-bold text-gray-900 dark:text-white transition-colors group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    {{ $prevPost->getTranslated('title', $currentLocale) }}
                                </span>
                            </a>
                        @endif

                        @if($nextPost)
                            <a href="{{ $nextPost->getUrl($currentLocale) }}"
                               class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 text-right transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-500">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3 block">
                                    Sonraki Yazı<i class="fas fa-arrow-right text-blue-500 ml-2"></i>
                                </span>
                                <span class="line-clamp-2 text-lg font-bold text-gray-900 dark:text-white transition-colors group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                    {{ $nextPost->getTranslated('title', $currentLocale) }}
                                </span>
                            </a>
                        @endif
                    </nav>
                @endif

                <footer class="mt-16 md:mt-20 pt-8 border-t-2 border-gray-200 dark:border-gray-700">
                    <a href="{{ $blogIndexUrl }}"
                       class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 dark:from-blue-500 dark:to-blue-400 dark:hover:from-blue-600 dark:hover:to-blue-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-arrow-left"></i>
                        <span>Tüm yazılara dön</span>
                    </a>
                </footer>
            </article>
        </div>

    </div>
</div>

@push('styles')
    <style>
        .heading-anchor {
            margin-left: 0.5rem;
            opacity: 0;
            color: #2563eb;
            font-weight: 400;
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .heading-anchor:focus-visible {
            outline: 2px solid rgba(37, 99, 235, 0.6);
            outline-offset: 4px;
            opacity: 1;
        }

        h2:hover .heading-anchor,
        h3:hover .heading-anchor,
        h4:hover .heading-anchor,
        h5:hover .heading-anchor,
        h6:hover .heading-anchor {
            opacity: 1;
        }

        html.dark .heading-anchor {
            color: #60a5fa;
        }

        /* Utility classes */
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
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-reading-base]').forEach((element) => {
                const base = parseInt(element.getAttribute('data-reading-base'), 10);
                if (!Number.isFinite(base) || base <= 0) {
                    return;
                }

                const randomFactor = 0.85 + Math.random() * 0.35;
                const minutes = Math.max(1, Math.round(base * randomFactor));
                const target = element.querySelector('.reading-time-text') || element;

                if (target) {
                    target.textContent = `~${minutes} dk okuma`;
                }
            });

            const anchors = document.querySelectorAll('.content-body .heading-anchor');
            anchors.forEach((anchor) => {
                anchor.addEventListener('click', async (event) => {
                    event.preventDefault();
                    const header = anchor.closest('h1, h2, h3, h4, h5, h6');
                    if (!header || !header.id) {
                        return;
                    }

                    const url = `${window.location.origin}${window.location.pathname}#${header.id}`;

                    try {
                        if (navigator.clipboard?.writeText) {
                            await navigator.clipboard.writeText(url);
                        } else {
                            const textarea = document.createElement('textarea');
                            textarea.value = url;
                            textarea.style.position = 'fixed';
                            textarea.style.opacity = '0';
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textarea);
                        }

                        history.replaceState(null, '', `#${header.id}`);
                        anchor.textContent = '✓';
                        anchor.classList.add('copied');

                        setTimeout(() => {
                            anchor.textContent = '#';
                            anchor.classList.remove('copied');
                        }, 1800);
                    } catch (error) {
                        window.location.hash = header.id;
                    }
                });
            });
        });
    </script>
@endpush
