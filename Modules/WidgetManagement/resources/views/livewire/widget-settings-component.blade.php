<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $tenantWidget->widget->name }} - Ayarlar</h3>
        </div>
        <div class="card-body">
            @if(empty($schema))
                <div class="alert alert-info">
                    Bu widget için tanımlanmış ayar bulunmuyor.
                </div>
            @else
                <form wire:submit.prevent="save">
                    @foreach($schema as $field)
                        <div class="form-group mb-3">
                            <label for="field-{{ $field['name'] }}" class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                {{ $field['label'] }}
                            </label>
                            
                            @if($field['type'] === 'text')
                                <input type="text" 
                                    wire:model="settings.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-control @error('settings.' . $field['name']) is-invalid @enderror">
                            
                            @elseif($field['type'] === 'textarea')
                                <textarea 
                                    wire:model="settings.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-control @error('settings.' . $field['name']) is-invalid @enderror"
                                    rows="3"></textarea>
                            
                            @elseif($field['type'] === 'image')
                                <div class="image-upload-container">
                                    @if(isset($settings[$field['name']]) && is_string($settings[$field['name']]))
                                        <div class="current-image mb-2">
                                            <img src="{{ $settings[$field['name']] }}" alt="Current Image" class="img-thumbnail" style="max-height: 100px;">
                                        </div>
                                    @endif
                                    
                                    <input type="file" 
                                        wire:model="temporaryUpload.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-control @error('temporaryUpload.' . $field['name']) is-invalid @enderror"
                                        accept="image/*">
                                </div>
                                
                                <div wire:loading wire:target="temporaryUpload.{{ $field['name'] }}">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ms-1">Yükleniyor...</span>
                                </div>
                            
                            @elseif($field['type'] === 'url')
                                <input type="url" 
                                    wire:model="settings.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-control @error('settings.' . $field['name']) is-invalid @enderror">
                            
                            @elseif($field['type'] === 'checkbox')
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                        wire:model="settings.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-check-input @error('settings.' . $field['name']) is-invalid @enderror">
                                    <label class="form-check-label" for="field-{{ $field['name'] }}">
                                        {{ isset($settings[$field['name']]) && $settings[$field['name']] ? 'Evet' : 'Hayır' }}
                                    </label>
                                </div>
                            
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
                                <div class="d-flex align-items-center">
                                    <input type="color" 
                                        wire:model="settings.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-control form-control-color @error('settings.' . $field['name']) is-invalid @enderror">
                                    <span class="ms-2">{{ $settings[$field['name']] ?? '#000000' }}</span>
                                </div>
                            
                            @elseif($field['type'] === 'number')
                                <input type="number" 
                                    wire:model="settings.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-control @error('settings.' . $field['name']) is-invalid @enderror">
                            @endif
                            
                            @error('settings.' . $field['name'])
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                    
                    <div class="form-actions d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Kaydet
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>