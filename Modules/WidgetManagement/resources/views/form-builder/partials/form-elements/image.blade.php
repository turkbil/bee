@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $helpText = $element['help_text'] ?? '';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-image me-2"></i>
                    {{ $fieldLabel }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                {{-- Universal MediaManagement Component (label gösterme - card-header'da zaten var) --}}
                @if(isset($widgetId))
                    @livewire('mediamanagement::universal-media', [
                        'modelId' => $widgetId,
                        'modelType' => 'widget',
                        'modelClass' => 'Modules\WidgetManagement\App\Models\Widget',
                        'collections' => [$fieldName],
                        'maxGalleryItems' => 1,
                        'sortable' => false,
                        'setFeaturedFromGallery' => false,
                        'hideLabel' => true
                    ], key('widget-media-fb-' . $widgetId . '-' . $fieldName))
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Widget henüz kaydedilmedi. Lütfen önce kaydedin, sonra görsel ekleyebilirsiniz.
                    </div>
                @endif

                @if($helpText)
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>