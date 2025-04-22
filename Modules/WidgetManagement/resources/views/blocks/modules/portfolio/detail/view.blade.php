<?php
use Modules\Portfolio\app\Models\Project;

// ID veya slug ayarlardan alınır
$projectId = $settings['project_id'] ?? null;
$projectSlug = $settings['project_slug'] ?? null;

// Proje bilgisini veritabanından çek
$project = null;

if ($projectId) {
    $project = Project::find($projectId);
} elseif ($projectSlug) {
    $project = Project::where('slug', $projectSlug)->first();
} else {
    // Eğer belirli bir proje belirtilmemişse, en son eklenen aktif projeyi göster
    $project = Project::where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->first();
}
?>

<div class="portfolio-detail">
    @if($project)
        <div class="portfolio-header mb-4">
            <h2 class="project-title">{{ $project->title }}</h2>
            
            @if($settings['show_date'] ?? true)
            <div class="project-date text-muted small">
                <i class="fas fa-calendar me-1"></i> {{ $project->created_at->format('d.m.Y') }}
            </div>
            @endif
            
            @if($project->category && ($settings['show_category'] ?? true))
            <div class="project-category">
                <span class="badge bg-primary">{{ $project->category->name }}</span>
            </div>
            @endif
        </div>
        
        @if($project->cover_image && ($settings['show_cover'] ?? true))
        <div class="project-cover mb-4">
            <img src="{{ $project->cover_image }}" class="img-fluid rounded" alt="{{ $project->title }}">
        </div>
        @endif
        
        <div class="project-content mb-4">
            {!! $project->content !!}
        </div>
        
        @if($project->gallery && count($project->gallery) > 0 && ($settings['show_gallery'] ?? true))
        <div class="project-gallery mb-4">
            <h4>Proje Görselleri</h4>
            <div class="row g-3">
                @foreach($project->gallery as $image)
                <div class="col-md-4">
                    <a href="{{ $image }}" data-fslightbox="gallery">
                        <img src="{{ $image }}" class="img-fluid rounded" alt="{{ $project->title }} - Görsel {{ $loop->iteration }}">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        @if($settings['show_related'] ?? false)
        <div class="related-projects">
            <h4>Benzer Projeler</h4>
            <div class="row">
                @php
                $relatedProjects = Project::where('is_active', true)
                    ->where('id', '!=', $project->id)
                    ->when($project->category_id, function($query) use ($project) {
                        return $query->where('category_id', $project->category_id);
                    })
                    ->limit(3)
                    ->get();
                @endphp
                
                @forelse($relatedProjects as $relatedProject)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        @if($relatedProject->cover_image)
                        <img src="{{ $relatedProject->cover_image }}" class="card-img-top" alt="{{ $relatedProject->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $relatedProject->title }}</h5>
                        </div>
                        <div class="card-footer">
                            <a href="/portfolio/{{ $relatedProject->slug }}" class="btn btn-sm btn-outline-primary">Detaylar</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted">Benzer proje bulunamadı.</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Proje bulunamadı veya belirtilmedi.
        </div>
    @endif
</div>

<style>
.portfolio-detail {
    margin-bottom: 2rem;
}
.project-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
}
.project-gallery {
    margin-top: 2rem;
}
</style>