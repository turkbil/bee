@include('modulemanagement::helper')

<div class="row">
    <div class="col-12">
        <!-- Ana Bilgi Kartı -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-lg bg-primary text-white">
                            <i class="fas fa-link"></i>
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
                            Tümünü Sıfırla
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
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2 bg-primary text-white">
                                    <i class="fas fa-{{ $key === 'index_slug' ? 'home' : ($key === 'show_slug' ? 'eye' : 'link') }}"></i>
                                </span>
                                <h3 class="card-title mb-0">{{ $displayName }}</h3>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Mevcut Slug</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-globe"></i>
                                </span>
                                <span class="input-group-text bg-primary text-white">/</span>
                                <input type="text" 
       class="form-control border-primary"
       wire:model.defer="routeSettings.{{ $key }}"
       wire:change="saveRouteSetting('{{ $key }}', $event.target.value)"
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
                                <small>
                                    <i class="fas fa-edit me-1"></i>
                                    Özelleştirilmiş
                                </small>
                                <code>/{{ $currentValue }}</code>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex align-items-center">
                            @if($isDefault)
                            <span class="badge bg-primary text-white">
                                <i class="fas fa-check me-1"></i>
                                Varsayılan
                            </span>
                            @else
                            <span class="badge bg-primary text-white">
                                <i class="fas fa-edit me-1"></i>
                                Özelleştirilmiş
                            </span>
                            @endif
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
                        <span class="avatar bg-primary text-white">
                            <i class="fas fa-lightbulb"></i>
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

        <!-- Toplu Sıfırlama Modalı -->
        <div class="modal modal-blur fade" id="resetAllModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tüm Ayarları Sıfırla</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h5>Tüm route ayarlarını varsayılana sıfırlamak istediğinizden emin misiniz?</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            İptal
                        </button>
                        <button class="btn btn-primary" wire:click="resetToDefaults" data-bs-dismiss="modal">
                            Tümünü Sıfırla
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- Config Dosyası Bulunamadı -->
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
