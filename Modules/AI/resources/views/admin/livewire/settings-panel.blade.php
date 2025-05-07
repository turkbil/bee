<div>
    @include('ai::admin.helper')
    
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">AI Ayarları</h3>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" data-bs-toggle="tabs">
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
                            
                            <div class="tab-content">
                                <!-- Temel Ayarlar -->
                                <div class="tab-pane active show" id="tabs-settings">
                                    <form wire:submit.prevent="saveSettings">
                                        <div class="mb-3">
                                            <label class="form-label">API Anahtarı</label>
                                            <div class="input-group input-group-flat">
                                                <input type="password" 
                                                    wire:model="settings.api_key" 
                                                    class="form-control @error('settings.api_key') is-invalid @enderror" 
                                                    placeholder="DeepSeek API anahtarınızı girin">
                                                <span class="input-group-text">
                                                    <a href="#" class="link-secondary" title="API anahtarını göster" id="togglePassword">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </span>
                                            </div>
                                            @error('settings.api_key')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-hint">DeepSeek API anahtarını <a href="https://platform.deepseek.com/" target="_blank">DeepSeek platformundan</a> alabilirsiniz.</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Model</label>
                                            <select wire:model="settings.model" class="form-select @error('settings.model') is-invalid @enderror">
                                                <option value="deepseek-chat">DeepSeek Chat</option>
                                                <option value="deepseek-coder">DeepSeek Coder</option>
                                            </select>
                                            @error('settings.model')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Maksimum Token</label>
                                            <input type="number" wire:model="settings.max_tokens" class="form-control @error('settings.max_tokens') is-invalid @enderror" placeholder="Maksimum token sayısı">
                                            @error('settings.max_tokens')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-hint">Bir yanıtın maksimum token sayısı (4096 önerilir)</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Sıcaklık (Temperature)</label>
                                            <input type="range" wire:model="settings.temperature" class="form-range" min="0" max="1" step="0.1">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <small class="form-hint">Daha düşük değerler daha tutarlı yanıtlar üretir</small>
                                                </div>
                                                <div class="col-auto">
                                                    <span class="form-label">{{ $settings['temperature'] }}</span>
                                                </div>
                                            </div>
                                            @error('settings.temperature')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model="settings.enabled">
                                                <span class="form-check-label">AI Asistanı Aktif</span>
                                            </label>
                                            <small class="form-hint">Devre dışı bırakıldığında, hiçbir kullanıcı AI asistanını kullanamaz</small>
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
                                    <form wire:submit.prevent="saveLimits">
                                        <div class="mb-3">
                                            <label class="form-label">Günlük Limit</label>
                                            <input type="number" wire:model="limits.daily_limit" class="form-control @error('limits.daily_limit') is-invalid @enderror" placeholder="Günlük kullanım limiti">
                                            @error('limits.daily_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-hint">Bir kullanıcının günlük olarak gönderebileceği mesaj sayısı</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Aylık Limit</label>
                                            <input type="number" wire:model="limits.monthly_limit" class="form-control @error('limits.monthly_limit') is-invalid @enderror" placeholder="Aylık kullanım limiti">
                                            @error('limits.monthly_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-hint">Bir kullanıcının aylık olarak gönderebileceği mesaj sayısı</small>
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
                                    
                                    <form wire:submit.prevent="savePrompt" class="card mb-4">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Prompt Adı</label>
                                                <input type="text" wire:model="prompt.name" class="form-control @error('prompt.name') is-invalid @enderror" placeholder="Prompt şablonu adı">
                                                @error('prompt.name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Prompt İçeriği</label>
                                                <textarea wire:model="prompt.content" class="form-control @error('prompt.content') is-invalid @enderror" rows="5" placeholder="Sistem prompt içeriği"></textarea>
                                                @error('prompt.content')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" wire:model="prompt.is_default">
                                                    <span class="form-check-label">Varsayılan Prompt</span>
                                                </label>
                                                <small class="form-hint">Varsayılan prompt, özel bir prompt seçilmediğinde kullanılır</small>
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
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('input[wire\\:model="settings.api_key"]');
            
            togglePassword.addEventListener('click', function(e) {
                e.preventDefault();
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>
    @endpush
</div>