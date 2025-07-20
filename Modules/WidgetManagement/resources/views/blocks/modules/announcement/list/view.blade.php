@php
    $widgetTitle = $settings['widget_title'] ?? 'Duyurular';
    $itemLimit = (int) ($settings['limit'] ?? $settings['announcement_limit'] ?? 5);
    if ($itemLimit <= 0) {
        $itemLimit = 5;
    }
    $columns = $settings['columns'] ?? '2'; // Varsayılan sütun sayısı
    $showDate = $settings['show_date'] ?? true;
    $showDescription = $settings['show_description'] ?? true;

    $items = \Modules\Announcement\App\Models\Announcement::query()
        ->where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->limit($itemLimit)
        ->get();

    $gridClass = 'grid-cols-1'; // Mobil için varsayılan
    if ($columns == '2') {
        $gridClass = 'sm:grid-cols-2';
    } elseif ($columns == '3') {
        $gridClass = 'sm:grid-cols-2 md:grid-cols-3';
    }
    
    // Debug: Current locale
    $currentLocale = app()->getLocale();
@endphp

<div class="announcement-list-widget p-4">

    @if($items->count() > 0)
        <div class="grid {{ $gridClass }} gap-4">
            @foreach($items as $item)
                <div class="announcement-item group overflow-hidden hover:shadow-sm transition-shadow duration-300">
                    <div class="p-4">
                        @php
                            $itemSlug = $item->slug[$currentLocale] ?? $item->slug['tr'] ?? $item->announcement_id;
                            $itemTitle = $item->title[$currentLocale] ?? $item->title['tr'] ?? 'Duyuru';
                        @endphp
                        
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            @if($itemSlug)
                                <a href="{{ route('announcements.show', $itemSlug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-300">
                                    {{ $itemTitle }}
                                </a>
                            @else
                                {{ $itemTitle }}
                            @endif
                        </h3>
                        
                        @if($showDate)
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $item->created_at->format('d.m.Y') }}
                        </div>
                        @endif
                        
                        @if($showDescription && (isset($item->metadesc) || isset($item->body) || isset($item->content)))
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            @php
                                $itemDescription = '';
                                if(isset($item->metadesc)) {
                                    $itemDescription = $item->metadesc[$currentLocale] ?? $item->metadesc['tr'] ?? '';
                                } elseif(isset($item->body)) {
                                    $itemBody = $item->body[$currentLocale] ?? $item->body['tr'] ?? '';
                                    $itemDescription = strip_tags($itemBody);
                                } elseif(isset($item->content)) {
                                    $itemContent = $item->content[$currentLocale] ?? $item->content['tr'] ?? '';
                                    $itemDescription = strip_tags($itemContent);
                                }
                            @endphp
                            @if($itemDescription)
                                {{ \Illuminate\Support\Str::limit($itemDescription, 100) }}
                            @endif
                        </p>
                        @endif
                        
                        @if($itemSlug)
                            <a href="{{ route('announcements.show', $itemSlug) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline font-medium">
                                Devamını Oku &rarr;
                            </a>
                        @endif
                        
                        @if(isset($item->attachment) && $item->attachment)
                        <div class="mt-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Ek Dosya</span>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-6">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Duyuru Bulunamadı</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Görüntülenecek herhangi bir duyuru bulunamadı.</p>
        </div>
    @endif
</div>
