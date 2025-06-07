@include('modulemanagement::helper')

<div class="row">
    <div class="col-12">
        <!-- Ana Bilgi Kartı -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-lg bg-primary-lt">
                            <i class="fas fa-link text-primary"></i>
                        </span>
                    </div>
                    <div class="col">
                        <h2 class="mb-1">{{ $module->display_name }}</h2>
                        <div class="text-muted">Route slug ayarlarını buradan yönetebilirsiniz</div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#resetAllModal">
                            <i class="fas fa-undo me-2"></i>
                            Varsayılana Sıfırla
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($defaultRoutes))
        <!-- Route Ayarları -->
        <div class="row">
            @foreach($defaultRoutes as $key => $defaultValue)
            @php
                $currentValue = $routeSettings[$key] ?? $defaultValue;
                $isDefault = $currentValue === $defaultValue;
                $displayName = ucfirst(str_replace(['_', 'slug'], [' ', ''], $key));
            @endphp
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 border-primary">
                    <div class="card-header pb-2">
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2 bg-primary-lt">
                                <i class="fas fa-{{ $key === 'index_slug' ? 'home' : ($key === 'show_slug' ? 'eye' : 'link') }} text-primary"></i>
                            </span>
                            <h3 class="card-title mb-0">{{ $displayName }}</h3>
                            @if(!$isDefault)
                            <div class="ms-auto">
                                <button class="btn btn-ghost-primary btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#resetSingleModal"
                                        onclick="setSingleResetData('{{ $key }}', '{{ $defaultValue }}', '{{ $displayName }}')"
                                        data-bs-toggle="tooltip" 
                                        title="Varsayılana döndür">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Mevcut Slug</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-lt text-primary">
                                    <i class="fas fa-globe"></i>
                                </span>
                                <span class="input-group-text bg-primary-lt">/</span>
                                <input type="text" 
                                       class="form-control border-primary" 
                                       value="{{ $currentValue }}" 
                                       wire:blur="saveRouteSetting('{{ $key }}', $event.target.value)"
                                       placeholder="{{ $defaultValue }}">
                            </div>
                        </div>
                        
                        <div class="border-top pt-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Varsayılan
                                </small>
                                <code class="text-muted">/{{ $defaultValue }}</code>
                            </div>
                            @if(!$isDefault)
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <small class="text-primary">
                                    <i class="fas fa-edit me-1"></i>
                                    Özelleştirilmiş
                                </small>
                                <code class="text-primary">/{{ $currentValue }}</code>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex align-items-center">
                            @if($isDefault)
                            <span class="badge bg-primary-lt text-primary">
                                <i class="fas fa-check me-1"></i>
                                Varsayılan
                            </span>
                            @else
                            <span class="badge bg-primary-lt text-primary">
                                <i class="fas fa-edit me-1"></i>
                                Özelleştirilmiş
                            </span>
                            @endif
                            <div class="ms-auto">
                                <small class="text-muted">Otomatik kaydedilir</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Bilgi Kartı -->
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-primary-lt">
                            <i class="fas fa-lightbulb text-primary"></i>
                        </span>
                    </div>
                    <div class="col">
                        <h4 class="mb-1">Nasıl Çalışır?</h4>
                        <div class="text-muted">
                            • Slug ayarlarınız otomatik olarak kaydedilir<br>
                            • Değişiklikler anında aktif olur<br>
                            • Varsayılan değerlere istediğiniz zaman dönebilirsiniz
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tekli Sıfırlama Modalı -->
        <div class="modal modal-blur fade" id="resetSingleModal" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-title">Varsayılana Döndür</div>
                        <div id="singleResetMessage" class="text-muted"></div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button class="btn w-100" data-bs-dismiss="modal">
                                        İptal
                                    </button>
                                </div>
                                <div class="col">
                                    <button id="confirmSingleReset" class="btn btn-primary w-100">
                                        Varsayılana Döndür
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toplu Sıfırlama Modalı -->
        <div class="modal modal-blur fade" id="resetAllModal" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="modal-title">Tüm Ayarları Sıfırla</div>
                        <div class="text-muted">Tüm route ayarlarını varsayılana sıfırlamak istediğinizden emin misiniz?</div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button class="btn w-100" data-bs-dismiss="modal">
                                        İptal
                                    </button>
                                </div>
                                <div class="col">
                                    <button class="btn btn-primary w-100" wire:click="resetToDefaults" data-bs-dismiss="modal">
                                        Tümünü Sıfırla
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- Config Dosyası Bulunamadı - Modal Tetikleyici -->
        <div class="empty">
            <div class="empty-img">
                <i class="fas fa-exclamation-triangle text-primary" style="font-size: 4rem;"></i>
            </div>
            <p class="empty-title">Config Dosyası Bulunamadı</p>
            <p class="empty-subtitle text-muted">
                Bu modül için config dosyası oluşturmanız gerekiyor
            </p>
            <div class="empty-action">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#configModal">
                    <i class="fas fa-info-circle me-2"></i>
                    Detayları Görüntüle
                </button>
            </div>
        </div>

        <!-- Config Modal -->
        <div class="modal modal-blur fade" id="configModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-primary me-2"></i>
                            Config Dosyası Oluşturma Rehberi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-primary">
                                    <h4 class="alert-title">
                                        <i class="fas fa-file-code me-2"></i>
                                        Config Dosyası Gerekli
                                    </h4>
                                    <div class="text-muted">
                                        {{ $module->display_name }} modülü için slug ayarları yapabilmek amacıyla config dosyası oluşturmanız gerekiyor.
                                    </div>
                                </div>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-folder me-2"></i>
                                            Dosya Konumu
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="bg-dark p-3 rounded">
                                            <code class="text-white fs-4">
                                                Modules/{{ ucfirst($module->name) }}/Config/module.config.php
                                            </code>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-code me-2"></i>
                                            Örnek Config İçeriği
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <pre class="bg-dark text-white p-3 rounded"><code>&lt;?php

