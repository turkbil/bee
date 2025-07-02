@include('widgetmanagement::helper')
<div>
    @include('admin.partials.error_message')
    
    @if(!$isNewWidget)
    <div class="row mb-4" wire:loading.remove wire:target="setMode">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <h3 class="mb-0 me-4">{{ $widget['name'] }}</h3>
                            <span class="badge fs-6 px-3 py-2">{{ ucfirst($widget['type']) }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" 
                                class="btn {{ $currentMode === 'basic' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                wire:click="setMode('basic')">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('widgetmanagement::admin.basic_info') }}
                            </button>
                            @if($widget['type'] !== 'file' && $widget['type'] !== 'module')
                            <a href="{{ route('admin.widgetmanagement.code-editor', $widgetId) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-code me-2"></i>
                                {{ __('widgetmanagement::admin.code_editor') }}
                            </a>
                            @endif
                            <a href="{{ route('admin.widgetmanagement.preview.template', $widgetId) }}" 
                               class="btn btn-outline-info" target="_blank">
                                <i class="ti ti-eye me-2"></i>
                                {{ __('widgetmanagement::admin.preview') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" wire:loading wire:target="setMode">
        <div class="col-12">
            <div class="progress mb-4">
                <div class="progress-bar progress-bar-indeterminate"></div>
            </div>
        </div>
    </div>
    @endif
    
    <form wire:submit.prevent="save(true)">
        <div class="row g-4">
            <div class="col-12 col-lg-8 order-2 order-lg-1">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-{{ $isNewWidget ? 'plus' : 'edit' }} me-2"></i>
                            {{ $isNewWidget ? __('widgetmanagement::admin.new_widget_add') : __('widgetmanagement::admin.basic_info') }}
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <input type="text" id="widget-name" wire:model="widget.name" 
                                                class="form-control @error('widget.name') is-invalid @enderror"
                                                placeholder="{{ __('widgetmanagement::admin.widget_name') }}">
                                            <label class="required">{{ __('widgetmanagement::admin.widget_name') }} *</label>
                                            @error('widget.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <input type="text" id="widget-slug" wire:model="widget.slug" 
                                                class="form-control font-monospace @error('widget.slug') is-invalid @enderror"
                                                placeholder="{{ __('widgetmanagement::admin.unique_identifier') }}">
                                            <label class="required">{{ __('widgetmanagement::admin.unique_identifier') }} *</label>
                                            @error('widget.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <small class="form-hint">{{ __('widgetmanagement::admin.only_lowercase_rule') }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-floating">
                                    <select wire:model="widget.widget_category_id" 
                                        class="form-control @error('widget.widget_category_id') is-invalid @enderror"
                                        data-choices
                                        data-choices-search="{{ count($categories) > 6 ? 'true' : 'false' }}"
                                        data-choices-placeholder="{{ __('widgetmanagement::admin.category_select') }}">
                                        <option value="">{{ __('widgetmanagement::admin.category_select') }}</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->widget_category_id }}">{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                    <label>Kategori</label>
                                    @error('widget.widget_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea wire:model="widget.description" 
                                        class="form-control @error('widget.description') is-invalid @enderror" 
                                        placeholder="{{ __('widgetmanagement::admin.widget_description_placeholder') }}"
                                        rows="4"></textarea>
                                    <label>{{ __('widgetmanagement::admin.description') }}</label>
                                    @error('widget.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label required mb-3">{{ __('widgetmanagement::admin.widget_type') }}</label>
                                <div class="row g-3">
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="static" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-blue text-white mb-2">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">{{ __('widgetmanagement::admin.static') }}</span>
                                                <span class="text-muted small text-center">{{ __('widgetmanagement::admin.static_desc') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="dynamic" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-green text-white mb-2">
                                                    <i class="fas fa-layer-group"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">{{ __('widgetmanagement::admin.dynamic') }}</span>
                                                <span class="text-muted small text-center">{{ __('widgetmanagement::admin.dynamic_desc') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="module" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-purple text-white mb-2">
                                                    <i class="fas fa-cubes"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">{{ __('widgetmanagement::admin.module') }}</span>
                                                <span class="text-muted small text-center">{{ __('widgetmanagement::admin.module_desc') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="file" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-orange text-white mb-2">
                                                    <i class="fas fa-file-code"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">{{ __('widgetmanagement::admin.file') }}</span>
                                                <span class="text-muted small text-center">{{ __('widgetmanagement::admin.file_desc') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @error('widget.type') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
                            </div>

                            @if($widget['type'] === 'module')
                            <div class="col-12">
                                @if($this->hasAvailableModuleFiles())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ __('widgetmanagement::admin.module') }} widget'ı için view dosyasını seçin.
                                </div>
                                
                                <label class="form-label required">{{ __('widgetmanagement::admin.module') }} View {{ __('widgetmanagement::admin.file') }}sı</label>
                                
                                @if($widget['file_path'])
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ ucwords(str_replace(['-', '_', '/'], ' ', str_replace(['modules/', '/view'], '', $widget['file_path']))) }}" readonly>
                                        <button type="button" class="btn btn-outline-danger" wire:click="$set('widget.file_path', '')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="form-hint">{{ __('widgetmanagement::admin.change_selection_tip') }}</small>
                                </div>
                                @else
                                <select wire:model="widget.file_path" 
                                    class="form-control @error('widget.file_path') is-invalid @enderror"
                                    data-choices
                                    data-choices-search="{{ count($this->getModuleFiles()) > 6 ? 'true' : 'false' }}"
                                    data-choices-placeholder="{{ __('widgetmanagement::admin.file_select') }}"
                                    <option value="">{{ __('widgetmanagement::admin.file_select') }}</option>
                                    @foreach($this->getModuleFiles() as $path => $name)
                                    <option value="{{ $path }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('widgetmanagement::admin.all_module_files_assigned') }}
                                </div>
                                
                                <label class="form-label required">{{ __('widgetmanagement::admin.module') }} View {{ __('widgetmanagement::admin.file') }}sı</label>
                                <select disabled class="form-select">
                                    <option>{{ __('widgetmanagement::admin.no_suitable_file') }}</option>
                                </select>
                                @endif
                            </div>
                            @endif

                            @if($widget['type'] === 'file')
                            <div class="col-12">
                                @if($this->hasAvailableViewFiles())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ __('widgetmanagement::admin.select_ready_view_file') }}
                                </div>
                                
                                <label class="form-label required">View {{ __('widgetmanagement::admin.file') }}sı</label>
                                
                                @if($widget['file_path'])
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ ucwords(str_replace(['-', '_', '/'], ' ', str_replace('/view', '', $widget['file_path']))) }}" readonly>
                                        <button type="button" class="btn btn-outline-danger" wire:click="$set('widget.file_path', '')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="form-hint">{{ __('widgetmanagement::admin.change_selection_tip') }}</small>
                                </div>
                                @else
                                <select wire:model="widget.file_path" 
                                    class="form-control @error('widget.file_path') is-invalid @enderror"
                                    data-choices
                                    data-choices-search="{{ count($this->getViewFiles()) > 6 ? 'true' : 'false' }}"
                                    data-choices-placeholder="{{ __('widgetmanagement::admin.file_select') }}"
                                    <option value="">{{ __('widgetmanagement::admin.file_select') }}</option>
                                    @foreach($this->getViewFiles() as $path => $name)
                                    <option value="{{ $path }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('widgetmanagement::admin.all_view_files_assigned') }}
                                </div>
                                
                                <label class="form-label required">View {{ __('widgetmanagement::admin.file') }}sı</label>
                                <select disabled class="form-select">
                                    <option>{{ __('widgetmanagement::admin.no_suitable_file') }}</option>
                                </select>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-4 order-1 order-lg-2">
                <div class="row g-4">
                    @if(!$isNewWidget)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('widgetmanagement::admin.widget_settings') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model.live="widget.has_items">
                                        <span class="form-check-label">{{ __('widgetmanagement::admin.content_adding_feature') }}</span>
                                    </label>
                                    <small class="form-hint ms-4">{{ __('widgetmanagement::admin.users_can_add_content') }}</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_core">
                                        <span class="form-check-label">{{ __('widgetmanagement::admin.system_widget') }}</span>
                                    </label>
                                    <small class="form-hint ms-4">{{ __('widgetmanagement::admin.system_widgets_not_deletable') }}</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_active">
                                        <span class="form-check-label">{{ __('widgetmanagement::admin.widget_active') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('widgetmanagement::admin.preview') }} Görseli</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-control position-relative" 
                                    onclick="document.getElementById('thumbnail-upload-existing').click()"
                                    style="height: auto; min-height: 200px; cursor: pointer; border: 2px dashed #ccc;">
                                    <input type="file" id="thumbnail-upload-existing" wire:model="thumbnail" class="d-none" accept="image/*">
                                    
                                    @if ($thumbnail && method_exists($thumbnail, 'temporaryUrl'))
                                        <img src="{{ $thumbnail->temporaryUrl() }}" class="img-fluid rounded" alt="Yeni {{ __('widgetmanagement::admin.preview') }}">
                                    @elseif ($imagePreview)
                                        <img src="{{ url($imagePreview) }}" class="img-fluid rounded" alt="Mevcut {{ __('widgetmanagement::admin.preview') }}">
                                    @endif

                                    @if (($thumbnail && method_exists($thumbnail, 'temporaryUrl')) || $imagePreview)
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                            wire:click.prevent="$set('thumbnail', null); $set('imagePreview', null)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                            <p class="mb-0">{{ __('widgetmanagement::admin.click_to_select_image') }}</p>
                                            <p class="text-muted small mb-0">{{ __('widgetmanagement::admin.image_format_size_info') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="progress mt-2" wire:loading wire:target="thumbnail">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                </div>
                                @error('thumbnail') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('widgetmanagement::admin.widget_settings') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model.live="widget.has_items">
                                        <span class="form-check-label">{{ __('widgetmanagement::admin.content_adding_feature') }}</span>
                                    </label>
                                    <small class="form-hint ms-4">{{ __('widgetmanagement::admin.users_can_add_content') }}</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_core">
                                        <span class="form-check-label">{{ __('widgetmanagement::admin.system_widget') }}</span>
                                    </label>
                                    <small class="form-hint ms-4">{{ __('widgetmanagement::admin.system_widgets_not_deletable') }}</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_active">
                                        <span class="form-check-label">{{ __('widgetmanagement::admin.widget_active') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('widgetmanagement::admin.preview') }} Görseli</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-control position-relative" 
                                    onclick="document.getElementById('thumbnail-upload-new').click()"
                                    style="height: auto; min-height: 120px; cursor: pointer; border: 2px dashed #ccc;">
                                    <input type="file" id="thumbnail-upload-new" wire:model="thumbnail" class="d-none" accept="image/*">
                                    @if($thumbnail && method_exists($thumbnail, 'temporaryUrl'))
                                        <img src="{{ $thumbnail->temporaryUrl() }}" class="img-fluid rounded" alt="{{ __('widgetmanagement::admin.preview') }}">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                            wire:click.prevent="$set('thumbnail', null)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">{{ __('widgetmanagement::admin.click_to_select_image') }}</p>
                                            <p class="text-muted small mb-0">{{ __('widgetmanagement::admin.image_format_size_info') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="progress mt-2" wire:loading wire:target="thumbnail">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                </div>
                                @error('thumbnail') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            @include('components.form-footer', [
                'route' => 'admin.widgetmanagement',
                'modelId' => $widgetId
            ])
        </div>
    </form>
</div>

@push('styles')
<style>
.form-label.required:after {
    content: " *";
    color: red;
}

.form-selectgroup-item:hover .form-selectgroup-label {
    border-color: var(--tblr-primary);
}

.form-selectgroup-input:checked ~ .form-selectgroup-label {
    border-color: var(--tblr-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.25);
}

@media (max-width: 991.98px) {
    .form-selectgroup-item .form-selectgroup-label {
        padding: 1rem !important;
    }
    
    .avatar.avatar-lg {
        width: 3rem;
        height: 3rem;
    }
}

@media (max-width: 575.98px) {
    .form-selectgroup-item .form-selectgroup-label {
        padding: 0.75rem !important;
    }
    
    .form-selectgroup-title {
        font-size: 0.875rem;
    }
    
    .avatar.avatar-lg {
        width: 2.5rem;
        height: 2.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    function slugify(text) {
        // Türkçe karakter haritası
        const turkishMap = {
            'ç': 'c', 'Ç': 'C',
            'ğ': 'g', 'Ğ': 'G',
            'ı': 'i', 'I': 'I',
            'İ': 'I', 'i': 'i',
            'ö': 'o', 'Ö': 'O',
            'ş': 's', 'Ş': 'S',
            'ü': 'u', 'Ü': 'U'
        };
        
        return text
            .toString()
            .toLowerCase()
            .trim()
            // Türkçe karakterleri dönüştür
            .replace(/[çğıöşüÇĞIÖŞÜ]/g, function(match) {
                return turkishMap[match] || match;
            })
            // Boşluk ve alt çizgiyi tire yap
            .replace(/[\s_]/g, '-')
            // Alfanumerik olmayan karakterleri kaldır (tire hariç)
            .replace(/[^\w\-]+/g, '')
            // Çoklu tireleri tek tire yap
            .replace(/\-\-+/g, '-')
            // Başlangıç ve sondaki tireleri kaldır
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }
    
    $('#widget-name').on('input', function() {
        var name = $(this).val();
        var slugField = $('#widget-slug');
        
        if (slugField.val() === '' || slugField.data('auto-generated')) {
            var slug = slugify(name);
            slugField.val(slug).data('auto-generated', true);
            @this.set('widget.slug', slug);
        }
    });
    
    $('#widget-slug').on('input', function() {
        $(this).data('auto-generated', false);
        @this.set('widget.slug', $(this).val());
    });
});
</script>
@endpush