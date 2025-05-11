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

<div class="recent-pages">
    <h3>{{ $settings['title'] ?? 'Son Eklenen Sayfalar' }}</h3>
    <div class="list-group">
        @forelse($pages as $page)
            <a href="/page/{{ $page->slug }}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $page->title }}</h5>
                    @if(!empty($settings['show_dates']) && $settings['show_dates'])
                    <small>{{ $page->created_at->format('d.m.Y H:i') }}</small>
                    @endif
                </div>
                <p class="mb-1">{{ Str::limit(strip_tags($page->body), 150) }}</p>
            </a>
        @empty
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Henüz sayfa bulunmuyor.
            </div>
        @endforelse
    </div>
</div>

<style>
.recent-pages {
    margin-bottom: 1.5rem;
}
</style>