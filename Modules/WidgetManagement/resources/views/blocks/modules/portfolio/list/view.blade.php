<?php
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Illuminate\Support\Str;

// Limit değerini ayarlardan al, varsayılan olarak 6
$limit = $settings['limit'] ?? 6;

// Sıralama yönünü ayarlardan al
$orderDirection = $settings['order_direction'] ?? 'desc';

// Kategori filtresi varsa ekle
$categoryId = $settings['category_id'] ?? null;

// Veritabanından en son eklenen projeleri çek
$query = Portfolio::where('is_active', true);

if ($categoryId) {
    $query->where('portfolio_category_id', $categoryId);
}

$projects = $query->orderBy('created_at', $orderDirection)
    ->limit($limit)
    ->get();
?>

<div class="portfolio-list">
    <h3 class="portfolio-title">{{ $settings['title'] ?? 'Projelerimiz' }}</h3>
    
    @if($settings['show_description'] ?? false)
    <div class="portfolio-description mb-4">
        {{ $settings['description'] ?? 'Son çalışmalarımızdan bazı örnekler.' }}
    </div>
    @endif
    
    <div class="row">
        @forelse($projects as $project)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($project->image)
                    <img src="{{ $project->image }}" class="card-img-top" alt="{{ $project->title }}">
                    @else
                    <div class="bg-light text-center py-5">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $project->title }}</h5>
                        <p class="card-text">{{ Str::limit($project->body, 100) }}</p>
                        
                        @if($project->category)
                        <span class="badge bg-primary">{{ $project->category->title }}</span>
                        @endif
                    </div>
                    <div class="card-footer">
                        <a href="/portfolio/{{ $project->slug }}" class="btn btn-primary">Detaylar</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Henüz proje bulunmuyor.
                </div>
            </div>
        @endforelse
    </div>
    
    @if($settings['show_all_link'] ?? false)
    <div class="text-center mt-4">
        <a href="/portfolio" class="btn btn-outline-primary">
            {{ $settings['all_link_text'] ?? 'Tüm Projeler' }}
        </a>
    </div>
    @endif
</div>

<style>
.portfolio-list {
    margin-bottom: 2rem;
}
.portfolio-title {
    margin-bottom: 1.5rem;
    font-weight: 600;
}
</style>