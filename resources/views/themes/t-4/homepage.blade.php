{{-- t-4 Unimad Madencilik Theme - Homepage (v5 Full Content) --}}
@php
    use Modules\Service\App\Models\ServiceCategory;
    use Modules\Service\App\Models\Service;

    // Kategorileri çek
    $categories = ServiceCategory::where('is_active', true)
        ->orderBy('category_id')
        ->get();

    // Dinamik URL helpers (ModuleSlugService kullanarak)
    $moduleSlugService = app(\App\Services\ModuleSlugService::class);
    $currentLocale = app()->getLocale();
    $serviceIndexSlug = $moduleSlugService->getMultiLangSlug('Service', 'index', $currentLocale);
    $serviceShowSlug = \App\Services\ModuleSlugService::getSlug('Service', 'show');
    $serviceCategorySlug = \App\Services\ModuleSlugService::getSlug('Service', 'category');
    $defaultLocale = get_tenant_default_locale();
    $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';

    // Kategori URL - route-based: /service/kategori/{slug}
    $categoryUrl = fn($cat) => url($localePrefix . '/' . $serviceIndexSlug . '/' . $serviceCategorySlug . '/' . ($cat->slug['tr'] ?? $cat->slug));

    // Dinamik yıl hesaplama (2026 - 1999 = 27)
    $yearsExperience = date('Y') - 1999;

    // Kategori ikonları
    $categoryIcons = [
        'madencilik' => 'fa-gem',
        'ytk' => 'fa-file-certificate',
        'jeoloji' => 'fa-mountain',
        'hidrojeoloji' => 'fa-water',
        'jeoteknik' => 'fa-layer-group',
        'mimarlik' => 'fa-drafting-compass',
    ];

    // Kategori açıklamaları
    $categoryDescriptions = [
        'madencilik' => 'Maden sahası teknik etüt, fizibilite, rezerv tespiti, açık/yeraltı işletme planlaması ve denetim hizmetleri.',
        'ytk' => 'Yetkilendirilmiş Tüzel Kişilik kapsamında ruhsatlandırma, resmi izin süreçleri ve mevzuat danışmanlığı.',
        'jeoloji' => 'Jeolojik etüt, haritalama, maden sahası değerlendirme, 3B modelleme ve sondaj hizmetleri.',
        'hidrojeoloji' => 'Jeotermal/doğal mineralli su araştırmaları, yeraltı suyu analizi ve hidrojeolojik modelleme.',
        'jeoteknik' => 'Zemin etüdü, temel stabilite analizi, laboratuvar ve arazi deneyleri.',
        'mimarlik' => 'Konut, otel, ofis, restoran, eğitim ve kültür yapıları için mimarlık ve iç mimarlık hizmetleri.',
    ];

    // Kategori hizmet başlıkları
    $categoryFeatures = [
        'madencilik' => ['Teknik Etüt ve Değerlendirme', 'Fizibilite ve Yatırım Analizleri', 'Açık Ocak ve Yeraltı Planlaması'],
        'ytk' => ['Ruhsatlandırma İşlemleri', 'Maden Hukuku Danışmanlığı', 'Resmi Süreç Yönetimi'],
        'jeoloji' => ['Jeolojik Haritalama', 'Rezerv Hesaplama', 'Sondaj Hizmetleri'],
        'hidrojeoloji' => ['Yeraltı Suyu Araştırma', 'Akifer Testleri', 'Su Kalite Analizi'],
        'jeoteknik' => ['Zemin Etüdü', 'Stabilite Analizi', 'Laboratuvar Deneyleri'],
        'mimarlik' => ['Konut Projeleri', 'Ticari Yapılar', '3D Modelleme'],
    ];
@endphp
@extends('themes.t-4.layouts.app')

