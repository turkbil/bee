@php
    use Illuminate\Support\Str;

    $currentLocale = app()->getLocale();
    $defaultLocale = get_tenant_default_locale();

    $title = $item->getTranslated('title', $currentLocale);
    $body = $item->getTranslated('body', $currentLocale);
    $excerpt = $item->getTranslated('excerpt', $currentLocale) ?: Str::limit(strip_tags($body ?? ''), 180);

    $bodyWithAnchors = \App\Services\TocService::addHeadingAnchors($body ?? '');
    $toc = \App\Services\TocService::generateToc($body ?? '');

    // Section başlıklarını TOC'ye ekle
    $hasFaq = !empty($item->faq_data);
    $hasHowto = !empty($item->howto_data);
    $hasRelated = method_exists($item, 'relatedBlogs') && $item->relatedBlogs()->count() > 0;

    if ($hasFaq) {
        $toc[] = ['id' => 'sik-sorulan-sorular', 'text' => 'Sık Sorulan Sorular', 'level' => 2, 'children' => []];
    }
    if ($hasHowto) {
        // HowTo'nun gerçek başlığını al
        $howtoData = is_string($item->howto_data) ? json_decode($item->howto_data, true) : $item->howto_data;
        $howtoTitle = is_array($howtoData['name'] ?? null) ? ($howtoData['name'][$currentLocale] ?? 'Nasıl Yapılır') : ($howtoData['name'] ?? 'Nasıl Yapılır');
        $toc[] = ['id' => 'nasil-yapilir', 'text' => $howtoTitle, 'level' => 2, 'children' => []];
    }
    $toc[] = ['id' => 'yorumlar-ve-degerlendirmeler', 'text' => 'Yorumlar ve Değerlendirmeler', 'level' => 2, 'children' => []];
    $toc[] = ['id' => 'yazi-paylas', 'text' => 'Yazıyı Paylaş', 'level' => 2, 'children' => []];
    $toc[] = ['id' => 'yazar-bilgileri', 'text' => 'Yazar Bilgileri', 'level' => 2, 'children' => []];
    if ($hasRelated) {
        $toc[] = ['id' => 'diger-yazilar', 'text' => 'Diğer Yazılar', 'level' => 2, 'children' => []];
    }

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
    $categoryName = $item->category ? $item->category->getTranslated('title', $currentLocale) : null;

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

{{-- Glass Subheader Component --}}
@php
    $breadcrumbsArray = [
        ['label' => __('blog::front.general.home'), 'url' => url('/'), 'icon' => 'fa-home'],
        ['label' => __('blog::front.general.blogs'), 'url' => url($blogIndexUrl)]
    ];
    if($categoryName) {
        $breadcrumbsArray[] = ['label' => $categoryName];
    }
    $breadcrumbsArray[] = ['label' => $title];
@endphp

@include('themes.ixtif.layouts.partials.glass-subheader', [
    'title' => $title,
    'icon' => 'fa-solid fa-newspaper',
    'breadcrumbs' => $breadcrumbsArray
])

