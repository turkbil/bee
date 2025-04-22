<?php
use Modules\Page\app\Models\Page;

// Ana sayfayı veritabanından çek
$homePage = Page::where('is_homepage', true)
    ->where('is_active', true)
    ->first();
?>

<div class="homepage-content">
    @if($settings['show_title'] ?? true)
    <h1>{{ $settings['title'] ?? 'Ana Sayfa' }}</h1>
    @endif
    
    @if($homePage)
        <div class="content">
            {!! $homePage->body !!}
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Ana sayfa olarak işaretlenmiş bir sayfa bulunamadı.
        </div>
    @endif
</div>

<style>
.homepage-content {
    margin-bottom: 2rem;
}
</style>