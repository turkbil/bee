<div class="card">
    <div class="card-header">
        <h3 class="card-title">AI Ayarları</h3>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('admin.ai.settings.api') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.ai.settings.api') || request()->routeIs('admin.ai.settings') ? 'active' : '' }}">
            <i class="fas fa-key me-2"></i>
            API Yapılandırması
        </a>
        <a href="{{ route('admin.ai.settings.limits') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.ai.settings.limits') ? 'active' : '' }}">
            <i class="fas fa-hourglass-half me-2"></i>
            Soru & Token Limitleri
        </a>
        <a href="{{ route('admin.ai.settings.prompts') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.ai.settings.prompts') ? 'active' : '' }}">
            <i class="fas fa-comments me-2"></i>
            Promptlar & Şablonlar
        </a>
        <a href="{{ route('admin.ai.settings.general') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('admin.ai.settings.general') ? 'active' : '' }}">
            <i class="fas fa-cog me-2"></i>
            Genel Ayarlar
        </a>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-lightning-bolt me-2 text-warning"></i>
            Hızlı Erişim
        </h3>
    </div>
    <div class="card-body p-3">
        <div class="row g-2">
            <div class="col-12">
                <a href="{{ route('admin.ai.index') }}" class="btn btn-primary btn-sm w-100 position-relative">
                    <i class="fas fa-robot me-2"></i>
                    AI Sohbet
                    <span class="badge bg-white text-primary position-absolute top-0 start-100 translate-middle">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-success btn-sm w-100">
                    <i class="fas fa-flask me-1"></i>
                    <small>Testler</small>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-info btn-sm w-100">
                    <i class="fas fa-history me-1"></i>
                    <small>Geçmiş</small>
                </a>
            </div>
        </div>
        
        <hr class="my-3">
        
        <div class="text-center">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                AI servisleri aktif
            </small>
        </div>
    </div>
</div>