@section('content')

    <!-- ========== HERO SECTION ========== -->
    <section class="relative py-20 lg:py-28 flex items-center hero-adaptive overflow-hidden">
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 right-0 w-96 h-96 bg-primary-500/10 dark:bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-primary-400/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-4 md:px-2 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                <!-- Left: Content -->
                <div data-aos="fade-right">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500/10 dark:bg-primary-500/20 border border-primary-500/20 dark:border-primary-500/30 rounded-full mb-6">
                        <i class="fal fa-mountain text-primary-600 dark:text-primary-400"></i>
                        <span class="text-sm font-medium text-primary-700 dark:text-primary-300">{{ $yearsExperience }} Yıllık Deneyim</span>
                    </div>

                    <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold mb-6" style="line-height: 1.15;">
                        <span class="text-slate-900 dark:text-white block">PROFESYONEL</span>
                        <span class="text-gradient-animated block">MÜHENDİSLİK</span>
                        <span class="text-gradient-animated block">ÇÖZÜMLERİ</span>
                    </h1>

                    <p class="text-lg sm:text-xl text-slate-600 dark:text-primary-200 max-w-xl mb-10 leading-relaxed">
                        Tecrübe ve bilgi birikimimizi güncel teknoloji ile birleştirerek, madencilik, mimarlık ve jeoteknik alanlarında profesyonel çözümler sunuyoruz.
                    </p>

                    <!-- CTA Button -->
                    <div>
                        <a href="#services" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
                            Hizmetlerimiz <i class="fal fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Right: Image Slider -->
                <div class="relative" data-aos="fade-left" data-aos-delay="200"
                     x-data="{
                         activeSlide: 0,
                         slides: {{ json_encode(
                             $categories->filter(function($cat) {
                                 $slug = $cat->slug['tr'] ?? $cat->slug;
                                 return in_array($slug, ['madencilik', 'ytk', 'jeoloji', 'mimarlik']);
                             })->map(function($cat) use ($categoryDescriptions) {
                                 $catImage = $cat->getFirstMedia('category_image');
                                 return [
                                     'img' => $catImage ? $catImage->getUrl() : '/images/placeholder.jpg',
                                     'title' => $cat->title['tr'] ?? $cat->title,
                                     'subtitle' => $categoryDescriptions[$cat->slug['tr'] ?? $cat->slug] ?? 'Profesyonel hizmetler'
                                 ];
                             })->values()->toArray()
                         ) }}
                     }"
                     x-init="setInterval(() => { activeSlide = (activeSlide + 1) % slides.length }, 4000)">
                    <div class="relative aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl">
                        <!-- Slides -->
                        <template x-for="(slide, index) in slides" :key="index">
                            <div class="absolute inset-0 transition-opacity duration-700"
                                 :class="activeSlide === index ? 'opacity-100' : 'opacity-0'">
                                <img :src="slide.img" :alt="slide.title" class="w-full h-full object-cover" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="absolute bottom-6 left-6 right-6 text-white z-10">
                                    <h3 class="font-heading text-2xl" x-text="slide.title"></h3>
                                </div>
                            </div>
                        </template>

                        <!-- Slide Indicators -->
                        <div class="absolute bottom-6 left-6 flex gap-2 z-20" style="left: auto; right: 50%; transform: translateX(50%);">
                            <template x-for="(slide, i) in slides" :key="i">
                                <button @click="activeSlide = i"
                                        class="w-3 h-3 rounded-full transition-colors"
                                        :class="activeSlide === i ? 'bg-primary-500' : 'bg-white/50 hover:bg-white'"></button>
                            </template>
                        </div>
                    </div>

                    <!-- Floating Card with Bounce Animation -->
                    <div class="absolute -bottom-6 -right-6 bg-white dark:bg-primary-800 rounded-xl shadow-xl p-4 hidden lg:flex items-center gap-4 z-30 animate-bounce-slow" data-aos="fade-up" data-aos-delay="400">
                        <div class="w-14 h-14 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <i class="fal fa-headset text-2xl text-primary-700"></i>
                        </div>
                        <div>
                            <span class="font-heading text-xl text-slate-900 dark:text-white">7/24 Destek</span>
                            <span class="block text-sm text-slate-500 dark:text-primary-300">Profesyonel Kadro</span>
                        </div>
                    </div>
                    <style>
                        @keyframes bounce-slow {
                            0%, 100% { transform: translateY(0); }
                            50% { transform: translateY(-8px); }
                        }
                        .animate-bounce-slow {
                            animation: bounce-slow 2s ease-in-out infinite;
                        }
                    </style>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== SERVICES SECTION - 6 Kategorili ========== -->
    <section id="services" class="section-padding bg-smooth-light">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <a href="{{ url('/' . $serviceIndexSlug) }}" class="inline-block px-4 py-1 bg-primary-100 dark:bg-primary-800 text-primary-600 dark:text-primary-300 text-sm font-semibold rounded-full mb-4 hover:bg-primary-200 dark:hover:bg-primary-700 transition-colors">
                    HİZMETLERİMİZ
                </a>
                <h2 class="font-heading text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white mb-4" style="line-height: 1.2;">
                    UZMANLIK <span class="text-gradient-animated">ALANLARIMIZ</span>
                </h2>
                <p class="text-slate-600 dark:text-primary-300">
                    Madencilik, mühendislik ve mimarlık alanlarında kapsamlı profesyonel hizmetler
                </p>
            </div>

            <!-- Services Grid - 6 Kategori -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                @foreach($categories as $index => $category)
                    @php
                        $catSlug = $category->slug['tr'] ?? $category->slug;
                        $catTitle = $category->title['tr'] ?? $category->title;
                        $catImage = $category->getFirstMedia('category_image');
                        $catIcon = $categoryIcons[$catSlug] ?? 'fa-cog';
                        $catDesc = $categoryDescriptions[$catSlug] ?? 'Profesyonel hizmetler sunuyoruz.';
                        $catFeatures = $categoryFeatures[$catSlug] ?? ['Profesyonel Hizmet', 'Uzman Kadro', 'Kaliteli Çözümler'];
                    @endphp

                    <div class="group bg-white dark:bg-dark-800 rounded-2xl overflow-hidden hover-shadow transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <a href="{{ $categoryUrl($category) }}" class="block aspect-video overflow-hidden relative">
                            @if($catImage)
                                <img src="{{ $catImage->getUrl() }}" alt="{{ $catTitle }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                    <i class="fal {{ $catIcon }} text-white/30 text-6xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </a>
                        <div class="p-8">
                            <a href="{{ $categoryUrl($category) }}" class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center group-hover:bg-primary-500 transition-all duration-300">
                                    <i class="fal {{ $catIcon }} text-2xl text-primary-700 group-hover:text-white transition-colors"></i>
                                </div>
                                <h3 class="font-heading text-xl text-slate-900 dark:text-white group-hover:text-gradient-animated transition-all uppercase">{{ $catTitle }}</h3>
                            </a>
                            <p class="text-slate-600 dark:text-primary-300 mb-6 text-sm line-clamp-2">
                                {{ $catDesc }}
                            </p>
                            <ul class="space-y-2">
                                @foreach($catFeatures as $feature)
                                    <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-primary-300">
                                        <i class="fal fa-check text-primary-500"></i>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <!-- ========== ABOUT SECTION ========== -->
    <section id="about" class="section-padding bg-white dark:bg-dark-900">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            <div class="grid lg:grid-cols-2 gap-16 items-center">

                <!-- Left: Image Composition -->
                <div class="relative" data-aos="fade-right">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-4">
                            @php
                                $aboutImage1 = $categories->first()?->getFirstMedia('category_image');
                            @endphp
                            <div class="aspect-[4/5] rounded-2xl overflow-hidden">
                                @if($aboutImage1)
                                    <img src="{{ $aboutImage1->getUrl() }}" alt="Maden sahası" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                        <i class="fal fa-mountain text-white/20 text-6xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="bg-primary-500 rounded-2xl p-6 text-white">
                                <span class="font-heading text-4xl"><i class="fal fa-shield-check"></i></span>
                                <span class="block text-primary-200 mt-2">Güvenilir Çözümler</span>
                            </div>
                        </div>
                        <div class="pt-8 space-y-4">
                            <div class="bg-primary-600 dark:bg-primary-800 rounded-2xl p-6 text-white">
                                <span class="font-heading text-4xl"><i class="fal fa-users-gear"></i></span>
                                <span class="block text-primary-200 mt-2">Uzman Kadro</span>
                            </div>
                            @php
                                $aboutImage2 = $categories->skip(1)->first()?->getFirstMedia('category_image');
                            @endphp
                            <div class="aspect-[4/5] rounded-2xl overflow-hidden">
                                @if($aboutImage2)
                                    <img src="{{ $aboutImage2->getUrl() }}" alt="Mühendislik" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                                        <i class="fal fa-hard-hat text-white/20 text-6xl"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Floating Badge -->
                    <div class="absolute -bottom-4 -right-4 bg-white dark:bg-dark-900 rounded-full p-4 shadow-xl hidden lg:block" data-aos="zoom-in" data-aos-delay="300">
                        <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center">
                            <div class="text-center text-white">
                                <i class="fal fa-medal text-2xl"></i>
                                <span class="block text-xs mt-1">Güvenilir</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Content -->
                <div data-aos="fade-left">
                    <a href="{{ url('/sayfa/hakkimizda') }}" class="inline-block px-4 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 text-sm font-semibold rounded-full mb-4 hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors">
                        HAKKIMIZDA
                    </a>
                    <h2 class="font-heading text-4xl sm:text-5xl font-extrabold mb-6" style="line-height: 1.2;">
                        <span class="text-slate-900 dark:text-white block">TECRÜBEYİ TEKNOLOJİ</span>
                        <span class="block"><span class="text-slate-900 dark:text-white">İLE </span><span class="text-gradient-animated">BİRLEŞTİRİYORUZ</span></span>
                    </h2>

                    <p class="text-lg text-slate-600 dark:text-primary-300 mb-6 leading-relaxed">
                        Ankara merkezli olarak madencilik, YTK, jeoloji, hidrojeoloji, jeoteknik ve mimarlık alanlarında profesyonel mühendislik hizmetleri sunmaktayız. Uzman kadromuz ve modern teknolojik altyapımız ile müşterilerimize en kaliteli hizmeti sunmayı hedefliyoruz.
                    </p>

                    <p class="text-slate-600 dark:text-primary-300 mb-8">
                        Kapsamlı hizmet yelpazemiz ile projelerin her aşamasında yanınızdayız. Maden sahası teknik etüdünden ruhsatlandırma süreçlerine, jeolojik araştırmalardan mimari tasarıma kadar geniş bir yelpazede çözümler üretiyoruz.
                    </p>

                    <!-- Values Grid -->
                    <div class="grid grid-cols-2 gap-4 sm:gap-6 mb-8">
                        <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-primary-800/50 rounded-xl">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fal fa-book text-primary-700"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Bilgi</h4>
                                <p class="text-sm text-slate-600 dark:text-primary-300">Güncel bilgi birikimi</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-primary-800/50 rounded-xl">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fal fa-shield-check text-primary-700"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Güvenilirlik</h4>
                                <p class="text-sm text-slate-600 dark:text-primary-300">Sağlam iş ortaklığı</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-primary-800/50 rounded-xl">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fal fa-chart-line text-primary-700"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Verimlilik</h4>
                                <p class="text-sm text-slate-600 dark:text-primary-300">Optimize çözümler</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-primary-800/50 rounded-xl">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fal fa-balance-scale text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Mevzuat Uyumu</h4>
                                <p class="text-sm text-slate-600 dark:text-primary-300">Yasal süreç takibi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== BLOG SECTION ========== -->
    @php
        use Modules\Blog\App\Models\Blog;

        // Son 4 blogu çek
        $latestBlogs = Blog::where('is_active', true)
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        // Blog URL slug
        $blogShowSlug = \App\Services\ModuleSlugService::getSlug('Blog', 'show');

        // Okuma süresi hesapla (kelime sayısı / 200)
        $calculateReadTime = function($content) {
            $text = is_array($content) ? ($content['tr'] ?? reset($content)) : $content;
            $wordCount = str_word_count(strip_tags($text));
            return max(1, ceil($wordCount / 200));
        };
    @endphp
    <section id="blog" class="section-padding bg-smooth-alt">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <a href="{{ url('/blog') }}" class="inline-block px-4 py-1 bg-primary-100 dark:bg-primary-800 text-primary-600 dark:text-primary-300 text-sm font-semibold rounded-full mb-4 hover:bg-primary-200 dark:hover:bg-primary-700 transition-colors">
                    BLOG
                </a>
                <h2 class="font-heading text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white mb-4" style="line-height: 1.2;">
                    HABERLER VE <span class="text-gradient-animated">YAZILAR</span>
                </h2>
                <p class="text-slate-600 dark:text-primary-300">
                    Sektörel gelişmeler, projelerimiz ve mühendislik dünyasından güncel haberler
                </p>
            </div>

            <!-- Blog Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                @forelse($latestBlogs as $index => $blog)
                    @php
                        $blogTitle = $blog->getTranslated('title') ?? ($blog->title['tr'] ?? $blog->title);
                        $blogExcerpt = $blog->getTranslated('excerpt') ?? ($blog->excerpt['tr'] ?? $blog->excerpt ?? '');
                        $blogContent = is_array($blog->body) ? ($blog->body['tr'] ?? '') : ($blog->body ?? '');
                        $blogSlug = is_array($blog->slug) ? ($blog->slug['tr'] ?? reset($blog->slug)) : $blog->slug;
                        $blogUrl = url($localePrefix . '/' . $blogShowSlug . '/' . $blogSlug);
                        $blogImage = $blog->getFirstMedia('hero') ?: $blog->getFirstMedia('featured_image');
                        $blogCategory = $blog->category;
                        $categoryTitle = $blogCategory ? ($blogCategory->title['tr'] ?? $blogCategory->title) : 'Blog';
                        $readTime = $calculateReadTime($blogContent);
                        $publishDate = $blog->published_at ? $blog->published_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
                    @endphp
                    {{-- Mobile/Tablet: Horizontal card, LG+: Vertical card --}}
                    <article class="group bg-white dark:bg-dark-800 rounded-2xl overflow-hidden hover-shadow transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        {{-- Mobile/Tablet: Flex row (col-6), LG+: Block (vertical) --}}
                        <div class="flex lg:block">
                            {{-- Image --}}
                            <a href="{{ $blogUrl }}" class="block relative w-1/3 md:w-1/2 lg:w-full aspect-square lg:aspect-[16/10] overflow-hidden flex-shrink-0">
                                @if($blogImage)
                                    <img src="{{ $blogImage->hasGeneratedConversion('medium') ? $blogImage->getUrl('medium') : $blogImage->getUrl() }}"
                                         alt="{{ $blogTitle }}"
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                                        <i class="fal fa-newspaper text-white/30 text-2xl lg:text-4xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="absolute top-2 left-2 lg:top-4 lg:left-4 px-2 py-0.5 lg:px-3 lg:py-1 bg-primary-500 text-white text-xs lg:text-sm font-medium rounded-full shadow-lg">{{ $categoryTitle }}</span>
                            </a>
                            {{-- Content --}}
                            <div class="p-3 md:p-4 lg:p-6 flex flex-col justify-center flex-1">
                                <div class="hidden lg:flex items-center gap-4 text-sm text-slate-500 dark:text-primary-400 mb-3">
                                    <span class="flex items-center gap-1">
                                        <i class="fal fa-calendar-days"></i> {{ $publishDate }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fal fa-clock-three"></i> {{ $readTime }} dk
                                    </span>
                                </div>
                                <h3 class="font-heading text-sm md:text-base lg:text-xl text-slate-900 dark:text-white mb-1 lg:mb-3 group-hover:text-gradient-animated transition-colors line-clamp-2">
                                    <a href="{{ $blogUrl }}">{{ $blogTitle }}</a>
                                </h3>
                                <p class="text-slate-600 dark:text-primary-300 text-xs lg:text-sm mb-2 lg:mb-4 line-clamp-2 hidden lg:block">
                                    {{ Str::limit(strip_tags($blogExcerpt), 100) }}
                                </p>
                                <a href="{{ $blogUrl }}" class="link-arrow inline-flex items-center gap-1 lg:gap-2 text-primary-600 dark:text-primary-400 font-semibold text-xs lg:text-sm">
                                    Devamını Oku <i class="fal fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <!-- Fallback: Henüz blog yok -->
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-primary-100 dark:bg-primary-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fal fa-newspaper text-primary-500 text-2xl"></i>
                        </div>
                        <p class="text-slate-600 dark:text-primary-300">Henüz blog yazısı eklenmemiş.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ========== CTA SECTION ========== -->
    <section class="section-padding cta-mesh relative overflow-hidden">

        <div class="container mx-auto px-4 sm:px-4 md:px-2 relative z-10">
            <div class="max-w-4xl mx-auto text-center" data-aos="zoom-in">
                <span class="inline-block px-4 py-2 bg-white/20 border border-white/30 rounded-full text-sm font-medium text-white mb-6">
                    <i class="fal fa-handshake mr-2"></i>Birlikte Çalışalım
                </span>
                <h2 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-6 text-white" style="line-height: 1.15;">
                    <span class="block">PROJENİZ İÇİN</span>
                    <span class="block text-white/90">BİZİMLE İLETİŞİME GEÇİN</span>
                </h2>
                <p class="text-xl text-white/80 mb-10 max-w-2xl mx-auto">
                    Madencilik, jeoloji, hidrojeoloji, jeoteknik veya mimarlık alanında profesyonel mühendislik hizmetleri için uzman ekibimiz yanınızda.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#contact" class="inline-flex items-center gap-2 px-8 py-4 bg-white hover:bg-primary-50 text-primary-700 font-semibold rounded-lg border-2 border-primary-200 hover:border-primary-500 transition-all">
                        <i class="fal fa-envelope"></i> İletişime Geç
                    </a>
                    <a href="tel:+903122126809" class="inline-flex items-center gap-2 px-8 py-4 bg-transparent hover:bg-white/10 border-2 border-white/50 hover:border-white text-white font-semibold rounded-lg transition-all">
                        <i class="fal fa-phone"></i> +90 (312) 212 68 09
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== CONTACT SECTION ========== -->
    <section id="contact" class="section-padding bg-white dark:bg-dark-900">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">
            <div class="grid lg:grid-cols-2 gap-12 items-stretch">

                <!-- Left: Contact Info -->
                <div data-aos="fade-right">
                    <a href="{{ url('/sayfa/iletisim') }}" class="inline-block px-4 py-1 bg-primary-100 dark:bg-primary-800 text-primary-600 dark:text-primary-300 text-sm font-semibold rounded-full mb-4 hover:bg-primary-200 dark:hover:bg-primary-700 transition-colors">
                        İLETİŞİM
                    </a>
                    <h2 class="font-heading text-4xl sm:text-5xl font-extrabold mb-4" style="line-height: 1.2;">
                        <span class="text-slate-900 dark:text-white">BİZE </span><span class="text-gradient-animated">ULAŞIN</span>
                    </h2>
                    <p class="text-slate-600 dark:text-primary-300 mb-6">
                        Projeleriniz hakkında bilgi almak veya teklif istemek için bizimle iletişime geçebilirsiniz.
                    </p>

                    <!-- Contact Cards -->
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-5 bg-slate-50 dark:bg-primary-800/50 rounded-xl">
                            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fal fa-map-marker-alt text-xl text-primary-700"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Adres</h4>
                                <p class="text-slate-600 dark:text-primary-300">
                                    Emek Mah. M. A. C. Kırımoğlu Sok. No:14/1<br>
                                    Çankaya, Ankara
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-5 bg-slate-50 dark:bg-primary-800/50 rounded-xl">
                            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fal fa-phone text-xl text-primary-700"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">Telefon</h4>
                                <p class="text-slate-600 dark:text-primary-300">
                                    <a href="tel:+903122126809" class="hover:text-primary-600 transition-colors">+90 (312) 212 68 09</a><br>
                                    <a href="tel:+903122126810" class="hover:text-primary-600 transition-colors">+90 (312) 212 68 10</a>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-5 bg-slate-50 dark:bg-primary-800/50 rounded-xl">
                            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fal fa-envelope text-xl text-primary-700"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900 dark:text-white mb-1">E-posta</h4>
                                <p class="text-slate-600 dark:text-primary-300">
                                    <a href="mailto:info@unimadmadencilik.com" class="hover:text-primary-600 transition-colors">info@unimadmadencilik.com</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Contact Form -->
                <div data-aos="fade-left" class="flex">
                    <div class="bg-slate-50 dark:bg-primary-800/50 rounded-2xl p-8 lg:p-10 w-full">
                        <h3 class="font-heading text-2xl text-slate-900 dark:text-white mb-6">TEKLİF FORMU</h3>

                        <form class="space-y-6">
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-primary-200 mb-2">Ad Soyad *</label>
                                    <input type="text" required
                                           class="w-full px-4 py-3 bg-white dark:bg-dark-800 border border-slate-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all hover:border-primary-300 dark:hover:border-primary-600"
                                           placeholder="Adınız Soyadınız">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-primary-200 mb-2">Telefon *</label>
                                    <input type="tel" required
                                           class="w-full px-4 py-3 bg-white dark:bg-dark-800 border border-slate-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all hover:border-primary-300 dark:hover:border-primary-600"
                                           placeholder="+90 (___) ___ __ __">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-primary-200 mb-2">E-posta *</label>
                                <input type="email" required
                                       class="w-full px-4 py-3 bg-white dark:bg-dark-800 border border-slate-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all hover:border-primary-300 dark:hover:border-primary-600"
                                       placeholder="email@example.com">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-primary-200 mb-2">Mesajınız *</label>
                                <textarea rows="5" required
                                          class="w-full px-4 py-3 bg-white dark:bg-dark-800 border border-slate-300 dark:border-dark-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 resize-none transition-all hover:border-primary-300 dark:hover:border-primary-600"
                                          placeholder="Projeniz hakkında kısaca bilgi veriniz..."></textarea>
                            </div>

                            <button type="submit"
                                    class="btn-primary w-full px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg border-2 border-primary-500 hover:border-primary-400 transition-all flex items-center justify-center gap-2">
                                <i class="fal fa-paper-plane"></i>
                                Teklif İste
                            </button>

                            <p class="text-xs text-slate-500 dark:text-primary-400 text-center">
                                * Zorunlu alanlar. Bilgileriniz gizli tutulacaktır.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
