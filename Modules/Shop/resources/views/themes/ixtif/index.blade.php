@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="relative" x-data="shopsList()" x-init="init()">

        <!-- Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:bg-slate-900 -z-10"></div>

        <!-- Header -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10 dark:from-blue-600/20 dark:to-purple-600/20"></div>
            <div class="relative py-12">
                <div class="container mx-auto px-4 sm:px-4 md:px-0">
                    <div class="max-w-3xl">
                        <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">
                            {{ $moduleTitle ?? __('shop::front.general.shops') }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-20">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                @if ($products->count() > 0)

                    <!-- Grid: 3 kolonlu modern tasarım -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                        @foreach ($products as $item)
                            @php
                                $currentLocale = app()->getLocale();
                                $slugData = $item->getRawOriginal('slug');

                                if (is_string($slugData)) {
                                    $slugData = json_decode($slugData, true) ?: [];
                                }

                                if (is_array($slugData) && isset($slugData[$currentLocale]) && !empty($slugData[$currentLocale])) {
                                    $slug = $slugData[$currentLocale];
                                } else {
                                    $slug = is_array($slugData) ? ($slugData['tr'] ?? reset($slugData)) : $slugData;
                                }
                                $slug = $slug ?: $item->shop_id;

                                $title = $item->getTranslated('title') ?? ($item->getRawOriginal('title') ?? ($item->title ?? 'Başlıksız'));

                                $showSlug = \App\Services\ModuleSlugService::getSlug('Shop', 'show');
                                $defaultLocale = get_tenant_default_locale();
                                if ($currentLocale === $defaultLocale) {
                                    $dynamicUrl = '/' . $showSlug . '/' . $slug;
                                } else {
                                    $dynamicUrl = '/' . $currentLocale . '/' . $showSlug . '/' . $slug;
                                }

                                $metadesc = $item->getTranslated('metadesc') ?? ($item->getRawOriginal('metadesc') ?? ($item->metadesc ?? null));
                                $body = $item->getTranslated('body') ?? ($item->getRawOriginal('body') ?? ($item->body ?? null));
                                $description = $metadesc ?? (strip_tags($body) ?? null);

                                $category = $item->category->getTranslated('title') ?? 'Ürün';
                                $categoryIcon = $item->category?->icon_class ?? 'fa-light fa-box';
                                $featuredImage = $item->getFirstMedia('featured_image');
                                $imageUrl = $featuredImage ? ($featuredImage->hasGeneratedConversion('thumb') ? $featuredImage->getUrl('thumb') : $featuredImage->getUrl()) : '';
                            @endphp

                            <!-- Modern Ürün Kartı -->
                            <article class="group relative bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-gray-100 dark:border-slate-700 hover:border-gray-200 dark:hover:border-slate-600 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/10">
                                <a href="{{ $dynamicUrl }}" class="block">
                                    <!-- Image Section -->
                                    <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600">
                                        @if ($featuredImage)
                                            <img src="{{ $imageUrl }}"
                                                 alt="{{ $title }}"
                                                 class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark"
                                                 loading="lazy">
                                        @else
                                            <!-- Category Icon Fallback -->
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="{{ $categoryIcon }} text-8xl text-blue-400 dark:text-blue-300"></i>
                                            </div>
                                        @endif

                                        <!-- Hover Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                        <!-- Badges - Dinamik Badge Sistemi -->
                                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                                            @php
                                                $productBadges = collect($item->badges ?? [])
                                                    ->where('is_active', true)
                                                    ->sortBy('priority')
                                                    ->take(3); // Max 3 badge göster
                                            @endphp

                                            @foreach ($productBadges as $badge)
                                                @php
                                                    $badgeColor = $badge['color'] ?? 'gray';
                                                    $badgeIcon = $badge['icon'] ?? 'tag';
                                                    $badgeType = $badge['type'] ?? 'custom';
                                                    $badgeValue = $badge['value'] ?? null;

                                                    // Badge label'ı belirle
                                                    $badgeLabels = [
                                                        'new_arrival' => 'Yeni',
                                                        'discount' => '%' . $badgeValue . ' İndirim',
                                                        'limited_stock' => 'Son ' . $badgeValue . ' Adet',
                                                        'free_shipping' => 'Ücretsiz Kargo',
                                                        'bestseller' => 'Çok Satan',
                                                        'featured' => 'Öne Çıkan',
                                                        'eco_friendly' => 'Çevre Dostu',
                                                        'warranty' => $badgeValue . ' Ay Garanti',
                                                        'pre_order' => 'Ön Sipariş',
                                                        'imported' => 'İthal',
                                                        'custom' => $badge['label']['tr'] ?? 'Özel'
                                                    ];

                                                    $badgeLabel = $badgeLabels[$badgeType] ?? ($badge['label']['tr'] ?? 'Badge');
                                                @endphp

                                                <span class="px-3 py-1.5 bg-{{ $badgeColor }}-500 text-white text-xs font-semibold rounded-lg shadow-lg">
                                                    <i class="fa-solid fa-{{ $badgeIcon }} mr-1"></i>{{ $badgeLabel }}
                                                </span>
                                            @endforeach
                                        </div>

                                        <!-- Quick Actions -->
                                        <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-4 group-hover:translate-x-0">
                                            <button class="w-10 h-10 bg-white text-gray-900 rounded-lg shadow-lg hover:scale-110 transition-transform">
                                                <i class="fa-solid fa-heart"></i>
                                            </button>
                                            <button @click.prevent="openProductModal({
                                                id: {{ $item->shop_id }},
                                                title: '{{ addslashes($title) }}',
                                                slug: '{{ $slug }}',
                                                url: '{{ $dynamicUrl }}',
                                                image: '{{ $imageUrl }}',
                                                category: '{{ $category }}',
                                                brand: 'iXtif',
                                                shortDescription: '{{ addslashes(Str::limit($description ?? '', 150)) }}',
                                                sku: '{{ $item->sku ?? '' }}',
                                                primarySpecs: [],
                                                images: ['{{ $imageUrl }}']
                                            })" class="w-10 h-10 bg-white text-gray-900 rounded-lg shadow-lg hover:scale-110 transition-transform">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Content Section -->
                                    <div class="p-6 space-y-4">
                                        <!-- Category -->
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-blue-600 font-medium uppercase tracking-wider">{{ $category }}</span>
                                        </div>

                                        <!-- Title -->
                                        <h3 class="text-xl font-bold text-gray-900 leading-tight line-clamp-2 group-hover:text-blue-600 transition-colors">
                                            {{ $title }}
                                        </h3>

                                        <!-- Description -->
                                        @if ($description)
                                            <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed">
                                                {{ Str::limit($description, 100) }}
                                            </p>
                                        @endif

                                        <!-- Meta Info -->
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            @if ($item->sku)
                                                <span class="flex items-center gap-1">
                                                    <i class="fa-solid fa-barcode"></i>
                                                    <span>{{ $item->sku }}</span>
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-eye"></i>
                                                <span>{{ $item->view_count ?? 0 }}</span>
                                            </span>
                                        </div>

                                        <!-- Price + CTA -->
                                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                                            @if ($item->price ?? false)
                                                <div class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                                    {{ number_format($item->price, 0, ',', '.') }} ₺
                                                </div>
                                            @else
                                                <div class="text-lg font-semibold text-gray-600">
                                                    Fiyat Sorunuz
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 group-hover:gap-3 transition-all">
                                                <span>Detay</span>
                                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                        @php
                            unset($item);
                        @endphp
                    </div>

                    <!-- Pagination -->
                    @if ($products->hasPages())
                        <div class="mt-20">
                            <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-white/30 dark:border-white/10 p-4">
                                {{ $products->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-20" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                        <div x-show="show" x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100" class="inline-block">
                            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <svg class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Henüz sayfa yok</h3>
                            <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                {{ __('shop::front.general.no_shops_found') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function shopsList() {
            return {
                loaded: false,
                hover: null,
                prefetchedUrls: new Set(),

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;
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
                    document.body.style.cursor = 'wait';
                    window.location.href = url;
                }
            }
        }
    </script>
@endsection