return [
    'routes' => [
        'index_slug' => '{{ strtolower($module->name) }}',
        'show_slug' => '{{ strtolower($module->name) }}',
        'category_slug' => '{{ strtolower($module->name) }}-category',
    ],
    'settings' => [
        'per_page' => 12,
        'cache_enabled' => true,
    ]
];</code></pre>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-list-check me-2"></i>
                                            Yapılması Gerekenler
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="steps">
                                            <div class="step-item">
                                                <div class="step-content">
                                                    <div class="step-title">Config klasörünü oluşturun</div>
                                                    <div class="text-muted">
                                                        Modules/{{ ucfirst($module->name) }}/Config/ klasörünü oluşturun
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="step-item">
                                                <div class="step-content">
                                                    <div class="step-title">module.config.php dosyasını oluşturun</div>
                                                    <div class="text-muted">
                                                        Yukarıdaki örnek kodu kullanarak dosyayı oluşturun
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="step-item">
                                                <div class="step-content">
                                                    <div class="step-title">Sayfayı yenileyin</div>
                                                    <div class="text-muted">
                                                        Config dosyası oluşturduktan sonra bu sayfayı yenileyin
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            Kapat
                        </button>
                        <button type="button" class="btn btn-primary" onclick="location.reload()">
                            <i class="fas fa-refresh me-2"></i>
                            Sayfayı Yenile
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('css')
<style>
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.border-primary {
    border-color: var(--tblr-primary) !important;
}
.bg-primary-lt {
    background-color: rgba(var(--tblr-primary-rgb), 0.1) !important;
}
.text-primary {
    color: var(--tblr-primary) !important;
}
.steps .step-item {
    position: relative;
    padding-left: 2rem;
    padding-bottom: 1.5rem;
}
.steps .step-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 1.5rem;
    height: 1.5rem;
    background: var(--tblr-primary);
    border-radius: 50%;
}
.steps .step-item:after {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 1.5rem;
    width: 1px;
    height: calc(100% - 1.5rem);
    background: var(--tblr-border-color);
}
.steps .step-item:last-child:after {
    display: none;
}
.step-content {
    margin-left: 0.5rem;
}
.step-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}
</style>
@endpush

@push('js')
<script>
let currentResetKey = '';
let currentResetValue = '';

function setSingleResetData(key, value, displayName) {
    currentResetKey = key;
    currentResetValue = value;
    document.getElementById('singleResetMessage').innerHTML = `<strong>${displayName}</strong> ayarını varsayılana döndürmek istediğinizden emin misiniz?`;
}

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    document.getElementById('confirmSingleReset').addEventListener('click', function() {
        if (currentResetKey && currentResetValue) {
            Livewire.dispatch('saveRouteSetting', [currentResetKey, currentResetValue]);
            bootstrap.Modal.getInstance(document.getElementById('resetSingleModal')).hide();
        }
    });
});
</script>
@endpush