<div class="min-h-screen bg-white dark:bg-gray-900 pb-20 lg:pb-8">
    <div class="container mx-auto px-4 sm:px-4 md:px-2 py-8 md:py-12">

        {{-- Hero Section: Başlık + Kategori + Featured Image + Etiketler --}}
        {{-- Kategori Badge + Etiketler (Header dışında, üstte) --}}
        @if($categoryName || $tags->isNotEmpty())
            <div class="flex flex-wrap items-center gap-3 mb-6">
                @if($categoryName)
                    <x-blog.category-badge :category="$categoryName" size="md" />
                @endif

                @if($tags->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($tags->take(3) as $tag)
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
                               class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 transition-all duration-300 shadow-sm hover:shadow-md"
                               rel="tag"
                               title="{{ $tagName }} etiketli yazılar">
                                <i class="fas fa-hashtag text-blue-500"></i>{{ $tagName }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-6">
            {{-- Sol Sidebar (Sadece TOC - Mobilde Gizli) --}}
            <aside class="hidden lg:block order-2 lg:order-1">
                @if(!empty($toc))
                    <div class="lg:sticky lg:top-24">
                        <x-blog.toc :toc="$toc" title="İçindekiler" :count="$totalHeadings" />
                    </div>
                @endif
            </aside>

            {{-- Ana İçerik --}}
            <article class="order-1 lg:order-2 {{ !empty($toc) ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                {{-- Meta Card: Tarih + Stats (Yeniden Kodlandı - Hatasız) --}}
                <div class="bg-transparent rounded-xl px-3 py-3 md:p-6 border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="flex flex-wrap md:flex-nowrap items-center justify-between gap-2 md:gap-4">

                        {{-- Tarih --}}
                        <div class="flex items-center gap-1.5 md:gap-2 text-gray-600 dark:text-gray-400">
                            <i class="fas fa-calendar text-blue-500 dark:text-blue-400 text-sm md:text-base"></i>
                            <span class="text-xs md:text-sm font-medium hidden sm:inline">{{ $publishedDate }}</span>
                            <span class="text-xs font-medium sm:hidden">{{ ($item->published_at ?? $item->created_at)->format('d.m.Y') }}</span>
                        </div>

                        {{-- Sağ Taraf: Okuma + Favori + Rating --}}
                        <div class="flex flex-wrap items-center gap-2 md:gap-6">

                            {{-- Okuma Süresi --}}
                            <div class="flex items-center gap-1.5 md:gap-2 text-gray-600 dark:text-gray-400">
                                <i class="fas fa-clock text-blue-500 dark:text-blue-400 text-sm md:text-base"></i>
                                <span class="text-xs md:text-sm font-medium">{{ $readingTime }}<span class="hidden sm:inline"> dk</span></span>
                            </div>

                            {{-- Favori Butonu (Guest/Auth Aware) --}}
                            @auth
                                <div class="flex items-center gap-1.5 md:gap-2 cursor-pointer hover:scale-110 transition-transform duration-200"
                                     x-data="{
                                         favorited: {{ $item->isFavoritedBy(auth()->id()) ? 'true' : 'false' }},
                                         count: {{ $item->favoritesCount() ?? 0 }},
                                         loading: false,
                                         async toggleFavorite() {
                                             if (this.loading) return;
                                             this.loading = true;
                                             try {
                                                 const response = await fetch('/api/favorites/toggle', {
                                                     method: 'POST',
                                                     headers: {
                                                         'Content-Type': 'application/json',
                                                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                                         'Accept': 'application/json'
                                                     },
                                                     body: JSON.stringify({
                                                         model_class: '{{ addslashes(get_class($item)) }}',
                                                         model_id: {{ $item->id }}
                                                     })
                                                 });
                                                 const data = await response.json();
                                                 if (data.success) {
                                                     this.favorited = data.data.is_favorited;
                                                     this.count = data.data.favorites_count;
                                                 }
                                             } catch (error) {
                                                 console.error('Favorite error:', error);
                                             } finally {
                                                 this.loading = false;
                                             }
                                         }
                                     }"
                                     @click="toggleFavorite()">
                                    <i x-bind:class="favorited ? 'fas fa-heart text-red-500' : 'far fa-heart text-gray-600 dark:text-gray-400'"
                                       class="text-sm md:text-lg transition-colors"></i>
                                    <span class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400"
                                          x-text="count"></span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 md:gap-2 text-gray-400 dark:text-gray-500 cursor-pointer"
                                     @click="window.location.href = '{{ route('login') }}'">
                                    <i class="far fa-heart text-sm md:text-lg"></i>
                                    <span class="text-xs md:text-sm font-medium">{{ $item->favoritesCount() ?? 0 }}</span>
                                </div>
                            @endauth

                            {{-- Rating Component (Guest/Auth Aware) --}}
                            @php
                                $averageRating = method_exists($item, 'averageRating') ? $item->averageRating() : 0;
                                $ratingsCount = method_exists($item, 'ratingsCount') ? $item->ratingsCount() : 0;
                                $currentUserRating = auth()->check() && method_exists($item, 'userRating') ? $item->userRating(auth()->id()) : 0;
                            @endphp

                            <div class="flex items-center gap-1.5 md:gap-2"
                                 x-data="{
                                     modelClass: '{{ addslashes(get_class($item)) }}',
                                     modelId: {{ $item->id }},
                                     currentRating: {{ $currentUserRating }},
                                     averageRating: {{ $averageRating }},
                                     ratingsCount: {{ $ratingsCount }},
                                     isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
                                     hoverRating: 0,
                                     showMessage: false,
                                     message: '',
                                     messageType: 'info',

                                     handleStarClick(value) {
                                         if (!this.isAuthenticated) {
                                             window.location.href = '{{ route('login') }}';
                                             return;
                                         }
                                         this.rateItem(value);
                                     },

                                     async rateItem(value) {
                                         try {
                                             const response = await fetch('/api/reviews/rating', {
                                                 method: 'POST',
                                                 headers: {
                                                     'Content-Type': 'application/json',
                                                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                                     'Accept': 'application/json'
                                                 },
                                                 body: JSON.stringify({
                                                     model_class: this.modelClass,
                                                     model_id: this.modelId,
                                                     rating_value: value
                                                 })
                                             });
                                             const data = await response.json();
                                             if (data.success) {
                                                 this.currentRating = value;
                                                 this.averageRating = parseFloat(data.data.average_rating);
                                                 this.ratingsCount = parseInt(data.data.ratings_count);
                                                 this.showToast('Puanınız kaydedildi! ⭐', 'success');
                                             } else {
                                                 this.showToast(data.message || 'Bir hata oluştu', 'error');
                                             }
                                         } catch (error) {
                                             console.error('Rating error:', error);
                                             this.showToast('Bir hata oluştu', 'error');
                                         }
                                     },

                                     showToast(msg, type) {
                                         this.message = msg;
                                         this.messageType = type;
                                         this.showMessage = true;
                                         setTimeout(() => { this.showMessage = false; }, 3000);
                                     },

                                     getStarClass(star) {
                                         const rating = this.hoverRating > 0 ? this.hoverRating : (this.currentRating || this.averageRating);
                                         return star <= rating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300 dark:text-gray-600';
                                     }
                                 }">

                                {{-- Yıldızlar --}}
                                <div class="flex items-center gap-0.5" @mouseleave="hoverRating = 0">
                                    <template x-for="star in 5" :key="star">
                                        <button type="button"
                                                @click="handleStarClick(star)"
                                                @mouseenter="hoverRating = star"
                                                class="text-xs md:text-sm cursor-pointer hover:scale-110 transition-all duration-150">
                                            <i x-bind:class="getStarClass(star)"></i>
                                        </button>
                                    </template>
                                </div>

                                {{-- Ortalama ve Sayı --}}
                                <div class="flex items-center gap-0.5 md:gap-1 text-xs md:text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold" x-text="averageRating.toFixed(1)"></span>
                                    <span class="text-xs opacity-75 hidden sm:inline">(<span x-text="ratingsCount"></span>)</span>
                                </div>

                                {{-- Toast Message --}}
                                <div x-show="showMessage"
                                     x-transition
                                     class="absolute top-full mt-2 right-0 px-4 py-2 rounded-lg text-sm font-medium shadow-lg"
                                     x-bind:class="{
                                         'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': messageType === 'success',
                                         'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': messageType === 'error'
                                     }">
                                    <span x-text="message"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Excerpt (Büyük, Dikkat Çekici) --}}
                @if($excerpt)
                    <p class="text-xl md:text-2xl leading-relaxed text-gray-700 dark:text-gray-300 font-light italic mb-8 pl-4 border-l-4 border-blue-500">
                        {{ $excerpt }}
                    </p>
                @endif

                {{-- Featured Image (Article'ın en üstünde - Thumbmaker optimized) --}}
                @if($featuredImage)
                    <figure class="overflow-hidden rounded-xl shadow-lg mb-8">
                        <a href="{{ $featuredImage->getUrl() }}"
                           class="glightbox block overflow-hidden"
                           data-gallery="blog-featured"
                           data-title="{{ $featuredImage->getCustomProperty('title')[$currentLocale] ?? '' }}"
                           data-description="{{ $featuredImage->getCustomProperty('description')[$currentLocale] ?? '' }}">
                            <img src="{{ thumb($featuredImage, 1200, 800, ['quality' => 90, 'format' => 'webp']) }}"
                                 alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                 width="1200"
                                 height="800"
                                 loading="eager"
                                 class="w-full h-auto max-h-[500px] object-cover cursor-pointer transition-transform duration-300 hover:scale-105">
                        </a>
                        @if($featuredImage->getCustomProperty('title')[$currentLocale] ?? false)
                            <figcaption class="bg-gray-100 dark:bg-gray-800 p-4 border-t border-gray-200 dark:border-gray-700">
                                <strong class="block font-semibold text-gray-900 dark:text-white mb-1 text-base">
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

                {{-- Body Content --}}
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
                          prose-li:text-gray-700 dark:prose-li:text-gray-300
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

                        // 🎨 POST-PROCESSING: Görsellere lazy loading + Thumbmaker
                        $parsedBody = process_blog_images($parsedBody);
                    @endphp
                    {!! Blade::render($parsedBody, [], true) !!}
                </div>

                {{-- FAQ Section --}}
                @if(!empty($item->faq_data))
                    @php
                        $faqData = is_string($item->faq_data) ? json_decode($item->faq_data, true) : $item->faq_data;
                        $hasValidFaq = false;
                        if (!empty($faqData) && is_array($faqData)) {
                            foreach ($faqData as $faq) {
                                $question = is_array($faq['question'] ?? null) ? ($faq['question'][$currentLocale] ?? '') : ($faq['question'] ?? '');
                                $answer = is_array($faq['answer'] ?? null) ? ($faq['answer'][$currentLocale] ?? '') : ($faq['answer'] ?? '');
                                if (!empty($question) && !empty($answer)) {
                                    $hasValidFaq = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($hasValidFaq)
                        <section id="sik-sorulan-sorular" class="mt-16 md:mt-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 shadow-xl border border-blue-100 dark:border-gray-600" itemscope itemtype="https://schema.org/FAQPage">
                            <header class="mb-8">
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                    <i class="fas fa-question-circle text-blue-600 dark:text-blue-400 mr-3"></i>
                                    {{ __('blog::front.general.faq_title') }}
                                </h2>
                                <div class="h-1 w-16 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
                            </header>
                            <div class="space-y-4">
                                @foreach($faqData as $index => $faq)
                                    @php
                                        $question = is_array($faq['question'] ?? null) ? ($faq['question'][$currentLocale] ?? '') : ($faq['question'] ?? '');
                                        $answer = is_array($faq['answer'] ?? null) ? ($faq['answer'][$currentLocale] ?? '') : ($faq['answer'] ?? '');
                                        $faqIcon = $faq['icon'] ?? 'fas fa-question-circle';
                                    @endphp
                                    @if(!empty($question) && !empty($answer))
                                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:shadow-xl"
                                             itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                                            <details class="group">
                                                <summary class="flex items-center justify-between w-full px-6 md:px-8 py-5 md:py-6 cursor-pointer list-none select-none hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-all duration-300">
                                                    <div class="flex items-center gap-4 flex-1">
                                                        @php
                                                            $iconClass = str_replace(['fas ', 'far ', 'fab ', 'fa-solid ', 'fa-regular '], '', $faqIcon);
                                                            $iconClass = trim($iconClass);
                                                        @endphp
                                                        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 flex items-center justify-center shadow-lg transition-all duration-500 group-hover:scale-110 group-hover:rotate-12 group-hover:shadow-2xl">
                                                            <i class="fa-light {{ $iconClass }} group-hover:fa-solid text-white text-2xl transition-all duration-300 group-hover:scale-110"></i>
                                                        </div>
                                                        <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white pr-4 transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400" itemprop="name">
                                                            {{ $question }}
                                                        </h3>
                                                    </div>
                                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 transition-transform group-open:rotate-180 duration-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </summary>
                                                <div class="px-6 md:px-8 pb-6 md:pb-8 pt-2 text-gray-700 dark:text-gray-300 prose prose-base md:prose-lg dark:prose-invert max-w-none pl-20 md:pl-24"
                                                     itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                                                    <div itemprop="text">
                                                        {!! nl2br(e($answer)) !!}
                                                    </div>
                                                </div>
                                            </details>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endif

                {{-- HowTo Section --}}
                @if(!empty($item->howto_data))
                    @php
                        $howtoData = is_string($item->howto_data) ? json_decode($item->howto_data, true) : $item->howto_data;
                        $howtoName = is_array($howtoData['name'] ?? null) ? ($howtoData['name'][$currentLocale] ?? '') : ($howtoData['name'] ?? '');
                        $howtoDesc = is_array($howtoData['description'] ?? null) ? ($howtoData['description'][$currentLocale] ?? '') : ($howtoData['description'] ?? '');
                    @endphp
                    @if(!empty($howtoData) && is_array($howtoData) && !empty($howtoData['steps']) && !empty($howtoName))
                        <section id="nasil-yapilir" class="mt-16 md:mt-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 shadow-xl border border-blue-100 dark:border-gray-600" itemscope itemtype="https://schema.org/HowTo">
                            <header class="mb-8">
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3" itemprop="name">
                                    <i class="fas fa-tasks text-blue-600 dark:text-blue-400 mr-3"></i>
                                    {{ $howtoName }}
                                </h2>
                                <div class="h-1 w-16 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
                                @if(!empty($howtoDesc))
                                    <p class="mt-4 text-base text-gray-600 dark:text-gray-400" itemprop="description">
                                        {{ $howtoDesc }}
                                    </p>
                                @endif
                            </header>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                @foreach($howtoData['steps'] as $index => $step)
                                    @php
                                        $stepName = is_array($step['name'] ?? null) ? ($step['name'][$currentLocale] ?? '') : ($step['name'] ?? '');
                                        $stepText = is_array($step['text'] ?? null) ? ($step['text'][$currentLocale] ?? '') : ($step['text'] ?? '');
                                        $stepIcon = $step['icon'] ?? 'fas fa-check-circle';
                                    @endphp
                                    @if(!empty($stepName))
                                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 hover:shadow-2xl transition-all duration-500"
                                             itemscope itemprop="step" itemtype="https://schema.org/HowToStep">
                                            {{-- Step Number Badge --}}
                                            <div class="absolute -top-3 -left-3 w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shadow-sm opacity-60 transition-all duration-300 group-hover:opacity-80 group-hover:scale-110">
                                                <span class="text-gray-600 dark:text-gray-400 font-semibold text-sm">{{ $index + 1 }}</span>
                                            </div>

                                            {{-- Icon --}}
                                            <div class="flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 mx-auto mb-6 shadow-lg transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-2xl">
                                                <i class="{{ $stepIcon }} text-4xl text-white transition-transform duration-300 group-hover:scale-110"></i>
                                            </div>

                                            {{-- Title --}}
                                            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center leading-tight transition-colors duration-300 group-hover:text-blue-600 dark:group-hover:text-blue-400" itemprop="name">
                                                {{ $stepName }}
                                            </h3>

                                            {{-- Description --}}
                                            @if(!empty($stepText))
                                                <div class="text-base text-gray-600 dark:text-gray-400 leading-relaxed text-center transition-all duration-300 group-hover:text-gray-900 dark:group-hover:text-gray-200" itemprop="text">
                                                    {!! nl2br(e($stepText)) !!}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endif

                {{-- Review & Rating Section --}}
                <section id="yorumlar-ve-degerlendirmeler" class="mt-16 md:mt-20">
                    <x-blog.review-section :blog="$item" />
                </section>

                {{-- Social Share (Article Bottom) --}}
                <div id="yazi-paylas" class="mt-16 md:mt-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 shadow-xl border border-blue-100 dark:border-gray-600">
                    <h3 class="text-2xl md:text-3xl font-bold mb-8 text-gray-900 dark:text-white flex items-center gap-3">
                        <i class="fas fa-share-alt text-blue-500"></i>
                        Bu yazıyı paylaş
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="https://wa.me/?text={{ urlencode($title) }}%20{{ urlencode($shareUrl) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-medium transition">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($title) }}&url={{ urlencode($shareUrl) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-900 hover:bg-black text-white font-medium transition">
                            <i class="fab fa-x-twitter"></i>
                            Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white font-medium transition">
                            <i class="fab fa-linkedin-in"></i>
                            LinkedIn
                        </a>
                    </div>
                </div>

                {{-- Author Card (Detaylı) --}}
                <section id="yazar-bilgileri" class="mt-16 md:mt-20">
                    <x-blog.author-card variant="full" />
                </section>

                {{-- İlgili Yazılar --}}
                @if($relatedBlogs->isNotEmpty())
                    <section id="diger-yazilar" class="mt-16 md:mt-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 shadow-xl border border-blue-100 dark:border-gray-600">
                        <header class="mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                Bunları da Beğenebilirsin
                            </h2>
                            <div class="h-1 w-16 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
                        </header>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($relatedBlogs as $relatedBlog)
                                <article class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                                    @if($relatedBlog->getFirstMedia('featured_image'))
                                        @php
                                            $relatedImage = $relatedBlog->getFirstMedia('featured_image');
                                        @endphp
                                        <a href="{{ $relatedBlog->getUrl($currentLocale) }}" class="block overflow-hidden">
                                            <img src="{{ thumb($relatedImage, 400, 300, ['quality' => 85, 'format' => 'webp']) }}"
                                                 alt="{{ $relatedBlog->getTranslated('title', $currentLocale) }}"
                                                 width="400"
                                                 height="300"
                                                 loading="lazy"
                                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        </a>
                                    @endif
                                    <a href="{{ $relatedBlog->getUrl($currentLocale) }}" class="block">
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

                {{-- Galeri --}}
                @if($galleryImages->count() > 0)
                    <section class="mt-16 md:mt-20 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 md:p-8 shadow-xl border border-blue-100 dark:border-gray-600">
                        <header class="mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                {{ __('mediamanagement::admin.gallery') }}
                            </h2>
                            <div class="h-1 w-16 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
                        </header>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                            @foreach($galleryImages as $image)
                                <figure class="group relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300">
                                    <a href="{{ $image->getUrl() }}"
                                       class="glightbox block overflow-hidden"
                                       data-gallery="blog-gallery"
                                       data-title="{{ $image->getCustomProperty('title')[$currentLocale] ?? '' }}"
                                       data-description="{{ $image->getCustomProperty('description')[$currentLocale] ?? '' }}">
                                        <img src="{{ thumb($image, 400, 300, ['quality' => 85, 'format' => 'webp']) }}"
                                             alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                             width="400"
                                             height="300"
                                             loading="lazy"
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

                {{-- Önceki/Sonraki --}}
                @if($prevPost || $nextPost)
                    <nav class="mt-16 md:mt-20 grid gap-8 md:grid-cols-2">
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

                <footer class="mt-16 md:mt-20">
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

        [x-cloak] {
            display: none !important;
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
