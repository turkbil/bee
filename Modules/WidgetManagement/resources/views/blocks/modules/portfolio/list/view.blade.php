@php
    $widgetTitle = $settings['widget_title'] ?? 'Portfolyolar';
    $itemLimit = (int) ($settings['itemLimit'] ?? $settings['portfolio_limit'] ?? $settings['limit'] ?? 5);
    if ($itemLimit <= 0) {
        $itemLimit = 5;
    }
    $categorySlug = $settings['categorySlug'] ?? $settings['portfolio_category_slug'] ?? null;
    $columns = $settings['columns'] ?? '3'; // Varsayılan sütun sayısı

    $itemsQuery = \Modules\Portfolio\App\Models\Portfolio::query()->where('is_active', true);

    if ($categorySlug) {
        $category = \Modules\Portfolio\App\Models\PortfolioCategory::where('slug', $categorySlug)->first();
        if ($category) {
            $itemsQuery->where('portfolio_category_id', $category->portfolio_category_id);
        }
        // Kategori bulunamazsa ekstra koşul eklenmez, tüm aktif portfolyolar listelenir
    }

    $items = $itemsQuery->orderBy('created_at', 'desc')->limit($itemLimit)->get();

    // Debug log kaldırıldı
    
    // Current locale
    $currentLocale = app()->getLocale();

    $gridClass = 'grid-cols-1'; // Mobil için varsayılan
    if ($columns == '2') {
        $gridClass = 'sm:grid-cols-2';
    } elseif ($columns == '3') {
        $gridClass = 'sm:grid-cols-2 md:grid-cols-3';
    } elseif ($columns == '4') {
        $gridClass = 'sm:grid-cols-2 md:grid-cols-4';
    }

@endphp

<div class="portfolio-list-widget p-4">
    @if($items->count() > 0)
        <div class="grid {{ $gridClass }} gap-4">
            @foreach($items as $item)
                @php
                    $itemSlug = $item->slug[$currentLocale] ?? $item->slug['tr'] ?? $item->portfolio_id;
                    $itemTitle = $item->title[$currentLocale] ?? $item->title['tr'] ?? 'Portfolio';
                    $itemBody = $item->body[$currentLocale] ?? $item->body['tr'] ?? '';
                @endphp
                <div class="portfolio-item group overflow-hidden hover:shadow-sm transition-shadow duration-300">
                    @if($itemSlug)
                        <a href="{{ route('portfolios.show', $itemSlug) }}" class="block">
                            @php
                                $imageUrl = null;
                                if ($item->image) {
                                    $imageUrl = asset($item->image);
                                } elseif ($item->hasMedia('default')) {
                                    $imageUrl = $item->getFirstMediaUrl('default');
                                } else {
                                    $imageUrl = 'https://placehold.co/600x400?text=' . urlencode($itemTitle);
                                }
                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $itemTitle }}" class="w-full h-48 object-cover group-hover:opacity-80 transition-opacity duration-300">
                        </a>
                    @else
                        @php
                            $imageUrl = null;
                            if ($item->image) {
                                $imageUrl = asset($item->image);
                            } elseif ($item->hasMedia('default')) {
                                $imageUrl = $item->getFirstMediaUrl('default');
                            } else {
                                $imageUrl = 'https://placehold.co/600x400?text=' . urlencode($itemTitle);
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $itemTitle }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                            @if($itemSlug)
                                <a href="{{ route('portfolios.show', $itemSlug) }}" class="hover:text-primary dark:hover:text-primary-400 transition-colors duration-300">
                                    {{ $itemTitle }}
                                </a>
                            @else
                                {{ $itemTitle }}
                            @endif
                        </h3>
                        @if($item->relationLoaded('portfolioCategory') && $item->portfolioCategory)
                            <p class="text-xs text-primary dark:text-primary-400 mb-2">{{ $item->portfolioCategory->title }}</p>
                        @elseif($item->portfolio_category_id && ($category = \Modules\Portfolio\App\Models\PortfolioCategory::find($item->portfolio_category_id)))
                             <p class="text-xs text-primary dark:text-primary-400 mb-2">{{ $category->title }}</p>
                        @endif
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            {{ \Illuminate\Support\Str::limit(strip_tags($itemBody), 80) }}
                        </p>
                        @if($itemSlug)
                            <a href="{{ route('portfolios.show', $itemSlug) }}" class="text-sm text-primary dark:text-primary-400 hover:underline font-medium">
                                Devamını Oku &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-6 border-t-4 border-primary-500 dark:border-primary-400">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Portfolyo Öğesi Bulunamadı</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Görüntülenecek herhangi bir portfolyo öğesi bulunamadı.</p>
        </div>
    @endif
</div>