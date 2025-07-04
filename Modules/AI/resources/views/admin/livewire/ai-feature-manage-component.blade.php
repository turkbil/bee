@include('ai::admin.helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">
                            <i class="fas fa-info-circle me-2"></i>Temel Bilgiler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-comments me-2"></i>Prompt Yönetimi
                            @if($featureId && isset($featureStats['prompts_count']))
                                <span class="badge bg-azure-lt ms-1">{{ $featureStats['prompts_count'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-3" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-palette me-2"></i>UI Ayarları
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tabs-4" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-lightbulb me-2"></i>Örnek İçerikler
                            <span class="badge bg-warning-lt ms-1">{{ count($inputs['example_inputs']) }}</span>
                        </a>
                    </li>
                    @if($featureId)
                    <li class="nav-item">
                        <a href="#tabs-5" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-chart-bar me-2"></i>İstatistikler
                        </a>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- Temel Bilgiler Tab -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Özellik Adı -->
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model.live="inputs.name"
                                        class="form-control @error('inputs.name') is-invalid @enderror"
                                        placeholder="AI özelliğinin adı">
                                    <label>Özellik Adı *</label>
                                    @error('inputs.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Slug -->
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model.defer="inputs.slug"
                                        class="form-control @error('inputs.slug') is-invalid @enderror"
                                        placeholder="url-slug">
                                    <label>URL Slug *</label>
                                    @error('inputs.slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">URL'de görünecek benzersiz tanımlayıcı</small>
                                </div>

                                <!-- Açıklama -->
                                <div class="form-floating mb-3">
                                    <textarea wire:model.defer="inputs.description" 
                                              class="form-control @error('inputs.description') is-invalid @enderror"
                                              placeholder="Özellik açıklaması" 
                                              style="height: 100px"></textarea>
                                    <label>Açıklama</label>
                                    @error('inputs.description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Emoji ve Icon -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" wire:model.defer="inputs.emoji"
                                                class="form-control @error('inputs.emoji') is-invalid @enderror"
                                                placeholder="🤖">
                                            <label>Emoji</label>
                                            @error('inputs.emoji')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" wire:model.defer="inputs.icon"
                                                class="form-control @error('inputs.icon') is-invalid @enderror"
                                                placeholder="fas fa-robot">
                                            <label>FontAwesome Icon</label>
                                            @error('inputs.icon')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Sıralama -->
                                <div class="form-floating mb-3">
                                    <input type="number" wire:model.defer="inputs.sort_order"
                                        class="form-control @error('inputs.sort_order') is-invalid @enderror"
                                        min="1" placeholder="Sıralama">
                                    <label>Sıralama</label>
                                    @error('inputs.sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Daha düşük sayılar önce görünür</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Kategori -->
                                <div class="form-floating mb-3">
                                    <select wire:model.defer="inputs.category" 
                                            class="form-control @error('inputs.category') is-invalid @enderror">
                                        <option value="">Kategori Seçin</option>
                                        @foreach($categories as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label>Kategori *</label>
                                    @error('inputs.category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Durum -->
                                <div class="form-floating mb-3">
                                    <select wire:model.defer="inputs.status" 
                                            class="form-control @error('inputs.status') is-invalid @enderror"
                                            {{ $feature && $feature->is_system ? 'disabled' : '' }}>
                                        @foreach($statuses as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label>Durum *</label>
                                    @if($feature && $feature->is_system)
                                        <small class="form-hint text-info">Sistem özelliği - durum değiştirilemez</small>
                                    @endif
                                    @error('inputs.status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Badge Rengi -->
                                <div class="form-floating mb-3">
                                    <select wire:model.defer="inputs.badge_color" 
                                            class="form-control @error('inputs.badge_color') is-invalid @enderror">
                                        <option value="success">Yeşil (Success)</option>
                                        <option value="primary">Mavi (Primary)</option>
                                        <option value="warning">Sarı (Warning)</option>
                                        <option value="info">Açık Mavi (Info)</option>
                                        <option value="danger">Kırmızı (Danger)</option>
                                        <option value="secondary">Gri (Secondary)</option>
                                    </select>
                                    <label>Badge Rengi</label>
                                    @error('inputs.badge_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Checkboxes -->
                                <div class="mb-3">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               wire:model.defer="inputs.show_in_examples" value="1">
                                        <label class="form-check-label">Examples sayfasında göster</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               wire:model.defer="inputs.is_featured" value="1">
                                        <label class="form-check-label">Öne çıkan özellik</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               wire:model.defer="inputs.requires_pro" value="1">
                                        <label class="form-check-label">Pro üyelik gerekli</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               wire:model.defer="inputs.requires_input" value="1">
                                        <label class="form-check-label">Input alanı gerekli</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Yanıt Ayarları -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select wire:model.defer="inputs.response_length" 
                                            class="form-control @error('inputs.response_length') is-invalid @enderror">
                                        <option value="short">Kısa</option>
                                        <option value="medium">Orta</option>
                                        <option value="long">Uzun</option>
                                        <option value="variable">Değişken</option>
                                    </select>
                                    <label>Yanıt Uzunluğu</label>
                                    @error('inputs.response_length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select wire:model.defer="inputs.response_format" 
                                            class="form-control @error('inputs.response_format') is-invalid @enderror">
                                        <option value="text">Düz Metin</option>
                                        <option value="markdown">Markdown</option>
                                        <option value="structured">Yapılandırılmış</option>
                                        <option value="code">Kod</option>
                                        <option value="list">Liste</option>
                                    </select>
                                    <label>Yanıt Formatı</label>
                                    @error('inputs.response_format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select wire:model.defer="inputs.complexity_level" 
                                            class="form-control @error('inputs.complexity_level') is-invalid @enderror">
                                        <option value="beginner">Başlangıç</option>
                                        <option value="intermediate">Orta</option>
                                        <option value="advanced">İleri</option>
                                        <option value="expert">Uzman</option>
                                    </select>
                                    <label>Karmaşıklık Seviyesi</label>
                                    @error('inputs.complexity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prompt Yönetimi Tab -->
                    <div class="tab-pane fade" id="tabs-2">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>Prompt Bağlantıları</h4>
                                <button type="button" class="btn btn-primary btn-sm" wire:click="addPrompt">
                                    <i class="fas fa-plus me-1"></i>Prompt Ekle
                                </button>
                            </div>
                            <small class="text-muted">Bu AI özelliği için kullanılacak prompt'ları ve rollerini belirleyin.</small>
                        </div>

                        <!-- Mevcut Prompt'lar -->
                        @if($featureId && count($existingPrompts) > 0)
                        <div class="mb-4">
                            <h5 class="text-muted mb-3">Mevcut Prompt'lar</h5>
                            @foreach($existingPrompts as $id => $promptData)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-floating mb-3">
                                                <select wire:model.defer="existingPrompts.{{ $id }}.prompt_id" 
                                                        class="form-control">
                                                    <option value="">Prompt Seçin</option>
                                                    @foreach($availablePrompts as $prompt)
                                                        <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Prompt</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating mb-3">
                                                <select wire:model.defer="existingPrompts.{{ $id }}.role" 
                                                        class="form-control">
                                                    <option value="primary">Ana Prompt</option>
                                                    <option value="secondary">İkincil Prompt</option>
                                                    <option value="hidden">Gizli Sistem</option>
                                                    <option value="conditional">Şartlı Prompt</option>
                                                    <option value="formatting">Format Düzenleme</option>
                                                    <option value="validation">Doğrulama</option>
                                                </select>
                                                <label>Rol</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating mb-3">
                                                <input type="number" wire:model.defer="existingPrompts.{{ $id }}.priority" 
                                                       class="form-control" min="0">
                                                <label>Öncelik</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch mt-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       wire:model.defer="existingPrompts.{{ $id }}.is_required" value="1">
                                                <label class="form-check-label">Zorunlu</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-3" 
                                                    wire:click="removeExistingPrompt({{ $id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Yeni Prompt'lar -->
                        @if(count($newPrompts) > 0)
                        <div class="mb-4">
                            <h5 class="text-success mb-3">Yeni Prompt'lar</h5>
                            @foreach($newPrompts as $index => $promptData)
                            <div class="card mb-3 border-success">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-floating mb-3">
                                                <select wire:model.defer="newPrompts.{{ $index }}.prompt_id" 
                                                        class="form-control">
                                                    <option value="">Prompt Seçin</option>
                                                    @foreach($availablePrompts as $prompt)
                                                        <option value="{{ $prompt->id }}">{{ $prompt->name }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Prompt</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating mb-3">
                                                <select wire:model.defer="newPrompts.{{ $index }}.role" 
                                                        class="form-control">
                                                    <option value="primary">Ana Prompt</option>
                                                    <option value="secondary">İkincil Prompt</option>
                                                    <option value="hidden">Gizli Sistem</option>
                                                    <option value="conditional">Şartlı Prompt</option>
                                                    <option value="formatting">Format Düzenleme</option>
                                                    <option value="validation">Doğrulama</option>
                                                </select>
                                                <label>Rol</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating mb-3">
                                                <input type="number" wire:model.defer="newPrompts.{{ $index }}.priority" 
                                                       class="form-control" min="0">
                                                <label>Öncelik</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch mt-3">
                                                <input class="form-check-input" type="checkbox" 
                                                       wire:model.defer="newPrompts.{{ $index }}.is_required" value="1">
                                                <label class="form-check-label">Zorunlu</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-3" 
                                                    wire:click="removePrompt({{ $index }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(count($existingPrompts) == 0 && count($newPrompts) == 0)
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Henüz prompt eklenmemiş</p>
                            <button type="button" class="btn btn-primary" wire:click="addPrompt">
                                <i class="fas fa-plus me-1"></i>İlk Prompt'u Ekle
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- UI Ayarları Tab -->
                    <div class="tab-pane fade" id="tabs-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model.defer="inputs.input_placeholder"
                                        class="form-control @error('inputs.input_placeholder') is-invalid @enderror"
                                        placeholder="Input placeholder metni">
                                    <label>Input Placeholder</label>
                                    @error('inputs.input_placeholder')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" wire:model.defer="inputs.button_text"
                                        class="form-control @error('inputs.button_text') is-invalid @enderror"
                                        placeholder="Test butonu metni">
                                    <label>Test Butonu Metni</label>
                                    @error('inputs.button_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model.defer="inputs.meta_title"
                                        class="form-control @error('inputs.meta_title') is-invalid @enderror"
                                        placeholder="SEO başlığı">
                                    <label>Meta Title</label>
                                    @error('inputs.meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea wire:model.defer="inputs.meta_description"
                                              class="form-control @error('inputs.meta_description') is-invalid @enderror"
                                              placeholder="SEO açıklaması" 
                                              style="height: 80px"></textarea>
                                    <label>Meta Description</label>
                                    @error('inputs.meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Örnek İçerikler Tab -->
                    <div class="tab-pane fade" id="tabs-4">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>Hızlı Örnekler</h4>
                                <button type="button" class="btn btn-primary btn-sm" wire:click="addExample">
                                    <i class="fas fa-plus me-1"></i>Örnek Ekle
                                </button>
                            </div>
                            <small class="text-muted">Kullanıcıların hızlıca test edebilmesi için örnek metinler ekleyin.</small>
                        </div>

                        @if(count($inputs['example_inputs']) > 0)
                        @foreach($inputs['example_inputs'] as $index => $example)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" wire:model.defer="inputs.example_inputs.{{ $index }}.label"
                                                   class="form-control" placeholder="Örnek etiketi">
                                            <label>Etiket</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-floating mb-3">
                                            <input type="text" wire:model.defer="inputs.example_inputs.{{ $index }}.text"
                                                   class="form-control" placeholder="Örnek metin">
                                            <label>Örnek Metin</label>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm mt-3" 
                                                wire:click="removeExample({{ $index }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Henüz örnek eklenmemiş</p>
                            <button type="button" class="btn btn-primary" wire:click="addExample">
                                <i class="fas fa-plus me-1"></i>İlk Örneği Ekle
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- İstatistikler Tab -->
                    @if($featureId)
                    <div class="tab-pane fade" id="tabs-5">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3 class="m-0 text-primary">{{ number_format($featureStats['usage_count'] ?? 0) }}</h3>
                                        <div class="text-muted">Toplam Kullanım</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3 class="m-0 text-success">{{ ($featureStats['avg_rating'] ?? 0) > 0 ? number_format($featureStats['avg_rating'], 1) : '0.0' }}</h3>
                                        <div class="text-muted">Ortalama Puan</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3 class="m-0 text-warning">{{ $featureStats['rating_count'] ?? 0 }}</h3>
                                        <div class="text-muted">Puan Sayısı</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h3 class="m-0 text-info">{{ number_format($featureStats['total_tokens'] ?? 0) }}</h3>
                                        <div class="text-muted">Kullanılan Token</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Özellik Detayları</h3>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row">
                                            <dt class="col-sm-3">Oluşturulma:</dt>
                                            <dd class="col-sm-9">{{ $featureStats['created_at']?->format('d.m.Y H:i') ?? 'Bilinmiyor' }}</dd>

                                            <dt class="col-sm-3">Son Güncelleme:</dt>
                                            <dd class="col-sm-9">{{ $featureStats['updated_at']?->format('d.m.Y H:i') ?? 'Bilinmiyor' }}</dd>

                                            <dt class="col-sm-3">Son Kullanım:</dt>
                                            <dd class="col-sm-9">{{ $featureStats['last_used_at'] ? $featureStats['last_used_at']->format('d.m.Y H:i') : 'Henüz kullanılmadı' }}</dd>

                                            <dt class="col-sm-3">Sistem Özelliği:</dt>
                                            <dd class="col-sm-9">
                                                @if($featureStats['is_system'] ?? false)
                                                    <span class="badge bg-info">Evet - Silinemez</span>
                                                @else
                                                    <span class="badge bg-secondary">Hayır</span>
                                                @endif
                                            </dd>

                                            <dt class="col-sm-3">Bağlı Prompt Sayısı:</dt>
                                            <dd class="col-sm-9">{{ $featureStats['prompts_count'] ?? 0 }} adet</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <x-form-footer route="admin.ai.features" :model-id="$featureId" 
                           :can-delete="$feature && !$feature->is_system" 
                           delete-action="delete" />
        </div>
    </form>
</div>