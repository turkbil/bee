<x-admin-layout>
    <div class="container-xl py-4">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Tema Yönetimi</h2>
                    <div class="text-muted mt-1">Tüm temaları yönetin</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="d-flex">
                        <a href="{{ route('admin.studio.themes.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <i class="fas fa-plus me-2"></i> Yeni Tema
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row row-cards mt-3">
            @forelse($themes as $theme)
            <div class="col-md-4">
                <div class="card h-100 theme-card {{ $theme['is_default'] ? 'border-primary' : '' }}">
                    <div class="card-img-top theme-img">
                        @if($theme['screenshot'])
                            <img src="{{ $theme['screenshot'] }}" alt="{{ $theme['title'] }}" class="img-fluid">
                        @else
                            <div class="theme-placeholder">
                                <i class="fa-solid fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="card-title mb-0">{{ $theme['title'] }}</h5>
                            @if($theme['is_default'])
                                <span class="badge bg-primary ms-2">Varsayılan</span>
                            @endif
                        </div>
                        <p class="card-text text-muted">{{ $theme['description'] }}</p>
                        <div class="text-muted small d-flex align-items-center">
                            <i class="fas fa-folder me-1"></i>
                            <span>{{ $theme['folder_name'] }}</span>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('admin.studio.themes.edit', $theme['id']) }}" class="btn btn-outline-primary flex-grow-1">
                            <i class="fas fa-pen me-1"></i> Düzenle
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="previewTheme('{{ $theme['id'] }}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if(!$theme['is_default'])
                            <form action="{{ route('admin.studio.themes.make-default', $theme['id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-paint-brush fa-3x text-muted"></i>
                            </div>
                            <p class="empty-title">Tema Bulunamadı</p>
                            <p class="empty-subtitle text-muted">
                                Henüz hiç tema eklenmemiş.
                            </p>
                            <div class="empty-action">
                                <a href="{{ route('admin.studio.themes.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> Yeni Tema Ekle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Tema Önizleme Modalı -->
    <div class="modal fade" id="themePreviewModal" tabindex="-1" aria-labelledby="themePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="themePreviewModalLabel">Tema Önizleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="themePreviewFrame" style="width: 100%; height: 600px; border: none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function previewTheme(themeId) {
            // Tema önizleme URL'i
            const previewUrl = `/admin/studio/themes/${themeId}/preview`;
            
            // iframe src'sini ayarla
            document.getElementById('themePreviewFrame').src = previewUrl;
            
            // Modalı aç
            const previewModal = new bootstrap.Modal(document.getElementById('themePreviewModal'));
            previewModal.show();
        }
    </script>
    @endpush
</x-admin-layout>