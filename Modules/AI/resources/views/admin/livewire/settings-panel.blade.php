<div>
    @include('ai::admin.helper')
    
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#tabs-settings" class="nav-link active" data-bs-toggle="tab">Temel Ayarlar</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-limits" class="nav-link" data-bs-toggle="tab">Kullanım Limitleri</a>
                </li>
                <li class="nav-item">
                    <a href="#tabs-prompts" class="nav-link" data-bs-toggle="tab">Prompt Şablonları</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Temel Ayarlar -->
                <div class="tab-pane active show" id="tabs-settings">
                    <form wire:submit="saveSettings">
                        <div class="form-floating mb-3">
                            <input type="password" wire:model="settings.api_key" 
                                class="form-control @error('settings.api_key') is-invalid @enderror" 
                                placeholder="DeepSeek API anahtarınızı girin" id="api_key_input">
                            <label for="api_key_input">API Anahtarı</label>
                            <div class="form-text d-flex align-items-center">
                                <i class="fa-thin fa-circle-info me-2"></i>
                                DeepSeek API anahtarını 
                                <a href="https://platform.deepseek.com/" target="_blank" class="ms-1">DeepSeek platformundan</a> alabilirsiniz.
                                <button type="button" id="togglePassword" class="btn btn-sm btn-ghost-secondary ms-2">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('settings.api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <select wire:model="settings.model" 
                                class="form-select @error('settings.model') is-invalid @enderror" 
                                id="model_select">
                                <option value="deepseek-chat">DeepSeek Chat</option>
                                <option value="deepseek-coder">DeepSeek Coder</option>
                            </select>
                            <label for="model_select">Model</label>
                            @error('settings.model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="number" wire:model="settings.max_tokens" 
                                class="form-control @error('settings.max_tokens') is-invalid @enderror" 
                                placeholder="Maksimum token sayısı" id="max_tokens_input">
                            <label for="max_tokens_input">Maksimum Token</label>
                            <div class="form-text">
                                <i class="fa-thin fa-circle-info me-2"></i>
                                Bir yanıtın maksimum token sayısı (4096 önerilir)
                            </div>
                            @error('settings.max_tokens')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sıcaklık (Temperature): {{ $settings['temperature'] }}</label>
                            <input type="range" wire:model="settings.temperature" 
                                class="form-range @error('settings.temperature') is-invalid @enderror" 
                                min="0" max="1" step="0.1" id="temperature_range">
                            <div class="form-text">
                                <i class="fa-thin fa-circle-info me-2"></i>
                                Daha düşük değerler daha tutarlı yanıtlar üretir
                            </div>
                            @error('settings.temperature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" 
                                    wire:model="settings.enabled" value="1">
                                <div class="state p-success p-on ms-2">
                                    <label>Aktif</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>Aktif Değil</label>
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                <i class="fa-thin fa-circle-info me-2"></i>
                                Devre dışı bırakıldığında, hiçbir kullanıcı AI asistanını kullanamaz
                            </div>
                        </div>
                        
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Ayarları Kaydet
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Kullanım Limitleri -->
                <div class="tab-pane" id="tabs-limits">
                    <form wire:submit="saveLimits">
                        <div class="form-floating mb-3">
                            <input type="number" wire:model="limits.daily_limit" 
                                class="form-control @error('limits.daily_limit') is-invalid @enderror" 
                                placeholder="Günlük kullanım limiti" id="daily_limit_input">
                            <label for="daily_limit_input">Günlük Limit</label>
                            <div class="form-text">
                                <i class="fa-thin fa-circle-info me-2"></i>
                                Bir kullanıcının günlük olarak gönderebileceği mesaj sayısı
                            </div>
                            @error('limits.daily_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="number" wire:model="limits.monthly_limit" 
                                class="form-control @error('limits.monthly_limit') is-invalid @enderror" 
                                placeholder="Aylık kullanım limiti" id="monthly_limit_input">
                            <label for="monthly_limit_input">Aylık Limit</label>
                            <div class="form-text">
                                <i class="fa-thin fa-circle-info me-2"></i>
                                Bir kullanıcının aylık olarak gönderebileceği mesaj sayısı
                            </div>
                            @error('limits.monthly_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Limitleri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Prompt Şablonları -->
                <div class="tab-pane" id="tabs-prompts">
                    <div class="d-flex justify-content-between mb-3">
                        <h4>Prompt Şablonları</h4>
                        <button class="btn btn-primary" wire:click="resetPromptForm">
                            <i class="fas fa-plus me-2"></i> Yeni Prompt
                        </button>
                    </div>
                    
                    <form wire:submit="savePrompt" class="card mb-4">
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="prompt.name" 
                                    class="form-control @error('prompt.name') is-invalid @enderror" 
                                    placeholder="Prompt şablonu adı" id="prompt_name_input">
                                <label for="prompt_name_input">Prompt Adı</label>
                                @error('prompt.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Prompt İçeriği</label>
                                <textarea wire:model="prompt.content" 
                                    class="form-control @error('prompt.content') is-invalid @enderror" 
                                    rows="5" placeholder="Sistem prompt içeriği"></textarea>
                                @error('prompt.content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_default" name="is_default" 
                                        wire:model="prompt.is_default" value="1">
                                    <div class="state p-success p-on ms-2">
                                        <label>Varsayılan Prompt</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>Varsayılan Değil</label>
                                    </div>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="fa-thin fa-circle-info me-2"></i>
                                    Varsayılan prompt, özel bir prompt seçilmediğinde kullanılır
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                @if($editingPromptId)
                                    <button type="button" class="btn btn-ghost-danger" wire:click="resetPromptForm">
                                        <i class="fas fa-times me-2"></i> İptal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Güncelle
                                    </button>
                                @else
                                    <div></div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Ekle
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Adı</th>
                                    <th>Varsayılan</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prompts as $promptItem)
                                    <tr>
                                        <td>{{ $promptItem->name }}</td>
                                        <td>
                                            @if($promptItem->is_default)
                                                <span class="badge bg-success">Varsayılan</span>
                                            @else
                                                <span class="badge bg-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <button class="btn btn-icon btn-ghost-secondary" wire:click="editPrompt({{ $promptItem->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-icon btn-ghost-danger" wire:click="deletePrompt({{ $promptItem->id }})" @if($promptItem->is_default) disabled @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3">
                                            <div class="empty">
                                                <p class="empty-title">Henüz prompt şablonu yok</p>
                                                <p class="empty-subtitle text-muted">
                                                    Yukarıdaki formu kullanarak yeni bir prompt şablonu ekleyebilirsiniz.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#api_key_input');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
    @endpush
</div>