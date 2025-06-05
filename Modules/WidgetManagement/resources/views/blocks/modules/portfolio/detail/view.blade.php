<?php
use Modules\Portfolio\App\Models\Portfolio;
use Illuminate\Support\Str;

// ID veya slug ayarlardan alınır
$projectId = $settings['project_id'] ?? null;
$projectSlug = $settings['project_slug'] ?? null;

// Proje bilgisini veritabanından çek
$project = null;

if ($projectId) {
    $project = Portfolio::find($projectId);
} elseif ($projectSlug) {
    $project = Portfolio::where('slug', $projectSlug)->first();
} else {
    // Eğer belirli bir proje belirtilmemişse, en son eklenen aktif projeyi göster
    $project = Portfolio::where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->first();
}
?>

@php
    $item = null;
    $widgetTitle = $settings['widget_title'] ?? 'Portfolyo Detayı';
    $errorMessage = null;

    $portfolioSlug = $settings['portfolio_slug'] ?? $settings['item_slug'] ?? null;
    $portfolioId = $settings['portfolio_id'] ?? null; 

    if ($portfolioSlug) {
        $item = \Modules\Portfolio\App\Models\Portfolio::where('slug', $portfolioSlug)
                                                  ->where('is_active', true)
                                                  ->first();
        if (!$item) {
            $errorMessage = "Belirtilen slug ('{$portfolioSlug}') ile eşleşen aktif bir portfolyo öğesi bulunamadı.";
        }
    } elseif ($portfolioId) {
        $item = \Modules\Portfolio\App\Models\Portfolio::where('id', $portfolioId) 
                                                  ->where('is_active', true)
                                                  ->first();
        if (!$item) {
            $errorMessage = "Belirtilen ID ('{$portfolioId}') ile eşleşen aktif bir portfolyo öğesi bulunamadı.";
        }
    }

    if (!$item) { 
        $item = \Modules\Portfolio\App\Models\Portfolio::where('is_active', true)
                                                  ->orderBy('created_at', 'desc')
                                                  ->first();
        if (!$item && !$errorMessage) {
            $errorMessage = 'Sistemde görüntülenecek aktif bir portfolyo öğesi bulunamadı.';
        } elseif ($item) {
            $errorMessage = null; 
        }
    }

    if ($item) {
        $widgetTitle = $settings['widget_title'] ?? $item->title;
    }

@endphp

<div class="portfolio-detail-widget p-4">
    @if($item)
        <article class="portfolio-item-detail">
            <header class="mb-3">
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">{{ $item->title }}</h1>
                @if($item->relationLoaded('portfolioCategory') && $item->portfolioCategory)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Kategori: <a href="#" class="text-primary hover:underline">{{ $item->portfolioCategory->title }}</a>
                    </p>
                @elseif($item->portfolio_category_id && ($category = \Modules\Portfolio\App\Models\PortfolioCategory::find($item->portfolio_category_id)))
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Kategori: <a href="#" class="text-primary hover:underline">{{ $category->title }}</a>
                    </p>
                @endif
                 <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                    Yayın Tarihi: {{ $item->created_at ? $item->created_at->translatedFormat('j F Y, H:i') : 'Belirtilmemiş' }}
                 </p>
            </header>

            @php
                $imageUrl = null;
                if ($item->image) {
                    $imageUrl = asset($item->image);
                } elseif ($item->hasMedia('default')) {
                    $imageUrl = $item->getFirstMediaUrl('default');
                } else {
                    $imageUrl = 'https://placehold.co/800x450?text=' . urlencode($item->title);
                }
            @endphp
            <div class="mb-4 overflow-hidden">
                <img src="{{ $imageUrl }}" alt="{{ $item->title }}" class="w-full h-auto object-cover max-h-[500px]">
            </div>

            <div class="prose prose-lg dark:prose-invert max-w-none ck-content">
                {!! $item->body !!}
            </div>

            <footer class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400">Bu içeriği faydalı buldunuz mu?</p>
            </footer>

        </article>
    @else
        <div class="text-yellow-700 dark:text-yellow-100 p-4 border-t-4 border-yellow-500 dark:border-yellow-400" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-yellow-500 dark:text-yellow-100 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM9 11v2h2v-2H9zm0-6v5h2V5H9z"/></svg>
                </div>
                <div>
                    <p class="font-bold">Portfolyo Öğesi Bulunamadı</p>
                    <p class="text-sm">{{ $errorMessage ?: 'Görüntülenecek uygun bir portfolyo öğesi bulunamadı. Lütfen widget ayarlarını kontrol edin veya sisteme aktif bir portfolyo öğesi ekleyin.' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>