@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3 class="card-title d-flex align-items-center mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        @if($itemId)
                            İçerik Düzenle - {{ $tenantWidget->settings['title'] ?? $tenantWidget->widget->name }}
                        @else
                            Yeni İçerik Ekle - {{ $tenantWidget->settings['title'] ?? $tenantWidget->widget->name }}
                        @endif
                    </h3>
                </div>
                <div class="col-md-3 position-relative d-flex justify-content-center align-items-center">
                    <div wire:loading
                        wire:target="render, save"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-md-end">
                    <a href="{{ route('admin.widgetmanagement.items', $tenantWidgetId) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Geri Dön
                    </a>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="save(true)">
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
                                                        'temporaryImages' => $temporaryImages,
                                                        'photos' => $photos
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
                                'temporaryImages' => $temporaryImages,
                                'photos' => $photos
                            ])
                        @endif
                    @endif
                @endforeach
            </div>

            <div class="card mt-3">
                @include('components.form-footer', [
                    'route' => 'admin.widgetmanagement.items',
                    'modelId' => $itemId
                ])
            </div>
        </form>
    </div>
</div>