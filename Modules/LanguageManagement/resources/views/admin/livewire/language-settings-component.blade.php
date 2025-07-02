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
                        <small class="text-muted">{{ __('languagemanagement::admin.admin_language') }}</small>
                    </div>
                    <div class="text-center">
                        <div class="h3 mb-0 text-success">{{ $siteLanguagesCount }}</div>
                        <small class="text-muted">{{ __('languagemanagement::admin.tenant_language') }}</small>
                    </div>
                </div>
            </div>
            <!-- Orta - Loading -->
            <div class="col position-relative">
                <div class="d-flex align-items-center justify-content-center h-100">
                    <div class="text-center">
                        <i class="fas fa-language fa-2x text-muted mb-2"></i>
                        <div class="text-muted">{{ __('languagemanagement::admin.language_management_center') }}</div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf - Mevcut Diller -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div class="text-center">
                        <div class="text-muted small">{{ __('languagemanagement::admin.admin_language') }}</div>
                        <code class="text-info">{{ strtoupper($currentAdminLanguage) }}</code>
                    </div>
                    <div class="text-center">
                        <div class="text-muted small">{{ __('languagemanagement::admin.tenant_language') }}</div>
                        <code class="text-info">{{ strtoupper($currentTenantLanguage) }}</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dil Kategorileri -->
        <div class="row row-cards">
            @php
            $languageTypes = [
                'system' => [
                    'title' => __('languagemanagement::admin.admin_languages'),
                    'description' => __('languagemanagement::admin.admin_panel_languages'),
                    'icon' => 'fas fa-cogs',
                    'color' => 'primary',
                    'languages' => $recentAdminLanguages,
                    'count' => $systemLanguagesCount,
                    'current' => $currentAdminLanguage,
                    'listRoute' => 'admin.languagemanagement.system.index',
                    'addRoute' => 'admin.languagemanagement.system.manage'
                ],
                'site' => [
                    'title' => __('languagemanagement::admin.tenant_languages'), 
                    'description' => __('languagemanagement::admin.frontend_content_languages'),
                    'icon' => 'fas fa-globe',
                    'color' => 'success',
                    'languages' => $recentTenantLanguages,
                    'count' => $siteLanguagesCount,
                    'current' => $currentTenantLanguage,
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
                            {{ $config['count'] }} {{ __('languagemanagement::admin.language_count') }}
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
                                    <i class="fas fa-list me-2" style="width: 14px;"></i>{{ __('languagemanagement::admin.view_all') }}
                                </a>
                                <a href="{{ route($config['addRoute']) }}" class="dropdown-item">
                                    <i class="fas fa-plus me-2" style="width: 14px;"></i>{{ __('languagemanagement::admin.add_new') }}
                                </a>
                                @if($type === 'system')
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.languagemanagement.translations') }}" class="dropdown-item">
                                    <i class="fas fa-edit me-2" style="width: 14px;"></i>{{ __('admin.edit_translations') }}
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
                                <strong>{{ __('languagemanagement::admin.available_languages') }}</strong>
                                <div class="ms-auto">
                                    <small class="text-muted">{{ __('languagemanagement::admin.current') }}: {{ strtoupper($config['current']) }}</small>
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
                                        <span class="badge bg-primary">{{ __('admin.default') }}</span>
                                    @endif
                                    @if($language->is_active)
                                        <span class="badge bg-success-lt">{{ __('admin.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary-lt">{{ __('admin.inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item py-3 text-center text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('languagemanagement::admin.no_languages_added_yet', ['type' => strtolower($config['title'])]) }}
                        </div>
                        @endforelse
                    </div>

                    <!-- Kart Footer -->
                    <div class="card-footer">
                        <div class="d-flex gap-2">
                            <a href="{{ route($config['listRoute']) }}" class="btn btn-{{ $config['color'] }} flex-fill">
                                <i class="fas fa-list me-1"></i> {{ __('languagemanagement::admin.manage_all') }}
                            </a>
                            <a href="{{ route($config['addRoute']) }}" class="btn btn-outline-{{ $config['color'] }}">
                                <i class="fas fa-plus me-1"></i> {{ __('languagemanagement::admin.add_new') }}
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
                        <div class="text-muted mb-3">{{ __('languagemanagement::admin.total') }} {{ $config['title'] }}</div>
                        
                        <div class="mt-auto">
                            @if($config['count'] > 0)
                                <div class="text-center">
                                    <div class="text-muted small mb-1">{{ __('languagemanagement::admin.current_language') }}</div>
                                    <code class="text-{{ $config['color'] }}">{{ strtoupper($config['current']) }}</code>
                                </div>
                            @else
                                <div class="text-muted">
                                    {{ __('languagemanagement::admin.no_language_added_yet') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- URL Prefix Ayarları -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-link me-2 text-warning"></i>
                            {{ __('languagemanagement::admin.url_structure_settings') }}
                        </h3>
                        <div class="text-muted">{{ __('languagemanagement::admin.url_prefix_description') }}</div>
                    </div>
                    
                    <div class="card-body">
                        <form wire:submit.prevent="saveUrlPrefixSettings">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('languagemanagement::admin.url_prefix_mode') }}</label>
                                        <select wire:model.live="urlPrefixMode" class="form-select">
                                            <option value="none">{{ __('languagemanagement::admin.no_prefix_anywhere') }}</option>
                                            <option value="except_default">{{ __('languagemanagement::admin.prefix_except_default') }}</option>
                                            <option value="all">{{ __('languagemanagement::admin.prefix_everywhere') }}</option>
                                        </select>
                                        <div class="form-text">
                                            @if($urlPrefixMode === 'none')
                                                <strong>Örnek:</strong> /hakkimizda, /about-us, /من-نحن
                                            @elseif($urlPrefixMode === 'except_default')
                                                <strong>Örnek:</strong> /hakkimizda, /en/about-us, /ar/من-نحن
                                            @else
                                                <strong>Örnek:</strong> /tr/hakkimizda, /en/about-us, /ar/من-نحن
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.default_language') }}</label>
                                        <select wire:model.live="defaultLanguageCode" class="form-select">
                                            @if($availableLanguages && count($availableLanguages) > 0)
                                                @foreach($availableLanguages as $language)
                                                    <option value="{{ $language['code'] }}">
                                                        {{ $language['native_name'] }} ({{ strtoupper($language['code']) }})
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="tr">Türkçe (TR)</option>
                                                <option value="en">English (EN)</option>
                                                <option value="ar">العربية (AR)</option>
                                            @endif
                                        </select>
                                        <div class="form-text">{{ __('admin.default_language_description') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- URL Önizleme -->
                            <div class="mb-3">
                                <label class="form-label">{{ __('languagemanagement::admin.url_preview') }}</label>
                                <div class="row g-2">
                                    @php
                                        $previewLanguages = [];
                                        if($recentTenantLanguages && count($recentTenantLanguages) > 0) {
                                            $previewLanguages = $recentTenantLanguages->take(3);
                                        } else {
                                            $previewLanguages = collect([
                                                (object)['code' => 'tr', 'native_name' => 'Türkçe'],
                                                (object)['code' => 'en', 'native_name' => 'English'],
                                                (object)['code' => 'ar', 'native_name' => 'العربية']
                                            ]);
                                        }
                                    @endphp
                                    
                                    @foreach($previewLanguages as $language)
                                        <div class="col-md-4">
                                            <div class="bg-light p-2 rounded">
                                                <div class="text-muted small">{{ $language->native_name }}</div>
                                                <code class="text-primary">
                                                    @if($urlPrefixMode === 'none')
                                                        /page/sayfa-{{ $language->code }}
                                                    @elseif($urlPrefixMode === 'except_default' && $language->code === $defaultLanguageCode)
                                                        /page/sayfa
                                                    @else
                                                        /{{ $language->code }}/page/sayfa
                                                    @endif
                                                </code>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-1"></i>
                                    {{ __('admin.save_settings') }}
                                </button>
                                <button type="button" wire:click="loadUrlPrefixSettings" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i>
                                    {{ __('languagemanagement::admin.reset') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>