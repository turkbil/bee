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

<div class="mb-8">
    <h3 class="text-2xl font-bold mb-4">{{ $settings['title'] ?? 'Projelerimiz' }}</h3>
    
    @if($settings['show_description'] ?? false)
    <div class="mb-6 text-gray-700">
        {{ $settings['description'] ?? 'Son çalışmalarımızdan bazı örnekler.' }}
    </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden h-full">
                @if($project->image)
                <img src="{{ $project->image }}" class="w-full h-48 object-cover" alt="{{ $project->title }}">
                @else
                <div class="bg-gray-100 flex items-center justify-center h-48">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                @endif
                <div class="p-4">
                    <h5 class="text-lg font-medium mb-2">{{ $project->title }}</h5>
                    <p class="text-gray-700 mb-4">{{ Str::limit(strip_tags($project->body), 100) }}</p>
                    
                    @if($project->category)
                    <span class="inline-block bg-blue-600 text-white text-sm px-2 py-1 rounded mb-3">{{ $project->category->title }}</span>
                    @endif
                </div>
                <div class="px-4 py-3 bg-gray-50">
                    <a href="/portfolio/{{ $project->slug }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Detaylar</a>
                </div>
            </div>
        @empty
            <div class="col-span-3">
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Henüz proje bulunmuyor.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    
    @if($settings['show_all_link'] ?? false)
    <div class="text-center mt-6">
        <a href="/portfolio" class="inline-block px-4 py-2 border border-blue-600 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition">
            {{ $settings['all_link_text'] ?? 'Tüm Projeler' }}
        </a>
    </div>
    @endif
</div>