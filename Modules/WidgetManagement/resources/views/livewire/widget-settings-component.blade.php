@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3 class="card-title d-flex align-items-center mb-0">
                        <i class="fas fa-sliders-h me-2"></i>
                        {{ $tenantWidget->widget->name }} - Özelleştirme
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
                    <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Bölümlere Dön
                    </a>
                </div>
            </div>
        </div>

    @if(empty($schema))
        <div class="alert alert-info mx-3 mb-3">
            <div class="d-flex">
                <div>
                    <i class="fas fa-info-circle fa-2x text-blue me-3"></i>
                </div>
                <div>
                    <h4>Özelleştirme seçeneği bulunamadı</h4>
                    <div class="text-muted">
                        Bu widget için tanımlanmış özelleştirme seçeneği bulunmuyor.
                    </div>
                </div>
            </div>
        </div>
    @else
        <form wire:submit.prevent="save(true)">
            <div class="alert alert-info mx-3 mb-3">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle text-blue me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Widget Özelleştirme</h4>
                        <div class="text-muted">
                            Bu sayfadaki ayarları değiştirerek widget görünümünü ve davranışını özelleştirebilirsiniz. Değişiklikler kaydedildikten sonra widget bu ayarlara göre görüntülenecektir.
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mx-2">
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
                    
                    <div class="card mt-3">
                        @include('components.form-footer', [
                            'route' => 'admin.widgetmanagement',
                            'modelId' => $tenantWidgetId
                        ])
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>