@include('languagemanagement::admin.helper')
<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Sol Taraf - İstatistikler -->
            <div class="col">
                <div class="d-flex align-items-center gap-4">
                    <div class="text-center">
                        <div class="h3 mb-0 text-primary">{{ $systemLanguagesCount }}</div>
                        <small class="text-muted">Sistem Dili</small>
                    </div>
                    <div class="text-center">
                        <div class="h3 mb-0 text-success">{{ $siteLanguagesCount }}</div>
                        <small class="text-muted">Site Dili</small>
                    </div>
                </div>
            </div>
            <!-- Orta - Loading -->
            <div class="col position-relative">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <i class="fas fa-language fa-2x text-muted mb-2"></i>
                        <div class="text-muted">Dil Yönetim Merkezi</div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf - Mevcut Diller -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div class="text-center">
                        <div class="text-muted small">Admin Dili</div>
                        <code class="text-info">{{ strtoupper($currentAdminLanguage) }}</code>
                    </div>
                    <div class="text-center">
                        <div class="text-muted small">Site Dili</div>
                        <code class="text-info">{{ strtoupper($currentSiteLanguage) }}</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dil Kategorileri -->
        <div class="row row-cards">
            @php
            $languageTypes = [
                'system' => [
                    'title' => 'Sistem Dilleri',
                    'description' => 'Admin paneli dilleri',
                    'icon' => 'fas fa-cogs',
                    'color' => 'primary',
                    'languages' => $recentSystemLanguages,
                    'count' => $systemLanguagesCount,
                    'current' => $currentAdminLanguage,
                    'listRoute' => 'admin.languagemanagement.system.index',
                    'addRoute' => 'admin.languagemanagement.system.manage'
                ],
                'site' => [
                    'title' => 'Site Dilleri', 
                    'description' => 'Frontend içerik dilleri',
                    'icon' => 'fas fa-globe',
                    'color' => 'success',
                    'languages' => $recentSiteLanguages,
                    'count' => $siteLanguagesCount,
                    'current' => $currentSiteLanguage,
                    'listRoute' => 'admin.languagemanagement.site.index',
                    'addRoute' => 'admin.languagemanagement.site.manage'
                ]
            ];
            @endphp

            @foreach($languageTypes as $type => $config)
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center p-2 bg-{{ $config['color'] }}-lt rounded">
                    <i class="{{ $config['icon'] }} me-2 text-{{ $config['color'] }}"></i>
                    <h3 class="mb-0 h4">{{ $config['title'] }}</h3>
                    <small class="text-muted ms-2">{{ $config['description'] }}</small>
                    <div class="ms-auto">
                        <span class="badge bg-{{ $config['color'] }}">
                            {{ $config['count'] }} dil
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-8">
                <div class="card language-type-card">
                    <!-- Kart Header -->
                    <div class="card-header d-flex align-items-center">
                        <div class="me-auto">
                            <h3 class="card-title mb-0">{{ $config['title'] }}</h3>
                            <div class="text-muted">{{ $config['description'] }}</div>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route($config['listRoute']) }}" class="dropdown-item">
                                    <i class="fas fa-list me-2" style="width: 14px;"></i>Tümünü Görüntüle
                                </a>
                                <a href="{{ route($config['addRoute']) }}" class="dropdown-item">
                                    <i class="fas fa-plus me-2" style="width: 14px;"></i>Yeni Ekle
                                </a>
                                @if($type === 'system')
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.languagemanagement.translations') }}" class="dropdown-item">
                                    <i class="fas fa-edit me-2" style="width: 14px;"></i>Çevirileri Düzenle
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Dil Listesi -->
                    <div class="list-group list-group-flush">
                        <div class="list-group-item py-2 bg-muted-lt">
                            <div class="d-flex align-items-center">
                                <i class="{{ $config['icon'] }} text-{{ $config['color'] }} me-2"></i>
                                <strong>Mevcut Diller</strong>
                                <div class="ms-auto">
                                    <small class="text-muted">Mevcut: {{ strtoupper($config['current']) }}</small>
                                </div>
                            </div>
                        </div>

                        @forelse($config['languages'] as $language)
                        <div class="list-group-item py-2">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2" style="font-size: 0.75rem;">
                                    {{ $language->flag_icon ?? '🌐' }}
                                </span>
                                <div class="flex-fill">
                                    <strong>{{ $language->native_name }}</strong>
                                    <div class="text-muted small">{{ $language->name }} ({{ strtoupper($language->code) }})</div>
                                </div>
                                <div class="d-flex gap-1">
                                    @if($type === 'site' && $language->is_default)
                                        <span class="badge bg-primary">Varsayılan</span>
                                    @endif
                                    @if($language->is_active)
                                        <span class="badge bg-success-lt">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary-lt">Pasif</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item py-3 text-center text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Henüz {{ strtolower($config['title']) }} eklenmemiş
                        </div>
                        @endforelse
                    </div>

                    <!-- Kart Footer -->
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <a href="{{ route($config['listRoute']) }}" class="btn btn-{{ $config['color'] }} flex-fill">
                                <i class="fas fa-list me-1"></i> Tümünü Yönet
                            </a>
                            <a href="{{ route($config['addRoute']) }}" class="btn btn-outline-{{ $config['color'] }}">
                                <i class="fas fa-plus me-1"></i> Yeni Ekle
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column text-center">
                        <i class="{{ $config['icon'] }} fa-3x text-{{ $config['color'] }} mb-3"></i>
                        <h4 class="card-title">{{ $config['count'] }}</h4>
                        <div class="text-muted mb-3">Toplam {{ $config['title'] }}</div>
                        
                        <div class="mt-auto">
                            @if($config['count'] > 0)
                                <div class="text-center">
                                    <div class="text-muted small mb-1">Mevcut Dil</div>
                                    <code class="text-{{ $config['color'] }}">{{ strtoupper($config['current']) }}</code>
                                </div>
                            @else
                                <div class="text-muted">
                                    Henüz dil eklenmemiş
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>