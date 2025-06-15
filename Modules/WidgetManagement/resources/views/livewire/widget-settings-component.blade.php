@include('widgetmanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">{{ $tenantWidget->widget->name }} - Ayarları</a>
                    </li>
                </ul>
                
                <div class="card-actions">
                    <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Geri Dön
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="tabs-1">
                        <!-- Display Title -->
                        <div class="form-floating mb-3">
                            <input type="text" 
                                   wire:model.blur="displayTitle" 
                                   class="form-control @error('displayTitle') is-invalid @enderror" 
                                   placeholder="Görüntüleme Adı">
                            <label>Görüntüleme Adı</label>
                            @error('displayTitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Widget Form Elements -->
                        @if(!empty($schema))
                            <div class="row g-3">
                                @foreach($schema as $element)
                                    @if(isset($element['type']))
                                        @if($element['type'] === 'row' && isset($element['columns']))
                                            <div class="col-12">
                                                <div class="row g-3">
                                                    @foreach($element['columns'] as $column)
                                                        <div class="col-md-{{ $column['width'] ?? 6 }}">
                                                            @if(isset($column['elements']) && is_array($column['elements']))
                                                                @foreach($column['elements'] as $columnElement)
                                                                    @include('widgetmanagement::form-builder.partials.form-elements.' . $columnElement['type'], [
                                                                        'element' => $columnElement,
                                                                        'formData' => $formData,
                                                                        'temporaryUpload' => $temporaryUpload
                                                                    ])
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @elseif(!isset($element['hidden']) || !$element['hidden'])
                                            @include('widgetmanagement::form-builder.partials.form-elements.' . $element['type'], [
                                                'element' => $element,
                                                'formData' => $formData,
                                                'temporaryUpload' => $temporaryUpload
                                            ])
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Bu widget için özelleştirme seçeneği bulunmuyor.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.widgetmanagement" :model-id="$tenantWidgetId" />

        </div>
    </form>
</div>