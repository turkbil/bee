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

<div class="mb-8">
    @if($project)
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-2">{{ $project->title }}</h2>
            
            @if($settings['show_date'] ?? true)
            <div class="text-gray-500 text-sm mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $project->created_at->format('d.m.Y') }}
            </div>
            @endif
            
            @if($project->category && ($settings['show_category'] ?? true))
            <div class="mb-3">
                <span class="inline-block bg-blue-600 text-white text-sm px-2 py-1 rounded">{{ $project->category->title }}</span>
            </div>
            @endif
        </div>
        
        @if($project->image && ($settings['show_cover'] ?? true))
        <div class="mb-6">
            <img src="{{ $project->image }}" class="w-full h-auto rounded-lg" alt="{{ $project->title }}">
        </div>
        @endif
        
        <div class="prose max-w-none mb-6">
            {!! $project->body !!}
        </div>
        
        @if(method_exists($project, 'getMedia') && $project->getMedia('images')->count() > 0 && ($settings['show_gallery'] ?? true))
        <div class="mb-6">
            <h4 class="text-xl font-medium mb-4">Proje Görselleri</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($project->getMedia('images') as $image)
                <div>
                    <a href="{{ $image->getUrl() }}" data-fslightbox="gallery">
                        <img src="{{ $image->getUrl() }}" class="w-full h-auto rounded-lg hover:opacity-90 transition" alt="{{ $project->title }} - Görsel {{ $loop->iteration }}">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        @if($settings['show_related'] ?? false)
        <div>
            <h4 class="text-xl font-medium mb-4">Benzer Projeler</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                $relatedProjects = Portfolio::where('is_active', true)
                    ->where('portfolio_id', '!=', $project->portfolio_id)
                    ->when($project->portfolio_category_id, function($query) use ($project) {
                        return $query->where('portfolio_category_id', $project->portfolio_category_id);
                    })
                    ->limit(3)
                    ->get();
                @endphp
                
                @forelse($relatedProjects as $relatedProject)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden h-full">
                    @if($relatedProject->image)
                    <img src="{{ $relatedProject->image }}" class="w-full h-auto" alt="{{ $relatedProject->title }}">
                    @endif
                    <div class="p-4">
                        <h5 class="text-lg font-medium mb-4">{{ $relatedProject->title }}</h5>
                    </div>
                    <div class="px-4 py-3 bg-gray-50">
                        <a href="/portfolio/{{ $relatedProject->slug }}" class="inline-block px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">Detaylar</a>
                    </div>
                </div>
                @empty
                <div class="col-span-3">
                    <p class="text-gray-500">Benzer proje bulunamadı.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
            <div class="flex">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Proje bulunamadı veya belirtilmedi.
            </div>
        </div>
    @endif
</div>