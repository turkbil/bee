{{-- AI Module Admin Helper - Page Settings --}}
@section('title', $featureId ? 'AI Özelliği Düzenle' : 'Yeni AI Özelliği')

@push('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.ai.index') }}">AI Modülü</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.ai.features.index') }}">AI Özellikleri</a>
            </li>
            <li class="breadcrumb-item active">
                {{ $featureId ? 'Düzenle' : 'Yeni Özellik' }}
            </li>
        </ol>
    </nav>
@endpush

@push('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">AI Modülü</div>
            <h2 class="page-title">
                @if($featureId)
                    {{ $inputs['name'] ?: 'AI Özelliği' }} - Düzenle
                @else
                    Yeni AI Özelliği Oluştur
                @endif
            </h2>
            @if($featureId && isset($inputs['status']))
                <div class="page-subtitle text-muted">
                    <span class="badge bg-{{ $inputs['badge_color'] ?? 'secondary' }}-lt">
                        {{ ucfirst($inputs['status']) }}
                    </span>
                    @if($feature && $feature->is_system)
                        <span class="badge bg-info-lt ms-2">Sistem Özelliği</span>
                    @endif
                </div>
            @endif
        </div>
        @if($featureId)
        <div class="col-auto">
            <div class="btn-list">
                <a href="{{ route('admin.ai.features.show', $featureId) }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye me-2"></i>Görüntüle
                </a>
                <button type="button" class="btn btn-outline-info" wire:click="save(true)">
                    <i class="fas fa-copy me-2"></i>Kopyala ve Düzenle
                </button>
            </div>
        </div>
        @endif
    </div>
@endpush

@push('css')
<style>
.nav-tabs .nav-link {
    border-radius: 0;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--tblr-body-color);
}

.nav-tabs .nav-link.active {
    background: none;
    border-bottom-color: var(--tblr-primary);
    color: var(--tblr-primary);
    font-weight: 600;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: var(--tblr-border-color);
}

.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: var(--tblr-primary);
    transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
}

.badge {
    font-weight: 500;
}

.card {
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.btn-sm {
    padding: 0.25rem 0.5rem;
}

.example-card, .prompt-card {
    border-left: 4px solid var(--tblr-border-color);
}

.example-card:hover, .prompt-card:hover {
    border-left-color: var(--tblr-primary);
}

.border-success {
    border-color: var(--tblr-success) !important;
    border-left-color: var(--tblr-success) !important;
}

.text-success {
    color: var(--tblr-success) !important;
}

.small-stats .card {
    transition: transform 0.2s;
}

.small-stats .card:hover {
    transform: translateY(-2px);
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('livewire:navigated', function () {
    // Tab geçişlerini yönet
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabLinks.forEach(link => {
        link.addEventListener('shown.bs.tab', function (event) {
            const targetTab = event.target.getAttribute('href');
            localStorage.setItem('ai-feature-active-tab', targetTab);
        });
    });
    
    // Son aktif tab'ı geri yükle
    const lastActiveTab = localStorage.getItem('ai-feature-active-tab');
    if (lastActiveTab) {
        const tabLink = document.querySelector(`[href="${lastActiveTab}"]`);
        if (tabLink) {
            const tab = new bootstrap.Tab(tabLink);
            tab.show();
        }
    }
});

// Toast mesajlarını dinle
document.addEventListener('toast', function(event) {
    const toast = event.detail;
    if (toast.type === 'success') {
        window.location.reload();
    }
});
</script>
@endpush