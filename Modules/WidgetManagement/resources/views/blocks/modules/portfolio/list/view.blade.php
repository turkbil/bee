@php
    $widgetTitle = $settings['widget_title'] ?? 'Portfolyolar';
    $itemLimit = (int) ($settings['itemLimit'] ?? $settings['portfolio_limit'] ?? $settings['limit'] ?? 5);
    $categorySlug = $settings['categorySlug'] ?? $settings['portfolio_category_slug'] ?? null;
    $columns = $settings['columns'] ?? '3'; // Varsayılan sütun sayısı

    $itemsQuery = \Modules\Portfolio\App\Models\Portfolio::query()->where('is_active', true);

    if ($categorySlug) {
        $category = \Modules\Portfolio\App\Models\PortfolioCategory::where('slug', $categorySlug)->first();
        if ($category) {
            $itemsQuery->where('portfolio_category_id', $category->id);
        }
    }

    $items = $itemsQuery->orderBy('created_at', 'desc')->limit($itemLimit)->get();

    $gridClass = 'grid-cols-1'; // Mobil için varsayılan
    if ($columns == '2') {
        $gridClass = 'sm:grid-cols-2';
    } elseif ($columns == '3') {
        $gridClass = 'sm:grid-cols-2 md:grid-cols-3';
    } elseif ($columns == '4') {
        $gridClass = 'sm:grid-cols-2 md:grid-cols-4';
    }

@endphp

<div class="portfolio-list-widget bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
    @if(!empty($widgetTitle))
        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">{{ $widgetTitle }}</h2>
    @endif

    @if($items->count() > 0)
        <div class="grid {{ $gridClass }} gap-6">
            @foreach($items as $item)
                <div class="portfolio-item group bg-gray-50 dark:bg-gray-700 rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <a href="{{ url('portfolio/' . $item->slug) }}" class="block">
                        @php
                            $imageUrl = null;
                            if ($item->image) {
                                $imageUrl = asset($item->image);
                            } elseif ($item->hasMedia('default')) {
                                $imageUrl = $item->getFirstMediaUrl('default');
                            } else {
                                $imageUrl = 'https://placehold.co/600x400?text=' . urlencode($item->title);
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $item->title }}" class="w-full h-48 object-cover group-hover:opacity-80 transition-opacity duration-300">
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                            <a href="{{ url('portfolio/' . $item->slug) }}" class="hover:text-primary dark:hover:text-primary-400 transition-colors duration-300">
                                {{ $item->title }}
                            </a>
                        </h3>
                        @if($item->relationLoaded('portfolioCategory') && $item->portfolioCategory)
                            <p class="text-xs text-primary dark:text-primary-400 mb-2">{{ $item->portfolioCategory->title }}</p>
                        @elseif($item->portfolio_category_id && ($category = \Modules\Portfolio\App\Models\PortfolioCategory::find($item->portfolio_category_id)))
                             <p class="text-xs text-primary dark:text-primary-400 mb-2">{{ $category->title }}</p>
                        @endif
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            {{ \Illuminate\Support\Str::limit(strip_tags($item->body), 80) }}
                        </p>
                        <a href="{{ url('portfolio/' . $item->slug) }}" class="text-sm text-primary dark:text-primary-400 hover:underline font-medium">
                            Devamını Oku &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Portfolyo Öğesi Bulunamadı</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Görüntülenecek herhangi bir portfolyo öğesi bulunamadı.</p>
        </div>
    @endif
</div>