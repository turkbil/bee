<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-{{ $widgetId ? 'edit' : 'plus' }} me-2"></i>
                {{ $widgetId ? 'Widget Düzenle: ' . $widget['name'] : 'Yeni Widget Ekle' }}
            </h3>
        </div>
        <div class="card-body">
            <div class="d-flex mb-3">
                <ul class="nav nav-tabs nav-fill">
                    <li class="nav-item">
                        <a class="nav-link {{ $formMode === 'base' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('base')">
                            <i class="fas fa-info-circle me-2"></i>
                            Temel Bilgiler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $formMode === 'design' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('design')">
                            <i class="fas fa-palette me-2"></i>
                            Tasarım
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $formMode === 'items' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('items')">
                            <i class="fas fa-list me-2"></i>
                            Öğe Şeması
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $formMode === 'settings' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('settings')">
                            <i class="fas fa-cog me-2"></i>
                            Ayar Şeması
                        </a>
                    </li>
                    @if($widgetId)
                    <li class="nav-item">
                        <a class="nav-link {{ $formMode === 'preview' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('preview')">
                            <i class="fas fa-eye me-2"></i>
                            Önizleme
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            
            <!-- Temel Bilgiler -->
            @if($formMode === 'base')
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label required">Widget Adı</label>
                        <input type="text" wire:model.live="widget.name" class="form-control @error('widget.name') is-invalid @enderror">
                        @error('widget.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Widget Slug</label>
                        <input type="text" wire:model="widget.slug" class="form-control @error('widget.slug') is-invalid @enderror">
                        @error('widget.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-hint">URL uyumlu olmalıdır. Örnek: my-first-widget</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea wire:model="widget.description" class="form-control @error('widget.description') is-invalid @enderror" rows="3"></textarea>
                        @error('widget.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Widget Tipi</label>
                        <select wire:model="widget.type" class="form-select @error('widget.type') is-invalid @enderror">
                            <option value="static">Statik</option>
                            <option value="dynamic">Dinamik</option>
                            <option value="module">Modül</option>
                            <option value="content">İçerik</option>
                        </select>
                        @error('widget.type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Modüller</label>
                        <select wire:model="widget.module_ids" class="form-select @error('widget.module_ids') is-invalid @enderror" multiple>
                            <option value="">Tüm modüllerde kullanılabilir</option>
                            @foreach($modules as $module)
                            <option value="{{ $module->get('id') }}">{{ $module->get('name') }}</option>
                            @endforeach
                        </select>
                        @error('widget.module_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-hint">Widget'ın kullanılabileceği modülleri seçin. Boş bırakırsanız tüm modüllerde kullanılabilir.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Önizleme Görseli</label>
                        <input type="file" wire:model="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror">
                        @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        
                        <div class="mt-3">
                            @if($thumbnail)
                                <img src="{{ $thumbnail->temporaryUrl() }}" class="img-fluid rounded" alt="Önizleme">
                            @elseif($widgetId && isset($widget['thumbnail']))
                                <img src="{{ asset('storage/widgets/' . $widget['slug'] . '/' . $widget['thumbnail']) }}" class="img-fluid rounded" alt="Mevcut görsel">
                            @else
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                        <p class="text-muted mt-2">Önizleme görseli yok</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="has_items" class="form-check-input" wire:model="widget.has_items">
                            <label class="form-check-label" for="has_items">Dinamik Öğe Desteği</label>
                        </div>
                        <div class="form-hint">Widget'a eklenebilecek içerik öğeleri (örn. slider için slaytlar)</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="is_active" class="form-check-input" wire:model="widget.is_active">
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" id="is_core" class="form-check-input" wire:model="widget.is_core">
                            <label class="form-check-label" for="is_core">Sistem Widget'ı</label>
                        </div>
                        <div class="form-hint">Sistem widget'ları tenant'lar tarafından silinemez veya devre dışı bırakılamaz</div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Tasarım -->
            @if($formMode === 'design')
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">HTML İçeriği</label>
                        <textarea wire:model="widget.content_html" class="form-control" rows="15" style="font-family: monospace;">{{ $widget['content_html'] }}</textarea>
                        <div class="form-hint">
                            Widget HTML şablonu. Değişkenler için {{değişken_adı}} biçimini kullanın.
                            <br>
                            Dinamik öğeler için: {{#each items}}...{{/each}} blokları kullanın.
                            <br>
                            Koşullu içerik için: {{#if değişken}}...{{else}}...{{/if}} blokları kullanın.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">CSS İçeriği</label>
                        <textarea wire:model="widget.content_css" class="form-control" rows="10" style="font-family: monospace;">{{ $widget['content_css'] }}</textarea>
                        <div class="form-hint">Widget CSS stilleri. Bu CSS kodu sadece bu widget için kullanılır.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">JavaScript İçeriği</label>
                        <textarea wire:model="widget.content_js" class="form-control" rows="10" style="font-family: monospace;">{{ $widget['content_js'] }}</textarea>
                        <div class="form-hint">Widget JavaScript kodu. Bu JS kodu sadece bu widget için kullanılır.</div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Öğe Şeması -->
            @if($formMode === 'items')
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Öğe şeması, dinamik widget'larda her bir öğe için toplanacak alanları tanımlar. Örneğin, bir slider widget'ı için her slayt öğesi için başlık, görsel, açıklama gibi alanlar tanımlayabilirsiniz.
                        <br>
                        <strong>Not:</strong> Bu özelliği kullanmak için "Dinamik Öğe Desteği" seçeneğini aktif etmelisiniz.
                    </div>
                    
                    @if(!$widget['has_items'])
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Öğe şeması tanımlamak için önce "Temel Bilgiler" sekmesinden "Dinamik Öğe Desteği" seçeneğini aktif etmelisiniz.
                    </div>
                    @else
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Yeni Alan Ekle</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label required">Alan Adı</label>
                                    <input type="text" wire:model="newField.name" class="form-control @error('newField.name') is-invalid @enderror" placeholder="title">
                                    @error('newField.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Etiket</label>
                                    <input type="text" wire:model="newField.label" class="form-control @error('newField.label') is-invalid @enderror" placeholder="Başlık">
                                    @error('newField.label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Alan Tipi</label>
                                    <select wire:model.live="newField.type" class="form-select @error('newField.type') is-invalid @enderror">
                                        <option value="text">Metin</option>
                                        <option value="textarea">Uzun Metin</option>
                                        <option value="number">Sayı</option>
                                        <option value="select">Seçim Kutusu</option>
                                        <option value="checkbox">Onay Kutusu</option>
                                        <option value="image">Resim</option>
                                        <option value="url">URL</option>
                                    </select>
                                    @error('newField.type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" id="required" class="form-check-input" wire:model="newField.required">
                                        <label class="form-check-label" for="required">Zorunlu</label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" wire:click="addItemSchemaField">Ekle</button>
                                </div>
                            </div>
                            
                            @if($newField['type'] === 'select')
                            <div class="mt-3">
                                <h4>Seçenekler</h4>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-3">
                                        <input type="text" wire:model="newOption.key" class="form-control" placeholder="Değer (key)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" wire:model="newOption.value" class="form-control" placeholder="Etiket">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-primary" wire:click="addFieldOption">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                @if(!empty($newField['options']))
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Değer</th>
                                                <th>Etiket</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($newField['options'] as $key => $value)
                                            <tr>
                                                <td>{{ $key }}</td>
                                                <td>{{ $value }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeFieldOption('{{ $key }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tanımlı Alanlar</h3>
                        </div>
                        <div class="card-body">
                            @if(empty($widget['item_schema']))
                            <div class="empty">
                                <div class="empty-img">
                                    <i class="fas fa-list fa-3x text-muted"></i>
                                </div>
                                <p class="empty-title">Henüz alan tanımlanmadı</p>
                                <p class="empty-subtitle text-muted">
                                    Yukarıdaki formu kullanarak widget öğeleri için alanlar tanımlayabilirsiniz.
                                </p>
                            </div>
                            @else
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Alan Adı</th>
                                            <th>Etiket</th>
                                            <th>Tip</th>
                                            <th>Zorunlu</th>
                                            <th>Seçenekler</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($widget['item_schema'] as $index => $field)
                                        <tr>
                                            <td><code>{{ $field['name'] }}</code></td>
                                            <td>{{ $field['label'] }}</td>
                                            <td>{{ $field['type'] }}</td>
                                            <td>
                                                @if(isset($field['required']) && $field['required'])
                                                <span class="badge bg-green">Evet</span>
                                                @else
                                                <span class="badge bg-gray">Hayır</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($field['options']) && is_array($field['options']))
                                                @foreach($field['options'] as $key => $value)
                                                <span class="badge bg-blue-lt me-1">{{ $key }} = {{ $value }}</span>
                                                @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeItemSchemaField({{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Ayar Şeması -->
            @if($formMode === 'settings')
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Ayar şeması, widget'ın görünümünü ve davranışını özelleştirmek için kullanılabilecek ayarları tanımlar. Örneğin, arkaplan rengi, başlık gösterme/gizleme, animasyon hızı gibi.
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Yeni Ayar Ekle</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
<div class="col-md-3">
                                    <label class="form-label required">Alan Adı</label>
                                    <input type="text" wire:model="newField.name" class="form-control @error('newField.name') is-invalid @enderror" placeholder="background_color">
                                    @error('newField.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Etiket</label>
                                    <input type="text" wire:model="newField.label" class="form-control @error('newField.label') is-invalid @enderror" placeholder="Arkaplan Rengi">
                                    @error('newField.label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Alan Tipi</label>
                                    <select wire:model.live="newField.type" class="form-select @error('newField.type') is-invalid @enderror">
                                        <option value="text">Metin</option>
                                        <option value="textarea">Uzun Metin</option>
                                        <option value="number">Sayı</option>
                                        <option value="select">Seçim Kutusu</option>
                                        <option value="checkbox">Onay Kutusu</option>
                                        <option value="image">Resim</option>
                                        <option value="url">URL</option>
                                        <option value="color">Renk</option>
                                    </select>
                                    @error('newField.type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" id="required_setting" class="form-check-input" wire:model="newField.required">
                                        <label class="form-check-label" for="required_setting">Zorunlu</label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" wire:click="addSettingsSchemaField">Ekle</button>
                                </div>
                            </div>
                            
                            @if($newField['type'] === 'select')
                            <div class="mt-3">
                                <h4>Seçenekler</h4>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-3">
                                        <input type="text" wire:model="newOption.key" class="form-control" placeholder="Değer (key)">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" wire:model="newOption.value" class="form-control" placeholder="Etiket">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-primary" wire:click="addFieldOption">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                @if(!empty($newField['options']))
                                <div class="table-responsive">
                                    <table class="table table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Değer</th>
                                                <th>Etiket</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($newField['options'] as $key => $value)
                                            <tr>
                                                <td>{{ $key }}</td>
                                                <td>{{ $value }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeFieldOption('{{ $key }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tanımlı Ayarlar</h3>
                        </div>
                        <div class="card-body">
                            @if(empty($widget['settings_schema']))
                            <div class="empty">
                                <div class="empty-img">
                                    <i class="fas fa-cogs fa-3x text-muted"></i>
                                </div>
                                <p class="empty-title">Henüz ayar tanımlanmadı</p>
                                <p class="empty-subtitle text-muted">
                                    Yukarıdaki formu kullanarak widget için ayarlar tanımlayabilirsiniz.
                                </p>
                            </div>
                            @else
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Alan Adı</th>
                                            <th>Etiket</th>
                                            <th>Tip</th>
                                            <th>Zorunlu</th>
                                            <th>Seçenekler</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($widget['settings_schema'] as $index => $field)
                                        <tr>
                                            <td><code>{{ $field['name'] }}</code></td>
                                            <td>{{ $field['label'] }}</td>
                                            <td>{{ $field['type'] }}</td>
                                            <td>
                                                @if(isset($field['required']) && $field['required'])
                                                <span class="badge bg-green">Evet</span>
                                                @else
                                                <span class="badge bg-gray">Hayır</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($field['options']) && is_array($field['options']))
                                                @foreach($field['options'] as $key => $value)
                                                <span class="badge bg-blue-lt me-1">{{ $key }} = {{ $value }}</span>
                                                @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeSettingsSchemaField({{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Önizleme -->
            @if($formMode === 'preview' && $widgetId)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Bu bölümde widget'ın nasıl görüneceğini önizleyebilirsiniz. Değişikliklerinizi görmek için önce kaydetmeniz gerekiyor.
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-label">Önizleme Stili</div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary active">Masaüstü</button>
                            <button type="button" class="btn btn-outline-primary">Tablet</button>
                            <button type="button" class="btn btn-outline-primary">Mobil</button>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Widget Önizleme</h3>
                        </div>
                        <div class="card-body">
                            <div class="widget-preview-frame border p-3">
                                <!-- Widget preview content will be loaded here dynamically -->
                                <iframe id="widget-preview-frame" src="{{ route('admin.widgetmanagement.preview', $widgetId) }}" style="width: 100%; min-height: 400px; border: none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-link">
                <i class="fas fa-arrow-left me-2"></i>
                Geri
            </a>
            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                <i class="fas fa-save me-2"></i>
                <span wire:loading.remove wire:target="save">Kaydet</span>
                <span wire:loading wire:target="save">Kaydediliyor...</span>
            </button>
        </div>
    </div>
</div>