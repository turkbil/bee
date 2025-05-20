<?php
use Modules\Page\app\Models\Page;
use Illuminate\Support\Str;

// Limit değerini ayarlardan al, varsayılan olarak 5
$limit = $settings['limit'] ?? 5;

// Veritabanından en son eklenen sayfaları çek
$pages = Page::where('is_active', true)
    ->orderBy('created_at', 'desc')
    ->limit($limit)
    ->get();
?>

<div class="mb-6">
    <h3 class="text-xl font-medium mb-4">{{ $settings['title'] ?? 'Son Eklenen Sayfalar' }}</h3>
    <div class="space-y-2">
        @forelse($pages as $page)
            <a href="/page/{{ $page->slug }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="flex justify-between items-start">
                    <h5 class="text-lg font-medium mb-1">{{ $page->title }}</h5>
                    @if(!empty($settings['show_dates']) && $settings['show_dates'])
                    <span class="text-sm text-gray-500">{{ $page->created_at->format('d.m.Y H:i') }}</span>
                    @endif
                </div>
                <p class="text-gray-700">{{ Str::limit(strip_tags($page->body), 150) }}</p>
            </a>
        @empty
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Henüz sayfa bulunmuyor.
                </div>
            </div>
        @endforelse
    </div>
</div>