@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';

    // Kategori sayısını kontrol et (controller'dan $selectedCategory gelmediyse)
    $categories = \Modules\Service\App\Models\ServiceCategory::where('is_active', true)
        ->withCount('services')
        ->orderBy('sort_order')
        ->get();
    $categoryCount = $categories->count();
    $showCategories = $categoryCount > 1;

    // Not: $selectedCategory controller'dan geliyor (/service/kategori/{slug} route'u ile)
    // Eğer gelmemişse null olarak kabul et
    $selectedCategory = $selectedCategory ?? null;
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    @php
        $currentLocale = app()->getLocale();
        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Service', 'index', $currentLocale);
        $showSlug = \App\Services\ModuleSlugService::getSlug('Service', 'show');
        $categorySlug = \App\Services\ModuleSlugService::getSlug('Service', 'category');
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';

        // Kategori URL helper - artık route-based: /service/kategori/{slug}
        $categoryUrl = fn($cat) => url($localePrefix . '/' . $indexSlug . '/' . $categorySlug . '/' . ($cat->slug['tr'] ?? $cat->slug));

        $breadcrumbsArray = [
            ['label' => 'Ana Sayfa', 'url' => url('/')],
        ];

        if ($selectedCategory) {
            $breadcrumbsArray[] = ['label' => $moduleTitle ?? 'Hizmetlerimiz', 'url' => url($localePrefix . '/' . $indexSlug)];
            $breadcrumbsArray[] = ['label' => $selectedCategory->title['tr'] ?? $selectedCategory->title];
        } else {
            $breadcrumbsArray[] = ['label' => $moduleTitle ?? 'Hizmetlerimiz'];
        }
    @endphp

    {{-- MINIMAL SUBHEADER --}}
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
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-gray-900 dark:text-white">
                {{ $selectedCategory ? ($selectedCategory->title['tr'] ?? $selectedCategory->title) : ($moduleTitle ?? 'Hizmetlerimiz') }}
            </h1>
        </div>
    </section>

    {{-- CONTENT --}}
    <section class="bg-white dark:bg-gray-900 py-10 md:py-16">
        <div class="container mx-auto px-4 sm:px-4 md:px-2">

            @if ($showCategories && !$selectedCategory)
                {{-- KATEGORI LISTESI --}}
                @php
                    // Kategori sayısına göre grid düzeni
                    $gridClass = match($categoryCount) {
                        2 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-2',
                        3 => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
                        4 => 'grid-cols-2 md:grid-cols-2 lg:grid-cols-4',
                        5 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-5',
                        6 => 'grid-cols-2 md:grid-cols-3',
                        default => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-4'
                    };
                    // 2 kategori için daha yüksek aspect ratio
                    $aspectClass = $categoryCount <= 2 ? 'aspect-[16/9]' : 'aspect-square';
                @endphp
                <div class="grid {{ $gridClass }} gap-6 md:gap-8">
                    @foreach ($categories as $category)
                        @php
                            $catSlug = is_array($category->slug) ? ($category->slug['tr'] ?? '') : $category->slug;
                            $catTitle = $category->title['tr'] ?? $category->title;
                            $catImage = $category->getFirstMedia('category_image');
                            $catUrl = $categoryUrl($category);
                            $serviceCount = $category->services_count ?? $category->services()->count();
                        @endphp

                        <a href="{{ $catUrl }}"
                           class="group relative {{ $aspectClass }} rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300">
                            @if($catImage)
                                <img src="{{ $catImage->hasGeneratedConversion('medium') ? $catImage->getUrl('medium') : $catImage->getUrl() }}"
                                     alt="{{ $catTitle }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                    <i class="fa-solid fa-folder text-white/20 text-6xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-5 md:p-8">
                                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white font-heading drop-shadow-lg leading-tight">{{ $catTitle }}</h2>
                            </div>
                        </a>
                    @endforeach
                </div>

            @else
                {{-- HIZMET LISTESI --}}
                @php
                    // Eğer kategori seçiliyse, sadece o kategorinin hizmetlerini göster
                    $displayItems = $selectedCategory
                        ? $selectedCategory->services()->where('is_active', true)->orderBy('service_id')->paginate(12)
                        : $items;
                @endphp

                @if ($displayItems->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                        @foreach ($displayItems as $item)
                            @php
                                $slugData = $item->getRawOriginal('slug');
                                if (is_string($slugData)) {
                                    $slugData = json_decode($slugData, true) ?: [];
                                }
                                $slug = is_array($slugData) && isset($slugData[$currentLocale]) && !empty($slugData[$currentLocale])
                                    ? $slugData[$currentLocale]
                                    : (is_array($slugData) ? ($slugData['tr'] ?? reset($slugData)) : $slugData);
                                $slug = $slug ?: $item->service_id;

                                $title = $item->getTranslated('title') ?? ($item->title ?? 'Başlıksız');
                                $serviceImage = $item->getFirstMedia('hero') ?: $item->getFirstMedia('featured_image');
                                $dynamicUrl = $localePrefix . '/' . $showSlug . '/' . $slug;
                            @endphp

                            <a href="{{ url($dynamicUrl) }}"
                               class="group relative aspect-square rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
                                @if($serviceImage)
                                    <img src="{{ $serviceImage->hasGeneratedConversion('medium') ? $serviceImage->getUrl('medium') : $serviceImage->getUrl() }}"
                                         alt="{{ $title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                                        <i class="fa-solid fa-cog text-white/20 text-5xl"></i>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6">
                                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white font-heading drop-shadow-lg leading-tight">{{ $title }}</h2>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @php unset($item); @endphp

                    {{-- Pagination --}}
                    @if ($displayItems->hasPages())
                        <div class="mt-12">
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                {{ $displayItems->links() }}
                            </div>
                        </div>
                    @endif

                    {{-- Tüm Kategoriler butonu (en altta) --}}
                    @if ($selectedCategory && $showCategories)
                        <div class="mt-12 text-center">
                            <a href="{{ url($localePrefix . '/' . $indexSlug) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl transition font-medium">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Tüm Kategoriler</span>
                            </a>
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-folder-open text-gray-400 dark:text-gray-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-heading">Henüz hizmet yok</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-body">Bu kategoride henüz hizmet bulunmuyor.</p>
                        @if ($selectedCategory && $showCategories)
                            <a href="{{ url($localePrefix . '/' . $indexSlug) }}"
                               class="inline-flex items-center gap-2 mt-4 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition font-medium">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Tüm Kategorilere Dön</span>
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection
