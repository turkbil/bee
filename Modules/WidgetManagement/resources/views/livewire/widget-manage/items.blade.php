<!-- İçerik Yapısı (Öğe Şeması) -->
<div class="tab-pane fade {{ $formMode === 'items' ? 'active show' : '' }}" id="tab-items">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-lightbulb text-blue me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">İçerik Yapısı Nedir?</h4>
                        <div class="text-muted">
                            İçerik yapısı, widgetınızın içerebileceği dinamik verilerin şablonunu belirler. Örneğin:<br>
                            <ul class="mb-0">
                                <li>Slider widget'ı için slaytların başlık, görsel ve açıklama alanları</li>
                                <li>SSS widget'ı için soru ve cevap alanları</li>
                                <li>Galeri widget'ı için resimler ve açıklamaları</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Yeni İçerik Alanı Ekle</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" wire:model="newField.name" 
                                    class="form-control @error('newField.name') is-invalid @enderror" 
                                    placeholder="title"
                                    id="field-name">
                                <label for="field-name">Alan Adı <span class="text-danger">*</span></label>
                                @error('newField.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-code me-1 text-blue"></i>
                                Harfler, rakamlar ve alt çizgi (_)
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" wire:model="newField.label" 
                                    class="form-control @error('newField.label') is-invalid @enderror" 
                                    placeholder="Başlık"
                                    id="field-label">
                                <label for="field-label">Etiket <span class="text-danger">*</span></label>
                                @error('newField.label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select wire:model.live="newField.type" 
                                    class="form-select @error('newField.type') is-invalid @enderror"
                                    id="field-type">
                                    @foreach($availableTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <label for="field-type">Alan Tipi <span class="text-danger">*</span></label>
                                @error('newField.type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check form-switch pt-4">
                                <input type="checkbox" id="required" class="form-check-input" wire:model.live="newField.required">
                                <label class="form-check-label" for="required">
                                    Zorunlu Alan
                                </label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary w-100 h-100" wire:click="addItemSchemaField">
                                <i class="fas fa-plus"></i>
                                <span class="d-none d-lg-inline ms-1">Ekle</span>
                            </button>
                        </div>
                    </div>
                    
                    @if($newField['type'] === 'select')
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card p-3">
                                <h4 class="mb-3">Seçenekler</h4>
                                
                                <div class="d-flex mb-3">
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" 
                                            class="btn {{ $optionFormat === 'key-value' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                            wire:click="$set('optionFormat', 'key-value')">
                                            <i class="fas fa-key me-1"></i> Anahtar-Değer Çiftleri
                                        </button>
                                        <button type="button" 
                                            class="btn {{ $optionFormat === 'text' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                            wire:click="$set('optionFormat', 'text')">
                                            <i class="fas fa-font me-1"></i> Metin Olarak Gir
                                        </button>
                                    </div>
                                </div>
                                
                                <div x-data="{}">
                                    <div x-show="$wire.optionFormat === 'key-value'">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="fw-bold text-muted">Gözüken Seçenek</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="fw-bold text-muted">Anahtar (slug)</div>
                                            </div>
                                        </div>
                                        
                                        @if(isset($newField['options_array']) && is_array($newField['options_array']) && count($newField['options_array']) > 0)
                                        @foreach($newField['options_array'] as $id => $option)
                                        <div class="row g-2 mb-3">
                                            <div class="col">
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-font"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                        wire:model.live="newField.options_array.{{ $id }}.value"
                                                        wire:change="slugifyOptionKey('{{ $id }}', $event.target.value)"
                                                        placeholder="Gözüken Değer">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-key"></i>
                                                    </span>
                                                    <input type="text" class="form-control" 
                                                        wire:model.live="newField.options_array.{{ $id }}.key" 
                                                        placeholder="Anahtar"
                                                        title="Değiştirmek isterseniz manuel düzenleyebilirsiniz">
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="javascript:void(0)" class="btn btn-outline-danger btn-icon" 
                                                    wire:click="removeFieldOption('{{ $id }}')" 
                                                    title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                        @endforeach
                                        @else
                                            <div class="text-muted text-center py-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Henüz seçenek eklenmemiş
                                            </div>
                                        @endif
                                        
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addFieldOption">
                                                <i class="fas fa-plus me-1"></i> Seçenek Ekle
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div x-show="$wire.optionFormat === 'text'" class="mt-3">
                                        <label class="form-label mb-2">Her satıra bir seçenek yazın:</label>
                                        <textarea wire:model.live.debounce.500ms="newField.options"
                                            class="form-control @error('newField.options') is-invalid @enderror" rows="6"
                                            placeholder="Her satıra bir seçenek yazın:
erkek=Erkek
kadin=Kadın
diger=Diğer

veya sadece:
Erkek
Kadın
Diğer"></textarea>
                                        <small class="form-hint">
                                            Her satıra bir seçenek. Örnek: "erkek=Erkek" veya sadece "Erkek" yazabilirsiniz. Seçenek anahtarı otomatik olarak slug'a çevrilecektir.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tanımlı İçerik Alanları</h3>
                </div>
                <div class="card-body">
                    @if(empty($widget['item_schema']))
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fas fa-database fa-4x text-muted"></i>
                        </div>
                        <p class="empty-title">Henüz içerik alanı tanımlanmadı</p>
                        <p class="empty-subtitle text-muted">
                            Yukarıdaki formu kullanarak widget içerikleri için veri alanları tanımlayabilirsiniz.
                        </p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover">
                            <thead>
                                <tr>
                                    <th>Alan Adı</th>
                                    <th>Etiket</th>
                                    <th>Tip</th>
                                    <th>Zorunlu</th>
                                    <th width="100"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($widget['item_schema'] as $index => $field)
                                @if(!isset($field['hidden']) || !$field['hidden'])
                                <tr>
                                    <td>
                                        <code>{{ $field['name'] }}</code>
                                        @if(isset($field['system']) && $field['system'])
                                        <span class="badge bg-orange ms-1">Sistem</span>
                                        @endif
                                    </td>
                                    <td>{{ $field['label'] }}</td>
                                    <td>
                                        <span class="badge bg-blue-lt">
                                            @switch($field['type'])
                                                @case('text')
                                                    <i class="fas fa-font me-1"></i> Metin
                                                    @break
                                                @case('textarea')
                                                    <i class="fas fa-align-left me-1"></i> Uzun Metin
                                                    @break
                                                @case('number')
                                                    <i class="fas fa-hashtag me-1"></i> Sayı
                                                    @break
                                                @case('select')
                                                    <i class="fas fa-list me-1"></i> Seçim Kutusu
                                                    @break
                                                @case('checkbox')
                                                    <i class="fas fa-check-square me-1"></i> Onay Kutusu
                                                    @break
                                                @case('image')
                                                    <i class="fas fa-image me-1"></i> Resim
                                                    @break
                                                @case('image_multiple')
                                                    <i class="fas fa-images me-1"></i> Çoklu Resim
                                                    @break
                                                @case('color')
                                                    <i class="fas fa-palette me-1"></i> Renk
                                                    @break
                                                @case('date')
                                                    <i class="fas fa-calendar me-1"></i> Tarih
                                                    @break
                                                @case('time')
                                                    <i class="fas fa-clock me-1"></i> Saat
                                                    @break
                                                @case('email')
                                                    <i class="fas fa-envelope me-1"></i> E-posta
                                                    @break
                                                @case('tel')
                                                    <i class="fas fa-phone me-1"></i> Telefon
                                                    @break
                                                @case('url')
                                                    <i class="fas fa-link me-1"></i> URL
                                                    @break
                                                @default
                                                    {{ $field['type'] }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($field['required']) && $field['required'])
                                        <span class="badge bg-green">
                                            <i class="fas fa-check me-1"></i> Evet
                                        </span>
                                        @else
                                        <span class="badge bg-gray">
                                            <i class="fas fa-minus me-1"></i> Hayır
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!isset($field['system']) || !$field['system'])
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeItemSchemaField({{ $index }})">
                                            <i class="fas fa-trash me-1"></i> Sil
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>