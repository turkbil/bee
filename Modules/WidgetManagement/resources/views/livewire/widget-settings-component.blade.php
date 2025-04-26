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
            
                <form wire:submit.prevent="save" class="row g-3">
                    <!-- Başlık alanı -->
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-status-start bg-primary"></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="field-title" class="form-label required">
                                        Bileşen Başlığı
                                    </label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-heading"></i>
                                        </span>
                                        <input type="text" 
                                            wire:model="settings.title" 
                                            id="field-title" 
                                            class="form-control @error('settings.title') is-invalid @enderror"
                                            placeholder="Bileşen Başlığı">
                                    </div>
                                    @error('settings.title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @foreach($schema as $field)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-status-start bg-green"></div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="field-{{ $field['name'] }}" class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                            {{ $field['label'] }}
                                        </label>
                                        
                                        @if($field['type'] === 'text')
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <i class="fas fa-font"></i>
                                                </span>
                                                <input type="text" 
                                                    wire:model="settings.{{ $field['name'] }}" 
                                                    id="field-{{ $field['name'] }}" 
                                                    class="form-control @error('settings.' . $field['name']) is-invalid @enderror"
                                                    placeholder="{{ $field['label'] }}">
                                            </div>
                                        
                                        @elseif($field['type'] === 'textarea')
                                            <textarea 
                                                wire:model="settings.{{ $field['name'] }}" 
                                                id="field-{{ $field['name'] }}" 
                                                class="form-control @error('settings.' . $field['name']) is-invalid @enderror"
                                                rows="4"
                                                placeholder="{{ $field['label'] }}"></textarea>
                                        
                                        @elseif($field['type'] === 'image')
                                            <div class="form-control p-3" style="height: auto;"
                                                onclick="document.getElementById('field-{{ $field['name'] }}').click()">
                                                <input type="file" 
                                                    wire:model="temporaryUpload.{{ $field['name'] }}" 
                                                    id="field-{{ $field['name'] }}" 
                                                    class="d-none"
                                                    accept="image/*">
                                                
                                                @if(isset($settings[$field['name']]) && is_string($settings[$field['name']]))
                                                    <img src="{{ $settings[$field['name']] }}" 
                                                        class="img-fluid rounded mx-auto d-block mb-2" 
                                                        style="max-height: 120px;">
                                                @elseif(isset($temporaryUpload[$field['name']]))
                                                    <img src="{{ $temporaryUpload[$field['name']]->temporaryUrl() }}" 
                                                        class="img-fluid rounded mx-auto d-block mb-2" 
                                                        style="max-height: 120px;">
                                                @endif
                                                
                                                <div class="text-center">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                    <p class="mb-0">Görseli sürükleyip bırakın veya seçmek için tıklayın</p>
                                                    <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 1MB</p>
                                                </div>
                                            </div>
                                        
                                        @elseif($field['type'] === 'checkbox')
                                            <label class="form-check form-switch">
                                                <input type="checkbox" 
                                                    wire:model="settings.{{ $field['name'] }}" 
                                                    id="field-{{ $field['name'] }}" 
                                                    class="form-check-input @error('settings.' . $field['name']) is-invalid @enderror">
                                                <span class="form-check-label">{{ isset($settings[$field['name']]) && $settings[$field['name']] ? 'Evet' : 'Hayır' }}</span>
                                            </label>
                                        
                                        @elseif($field['type'] === 'select')
                                            <select 
                                                wire:model="settings.{{ $field['name'] }}" 
                                                id="field-{{ $field['name'] }}" 
                                                class="form-select @error('settings.' . $field['name']) is-invalid @enderror">
                                                <option value="">Seçiniz</option>
                                                @foreach($field['options'] ?? [] as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        
                                        @elseif($field['type'] === 'color')
                                            <div class="row g-2 align-items-center">
                                                <div class="col-auto">
                                                    <input type="color" 
                                                        wire:model="settings.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control form-control-color @error('settings.' . $field['name']) is-invalid @enderror"
                                                        title="Renk seçin">
                                                </div>
                                                <div class="col-auto">
                                                    <span class="form-color-swatch" style="background-color: {{ $settings[$field['name']] ?? '#ffffff' }}; width: 30px; height: 30px; display: inline-block; border-radius: 4px; border: 1px solid #dee2e6;"></span>
                                                </div>
                                                <div class="col">
                                                    <span class="text-muted">{{ $settings[$field['name']] ?? '#ffffff' }}</span>
                                                </div>
                                            </div>
                                        
                                        @elseif($field['type'] === 'number')
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <i class="fas fa-hashtag"></i>
                                                </span>
                                                <input type="number" 
                                                    wire:model="settings.{{ $field['name'] }}" 
                                                    id="field-{{ $field['name'] }}" 
                                                    class="form-control @error('settings.' . $field['name']) is-invalid @enderror">
                                            </div>
                                        @endif
                                        
                                        @error('settings.' . $field['name'])
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        
                                        @if(isset($field['description']))
                                            <div class="form-hint">
                                                <i class="fas fa-info-circle me-1 text-blue"></i>
                                                {{ $field['description'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="col-12">
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
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>