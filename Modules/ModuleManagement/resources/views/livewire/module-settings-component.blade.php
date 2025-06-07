@push('pretitle')
Modül Ayarları
@endpush

@push('title')
{{ $module->display_name }} - Slug Ayarları
@endpush

@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <a href="{{ route('admin.modulemanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Modül Listesi
            </a>
            <a href="{{ route('admin.modulemanagement.manage', $moduleId) }}" class="dropdown-module-item btn btn-ghost-secondary">
                Modül Düzenle
            </a>
        </div>
    </div>
</div>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title">{{ $module->display_name }} - Route Slug Ayarları</h3>
                    <button wire:click="resetToDefaults" class="btn btn-outline-danger btn-sm" 
                            onclick="return confirm('Route ayarlarını varsayılana sıfırlamak istediğinizden emin misiniz?')">
                        Varsayılana Sıfırla
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(!empty($defaultRoutes))
                <div class="row">
                    @foreach($defaultRoutes as $key => $defaultValue)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <strong>{{ ucfirst(str_replace(['_', 'slug'], [' ', ''], $key)) }}</strong>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">/</span>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $routeSettings[$key] ?? $defaultValue }}" 
                                   wire:blur="saveRouteSetting('{{ $key }}', $event.target.value)"
                                   placeholder="{{ $defaultValue }}">
                        </div>
                        <small class="text-muted">Varsayılan: /{{ $defaultValue }}</small>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="alert alert-warning">
                    <h4 class="alert-title">Config Dosyası Bulunamadı</h4>
                    <div class="text-muted">
                        Bu modül için config dosyası oluşturmanız gerekiyor:<br>
                        <code>Modules/{{ ucfirst($module->name) }}/Config/module.config.php</code>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>