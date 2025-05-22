@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-sliders-h me-2"></i>
                    {{ $tenantWidget->widget->name }} - Özelleştirme
                </h3>
                <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Bölümlere Dön
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(empty($schema))
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-sliders-h fa-4x text-muted"></i>
                    </div>
                    <p class="empty-title">Özelleştirme seçeneği bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Bu widget için tanımlanmış özelleştirme seçeneği bulunmuyor.
                    </p>
                </div>
            @else
                <div class="alert alert-info mb-4">
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
            
                <form wire:submit.prevent="save">
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
                                                                'settings' => $settings,
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
                                        'settings' => $settings,
                                        'temporaryUpload' => $temporaryUpload
                                    ])
                                @endif
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="card-footer d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> İptal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <div wire:loading.remove wire:target="save">
                                <i class="fas fa-save me-1"></i> Kaydet
                            </div>
                            <div wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin me-1"></i> Kaydediliyor...
                            </div>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>