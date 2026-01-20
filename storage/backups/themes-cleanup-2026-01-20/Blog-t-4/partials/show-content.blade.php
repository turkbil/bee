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
    $categoryName = $item->category ? $item->category->getTranslated('name', $currentLocale) : null;

    $tags = $item->relationLoaded('tags') ? $item->tags : $item->tags()->get();

    $featuredImage = getFirstMediaWithFallback($item);
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

{{-- MINIMAL SUBHEADER (Service gibi) --}}
@php
    $breadcrumbsArray = [
        ['label' => 'Ana Sayfa', 'url' => url('/')],
        ['label' => 'Blog', 'url' => url($blogIndexUrl)]
    ];
    if($categoryName) {
        $breadcrumbsArray[] = ['label' => $categoryName];
    }
    $breadcrumbsArray[] = ['label' => $title];
@endphp

<section class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 pt-20">
    <div class="container mx-auto px-4 sm:px-4 md:px-2 py-4">
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

<div class="min-h-screen bg-white dark:bg-gray-900 pb-20 lg:pb-8">
    <div class="container mx-auto px-4 sm:px-4 md:px-2 py-8 md:py-12">

        {{-- Kategori Badge + Etiketler --}}
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
                                <i class="fas fa-hashtag text-primary-500"></i>{{ $tagName }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-6">
            {{-- Sol Sidebar (İletişim + TOC - Mobilde Gizli) --}}
            <aside class="hidden lg:block order-2 lg:order-1">
                <div class="lg:sticky lg:top-24 space-y-4">
                    {{-- İLETİŞİM WIDGET - Dinamik Grid --}}
                    @php
                        $contactPhone = setting('contact_phone_1');
                        $contactWhatsapp = setting('contact_whatsapp_1');
                        $contactEmail = setting('contact_email_1');
                        // Email uzunsa gösterme (taşma sorunu)
                        $showEmail = $contactEmail && strlen($contactEmail) <= 25;

                        // Aktif iletişim öğelerini say
                        $contactItems = collect();
                        if($contactPhone) $contactItems->push('phone');
                        if($contactWhatsapp && function_exists('whatsapp_link')) $contactItems->push('whatsapp');
                        if($showEmail) $contactItems->push('email');
                        $contactCount = $contactItems->count();

                        // Öğe sayısına göre grid class
                        $contactGridClass = match($contactCount) {
                            1 => 'grid-cols-1',
                            2 => 'grid-cols-2',
                            3 => 'grid-cols-3',
                            default => 'grid-cols-1'
                        };
                    @endphp
                    @if($contactCount > 0)
                        <div class="contact-widget-card bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4">
                            {{-- Başlık --}}
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-headset text-primary-600 dark:text-primary-400"></i>
                                <span class="font-semibold text-gray-900 dark:text-white">İletişim</span>
                            </div>

                            {{-- Butonlar - Dinamik Grid --}}
                            <div class="grid {{ $contactGridClass }} gap-3">
                                @if($contactPhone)
                                    <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                                       class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg p-3 transition-all group">
                                        <i class="fas fa-phone text-primary-600 dark:text-primary-400"></i>
                                        <div class="min-w-0">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Telefon</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $contactPhone }}</div>
                                        </div>
                                    </a>
                                @endif

                                @if($contactWhatsapp && function_exists('whatsapp_link'))
                                    <a href="{{ whatsapp_link() }}" target="_blank"
                                       class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg p-3 transition-all group">
                                        <i class="fab fa-whatsapp text-green-600 dark:text-green-400 text-lg"></i>
                                        <div class="min-w-0">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">WhatsApp</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $contactWhatsapp }}</div>
                                        </div>
                                    </a>
                                @endif

                                @if($showEmail)
                                    <a href="mailto:{{ $contactEmail }}"
                                       class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg p-3 transition-all group">
                                        <i class="fas fa-envelope text-red-600 dark:text-red-400"></i>
                                        <div class="min-w-0">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">E-posta</div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $contactEmail }}</div>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- İçindekiler (TOC) --}}
                    @if(!empty($toc))
                        <x-blog.toc :toc="$toc" title="İçindekiler" :count="$totalHeadings" />
                    @endif
                </div>
            </aside>

            {{-- Ana İçerik --}}
            <article class="order-1 lg:order-2 {{ !empty($toc) ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                {{-- Meta Card: Tarih + Stats --}}
                <div class="bg-transparent rounded-xl px-3 py-3 md:p-6 border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="flex flex-wrap md:flex-nowrap items-center justify-between gap-2 md:gap-4">

                        {{-- Tarih --}}
                        <div class="flex items-center gap-1.5 md:gap-2 text-gray-600 dark:text-gray-400"
                             title="{{ ($item->published_at ?? $item->created_at)->translatedFormat('d F Y - H:i') }}">
                            <i class="fas fa-calendar text-primary-500 dark:text-primary-400 text-sm md:text-base"></i>
                            <span class="text-xs md:text-sm font-medium hidden sm:inline">{{ $publishedDate }}</span>
                            <span class="text-xs font-medium sm:hidden">{{ ($item->published_at ?? $item->created_at)->format('d.m.Y') }}</span>
                        </div>

                        {{-- Sağ Taraf: Okuma + Favori + Rating --}}
                        <div class="flex flex-wrap items-center gap-2 md:gap-6">

                            {{-- Okuma Süresi --}}
                            <div class="flex items-center gap-1.5 md:gap-2 text-gray-600 dark:text-gray-400">
                                <i class="fas fa-clock text-primary-500 dark:text-primary-400 text-sm md:text-base"></i>
                                <span class="text-xs md:text-sm font-medium">{{ $readingTime }}<span class="hidden sm:inline"> dk</span></span>
                            </div>

                            {{-- Favori Butonu (Guest/Auth Aware) --}}
                            @auth
                                <div class="flex items-center gap-1.5 md:gap-2 cursor-pointer hover:scale-110 transition-transform duration-200"
                                     x-data="favoriteButton('{{ addslashes(get_class($item)) }}', {{ $item->id }}, {{ $item->isFavoritedBy(auth()->id()) ? 'true' : 'false' }}, {{ $item->favoritesCount() ?? 0 }})"
                                     @click="toggleFavorite()">
                                    <i x-bind:class="favorited ? 'fas fa-heart text-red-500' : 'far fa-heart text-gray-600 dark:text-gray-400'"
                                       class="text-sm md:text-lg transition-colors"></i>
                                    <span class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400"
                                          x-text="count"></span>
                                </div>
                            @else
                                <div class="flex items-center gap-1.5 md:gap-2 text-gray-400 dark:text-gray-500 cursor-pointer"
                                     @click="savePendingFavorite('{{ addslashes(get_class($item)) }}', {{ $item->id }}, '{{ $shareUrl }}')">
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
                                                 this.showToast('Puanınız kaydedildi!', 'success');
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

                {{-- Excerpt --}}
                @if($excerpt)
                    <p class="text-base md:text-lg leading-relaxed text-gray-600 dark:text-gray-300 font-body italic mb-6 pl-4 border-l-4 border-primary-500">
                        {{ $excerpt }}
                    </p>
                @endif

                {{-- Featured Image (Article'ın en üstünde) --}}
                @if($featuredImage)
                    <figure class="overflow-hidden rounded-xl shadow-lg mb-8">
                        <a href="{{ $featuredImage->getUrl() }}"
                           class="glightbox block overflow-hidden"
                           data-gallery="blog-featured"
                           data-title="{{ $featuredImage->getCustomProperty('title')[$currentLocale] ?? '' }}"
                           data-description="{{ $featuredImage->getCustomProperty('description')[$currentLocale] ?? '' }}">
                            <img src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                 alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
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
                <div class="content-body prose prose-base md:prose-lg max-w-none font-body dark:prose-invert
                          prose-headings:font-heading prose-headings:font-bold prose-headings:tracking-tight prose-headings:scroll-mt-24
                          prose-headings:text-gray-900 dark:prose-headings:text-gray-100
                          prose-h2:text-xl prose-h2:md:text-2xl prose-h2:mt-10 prose-h2:mb-4
                          prose-h3:text-lg prose-h3:md:text-xl prose-h3:mt-8 prose-h3:mb-3
                          prose-h4:text-base prose-h4:md:text-lg prose-h4:mt-6 prose-h4:mb-2
                          prose-p:text-gray-700 dark:prose-p:text-gray-200 prose-p:leading-relaxed prose-p:mb-4
                          prose-a:text-primary-600 dark:prose-a:text-primary-400 prose-a:no-underline hover:prose-a:underline
                          prose-a:font-medium hover:prose-a:text-primary-700 dark:hover:prose-a:text-primary-300
                          prose-strong:text-gray-900 dark:prose-strong:text-gray-100 prose-strong:font-semibold
                          prose-ul:my-4 prose-ol:my-4 prose-li:my-1.5 prose-li:leading-relaxed
                          prose-li:text-gray-700 dark:prose-li:text-gray-200
                          prose-blockquote:border-l-4 prose-blockquote:border-l-primary-500 prose-blockquote:bg-primary-50/50
                          dark:prose-blockquote:bg-gray-800 prose-blockquote:py-3 prose-blockquote:px-5
                          prose-blockquote:italic prose-blockquote:my-6 prose-blockquote:text-gray-600 dark:prose-blockquote:text-gray-300
                          prose-code:text-primary-600 dark:prose-code:text-primary-400 prose-code:bg-primary-50
                          dark:prose-code:bg-gray-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                          prose-code:font-mono prose-code:text-sm
                          prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800 prose-pre:rounded-lg
                          prose-pre:shadow-lg prose-pre:my-6
                          prose-img:rounded-xl prose-img:shadow-md prose-img:my-6
                          prose-hr:my-8 prose-hr:border-gray-200 dark:prose-hr:border-gray-700">
                    @parsewidgets($bodyWithAnchors ?? '')
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
                        <section id="sik-sorulan-sorular" class="mt-16 md:mt-20 md:bg-gradient-to-br md:from-primary-50 md:to-indigo-50 md:dark:from-gray-800 md:dark:to-gray-700 md:rounded-2xl md:p-8 md:shadow-xl md:border md:border-primary-100 md:dark:border-gray-600" itemscope itemtype="https://schema.org/FAQPage">
                            <header class="mb-8">
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                    <i class="fas fa-question-circle text-primary-600 dark:text-primary-400 mr-3"></i>
                                    {{ __('blog::front.general.faq_title') }}
                                </h2>
                                <div class="h-1 w-16 bg-gradient-to-r from-primary-600 to-primary-400 dark:from-primary-500 dark:to-primary-300 rounded-full"></div>
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
                                                <summary class="flex items-center justify-between w-full px-4 md:px-8 py-4 md:py-6 cursor-pointer list-none select-none hover:bg-primary-50 dark:hover:bg-gray-700/50 transition-all duration-300">
                                                    <div class="flex items-center gap-4 flex-1">
                                                        @php
                                                            $iconClass = str_replace(['fas ', 'far ', 'fab ', 'fa-solid ', 'fa-regular '], '', $faqIcon);
                                                            $iconClass = trim($iconClass);
                                                        @endphp
                                                        <div class="flex-shrink-0 w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-gradient-to-br from-primary-600 to-indigo-600 dark:from-primary-500 dark:to-indigo-500 flex items-center justify-center shadow-lg transition-all duration-500 group-hover:scale-110 group-hover:rotate-12 group-hover:shadow-2xl">
                                                            <i class="fa-light {{ $iconClass }} group-hover:fa-solid text-white text-xl md:text-2xl transition-all duration-300 group-hover:scale-110"></i>
                                                        </div>
                                                        <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white pr-4 transition-colors duration-300 group-hover:text-primary-600 dark:group-hover:text-primary-400" itemprop="name">
                                                            {{ $question }}
                                                        </h3>
                                                    </div>
                                                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 transition-transform group-open:rotate-180 duration-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </summary>
                                                <div class="px-4 md:px-8 pb-4 md:pb-8 pt-2 text-gray-700 dark:text-gray-300 prose prose-base md:prose-lg dark:prose-invert max-w-none pl-16 md:pl-24"
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
                        <section id="nasil-yapilir" class="mt-16 md:mt-20 md:bg-gradient-to-br md:from-primary-50 md:to-indigo-50 md:dark:from-gray-800 md:dark:to-gray-700 md:rounded-2xl md:p-8 md:shadow-xl md:border md:border-primary-100 md:dark:border-gray-600" itemscope itemtype="https://schema.org/HowTo">
                            <header class="mb-8">
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3" itemprop="name">
                                    <i class="fas fa-tasks text-primary-600 dark:text-primary-400 mr-3"></i>
                                    {{ $howtoName }}
                                </h2>
                                <div class="h-1 w-16 bg-gradient-to-r from-primary-600 to-primary-400 dark:from-primary-500 dark:to-primary-300 rounded-full"></div>
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
                                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 md:p-8 hover:shadow-2xl transition-all duration-500"
                                             itemscope itemprop="step" itemtype="https://schema.org/HowToStep">
                                            {{-- Step Number Badge --}}
                                            <div class="absolute -top-3 -left-3 w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center shadow-sm opacity-60 transition-all duration-300 group-hover:opacity-80 group-hover:scale-110">
                                                <span class="text-gray-600 dark:text-gray-400 font-semibold text-sm">{{ $index + 1 }}</span>
                                            </div>

                                            {{-- Icon --}}
                                            <div class="flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-xl md:rounded-2xl bg-gradient-to-br from-primary-600 to-indigo-600 dark:from-primary-500 dark:to-indigo-500 mx-auto mb-4 md:mb-6 shadow-lg transition-all duration-500 group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-2xl">
                                                <i class="{{ $stepIcon }} text-3xl md:text-4xl text-white transition-transform duration-300 group-hover:scale-110"></i>
                                            </div>

                                            {{-- Title --}}
                                            <h3 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mb-4 text-center leading-tight transition-colors duration-300 group-hover:text-primary-600 dark:group-hover:text-primary-400" itemprop="name">
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

                {{-- Share & Author Combined Card --}}
                @php
                    // Copy button için body hazırla
                    $rawBody = $body ?? '';
                    $rawBody = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n\n▸ $1\n", $rawBody);
                    $rawBody = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n\n• $1\n", $rawBody);
                    $rawBody = preg_replace('/<h4[^>]*>(.*?)<\/h4>/is', "\n\n◦ $1\n", $rawBody);
                    $rawBody = preg_replace('/<\/p>\s*<p[^>]*>/is', "\n\n", $rawBody);
                    $rawBody = preg_replace('/<p[^>]*>/is', "\n\n", $rawBody);
                    $rawBody = preg_replace('/<\/p>/is', "", $rawBody);
                    $rawBody = preg_replace('/<li[^>]*>/is', "\n  • ", $rawBody);
                    $rawBody = preg_replace('/<\/li>/is', "", $rawBody);
                    $rawBody = preg_replace('/<\/?[uo]l[^>]*>/is', "\n", $rawBody);
                    $rawBody = preg_replace('/<br\s*\/?>/is', "\n", $rawBody);
                    $cleanBody = strip_tags($rawBody);
                    $cleanBody = html_entity_decode($cleanBody, ENT_QUOTES, 'UTF-8');
                    $cleanBody = preg_replace('/[ \t]+/', ' ', $cleanBody);
                    $cleanBody = preg_replace('/\n{4,}/', "\n\n\n", $cleanBody);
                    $cleanBody = trim($cleanBody);

                    if (mb_strlen($cleanBody) > 1500) {
                        $searchFrom = 1500;
                        $maxSearch = min(mb_strlen($cleanBody), 2000);
                        $sentenceEnd = false;
                        for ($i = $searchFrom; $i < $maxSearch; $i++) {
                            $char = mb_substr($cleanBody, $i, 1);
                            if ($char === '.' || $char === '!' || $char === '?') {
                                $sentenceEnd = $i + 1;
                                break;
                            }
                        }
                        if ($sentenceEnd) {
                            $cleanBody = mb_substr($cleanBody, 0, $sentenceEnd) . '...';
                        } else {
                            $cleanBody = mb_substr($cleanBody, 0, 1500);
                            $lastSpace = mb_strrpos($cleanBody, ' ');
                            if ($lastSpace !== false && $lastSpace > 1300) {
                                $cleanBody = mb_substr($cleanBody, 0, $lastSpace);
                            }
                            $cleanBody .= '...';
                        }
                    }
                @endphp

                <section id="yazi-paylas" class="mt-12 md:mt-16">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
                        {{-- Üst: Paylaş Butonları --}}
                        <div class="px-4 py-3 flex items-center justify-between flex-wrap gap-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                <i class="fas fa-share-alt mr-1.5 text-primary-500"></i>Paylaş
                            </span>
                            <div class="flex items-center gap-1.5">
                                {{-- Kopyala --}}
                                <button type="button"
                                        id="copy-article-btn"
                                        data-title="{{ $title }}"
                                        data-excerpt="{{ $excerpt ?: '' }}"
                                        data-body="{{ $cleanBody }}"
                                        data-url="{{ $shareUrl }}"
                                        data-site="{{ setting('site_title') ?? config('app.name') }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                                        title="Kopyala">
                                    <i class="fas fa-copy text-sm" id="copy-article-icon"></i>
                                </button>
                                {{-- WhatsApp --}}
                                <a href="https://wa.me/?text={{ urlencode($title) }}%20{{ urlencode($shareUrl) }}"
                                   target="_blank"
                                   class="w-8 h-8 flex items-center justify-center rounded-full bg-green-500 text-white hover:bg-green-600 transition"
                                   title="WhatsApp">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </a>
                                {{-- Facebook --}}
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                                   target="_blank"
                                   class="w-8 h-8 flex items-center justify-center rounded-full bg-primary-600 text-white hover:bg-primary-700 transition"
                                   title="Facebook">
                                    <i class="fab fa-facebook-f text-sm"></i>
                                </a>
                                {{-- Twitter --}}
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($title) }}&url={{ urlencode($shareUrl) }}"
                                   target="_blank"
                                   class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-900 text-white hover:bg-black transition"
                                   title="Twitter">
                                    <i class="fab fa-x-twitter text-sm"></i>
                                </a>
                                {{-- LinkedIn --}}
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}"
                                   target="_blank"
                                   class="w-8 h-8 flex items-center justify-center rounded-full bg-primary-700 text-white hover:bg-primary-800 transition"
                                   title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-sm"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Alt: Yazar Bilgisi (Accordion) --}}
                        <div id="yazar-bilgileri" x-data="{ open: false }">
                            <button @click="open = !open"
                                    type="button"
                                    class="w-full px-4 py-3 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                {{-- Avatar --}}
                                <x-blog.author-card variant="mini" :blog="$item" />

                                {{-- Expand/Collapse --}}
                                <div class="ml-auto text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="{'rotate-180': open}"></i>
                                </div>
                            </button>

                            {{-- Expanded Content --}}
                            <div x-show="open"
                                 x-collapse
                                 style="display: none;">
                                @php
                                    $blogSeo = optional($item)->seoSetting;
                                    $authorBio = optional($blogSeo)->author_bio ?? setting('seo_default_author_bio') ?? null;
                                    $authorWebsite = optional($blogSeo)->author_url ?? setting('seo_default_author_url') ?? null;
                                    $facebook = setting('social_facebook');
                                    $twitter = setting('social_twitter');
                                    $instagram = setting('social_instagram');
                                    $linkedin = setting('social_linkedin');
                                    $youtube = setting('social_youtube');
                                @endphp

                                <div class="px-4 pb-4 pt-1 border-t border-gray-100 dark:border-gray-700">
                                    @if($authorBio)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-3">{{ $authorBio }}</p>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if($authorWebsite && $authorWebsite !== url('/'))
                                            <a href="{{ $authorWebsite }}" target="_blank" rel="noopener"
                                               class="text-xs px-2.5 py-1 bg-primary-600 text-white rounded hover:bg-primary-700 transition">
                                                <i class="fas fa-globe mr-1"></i>Web
                                            </a>
                                        @endif
                                        @if($facebook)
                                            <a href="{{ $facebook }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-primary-600 text-white text-xs hover:scale-110 transition-transform"><i class="fab fa-facebook-f"></i></a>
                                        @endif
                                        @if($twitter)
                                            <a href="{{ $twitter }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-900 text-white text-xs hover:scale-110 transition-transform"><i class="fab fa-x-twitter"></i></a>
                                        @endif
                                        @if($instagram)
                                            <a href="{{ $instagram }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-pink-500 text-white text-xs hover:scale-110 transition-transform"><i class="fab fa-instagram"></i></a>
                                        @endif
                                        @if($linkedin)
                                            <a href="{{ $linkedin }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-primary-700 text-white text-xs hover:scale-110 transition-transform"><i class="fab fa-linkedin-in"></i></a>
                                        @endif
                                        @if($youtube)
                                            <a href="{{ $youtube }}" target="_blank" class="w-7 h-7 flex items-center justify-center rounded-full bg-red-600 text-white text-xs hover:scale-110 transition-transform"><i class="fab fa-youtube"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- İlgili Yazılar --}}
                @if($relatedBlogs->isNotEmpty())
                    <section id="diger-yazilar" class="mt-16 md:mt-20">
                        <header class="mb-8 flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                    Bunları da Beğenebilirsin
                                </h2>
                                <div class="h-1 w-16 bg-gradient-to-r from-primary-600 to-primary-400 dark:from-primary-500 dark:to-primary-300 rounded-full"></div>
                            </div>
                            <a href="{{ $blogIndexUrl }}"
                               class="inline-flex items-center gap-1.5 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium transition-colors group">
                                <span>Tüm Yazılar</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </header>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($relatedBlogs as $relatedBlog)
                                <article class="group bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-500 transition-all duration-300">
                                    @php $relatedImage = getFirstMediaWithFallback($relatedBlog); @endphp
                                    @if($relatedImage)
                                        <a href="{{ $relatedBlog->getUrl($currentLocale) }}" class="block overflow-hidden">
                                            <img src="{{ $relatedImage->hasGeneratedConversion('thumb') ? $relatedImage->getUrl('thumb') : $relatedImage->getUrl() }}"
                                                 alt="{{ $relatedBlog->getTranslated('title', $currentLocale) }}"
                                                 loading="lazy"
                                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        </a>
                                    @endif
                                    <div class="p-5">
                                        <a href="{{ $relatedBlog->getUrl($currentLocale) }}" class="block">
                                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2">
                                                {{ $relatedBlog->getTranslated('title', $currentLocale) }}
                                            </h3>
                                        </a>
                                        @if($relatedBlog->getTranslated('excerpt', $currentLocale))
                                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                                {{ $relatedBlog->getTranslated('excerpt', $currentLocale) }}
                                            </p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Galeri --}}
                @if($galleryImages->count() > 0)
                    <section class="mt-16 md:mt-20 md:bg-gradient-to-br md:from-primary-50 md:to-indigo-50 md:dark:from-gray-800 md:dark:to-gray-700 md:rounded-2xl md:p-6 md:md:p-8 md:shadow-xl md:border md:border-primary-100 md:dark:border-gray-600">
                        <header class="mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                                {{ __('mediamanagement::admin.gallery') }}
                            </h2>
                            <div class="h-1 w-16 bg-gradient-to-r from-primary-600 to-primary-400 dark:from-primary-500 dark:to-primary-300 rounded-full"></div>
                        </header>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                            @foreach($galleryImages as $image)
                                <figure class="group relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300">
                                    <a href="{{ $image->getUrl() }}"
                                       class="glightbox block overflow-hidden"
                                       data-gallery="blog-gallery"
                                       data-title="{{ $image->getCustomProperty('title')[$currentLocale] ?? '' }}"
                                       data-description="{{ $image->getCustomProperty('description')[$currentLocale] ?? '' }}">
                                        <img src="{{ $image->getUrl('thumb') }}"
                                             alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
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
                               class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-500">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3 block">
                                    <i class="fas fa-arrow-left text-primary-500 mr-2"></i>Önceki Yazı
                                </span>
                                <span class="line-clamp-2 text-lg font-bold text-gray-900 dark:text-white transition-colors group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                    {{ $prevPost->getTranslated('title', $currentLocale) }}
                                </span>
                            </a>
                        @endif

                        @if($nextPost)
                            <a href="{{ $nextPost->getUrl($currentLocale) }}"
                               class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 text-right transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-primary-300 dark:hover:border-primary-500">
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-3 block">
                                    Sonraki Yazı<i class="fas fa-arrow-right text-primary-500 ml-2"></i>
                                </span>
                                <span class="line-clamp-2 text-lg font-bold text-gray-900 dark:text-white transition-colors group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                    {{ $nextPost->getTranslated('title', $currentLocale) }}
                                </span>
                            </a>
                        @endif
                    </nav>
                @endif
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

        /* Table dark mode styles */
        .prose table {
            margin: 2rem 0;
        }

        .prose th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #111827;
            background-color: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
        }

        .prose td {
            padding: 0.75rem;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        html.dark .prose th {
            color: #ffffff;
            background-color: #374151;
            border-bottom-color: #4b5563;
        }

        html.dark .prose td {
            color: #d1d5db;
            border-bottom-color: #4b5563;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Copy Article Button
            const copyBtn = document.getElementById('copy-article-btn');
            if (copyBtn) {
                copyBtn.addEventListener('click', async () => {
                    const title = copyBtn.dataset.title;
                    const excerpt = copyBtn.dataset.excerpt;
                    const body = copyBtn.dataset.body;
                    const url = copyBtn.dataset.url;
                    const siteName = copyBtn.dataset.site || 'Blog';
                    const icon = document.getElementById('copy-article-icon');

                    // Header
                    let copyText = `${siteName}
${window.location.hostname}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

${title}`;

                    // Excerpt
                    if (excerpt) {
                        copyText += `\n\n${excerpt}`;
                    }

                    // Body
                    if (body) {
                        copyText += `\n\n${body}`;
                    }

                    // Footer
                    copyText += `\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n📖 Yazının devamı: ${url}`;

                    const showSuccess = () => {
                        copyBtn.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                        copyBtn.classList.add('bg-green-500', 'text-white');
                        icon.classList.remove('fa-copy');
                        icon.classList.add('fa-check');

                        setTimeout(() => {
                            copyBtn.classList.remove('bg-green-500', 'text-white');
                            copyBtn.classList.add('bg-gray-100', 'dark:bg-gray-700');
                            icon.classList.remove('fa-check');
                            icon.classList.add('fa-copy');
                        }, 2000);
                    };

                    try {
                        await navigator.clipboard.writeText(copyText);
                        showSuccess();
                    } catch (err) {
                        const textarea = document.createElement('textarea');
                        textarea.value = copyText;
                        textarea.style.position = 'fixed';
                        textarea.style.opacity = '0';
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        showSuccess();
                    }
                });
            }

            // Heading Anchors
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
