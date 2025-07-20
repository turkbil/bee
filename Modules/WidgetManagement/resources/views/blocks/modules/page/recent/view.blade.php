@php
use Modules\Page\app\Models\Page;

$widgetTitle = $settings['widget_title'] ?? 'Son Sayfalar';
$itemLimit = (int) ($settings['itemLimit'] ?? $settings['page_limit'] ?? $settings['limit'] ?? 5);
if ($itemLimit <= 0) {
    $itemLimit = 5;
}

$pages = Page::query()
    ->where('is_active', true)
    ->orderBy('created_at', 'desc')
    ->limit($itemLimit)
    ->get();

// Current locale
$currentLocale = app()->getLocale();
@endphp

<div class="recent-pages-widget p-4">

    @if($pages->count() > 0)
        <ul class="space-y-2">
            @foreach($pages as $page)
                @php
                    $pageSlug = $page->slug[$currentLocale] ?? $page->slug['tr'] ?? $page->page_id;
                    $pageTitle = $page->title[$currentLocale] ?? $page->title['tr'] ?? 'Sayfa';
                @endphp
                <li class="pb-2">
                    @if($pageSlug)
                        <a href="{{ url($pageSlug) }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-primary-400 transition-colors duration-300">
                            {{ $pageTitle }}
                        </a>
                    @else
                        <span class="text-gray-700 dark:text-gray-300">{{ $pageTitle }}</span>
                    @endif
                    @if($settings['show_date'] ?? false)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $page->created_at->translatedFormat('j M Y') }}
                        </p>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <div class="text-center py-6">
            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Sayfa Bulunamadı</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Görüntülenecek herhangi bir sayfa bulunamadı.</p>
        </div>
    @endif
</div>